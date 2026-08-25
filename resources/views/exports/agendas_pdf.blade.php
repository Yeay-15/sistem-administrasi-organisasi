<!DOCTYPE html>
<html>

<head>
    <title>Rekap Agenda</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 11px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid black;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header h2 {
            margin: 0;
            font-size: 16px;
            font-weight: bold;
        }

        .header p {
            margin: 5px 0 0 0;
            font-size: 12px;
        }

        .title {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background-color: #e2e8f0;
            text-align: center;
        }

        .text-center {
            text-align: center;
        }

        .signature-table {
            width: 100%;
            margin-top: 40px;
            border: none;
        }

        .signature-table td {
            border: none;
            text-align: center;
            width: 50%;
            padding-top: 50px;
        }
    </style>
</head>

<body>

    <div class="title">REKAP AGENDA & KEGIATAN</div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%">No</th>
                <th style="width: 12%">Kode Agenda</th>
                <th style="width: 25%">Nama Agenda</th>
                <th style="width: 12%">Tanggal</th>
                <th style="width: 13%">Jenis</th>
                <th style="width: 15%">Penanggung Jawab</th>
                <th style="width: 10%">Status</th>
                <th style="width: 8%">Catatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($agendas as $index => $agenda)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $agenda->agenda_code }}</td>
                    <td>{{ $agenda->name }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($agenda->date)->format('d/m/Y') }}</td>
                    <td>{{ $agenda->type }}</td>
                    <td>{{ $agenda->person_in_charge }}</td>
                    <td class="text-center">{{ $agenda->status }}</td>
                    <td>{{ $agenda->notes }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">Tidak ada data agenda yang cocok dengan pencarian.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>

</html>
