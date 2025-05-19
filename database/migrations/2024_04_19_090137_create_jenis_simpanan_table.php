<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jenis_simpanan', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique();
            $table->text('deskripsi')->nullable();
            $table->foreignId('created_by')
                  ->constrained('users')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete();
            $table->foreignId('updated_by')
                  ->constrained('users')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jenis_simpanan');
    }
};
