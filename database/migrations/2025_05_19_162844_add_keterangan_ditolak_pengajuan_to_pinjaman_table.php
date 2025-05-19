<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('pinjaman', function (Blueprint $table) {
        $table->text('keterangan_ditolak_pengajuan')->nullable()->after('status');
    });
}

public function down()
{
    Schema::table('pinjaman', function (Blueprint $table) {
        $table->dropColumn('keterangan_ditolak_pengajuan');
    });
}
};
