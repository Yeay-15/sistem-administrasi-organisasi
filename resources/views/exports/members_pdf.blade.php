<!DOCTYPE html>
<html>

<head>
    <title>Data Pengurus KATIBER</title>
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

    <div class="title">DATA PENGURUS KATIBER</div>

    <table>
        <thead>
            <tr>
                <th style="width: 4%">No</th>
                <th style="width: 16%">Nama</th>
                <th style="width: 10%">NIM</th>
                <th style="width: 14%">Jurusan</th>
                <th style="width: 12%">Divisi</th>
                <th style="width: 14%">Jabatan</th>
                <th style="width: 8%">Status</th>
                <th style="width: 12%">Bergabung</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($members as $index => $member)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $member->name }}</td>
                    <td>{{ $member->student_id }}</td>
                    <td>{{ $member->major }}</td>
                    <td>{{ $member->division->name ?? '-' }}</td>
                    <td>{{ $member->position }}</td>
                    <td class="text-center">{{ $member->status }}</td>
                    <td class="text-center">{{ $member->join_date ? \Carbon\Carbon::parse($member->join_date)->format('d/m/Y') : '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">Tidak ada data pengurus.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>

</html>
