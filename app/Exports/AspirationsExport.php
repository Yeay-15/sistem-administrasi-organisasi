<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AspirationsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $aspirations;

    public function __construct($aspirations)
    {
        $this->aspirations = $aspirations;
    }

    public function collection(): Collection
    {
        return $this->aspirations;
    }

    public function headings(): array
    {
        return [
            'Tanggal Masuk',
            'Nama Pengirim',
            'Kontak',
            'Kategori',
            'Isi Pesan',
            'Status',
        ];
    }

    public function map($aspiration): array
    {
        return [
            $aspiration->created_at->format('d/m/Y H:i'),
            // display_name otomatis jadi "Anonim" kalau pengirim memilih kirim
            // anonim — jadi file export ini tidak membocorkan identitas asli
            // pengirim anonim, konsisten dengan yang ditampilkan di dashboard.
            $aspiration->display_name,
            $aspiration->is_anonymous ? '-' : ($aspiration->contact ?: '-'),
            $aspiration->category,
            $aspiration->message,
            $aspiration->is_read ? 'Sudah Dibaca' : 'Belum Dibaca',
        ];
    }
}
