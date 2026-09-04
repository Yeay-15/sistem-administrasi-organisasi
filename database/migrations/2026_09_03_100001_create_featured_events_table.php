<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Modul "Event Unggulan" — dipakai untuk menyorot acara besar (mis.
     * KATIBER Cup) di beranda & pita pengumuman, sekaligus jadi microsite
     * (halaman detail) untuk acara tersebut. Dirancang reusable: setelah
     * satu acara selesai, tinggal diarsipkan (status='archived') dan admin
     * bisa membuat entri baru untuk acara berikutnya tanpa ubah kode sama
     * sekali.
     */
    public function up(): void
    {
        Schema::create('featured_events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('poster_path')->nullable();
            $table->string('short_description', 500)->nullable();
            $table->longText('content')->nullable();
            $table->string('location')->nullable();
            $table->date('event_start_date');
            $table->date('event_end_date')->nullable();
            $table->string('registration_url')->nullable();
            $table->string('cta_label')->default('Daftar Sekarang');

            // Bracket turnamen bersifat opsional — hanya diaktifkan untuk
            // event berbentuk kompetisi (mis. futsal). team_count harus
            // pangkat 2 (8/16/32/64) supaya bagan gugur tunggal bisa
            // dibuat rapi tanpa bye yang rumit.
            $table->boolean('has_bracket')->default(false);
            $table->unsignedTinyInteger('team_count')->nullable();

            $table->enum('status', ['draft', 'active', 'archived'])->default('draft');
            $table->boolean('show_on_homepage')->default(false);
            $table->boolean('show_announcement_bar')->default(false);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('featured_events');
    }
};
