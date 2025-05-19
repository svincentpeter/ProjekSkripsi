<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('simpanan', function (Blueprint $table) {
            $table->id();
            $table->string('kode_transaksi')->unique();
            $table->date('tanggal_simpanan');
            $table->foreignId('anggota_id')
                  ->constrained('anggota')
                  ->cascadeOnUpdate()
                  ->cascadeOnDelete();
            $table->foreignId('jenis_simpanan_id')
                  ->constrained('jenis_simpanan')
                  ->cascadeOnUpdate()
                  ->cascadeOnDelete();
            $table->decimal('jumlah_simpanan', 12, 2);
            $table->string('bukti_pembayaran');
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
        Schema::dropIfExists('simpanan');
    }
};
