<?php

namespace App\Http\Controllers;

use App\Models\OutgoingLetter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\AuditLog;
use App\Exports\OutgoingLettersExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Carbon\Carbon;

class OutgoingLetterController extends Controller
{
    public function index(Request $request)
    {
        $query = OutgoingLetter::query();

        // 1. Logika Filter
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('reference_number', 'like', '%' . $request->search . '%')
                    ->orWhere('subject', 'like', '%' . $request->search . '%')
                    ->orWhere('destination', 'like', '%' . $request->search . '%');
            });
        }

        // PERBAIKAN: Urutkan berdasarkan tanggal menurun, LALU berdasarkan ID menurun
        $filteredLetters = $query->orderBy('date', 'desc')->orderBy('id', 'desc')->get();

        // 2. Logika Export Excel
        if ($request->export === 'excel') {
            return Excel::download(new OutgoingLettersExport($filteredLetters), 'LPJ_Surat_Keluar_KATIBER.xlsx');
        }

        // 3. Logika Export PDF
        if ($request->export === 'pdf') {
            // Kita setting kertas menjadi A4 Landscape
            $pdf = Pdf::loadView('exports.outgoing_letters_pdf', compact('filteredLetters'))
                ->setPaper('a4', 'landscape');
            return $pdf->download('LPJ_Surat_Keluar_KATIBER.pdf');
        }

        // 4. Jika bukan export, tampilkan halaman HTML biasa
        return view('outgoing_letters.index', compact('filteredLetters'));
    }

    public function create()
    {
        return view('outgoing_letters.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'reference_number' => ['required', 'string', 'max:100', 'unique:outgoing_letters,reference_number'],
            'date' => ['required', 'date'],
            'type' => ['required', 'in:A,B'],
            'subject' => ['required', 'string', 'max:255'],
            'destination' => ['required', 'string', 'max:255'],
            'signatory' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:Draft,Terkirim,Dibatalkan'],
            'file_path' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
        ]);

        $data = $request->all();

        if ($request->hasFile('file_path')) {
            // 1. Buat format nama file
            $date = Carbon::parse($request->date)->format('Y-m-d');
            $no = Str::slug($request->reference_number, '-');
            $dest = Str::slug($request->destination, '-');
            $subject = Str::slug(Str::limit($request->subject, 30, ''), '-');

            $filename = "{$date}_{$no}_{$dest}_{$subject}.pdf";

            // 2. Simpan menggunakan storeAs
            $path = $request->file('file_path')->storeAs('outgoing_letters', $filename, 'public');
            $data['file_path'] = $path;
        }

        OutgoingLetter::create($data);

        AuditLog::record('Create Surat Keluar', 'Meregistrasi Surat Keluar nomor: ' . $data['reference_number']);

        return redirect()->route('outgoing-letters.index')->with('success', 'Data Surat Keluar berhasil ditambahkan.');
    }

    public function edit(OutgoingLetter $outgoingLetter)
    {
        return view('outgoing_letters.edit', compact('outgoingLetter'));
    }

    public function update(Request $request, OutgoingLetter $outgoingLetter)
    {
        $request->validate([
            'reference_number' => ['required', 'string', 'max:100', 'unique:outgoing_letters,reference_number,' . $outgoingLetter->id],
            'date' => ['required', 'date'],
            'type' => ['required', 'in:A,B'],
            'subject' => ['required', 'string', 'max:255'],
            'destination' => ['required', 'string', 'max:255'],
            'signatory' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:Draft,Terkirim,Dibatalkan'],
            'file_path' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
        ]);

        $data = $request->all();

        if ($request->hasFile('file_path')) {
            if ($outgoingLetter->file_path && Storage::disk('public')->exists($outgoingLetter->file_path)) {
                Storage::disk('public')->delete($outgoingLetter->file_path);
            }

            $date = Carbon::parse($request->date)->format('Y-m-d');
            $no = Str::slug($request->reference_number, '-');
            $dest = Str::slug($request->destination, '-');
            $subject = Str::slug(Str::limit($request->subject, 30, ''), '-');

            $filename = "{$date}_{$no}_{$dest}_{$subject}.pdf";

            // 2. Simpan menggunakan storeAs
            $path = $request->file('file_path')->storeAs('outgoing_letters', $filename, 'public');
            $data['file_path'] = $path;
        }

        $outgoingLetter->update($data);

        AuditLog::record('Update Surat Keluar', 'Mengubah data/status Surat Keluar nomor: ' . $data['reference_number'] . ' menjadi ' . $data['status']);

        return redirect()->route('outgoing-letters.index')->with('success', 'Data Surat Keluar berhasil diperbarui.');
    }

    public function destroy(OutgoingLetter $outgoingLetter)
    {
        if ($outgoingLetter->file_path && Storage::disk('public')->exists($outgoingLetter->file_path)) {
            Storage::disk('public')->delete($outgoingLetter->file_path);
        }

        $outgoingLetter->delete();
        AuditLog::record('Delete Surat Keluar', 'Menghapus Surat Keluar nomor: ' . $outgoingLetter->reference_number);

        return redirect()->route('outgoing-letters.index')->with('success', 'Surat Keluar beserta arsipnya berhasil dihapus.');
    }
}
