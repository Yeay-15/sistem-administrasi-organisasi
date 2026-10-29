<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Committee;
use App\Models\LetterTemplate;
use App\Models\OutgoingLetter;
use App\Services\LetterGenerator;
use DateTimeImmutable;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * Surat Generator: buat surat keluar (.docx) dari template + preset kepanitiaan.
 * Setiap surat otomatis tercatat di register Surat Keluar berstatus Draft.
 */
class LetterGeneratorController extends Controller
{
    public function __construct(private LetterGenerator $generator)
    {
    }

    // ---- Surat untuk sebuah kepanitiaan (acara) --------------------------

    public function pickForCommittee(Committee $committee)
    {
        $templates = LetterTemplate::active()->category(LetterTemplate::CATEGORY_COMMITTEE)->orderBy('sort_order')->get();

        return view('letter_generator.templates', [
            'templates' => $templates,
            'committee' => $committee,
            'heading' => 'Buat Surat untuk ' . $committee->name,
            'backUrl' => route('committees.show', $committee),
            'nextRoute' => fn (LetterTemplate $t) => route('committees.letters.create', [$committee, $t]),
        ]);
    }

    public function formForCommittee(Committee $committee, LetterTemplate $letterTemplate)
    {
        abort_unless($letterTemplate->is_active && $letterTemplate->isCommitteeBased(), 404);

        return $this->form($letterTemplate, $committee, route('committees.letters.store', [$committee, $letterTemplate]), route('committees.letters.index', $committee));
    }

    public function storeForCommittee(Request $request, Committee $committee, LetterTemplate $letterTemplate)
    {
        abort_unless($letterTemplate->is_active && $letterTemplate->isCommitteeBased(), 404);

        return $this->store($request, $letterTemplate, $committee);
    }

    // ---- Pintu masuk tunggal: semua template, dari menu Surat Keluar ------

    public function hub()
    {
        $templates = LetterTemplate::active()->orderBy('sort_order')->get();

        // Yang belum selesai di atas, yang Selesai tetap bisa dipilih (mis. surat susulan/ucapan terima kasih).
        $committees = Committee::query()
            ->orderByRaw("CASE WHEN status = 'Selesai' THEN 1 ELSE 0 END")
            ->orderByDesc('id')
            ->get()
            ->map(fn (Committee $c) => [
                'id' => $c->id,
                'name' => $c->name,
                'status' => $c->status,
                'acara' => LetterGenerator::eventDatesLabel($c->event_dates),
                'theme' => (string) $c->theme,
                'venue' => (string) $c->venue,
                'time' => $c->start_time ? str_replace(':', '.', substr((string) $c->start_time, 0, 5)) . ' WIB' : '',
            ])->values()->all();

        // "__C__" diganti id kepanitiaan di browser setelah dipilih.
        $urls = $templates->filter(fn (LetterTemplate $t) => $t->isCommitteeBased())
            ->mapWithKeys(fn (LetterTemplate $t) => [
                $t->code => route('committees.letters.create', ['committee' => '__C__', 'letterTemplate' => $t]),
            ])->all();

        return view('letter_generator.index', [
            'templates' => $templates,
            'hub' => ['committees' => $committees, 'urls' => $urls],
        ]);
    }

    // ---- Surat organisasi (tanpa kepanitiaan), mis. audiensi -------------

    public function formForOrganization(LetterTemplate $letterTemplate)
    {
        abort_unless($letterTemplate->is_active && ! $letterTemplate->isCommitteeBased(), 404);

        return $this->form($letterTemplate, null, route('letter-generator.store', $letterTemplate), route('letter-generator.index'));
    }

    public function storeForOrganization(Request $request, LetterTemplate $letterTemplate)
    {
        abort_unless($letterTemplate->is_active && ! $letterTemplate->isCommitteeBased(), 404);

        return $this->store($request, $letterTemplate, null);
    }

    // ---- Unduh ulang dari surat yang sudah tercatat -----------------------

    public function download(OutgoingLetter $outgoingLetter)
    {
        $template = $this->templateOf($outgoingLetter);

        $path = $this->generator->render($template, $outgoingLetter->payload);

        return response()
            ->download($path, $this->generator->downloadName($template, $outgoingLetter, 'docx'))
            ->deleteFileAfterSend(true);
    }

    public function downloadPdf(OutgoingLetter $outgoingLetter)
    {
        $template = $this->templateOf($outgoingLetter);

        try {
            $path = $this->generator->renderPdf($template, $outgoingLetter->payload);
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', 'PDF belum bisa dibuat di server ini (LibreOffice tidak tersedia / gagal). Unduh versi Word lalu simpan sebagai PDF.');
        }

        return response()
            ->download($path, $this->generator->downloadName($template, $outgoingLetter, 'pdf'), ['Content-Type' => 'application/pdf'])
            ->deleteFileAfterSend(true);
    }

    private function templateOf(OutgoingLetter $letter): LetterTemplate
    {
        $template = $letter->letterTemplate;

        abort_if(! $template || ! $letter->payload, 404, 'Surat ini tidak dibuat lewat Surat Generator.');
        abort_unless(is_file($template->path()), 404, 'File template tidak ditemukan di server.');

        return $template;
    }

    // ---- Internal ---------------------------------------------------------

    private function form(LetterTemplate $template, ?Committee $committee, string $action, string $backUrl)
    {
        abort_unless(is_file($template->path()), 500, 'File template "' . $template->file_name . '" belum ada di resources/letter_templates.');

        return view('letter_generator.form', [
            'template' => $template,
            'committee' => $committee,
            'defaults' => $this->generator->defaults($template, $committee),
            'templateFields' => $this->generator->templateFields($template),
            'eventFields' => $template->isCommitteeBased() ? LetterGenerator::eventFields() : [],
            'signerLabels' => LetterGenerator::SIGNER_LABELS,
            'pdfAvailable' => $this->generator->pdfAvailable(),
            'action' => $action,
            'backUrl' => $backUrl,
        ]);
    }

    private function store(Request $request, LetterTemplate $template, ?Committee $committee)
    {
        $validated = $request->validate($this->generator->rules($template) + [
            'format' => ['nullable', 'in:docx,pdf'],
        ]);
        $format = $validated['format'] ?? 'docx';

        if ($format === 'pdf' && ! $this->generator->pdfAvailable()) {
            throw ValidationException::withMessages([
                'format' => 'PDF belum tersedia di server ini (LibreOffice belum terpasang). Pilih Word.',
            ]);
        }

        $date = new DateTimeImmutable($validated['tanggal_surat']);
        $reference = $this->generator->referenceNumber($template, (int) $validated['nomor_urut'], $date);

        if (OutgoingLetter::where('reference_number', $reference)->exists()) {
            throw ValidationException::withMessages([
                'nomor_urut' => "Nomor {$reference} sudah terpakai. Gunakan nomor urut lain.",
            ]);
        }

        $payload = $this->generator->buildPayload($template, $validated);

        // committee_id / letter_template_id / payload sengaja TIDAK masuk $fillable
        // (controller surat keluar manual memakai $request->all()), jadi diisi langsung.
        $letter = new OutgoingLetter([
            'reference_number' => $reference,
            'date' => $date->format('Y-m-d'),
            'type' => $template->letter_type,
            'subject' => $template->subject,
            'destination' => $this->generator->destination($template, $payload),
            'signatory' => $template->signatory_label,
            'status' => 'Draft',
        ]);
        $letter->committee_id = $committee?->id;
        $letter->letter_template_id = $template->id;
        $letter->payload = $payload;
        $letter->save();

        AuditLog::record('Buat Surat dari Template', "Membuat surat \"{$template->name}\" nomor: {$reference}" . ($committee ? " untuk kepanitiaan \"{$committee->name}\"" : ''));

        return redirect()
            ->route('outgoing-letters.index')
            ->with('success', "Surat nomor {$reference} dibuat sebagai Draft. Unduhan " . ($format === 'pdf' ? 'PDF' : 'Word') . ' akan dimulai otomatis.')
            ->with('download', route($format === 'pdf' ? 'outgoing-letters.download-pdf' : 'outgoing-letters.download-docx', $letter));
    }
}
