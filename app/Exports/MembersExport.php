<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class MembersExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $members;

    public function __construct($members)
    {
        $this->members = $members;
    }

    public function collection(): Collection
    {
        return $this->members;
    }

    public function headings(): array
    {
        return [
            'Nama',
            'NIM',
            'Angkatan',
            'Jurusan',
            'Universitas',
            'Divisi',
            'Jabatan',
            'Status',
            'Tanggal Bergabung',
            'Keterangan',
        ];
    }

    public function map($member): array
    {
        return [
            $member->name,
            $member->student_id,
            $member->batch,
            $member->major,
            $member->university,
            $member->division->name ?? '-',
            $member->position,
            $member->status,
            $member->join_date ? \Carbon\Carbon::parse($member->join_date)->format('d/m/Y') : '-',
            $member->notes,
        ];
    }
}
