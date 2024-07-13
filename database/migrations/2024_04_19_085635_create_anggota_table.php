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
        Schema::create('_anggota', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->notNull()->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->string('nip');
            $table->string('name');
            $table->string('telphone');
            $table->string('agama')->nullable();
            $table->string('jenis_kelamin');
            $table->date('tgl_lahir')->nullable();
            $table->string('pekerjaan')->nullable();
            $table->string('alamat')->nullable();
            $table->string('image')->nullable();
            $table->string('status_anggota')->nullable();
            $table->decimal('saldo', 10, 2)->default(0);
            $table->date('tgl_gabung');
            $table->foreignId('created_by')->notNull()->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('updated_by')->notNull()->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('_anggota');
    }
};
