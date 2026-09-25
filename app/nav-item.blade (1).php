<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Anggota satu kepanitiaan. Selalu merujuk ke `members` (boleh row
     * Pengurus maupun Non-Pengurus) supaya histori kepanitiaan & statistik
     * kehadiran tetap konsisten nempel ke satu identitas orang, apa pun
     * status keanggotaannya sekarang. `panitia_bidang_id` hanya diisi kalau
     * posisinya berbasis bidang (Ketua Bidang / Anggota Bidang).
     */
    public function up(): void
    {
        Schema::create('committee_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('committee_id')->constrained('committees')->cascadeOnDelete();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->enum('position_category', [
                'Ketua Panitia',
                'Sekretaris Panitia',
                'Bendahara Panitia',
                'Bendahara Peserta',
                'Ketua Bidang',
                'Anggota Bidang',
            ]);
            $table->foreignId('panitia_bidang_id')->nullable()->constrained('panitia_bidang')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Satu orang tidak boleh didaftarkan dua kali dengan peran yang
            // sama persis di kepanitiaan yang sama (mencegah entri ganda
            // tidak sengaja), tapi tetap boleh merangkap >1 peran berbeda
            // (mis. sekaligus Bendahara Panitia & Anggota Bidang Acara).
            $table->unique(['committee_id', 'member_id', 'position_category', 'panitia_bidang_id'], 'committee_member_role_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('committee_members');
    }
};
