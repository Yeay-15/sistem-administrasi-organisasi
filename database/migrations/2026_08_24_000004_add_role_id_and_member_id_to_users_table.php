<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Kolom dibuat nullable agar aman untuk database yang sudah berisi data
        // (tidak perlu migrate:fresh). Nilai default akan diisi lewat RolePermissionSeeder.
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'role_id')) {
                $table->foreignId('role_id')->nullable()->after('id')
                    ->constrained('roles')->nullOnDelete();
            }

            // Menjembatani akun login dengan data pengurus di tabel members
            if (! Schema::hasColumn('users', 'member_id')) {
                $table->foreignId('member_id')->nullable()->after('role_id')
                    ->constrained('members')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropForeign(['member_id']);
            $table->dropColumn(['role_id', 'member_id']);
        });
    }
};
