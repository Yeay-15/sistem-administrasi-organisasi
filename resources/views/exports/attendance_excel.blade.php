<table>
    <thead>
        <tr>
            <th colspan="{{ $agendas->count() + 2 }}" style="text-align: center; font-weight: bold; font-size: 14px;">
                REKAPITULASI KEHADIRAN PENGURUS KATIBER
            </th>
        </tr>
        <tr>
            <th colspan="{{ $agendas->count() + 2 }}" style="text-align: center; font-weight: bold;">
                PERIODE: {{ strtoupper($monthName) }}
            </th>
        </tr>
        <tr>
            <th></th>
        </tr>
        <tr>
            <th style="font-weight: bold; text-align: center;">No</th>
            <th style="font-weight: bold; width: 300px;">Nama Pengurus</th>
            @foreach ($agendas as $agenda)
                <th style="font-weight: bold; text-align: center; width: 100px;">
                    {{ \Carbon\Carbon::parse($agenda->date)->format('d/m/Y') }}<br>
                    ({{ $agenda->agenda_code }})
                </th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach ($members as $index => $member)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>{{ $member->name }}</td>
                @foreach ($agendas as $agenda)
                    @php
                        $absen = isset($attendances[$member->id])
                            ? $attendances[$member->id]->firstWhere('agenda_id', $agenda->id)
                            : null;
                        $status = $absen ? $absen->status : '-';
                    @endphp
                    <td style="text-align: center;">{{ $status }}</td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>
