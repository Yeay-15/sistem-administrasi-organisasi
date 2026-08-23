<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AttendanceReportExport implements FromView, ShouldAutoSize
{
    protected $agendas;
    protected $members;
    protected $attendances;
    protected $month;

    public function __construct($agendas, $members, $attendances, $month)
    {
        $this->agendas = $agendas;
        $this->members = $members;
        $this->attendances = $attendances;
        $this->month = $month;
    }

    public function view(): View
    {
        // Me-render file blade khusus excel
        return view('exports.attendance_excel', [
            'agendas' => $this->agendas,
            'members' => $this->members,
            'attendances' => $this->attendances,
            'monthName' => $this->month
        ]);
    }
}
