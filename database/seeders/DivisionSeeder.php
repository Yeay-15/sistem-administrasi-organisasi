<?php

namespace Database\Seeders;

use App\Models\Division;
use Illuminate\Database\Seeder;

class DivisionSeeder extends Seeder
{
    public function run(): void
    {
        $divisions = [
            ['name' => 'Badan Pengurus Harian', 'abbreviation' => 'BPH'],
            ['name' => 'Divisi Kaderisasi', 'abbreviation' => 'Kader'],
            ['name' => 'Koordinator Wilayah dan Keanggotaan', 'abbreviation' => 'Korwil'],
            ['name' => 'Hubungan Masyarakat', 'abbreviation' => 'Humas'],
            ['name' => 'Informasi dan Komunikasi', 'abbreviation' => 'Infokom'],
            ['name' => 'Minat dan Bakat', 'abbreviation' => 'Mikat'],
            ['name' => 'Agama', 'abbreviation' => 'Agama'],
            ['name' => 'Pendidikan', 'abbreviation' => 'Pendidikan'],
            ['name' => 'Inventaris', 'abbreviation' => 'Invent'],
        ];

        foreach ($divisions as $div) {
            Division::firstOrCreate(['abbreviation' => $div['abbreviation']], $div);
        }
    }
}