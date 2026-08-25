<?php

namespace Database\Seeders;

use App\Models\Division;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Seeder ini AMAN dijalankan berkali-kali di database yang sudah berisi data
     * (tidak melakukan delete/truncate). Gunakan:
     *   php artisan db:seed --class=RolePermissionSeeder
     */
    public function run(): void
    {
        $permissions = [
            ['slug' => 'view_dashboard', 'label' => 'Lihat Dashboard', 'group' => 'Umum'],

            ['slug' => 'view_divisions', 'label' => 'Lihat Divisi', 'group' => 'Pengurus'],
            ['slug' => 'manage_divisions', 'label' => 'Kelola Divisi', 'group' => 'Pengurus'],
            ['slug' => 'view_members', 'label' => 'Lihat Pengurus', 'group' => 'Pengurus'],
            ['slug' => 'manage_members', 'label' => 'Kelola Pengurus', 'group' => 'Pengurus'],

            ['slug' => 'view_agendas', 'label' => 'Lihat Agenda', 'group' => 'Kegiatan'],
            ['slug' => 'manage_agendas', 'label' => 'Kelola Agenda', 'group' => 'Kegiatan'],
            ['slug' => 'manage_attendances', 'label' => 'Kelola Absensi', 'group' => 'Kegiatan'],
            ['slug' => 'view_guidances', 'label' => 'Lihat Pembinaan', 'group' => 'Kegiatan'],
            ['slug' => 'manage_guidances', 'label' => 'Kelola Pembinaan', 'group' => 'Kegiatan'],
            ['slug' => 'view_guests', 'label' => 'Lihat Buku Tamu', 'group' => 'Kegiatan'],
            ['slug' => 'manage_guests', 'label' => 'Kelola Buku Tamu', 'group' => 'Kegiatan'],

            ['slug' => 'view_incoming_letters', 'label' => 'Lihat Surat Masuk', 'group' => 'Persuratan'],
            ['slug' => 'manage_incoming_letters', 'label' => 'Kelola Surat Masuk', 'group' => 'Persuratan'],
            ['slug' => 'view_outgoing_letters', 'label' => 'Lihat Surat Keluar', 'group' => 'Persuratan'],
            ['slug' => 'manage_outgoing_letters', 'label' => 'Kelola Surat Keluar', 'group' => 'Persuratan'],

            ['slug' => 'view_audit_logs', 'label' => 'Akses Audit Log', 'group' => 'Sistem'],
            ['slug' => 'manage_roles', 'label' => 'Manajemen Peran & Akses', 'group' => 'Sistem'],

            ['slug' => 'manage_news', 'label' => 'Kelola Berita', 'group' => 'Konten'],
            ['slug' => 'manage_gallery', 'label' => 'Kelola Galeri', 'group' => 'Konten'],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(['slug' => $permission['slug']], $permission);
        }

        $superAdmin = Role::updateOrCreate(
            ['slug' => 'super-admin'],
            ['name' => 'Super Admin', 'description' => 'Akses penuh ke seluruh sistem, tidak dapat dibatasi.']
        );

        $kadiv = Role::updateOrCreate(
            ['slug' => 'kadiv'],
            ['name' => 'Kadiv', 'description' => 'Mengelola kegiatan & absensi, tidak bisa akses data sensitif.']
        );

        $anggota = Role::updateOrCreate(
            ['slug' => 'anggota'],
            ['name' => 'Anggota', 'description' => 'Akses dasar, hanya dapat melihat dashboard.']
        );

        // Super Admin selalu mendapat SEMUA permission yang ada.
        $superAdmin->permissions()->sync(Permission::pluck('id'));

        // Default awal untuk Kadiv & Anggota — bisa diubah lagi lewat halaman
        // Manajemen Peran & Akses tanpa perlu seed ulang.
        if ($kadiv->permissions()->count() === 0) {
            $kadiv->permissions()->sync(
                Permission::whereIn('slug', [
                    'view_dashboard',
                    'view_agendas',
                    'manage_agendas',
                    'manage_attendances',
                    'view_guests',
                    'manage_guests',
                    'view_guidances',
                    'manage_guidances',
                ])->pluck('id')
            );
        }

        if ($anggota->permissions()->count() === 0) {
            $anggota->permissions()->sync(
                Permission::whereIn('slug', ['view_dashboard'])->pluck('id')
            );
        }

        // Hak akses per-Divisi (bukan per-Role). Secara default, Divisi Infokom
        // diberi 'manage_news' & 'manage_gallery' di sini SEKALI SAJA — supaya
        // perilaku sebelum & sesudah fitur "Hak Akses per Divisi" ini terpasang
        // tetap sama. Setelah ini, semuanya diatur lewat toggle di halaman
        // Manajemen Peran & Akses, bukan lewat kode.
        $infokom = Division::where('abbreviation', 'Infokom')->first();
        if ($infokom && $infokom->permissions()->count() === 0) {
            $infokom->permissions()->sync(
                Permission::whereIn('slug', ['manage_news', 'manage_gallery'])->pluck('id')
            );
        }

        // Backfill: akun yang sudah ada sebelum fitur ini dipasang belum punya role_id.
        // Supaya tidak ada yang terkunci dari sistem, akun lama otomatis dijadikan Super Admin.
        // Setelah ini, ubah peran akun satu per satu lewat halaman Manajemen Peran & Akses.
        User::whereNull('role_id')->update(['role_id' => $superAdmin->id]);
    }
}
