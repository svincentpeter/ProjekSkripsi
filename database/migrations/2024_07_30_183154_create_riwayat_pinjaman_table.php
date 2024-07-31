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
        Schema::create('riwayat_pinjaman', function (Blueprint $table) {
            $table->id();
            $table->string('kodeTransaksiPinjaman');
            $table->foreignId('id_pinjaman')->references('id')->on('pinjaman')->onUpdate('cascade')->onDelete('cascade');
            $table->string('tanggal_pinjam');
            $table->string('jatuh_tempo');
            $table->integer('jml_pinjam');
            $table->integer('sisa_pinjam');
            $table->string('jml_cicilan');
            $table->string('status_pengajuan');
            $table->string('keterangan_ditolak_pengajuan');
            $table->foreignId('created_by')->notNull()->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('updated_by')->notNull()->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_pinjaman');
    }
};
