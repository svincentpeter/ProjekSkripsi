<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_pinjaman', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pinjaman_id')
                  ->constrained('pinjaman')
                  ->cascadeOnUpdate()
                  ->cascadeOnDelete();
            $table->string('event');
            $table->json('old_data')->nullable();
            $table->json('new_data')->nullable();
            $table->foreignId('created_by')
                  ->constrained('users')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_pinjaman');
    }
};
