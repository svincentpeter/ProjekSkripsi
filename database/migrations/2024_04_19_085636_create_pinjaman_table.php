<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pinjaman', function (Blueprint $table) {
            $table->id();
            $table->string('kode_transaksi')->unique();
            $table->foreignId('anggota_id')
                  ->constrained('anggota')
                  ->cascadeOnUpdate()
                  ->cascadeOnDelete();
            $table->date('tanggal_pinjam');
            $table->date('jatuh_tempo');
            $table->decimal('jumlah_pinjam', 12, 2);
            $table->decimal('bunga', 5, 2);
            $table->unsignedSmallInteger('tenor');
            $table->enum('status', ['PENDING', 'DISETUJUI', 'DITOLAK'])->default('PENDING');
            $table->text('keterangan_ditolak')->nullable();
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
        Schema::dropIfExists('pinjaman');
    }
};
