<?php

namespace Database\Seeders;

use App\Models\InventoryItem;
use Illuminate\Database\Seeder;

class InventoryItemSeeder extends Seeder
{
    /** Tanggal pendataan di dokumen sumber ("Di Data Tanggal : 10 Oktober 2026"). */
    private const DATA_DATE = '2026-10-10';

    /**
     * Data awal dari dokumen "DATA BARANG-BARANG INVENTARIS 2026/2027"
     * (per 10 Oktober 2026).
     *
     * AMAN dijalankan berkali-kali (updateOrCreate per nama + tempat, tidak
     * menghapus apa pun):
     *   php artisan db:seed --class=InventoryItemSeeder
     *
     * Catatan konversi dari dokumen:
     * - Semua barang diisi Tanggal Ditambahkan = tanggal pendataan di dokumen (10 Okt 2026).
     * - Kolom "Jumlah" dipecah jadi angka + satuan ("1 gulung" -> 1 + "gulung").
     * - Tanggal "exp MM/YY" disimpan sebagai tanggal terakhir bulan tsb, dan
     *   dipindah dari nama/keterangan ke kolom Kedaluwarsa.
     * - "Oxygen (exp 12/08)" TIDAK dikonversi ke tanggal karena formatnya tidak
     *   sama dengan yang lain (belum jelas 12/2028 atau 12 Agustus) — tetap
     *   disimpan apa adanya di keterangan. Silakan koreksi lewat menu Ubah.
     */
    public function run(): void
    {
        // [nama, jumlah, satuan, tempat, kedaluwarsa (Y-m-d), keterangan]
        $items = [
            ['Nampan', 4, null, 'Sekret 24', null, null],
            ['Memori card', 1, null, 'Infokom', null, null],
            ['Panggangan', 1, null, 'Sekret 24', null, null],
            ['Kawat', 1, 'gulung', 'Sekret 24', null, null],
            ['Palu sidang', 1, null, 'Sekret 24', null, null],
            ['Lilin', 10, null, 'Sekret 24', null, null],
            ['Pita (hitam, putih, pink, hijau)', 1, 'gulung', 'Sekret 24', null, null],
            ['Name tag biasa tanpa tali', 10, null, 'Sekret 24', null, null],
            ['Pipet plastik', 1, 'plastik', 'Sekret 24', null, null],
            ['Hekter', 1, 'buah', 'Sekret 24', null, null],
            ['Anak hekter', 1, 'buah', 'Sekret 24', null, null],
            ['Oxygen', 2, null, 'Sekret 24', null, '(exp 12/08)'],
            ['Plester, kasa dan kapas', 1, 'set', 'Sekret 24', null, null],
            ['Freshcare', 2, 'btl', 'Sekret 24', null, null],
            ['Betadine', 2, 'btl', 'Sekret 24', '2027-10-31', null],
            ['Methylprednisolone', 10, 'tablet', 'Sekret 24', '2028-06-30', null],
            ['Tifestan Forte', 9, 'tablet', 'Sekret 24', '2028-07-31', null],
            ['Novagesic Paracetamol', 6, 'tablet', 'Sekret 24', '2027-07-31', null],
            ['Promag', 6, 'tablet', 'Sekret 24', '2027-08-31', null],
            ['Panadol', 3, 'tablet', 'Sekret 24', '2027-08-31', null],
            ['Tisu Alkohol', 1, 'kotak', 'Sekret 24', '2027-07-31', null],
            ['Hotin Cream Tetes/Salep', 2, null, 'Sekret 24', '2028-09-30', null],
            ['Kompres', 1, null, 'Sekret 24', null, null],
            ['Gunting', 1, null, 'Sekret 24', null, null],
            ['Kotak P3K', 1, null, 'Sekret 24', null, null],
            ['Bola futsal', 2, null, 'Umay', null, null],
            ['Spidol', 7, null, 'Sekret 24', null, null],
            ['Mikrofon', 2, null, 'Sekret 24', null, null],
            ['Sound System', 1, null, 'Sekret 24', null, null],
            ['Clip on mikropon', 1, null, 'Sekret 24', null, null],
            ['Balon', 7, null, 'Sekret 24', null, null],
            ['Rompi', 12, null, 'Umay', null, null],
            ['Papan skor', 1, null, 'Umay', null, null],
            ['Soffel', 6, null, 'Sekret 24', null, null],
            ['Materai', 1, null, 'Sekret 24', null, null],
            ['Selotip kertas putih', 1, null, 'Sekret 24', null, null],
            ['Binder clip', 1, 'kotak', 'Sekret 24', null, null],
            ['Baterai ABC besar', 2, 'buah', 'Sekret 24', null, null],
            ['Baterai ABC kecil', 6, 'buah', 'Sekret 24', null, null],
            ['Stempel KATIBER CUP', 2, 'buah', 'Sekret 24', null, null],
            ['Stempel Panitia Pelaksana', 1, null, 'Sekret 24', null, null],
            ['Piala', 1, null, 'Kak Nadila', null, null],
            ['Box Penyimpanan', 1, null, 'Sekret 24', null, null],
            ['Plastik Asoy', 14, null, 'Sekret 24', null, null],
            ['Bendera KATIBER', 1, null, 'Sekret 24', null, null],
            ['Cup gelas plastik', 0.5, 'plastik', 'Sekret 24', null, null],
            ['Handy Talky (HT)', 8, 'buah', 'Sekret 24', null, null],
            ['Tinta Stempel', 1, null, 'Sekret 24', null, null],
            ['Bedak Tabur Wardah', 1, null, 'Rasya 25', null, null],
            ['Two Way Cake OMG', 1, null, 'Rasya 25', null, null],
            ['Concealer Pixy', 1, null, 'Rasya 25', null, null],
            ['Lem Bulu Mata', 1, null, 'Rasya 25', null, null],
            ['Paku payung', 200, 'g', 'Sekret 24', null, null],
            ['Selasiban Hitam', 1, null, 'Sekret 24', null, null],
            ['Spanduk Dari Kepengurusan 2025-2026', 8, null, 'Sekret 24', null, null],
            ['Spanduk Pelantikan 2026', 1, null, 'Rumah Kadiv INVEN', null, null],
            ['Spanduk Temu Ramah Maba 2026', 1, null, 'Sekret 24', null, null],

            // Dari bagian "Noted" di akhir dokumen. Tempatnya tidak disebutkan.
            ['Name tag biasa plastik', 17, null, null, null, 'Dari catatan di akhir dokumen data inventaris.'],
            ['Name tag biru tua', 44, null, null, null, 'Dari catatan di akhir dokumen data inventaris.'],
        ];

        foreach ($items as [$name, $quantity, $unit, $location, $expiredAt, $notes]) {
            InventoryItem::updateOrCreate(
                ['name' => $name, 'location' => $location],
                [
                    'quantity' => $quantity,
                    'unit' => $unit,
                    'added_at' => self::DATA_DATE,
                    'expired_at' => $expiredAt,
                    'notes' => $notes,
                ]
            );
        }
    }
}
