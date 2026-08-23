<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Member;
use App\Models\Agenda;
use App\Models\Attendance;
use App\Models\IncomingLetter;
use App\Models\OutgoingLetter;
use App\Models\Guidance;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // 1. Data KPI
        $kpi = [
            'total_members' => Member::where('status', 'Aktif')->count(),
            'total_agendas' => Agenda::whereMonth('date', $currentMonth)->whereYear('date', $currentYear)->count(),
            'total_incoming' => IncomingLetter::whereMonth('received_date', $currentMonth)->whereYear('received_date', $currentYear)->count(),
            'total_outgoing' => OutgoingLetter::whereMonth('date', $currentMonth)->whereYear('date', $currentYear)->count(),
        ];

        // 2. Data Statistik Absensi (Untuk Grafik)
        $attendanceStats = Attendance::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // 3. Aktivitas Administrasi Terkini (3 Surat Terbaru)
        $recentIncoming = IncomingLetter::latest('received_date')->take(3)->get();
        $recentOutgoing = OutgoingLetter::latest('date')->take(3)->get();

        // 4. Widget Tambahan: Agenda Terdekat (Mulai dari hari ini ke depan)
        $upcomingAgendas = Agenda::where('date', '>=', Carbon::today())
            ->orderBy('date', 'asc')
            ->take(3)
            ->get();

        // 5. Widget Tambahan: Radar Pembinaan (Status Berlaku)
        $activeGuidances = Guidance::with('member')
            ->where('status', 'Berlaku')
            ->latest()
            ->take(4)
            ->get();

        return view('dashboard', compact(
            'kpi',
            'attendanceStats',
            'recentIncoming',
            'recentOutgoing',
            'upcomingAgendas',
            'activeGuidances'
        ));
    }
}
