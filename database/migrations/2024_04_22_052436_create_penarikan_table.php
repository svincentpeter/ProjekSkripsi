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
        Schema::create('penarikan', function (Blueprint $table) {
            $table->id();
            $table->string('kodeTransaksiPenarikan');
            $table->foreignId('id_anggota')->references('id')->on('_anggota')->onDelete('cascade');
            $table->date('tanggal_penarikan');
            $table->decimal('jumlah_penarikan', 12, 2);
            $table->string('keterangan')->nullable();
            $table->foreignId('created_by')->notNull()->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('updated_by')->notNull()->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            // $table->string('status_penarikan');
            // $table->string('keterangan_ditolak_penarikan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penarikan');
    }
};
