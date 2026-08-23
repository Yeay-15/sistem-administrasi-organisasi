<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('outgoing_letters', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number');
            $table->date('date');
            $table->enum('type', ['A', 'B']); // Internal/Eksternal
            $table->string('subject');
            $table->string('destination');
            $table->string('signatory');
            $table->enum('status', ['Draft', 'Terkirim', 'Dibatalkan'])->default('Draft');
            $table->string('file_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outgoing_letters');
    }
};
