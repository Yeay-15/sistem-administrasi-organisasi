<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Template Excel untuk impor inventaris:
 *  - Sheet 1 "Data Inventaris": judul kolom saja, siap diisi (inilah yang dibaca saat impor).
 *  - Sheet 2 "Petunjuk": aturan pengisian + contoh (TIDAK ikut diimpor).
 */
class InventoryItemsTemplate
{
    public const HEADINGS = [
        'Nama Barang',
        'Jumlah',
        'Satuan',
        'Tempat',
        'Tanggal Ditambahkan',
        'Tanggal Kedaluwarsa',
        'Keterangan',
    ];

    public static function build(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();

        // ---- Sheet 1: Data
        $data = $spreadsheet->getActiveSheet();
        $data->setTitle('Data Inventaris');
        $data->fromArray(self::HEADINGS, null, 'A1');

        $header = $data->getStyle('A1:G1');
        $header->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $header->getFill()->setFillType('solid')->getStartColor()->setRGB('2563EB');
        $header->getAlignment()->setVertical('center');
        $data->getRowDimension(1)->setRowHeight(24);

        foreach (['A' => 36, 'B' => 10, 'C' => 12, 'D' => 22, 'E' => 22, 'F' => 22, 'G' => 40] as $column => $width) {
            $data->getColumnDimension($column)->setWidth($width);
        }

        // Kolom tanggal berformat dd/mm/yyyy supaya terbaca sebagai tanggal oleh Excel.
        $data->getStyle('E2:F1001')->getNumberFormat()->setFormatCode('dd/mm/yyyy');
        $data->freezePane('A2');

        // ---- Sheet 2: Petunjuk
        $guide = $spreadsheet->createSheet();
        $guide->setTitle('Petunjuk');

        $guide->setCellValue('A1', 'PETUNJUK PENGISIAN');
        $guide->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        $rows = [
            ['Kolom', 'Wajib?', 'Aturan', 'Contoh'],
            ['Nama Barang', 'Ya', 'Nama barang, maksimal 255 karakter.', 'Handy Talky (HT)'],
            ['Jumlah', 'Ya', 'Angka (boleh desimal, mis. 0,5). Boleh juga ditulis bersama satuan, mis. "1 gulung" (satuan otomatis terisi bila kolom Satuan kosong).', '8'],
            ['Satuan', 'Tidak', 'Teks bebas, maksimal 50 karakter.', 'buah'],
            ['Tempat', 'Tidak', 'Teks bebas, maksimal 100 karakter.', 'Sekret 24'],
            ['Tanggal Ditambahkan', 'Tidak', 'Format dd/mm/yyyy. Dikosongkan = hari ini (untuk barang baru).', '10/10/2026'],
            ['Tanggal Kedaluwarsa', 'Tidak', 'Format dd/mm/yyyy, atau bulan/tahun (mm/yy) yang dianggap akhir bulan tsb.', '31/12/2027 atau 12/27'],
            ['Keterangan', 'Tidak', 'Catatan bebas, maksimal 1000 karakter.', 'Dipinjam untuk acara'],
        ];

        $rowNumber = 3;
        foreach ($rows as $row) {
            foreach ($row as $index => $value) {
                $cell = chr(ord('A') + $index) . $rowNumber;
                $guide->setCellValueExplicit($cell, (string) $value, DataType::TYPE_STRING);
            }
            $rowNumber++;
        }

        $tableHeader = $guide->getStyle('A3:D3');
        $tableHeader->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $tableHeader->getFill()->setFillType('solid')->getStartColor()->setRGB('2563EB');

        $notes = [
            'Catatan:',
            '1. Isi data di sheet "Data Inventaris", mulai dari baris ke-2. Jangan mengubah atau menghapus judul kolom di baris pertama.',
            '2. Hanya sheet pertama yang diimpor. Sheet "Petunjuk" ini tidak ikut diimpor.',
            '3. Baris yang sepenuhnya kosong akan dilewati. Maksimal 1000 baris per impor.',
            '4. Jika ada baris yang salah, tidak ada data yang diimpor dan aplikasi menampilkan nomor baris beserta kesalahannya.',
        ];
        $rowNumber += 1;
        foreach ($notes as $note) {
            $guide->setCellValueExplicit('A' . $rowNumber, $note, DataType::TYPE_STRING);
            $rowNumber++;
        }
        $guide->getStyle('A' . ($rowNumber - count($notes)))->getFont()->setBold(true);

        foreach (['A' => 24, 'B' => 10, 'C' => 80, 'D' => 26] as $column => $width) {
            $guide->getColumnDimension($column)->setWidth($width);
        }
        $guide->getStyle('C4:C10')->getAlignment()->setWrapText(true);
        $guide->getStyle('A4:D10')->getAlignment()->setVertical('top');

        $spreadsheet->setActiveSheetIndex(0);

        return $spreadsheet;
    }

    public static function response(): StreamedResponse
    {
        $spreadsheet = self::build();

        return response()->streamDownload(function () use ($spreadsheet) {
            (new Xlsx($spreadsheet))->save('php://output');
        }, 'Template_Impor_Inventaris.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
