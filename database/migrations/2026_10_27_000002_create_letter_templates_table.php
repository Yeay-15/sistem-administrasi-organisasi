<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('letter_templates', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();          // slug, dipakai di URL
            $table->string('name');                    // mis. "Undangan Wali Kota"
            $table->text('description')->nullable();
            $table->string('category')->default('kepanitiaan'); // kepanitiaan | organisasi
            $table->enum('letter_type', ['A', 'B']);   // sama dengan outgoing_letters.type
            $table->string('subject');                 // perihal
            $table->string('file_name');               // file .docx di resources/letter_templates
            $table->string('number_pattern');          // pola nomor surat (disimpan tanpa spasi)
            $table->string('destination_pattern');     // pola kolom "tujuan" di register surat keluar
            $table->string('signatory_label');         // teks kolom "penandatangan" di register
            $table->json('signers');                   // peran penandatangan yang dibutuhkan
            $table->json('fields');                    // isian tambahan khusus template
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('letter_templates');
    }
};
