<!DOCTYPE html>
<html>

<head>
    <title>Rekap Surat Masuk</title>
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

    <div class="title">REGISTER SURAT MASUK</div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%">No</th>
                <th style="width: 15%">Tanggal Masuk</th>
                <th style="width: 20%">Nomor Surat</th>
                <th style="width: 15%">Asal Surat</th>
                <th style="width: 20%">Perihal</th>
                <th style="width: 15%">Ditujukan Kepada</th>
                <th style="width: 10%">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($filteredLetters as $index => $letter)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($letter->received_date)->format('d/m/Y') }}</td>
                    <td>{{ $letter->reference_number }}</td>
                    <td>{{ $letter->sender }}</td>
                    <td>{{ $letter->subject }}</td>
                    <td>{{ $letter->addressed_to }}</td>
                    <td>{{ $letter->notes }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Tidak ada data surat masuk pada rentang waktu ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>

</html>
