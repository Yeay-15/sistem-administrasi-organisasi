<?php

namespace Database\Seeders;

use App\Models\Member;
use Illuminate\Database\Seeder;

class UpdateBatchAngkatanSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Update semua pengurus yang NIM-nya berawalan '24' menjadi angkatan 2024
        $updated2024 = Member::where('student_id', 'like', '24%')->update(['batch' => '2024']);

        // 2. Update semua pengurus yang NIM-nya berawalan '25' menjadi angkatan 2025
        $updated2025 = Member::where('student_id', 'like', '25%')->update(['batch' => '2025']);

        // Tampilkan pesan di terminal agar kita tahu berapa data yang berhasil diubah
        $this->command->info("Berhasil mengupdate {$updated2024} pengurus menjadi angkatan 2024.");
        $this->command->info("Berhasil mengupdate {$updated2025} pengurus menjadi angkatan 2025.");
        $this->command->info("Sisa pengurus dengan format NIM berbeda tidak diubah.");
    }
}
