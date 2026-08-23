<?php

namespace App\Http\Controllers;

use App\Models\IncomingLetter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Exports\IncomingLettersExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Carbon\Carbon;

class IncomingLetterController extends Controller
{
    public function index(Request $request)
    {
        $query = IncomingLetter::query();

        // 1. Logika Filter
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('received_date', [$request->start_date, $request->end_date]);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('reference_number', 'like', '%' . $request->search . '%')
                    ->orWhere('subject', 'like', '%' . $request->search . '%')
                    ->orWhere('sender', 'like', '%' . $request->search . '%');
            });
        }

        // PERBAIKAN: Urutkan berdasarkan tanggal masuk menurun, LALU berdasarkan ID menurun
        $filteredLetters = $query->orderBy('received_date', 'desc')->orderBy('id', 'desc')->get();

        // 2. Logika Export Excel
        if ($request->export === 'excel') {
            return Excel::download(new IncomingLettersExport($filteredLetters), 'LPJ_Surat_Masuk_KATIBER.xlsx');
        }

        // 3. Logika Export PDF
        if ($request->export === 'pdf') {
            $pdf = Pdf::loadView('exports.incoming_letters_pdf', compact('filteredLetters'))
                ->setPaper('a4', 'landscape');
            return $pdf->download('LPJ_Surat_Masuk_KATIBER.pdf');
        }

        // 4. Jika bukan export, tampilkan halaman HTML
        return view('incoming_letters.index', compact('filteredLetters'));
    }

    public function create()
    {
        return view('incoming_letters.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'received_date' => ['required', 'date'],
            'reference_number' => ['required', 'string', 'max:100'],
            'sender' => ['required', 'string', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'addressed_to' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'file_path' => ['nullable', 'file', 'mimes:pdf', 'max:5120'], // Hanya PDF, Max 5MB
        ]);

        $data = $request->all();

        // Menangani proses upload file jika ada
        if ($request->hasFile('file_path')) {
            // 1. Buat format nama file
            $date = Carbon::parse($request->received_date)->format('Y-m-d');
            $no = Str::slug($request->reference_number, '-');
            $sender = Str::slug($request->sender, '-');
            $subject = Str::slug(Str::limit($request->subject, 30, ''), '-'); // Batasi perihal max 30 karakter

            $filename = "{$date}_{$no}_{$sender}_{$subject}.pdf";

            // 2. Simpan menggunakan storeAs
            $path = $request->file('file_path')->storeAs('incoming_letters', $filename, 'public');
            $data['file_path'] = $path;
        }

        IncomingLetter::create($data);

        return redirect()->route('incoming-letters.index')->with('success', 'Data Surat Masuk berhasil dicatat.');
    }

    public function edit(IncomingLetter $incomingLetter)
    {
        return view('incoming_letters.edit', compact('incomingLetter'));
    }

    public function update(Request $request, IncomingLetter $incomingLetter)
    {
        $request->validate([
            'received_date' => ['required', 'date'],
            'reference_number' => ['required', 'string', 'max:100'],
            'sender' => ['required', 'string', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'addressed_to' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'file_path' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
        ]);

        $data = $request->all();

        if ($request->hasFile('file_path')) {
            // Hapus file lama jika ada
            if ($incomingLetter->file_path && Storage::disk('public')->exists($incomingLetter->file_path)) {
                Storage::disk('public')->delete($incomingLetter->file_path);
            }
            $date = Carbon::parse($request->received_date)->format('Y-m-d');
            $no = Str::slug($request->reference_number, '-');
            $sender = Str::slug($request->sender, '-');
            $subject = Str::slug(Str::limit($request->subject, 30, ''), '-'); // Batasi perihal max 30 karakter

            $filename = "{$date}_{$no}_{$sender}_{$subject}.pdf";

            // 2. Simpan menggunakan storeAs
            $path = $request->file('file_path')->storeAs('incoming_letters', $filename, 'public');
            $data['file_path'] = $path;
        }

        $incomingLetter->update($data);

        return redirect()->route('incoming-letters.index')->with('success', 'Data Surat Masuk berhasil diperbarui.');
    }

    public function destroy(IncomingLetter $incomingLetter)
    {
        // Hapus file fisik dari storage jika ada
        if ($incomingLetter->file_path && Storage::disk('public')->exists($incomingLetter->file_path)) {
            Storage::disk('public')->delete($incomingLetter->file_path);
        }

        $incomingLetter->delete();

        return redirect()->route('incoming-letters.index')->with('success', 'Surat Masuk beserta arsipnya berhasil dihapus.');
    }
}
