<?php

namespace App\Http\Controllers;

use App\Models\Aspiration;
use App\Exports\AspirationsExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class AspirationController extends Controller
{
    public function index(Request $request)
    {
        $query = Aspiration::latest();

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->boolean('unread_only')) {
            $query->where('is_read', false);
        }

        if ($request->export === 'excel') {
            return Excel::download(new AspirationsExport($query->get()), 'Laporan_Aspirasi_KATIBER.xlsx');
        }

        if ($request->export === 'pdf') {
            $aspirations = $query->get();
            $pdf = Pdf::loadView('exports.aspirations_pdf', compact('aspirations'))->setPaper('a4', 'landscape');
            return $pdf->download('Laporan_Aspirasi_KATIBER.pdf');
        }

        $aspirations = $query->paginate(15)->withQueryString();
        $unreadCount = Aspiration::where('is_read', false)->count();

        return view('aspirations.index', compact('aspirations', 'unreadCount'));
    }

    public function markAsRead(Aspiration $aspiration)
    {
        $aspiration->update(['is_read' => true]);

        return back()->with('success', 'Pesan ditandai sudah dibaca.');
    }

    public function destroy(Aspiration $aspiration)
    {
        $aspiration->delete();

        return back()->with('success', 'Pesan aspirasi berhasil dihapus.');
    }
}
