<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Agenda;
use App\Models\Member;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Exports\AttendanceReportExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class AttendanceReportController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil filter dari request
        $monthFilter = $request->input('month', Carbon::now()->format('Y-m'));
        $sortFilter = $request->input('sort', 'abjad'); // Default urut abjad

        $year = substr($monthFilter, 0, 4);
        $month = substr($monthFilter, 5, 2);

        // 2. Ambil semua agenda pada bulan tersebut (sebagai Kolom Matriks)
        $agendas = Agenda::whereYear('date', $year)
            ->whereMonth('date', $month)
            ->orderBy('date', 'asc')
            ->get();

        // 3. Query dasar pengurus aktif
        $membersQuery = Member::with('division')->where('status', 'Aktif');

        // 4. Logika Pengurutan (Sorting)
        if ($sortFilter === 'divisi_jabatan') {
            // Urutkan berdasarkan ID Divisi (BPH -> Kader -> Korwil, dll)
            // LALU urutkan berdasarkan hierarki jabatan khusus menggunakan FIELD()
            $membersQuery->orderBy('division_id', 'asc')
                ->orderByRaw("FIELD(position, 'Ketua Umum', 'Sekretaris Umum', 'Bendahara Umum', 'Ketua Divisi', 'Sekretaris Divisi', 'Anggota Divisi')")
                ->orderBy('name', 'asc'); // Jika jabatannya sama, urutkan namanya sesuai abjad
        } else {
            // Default: Urut abjad biasa
            $membersQuery->orderBy('name', 'asc');
        }

        $members = $membersQuery->get();

        // 5. Ambil data absensi dan kelompokkan berdasarkan member_id
        $agendaIds = $agendas->pluck('id');
        $attendances = Attendance::whereIn('agenda_id', $agendaIds)
            ->get()
            ->groupBy('member_id');

        $monthName = Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y');

        // 6. Logika Export (Karena $members sudah berurut, hasil export otomatis ikut berurut)
        if ($request->export === 'excel') {
            return Excel::download(new AttendanceReportExport($agendas, $members, $attendances, $monthName), 'Rekap_Absensi_' . $monthName . '.xlsx');
        }

        if ($request->export === 'pdf') {
            $pdf = Pdf::loadView('exports.attendance_pdf', compact('agendas', 'members', 'attendances', 'monthName'))
                ->setPaper('a4', 'landscape');
            return $pdf->download('Rekap_Absensi_' . $monthName . '.pdf');
        }

        // Tampilkan halaman HTML
        return view('attendance_reports.index', compact('agendas', 'members', 'attendances', 'monthFilter', 'monthName', 'sortFilter'));
    }
}
