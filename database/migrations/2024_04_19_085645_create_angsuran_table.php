<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('angsuran', function (Blueprint $table) {
            $table->id();
            $table->string('kode_transaksi')->unique();
            $table->foreignId('pinjaman_id')
                  ->constrained('pinjaman')
                  ->cascadeOnUpdate()
                  ->cascadeOnDelete();
            $table->date('tanggal_angsuran');
            $table->decimal('jumlah_angsuran', 12, 2);
            $table->decimal('sisa_pinjam', 12, 2);
            $table->unsignedSmallInteger('cicilan');
            $table->enum('status', ['PENDING', 'LUNAS'])->default('PENDING');
            $table->text('keterangan')->nullable();
            $table->string('bukti_pembayaran');
            $table->decimal('bunga_pinjaman', 5, 2);
            $table->decimal('denda', 12, 2)->default(0);
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
        Schema::dropIfExists('angsuran');
    }
};
