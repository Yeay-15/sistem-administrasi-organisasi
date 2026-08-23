<!DOCTYPE html>
<html>

<head>
    <title>Rekap Surat Keluar</title>
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

    <div class="header">
        <h2>KELUARGA MAHASISWA TEBING TINGGI BERSATU (KATIBER)</h2>
        <p>Lhokseumawe - Aceh Utara</p>
    </div>

    <div class="title">REGISTER SURAT KELUAR</div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%">No</th>
                <th style="width: 20%">Nomor Surat</th>
                <th style="width: 10%">Tanggal</th>
                <th style="width: 5%">Jenis</th>
                <th style="width: 20%">Tujuan</th>
                <th style="width: 25%">Perihal</th>
                <th style="width: 15%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($filteredLetters as $index => $letter)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $letter->reference_number }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($letter->date)->format('d/m/Y') }}</td>
                    <td class="text-center">{{ $letter->type }}</td>
                    <td>{{ $letter->destination }}</td>
                    <td>{{ $letter->subject }}</td>
                    <td class="text-center">{{ $letter->status }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Tidak ada data surat keluar pada rentang waktu ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="signature-table">
        <tr>
            <td>
                Mengetahui,<br>
                <strong>Ketua Umum</strong>
                <br><br><br><br>
                ( ......................................... )
            </td>
            <td>
                Lhokseumawe, {{ \Carbon\Carbon::now()->format('d F Y') }}<br>
                <strong>Sekretaris Umum</strong>
                <br><br><br><br>
                ( ......................................... )
            </td>
        </tr>
    </table>

</body>

</html>
