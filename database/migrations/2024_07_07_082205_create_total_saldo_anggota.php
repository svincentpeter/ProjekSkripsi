<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('total_saldo_anggota', function (Blueprint $table) {
            $table->unsignedBigInteger('anggota_id')->primary();
            $table->decimal('gradesaldo', 15, 2)->default(0);
            $table->foreign('anggota_id')
                  ->references('id')
                  ->on('anggota')
                  ->cascadeOnUpdate()
                  ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('total_saldo_anggota');
    }
};
