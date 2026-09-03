<!DOCTYPE html>
<html>

<head>
    <title>Laporan Aspirasi KATIBER</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 11px;
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
    </style>
</head>

<body>

    <div class="title">LAPORAN ASPIRASI MAHASISWA KATIBER</div>

    <table>
        <thead>
            <tr>
                <th style="width: 4%">No</th>
                <th style="width: 12%">Tanggal</th>
                <th style="width: 15%">Pengirim</th>
                <th style="width: 10%">Kategori</th>
                <th style="width: 45%">Isi Pesan</th>
                <th style="width: 14%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($aspirations as $index => $aspiration)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $aspiration->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $aspiration->display_name }}</td>
                    <td>{{ $aspiration->category }}</td>
                    <td>{{ $aspiration->message }}</td>
                    <td class="text-center">{{ $aspiration->is_read ? 'Sudah Dibaca' : 'Belum Dibaca' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Tidak ada data aspirasi.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>

</html>
