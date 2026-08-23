<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class IncomingLettersExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $letters;

    public function __construct($letters)
    {
        $this->letters = $letters;
    }

    public function collection(): Collection
    {
        return $this->letters;
    }

    public function headings(): array
    {
        return [
            'Tanggal Masuk',
            'Nomor Surat',
            'Asal Surat',
            'Perihal',
            'Ditujukan Kepada',
            'Keterangan'
        ];
    }

    public function map($letter): array
    {
        return [
            Carbon::parse($letter->received_date)->format('d/m/Y'),
            $letter->reference_number,
            $letter->sender,
            $letter->subject,
            $letter->addressed_to,
            $letter->notes,
        ];
    }
}
