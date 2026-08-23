<!DOCTYPE html>
<html>

<head>
    <title>Rekap Absensi - {{ $monthName }}</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 10px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid black;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .header h2 {
            margin: 0;
            font-size: 14px;
            font-weight: bold;
        }

        .header p {
            margin: 5px 0 0 0;
            font-size: 11px;
        }

        .title {
            text-align: center;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
        }

        th {
            background-color: #e2e8f0;
        }

        .text-left {
            text-align: left;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>KELUARGA MAHASISWA TEBING TINGGI BERSATU (KATIBER)</h2>
        <p>Lhokseumawe - Aceh Utara</p>
    </div>
    <div class="title">REKAPITULASI KEHADIRAN PENGURUS - PERIODE {{ strtoupper($monthName) }}</div>

    <table>
        <thead>
            <tr>
                <th style="width: 3%">No</th>
                <th class="text-left" style="width: 15%">Nama Pengurus</th>
                @foreach ($agendas as $agenda)
                    <th>{{ \Carbon\Carbon::parse($agenda->date)->format('d/m') }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($members as $index => $member)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="text-left">{{ $member->name }}</td>
                    @foreach ($agendas as $agenda)
                        @php
                            $absen = isset($attendances[$member->id])
                                ? $attendances[$member->id]->firstWhere('agenda_id', $agenda->id)
                                : null;
                            $status = $absen ? $absen->status : '-';
                        @endphp
                        <td>{{ $status }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
