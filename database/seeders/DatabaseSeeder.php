<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Admin Pertama (Super Admin)
        User::firstOrCreate(
            ['email' => 'admin@katiber.local'],
            [
                'name' => 'Super Admin KATIBER',
                'password' => Hash::make('rahasia123'),
            ]
        );

        // 2. Akun Admin Kedua
        User::firstOrCreate(
            ['email' => 'gilang@admin.sekum'],
            [
                'name' => 'Sekum',
                'password' => Hash::make('sekum0710'),
            ]
        );

        // 3. Panggil Seeder Divisi dan Pengurus (dari langkah sebelumnya)
        $this->call([
            DivisionSeeder::class,
            MemberSeeder::class,
        ]);
    }
}
