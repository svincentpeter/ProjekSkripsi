<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('simpanan', function (Blueprint $table) {
            $table->id();
            $table->string('kodeTransaksiSimpanan');
            $table->string('tanggal_simpanan');
            $table->foreignId('id_anggota')->references('id')->on('_anggota')->onDelete('cascade');
            $table->foreignId('id_jenis_simpanan')->references('id')->on('jenis_simpanan')->onDelete('cascade');
            $table->string('jml_simpanan');
            $table->string('bukti_pembayaran');
            $table->foreignId('created_by')->notNull()->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('updated_by')->notNull()->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
            // $table->string('status_simpanan');
            // $table->string('keterangan_ditolak_simpanan');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('simpanan');
    }
};
