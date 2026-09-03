<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Member;
use App\Models\Agenda;
use App\Models\Attendance;
use App\Models\IncomingLetter;
use App\Models\OutgoingLetter;
use App\Models\Guidance;
use App\Models\PageVisit;
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

        // 6. Statistik Website (traffic pengunjung situs publik)
        $websiteStats = $this->buildWebsiteStats();

        return view('dashboard', compact(
            'kpi',
            'attendanceStats',
            'recentIncoming',
            'recentOutgoing',
            'upcomingAgendas',
            'activeGuidances',
            'websiteStats'
        ));
    }

    /**
     * Menyusun ringkasan traffic situs publik dari tabel page_visits:
     * KPI 30 hari terakhir, tren kunjungan harian 14 hari, dan halaman
     * yang paling banyak dikunjungi bulan ini.
     */
    private function buildWebsiteStats(): array
    {
        $since30d = Carbon::today()->subDays(29);

        $kpi = [
            'visits_today' => PageVisit::whereDate('created_at', Carbon::today())->count(),
            'visits_30d' => PageVisit::where('created_at', '>=', $since30d)->count(),
            'unique_visitors_30d' => PageVisit::where('created_at', '>=', $since30d)
                ->distinct('visitor_key')
                ->count('visitor_key'),
        ];

        // Tren kunjungan harian, 14 hari terakhir
        $dailyRaw = PageVisit::selectRaw('DATE(created_at) as visit_date, COUNT(*) as total')
            ->where('created_at', '>=', Carbon::today()->subDays(13))
            ->groupBy('visit_date')
            ->pluck('total', 'visit_date');

        $trafficLabels = [];
        $trafficSeries = [];
        for ($i = 13; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $trafficLabels[] = $date->translatedFormat('d M');
            $trafficSeries[] = (int) ($dailyRaw[$date->toDateString()] ?? 0);
        }

        // Halaman terpopuler, 30 hari terakhir
        $topPages = PageVisit::where('created_at', '>=', $since30d)
            ->selectRaw('path, route_name, COUNT(*) as total')
            ->groupBy('path', 'route_name')
            ->orderByDesc('total')
            ->take(6)
            ->get();

        return compact('kpi', 'trafficLabels', 'trafficSeries', 'topPages');
    }
}
