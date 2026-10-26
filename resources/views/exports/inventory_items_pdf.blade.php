<!DOCTYPE html>
<html>

<head>
    <title>Data Inventaris</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 11px;
        }

        .title {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .subtitle {
            text-align: center;
            font-size: 10px;
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

    <div class="title">DATA INVENTARIS</div>
    <div class="subtitle">Dicetak pada {{ now()->format('d/m/Y') }} &dash; {{ $items->count() }} jenis barang</div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%">No</th>
                <th style="width: 27%">Nama Barang</th>
                <th style="width: 9%">Jumlah</th>
                <th style="width: 10%">Satuan</th>
                <th style="width: 16%">Tempat</th>
                <th style="width: 13%">Kedaluwarsa</th>
                <th style="width: 20%">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->name }}</td>
                    <td class="text-center">{{ $item->quantity_label }}</td>
                    <td class="text-center">{{ $item->unit }}</td>
                    <td>{{ $item->location }}</td>
                    <td class="text-center">{{ $item->expired_at ? $item->expired_at->format('d/m/Y') : '' }}</td>
                    <td>{{ $item->notes }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Tidak ada data inventaris.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>

</html>
