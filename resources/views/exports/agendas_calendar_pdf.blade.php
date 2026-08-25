<!DOCTYPE html>
<html>

<head>
    <title>Kalender Agenda</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 10px;
            color: #1e293b;
        }

        .title {
            text-align: center;
            font-size: 15px;
            font-weight: bold;
            margin-bottom: 3px;
        }

        .subtitle {
            text-align: center;
            font-size: 12px;
            color: #475569;
            margin-bottom: 16px;
        }

        table.calendar {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        table.calendar th {
            background-color: #e2e8f0;
            border: 1px solid #cbd5e1;
            padding: 6px 4px;
            font-size: 10px;
            text-transform: uppercase;
        }

        table.calendar td {
            border: 1px solid #cbd5e1;
            vertical-align: top;
            height: 78px;
            padding: 3px;
        }

        .day-number {
            font-weight: bold;
            font-size: 10px;
            color: #334155;
        }

        .event-badge {
            color: #ffffff;
            font-size: 7.5px;
            font-weight: bold;
            padding: 2px 4px;
            border-radius: 3px;
            margin-top: 2px;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }

        .blank-cell {
            background-color: #f8fafc;
        }

        .legend {
            margin-top: 16px;
        }

        .legend-item {
            display: inline-block;
            margin-right: 12px;
            font-size: 9px;
        }

        .legend-swatch {
            display: inline-block;
            width: 9px;
            height: 9px;
            margin-right: 3px;
            border-radius: 2px;
        }
    </style>
</head>

<body>

    @php
        $monthNames = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        $daysInMonth = \Carbon\Carbon::create($year, $month + 1, 1)->daysInMonth;
        $firstDayOfWeek = \Carbon\Carbon::create($year, $month + 1, 1)->dayOfWeekIso; // 1 (Senin) - 7 (Minggu)
        $leadingBlanks = $firstDayOfWeek - 1;

        $eventsByDate = [];
        foreach ($calendarEvents as $event) {
            $eventsByDate[$event['date']][] = $event;
        }

        $cells = array_fill(0, $leadingBlanks, null);
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $cells[] = $d;
        }
        while (count($cells) % 7 !== 0) {
            $cells[] = null;
        }
        $weeks = array_chunk($cells, 7);
    @endphp

    <div class="title">KALENDER AGENDA & KEGIATAN</div>
    <div class="subtitle">{{ $monthNames[$month] }} {{ $year }}</div>

    <table class="calendar">
        <thead>
            <tr>
                @foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $dayName)
                    <th>{{ $dayName }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($weeks as $week)
                <tr>
                    @foreach ($week as $day)
                        @if (is_null($day))
                            <td class="blank-cell"></td>
                        @else
                            @php
                                $dateKey = sprintf('%04d-%02d-%02d', $year, $month + 1, $day);
                                $dayEvents = $eventsByDate[$dateKey] ?? [];
                            @endphp
                            <td>
                                <div class="day-number">{{ $day }}</div>
                                @foreach ($dayEvents as $event)
                                    <div class="event-badge" style="{{ $event['colorStyle'] }}">{{ $event['title'] }}</div>
                                @endforeach
                            </td>
                        @endif
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="legend">
        <span class="legend-item"><span class="legend-swatch" style="background-color:#2563eb;"></span>BPH</span>
        <span class="legend-item"><span class="legend-swatch" style="background-color:#ef4444;"></span>Kaderisasi</span>
        <span class="legend-item"><span class="legend-swatch" style="background-color:#f97316;"></span>Korwil/Koordinator</span>
        <span class="legend-item"><span class="legend-swatch" style="background-color:#10b981;"></span>Humas</span>
        <span class="legend-item"><span class="legend-swatch" style="background-color:#06b6d4;"></span>Infokom</span>
        <span class="legend-item"><span class="legend-swatch" style="background-color:#ec4899;"></span>Minat & Bakat</span>
        <span class="legend-item"><span class="legend-swatch" style="background-color:#059669;"></span>Agama</span>
        <span class="legend-item"><span class="legend-swatch" style="background-color:#eab308;"></span>Pendidikan</span>
        <span class="legend-item"><span class="legend-swatch" style="background: linear-gradient(90deg, #ef4444, #f97316);"></span>Kolaborasi Antar Divisi</span>
    </div>

</body>

</html>
