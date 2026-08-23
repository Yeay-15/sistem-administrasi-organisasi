<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class OutgoingLettersExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $letters;

    // Menerima data hasil filter dari Controller
    public function __construct($letters)
    {
        $this->letters = $letters;
    }

    public function collection(): Collection
    {
        return $this->letters;
    }

    // Header Kolom Excel
    public function headings(): array
    {
        return [
            'Nomor Surat',
            'Tanggal',
            'Jenis (A/B)',
            'Perihal',
            'Tujuan/Pemohon',
            'Penandatangan',
            'Status'
        ];
    }

    // Memetakan isi baris (Format sesuai format LPJ)
    public function map($letter): array
    {
        return [
            $letter->reference_number,
            Carbon::parse($letter->date)->format('d/m/Y'),
            $letter->type,
            $letter->subject,
            $letter->destination,
            $letter->signatory,
            $letter->status,
        ];
    }
}
