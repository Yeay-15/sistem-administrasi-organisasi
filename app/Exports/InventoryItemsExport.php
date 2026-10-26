<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class InventoryItemsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $items;

    public function __construct($items)
    {
        $this->items = $items;
    }

    public function collection(): Collection
    {
        return $this->items;
    }

    public function headings(): array
    {
        return [
            'Nama Barang',
            'Jumlah',
            'Satuan',
            'Tempat',
            'Ditambahkan',
            'Kedaluwarsa',
            'Keterangan',
        ];
    }

    public function map($item): array
    {
        return [
            $item->name,
            (float) $item->quantity,
            $item->unit,
            $item->location,
            $item->added_at ? $item->added_at->format('d/m/Y') : null,
            $item->expired_at ? $item->expired_at->format('d/m/Y') : null,
            $item->notes,
        ];
    }
}
