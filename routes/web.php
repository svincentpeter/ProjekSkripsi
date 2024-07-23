<?php

use App\Http\Controllers\NasabahController;
use App\Http\Controllers\RoleAndPermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});


Route::middleware('auth')->group(function () {
    //home
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home'); 
    Route::get('/chart-data', [App\Http\Controllers\HomeController::class, 'chartData'])->name('chart.data');
    //user
    Route::get('/users', [\App\Http\Controllers\UserController::class, 'index'])->name('user');
    Route::get('/adduser', [\App\Http\Controllers\UserController::class, 'create'])->name('createUser');
    Route::post('/adduser', [\App\Http\Controllers\UserController::class, 'store'])->name('storeUser');
    Route::get('/editusers/{id}',  [\App\Http\Controllers\UserController::class, 'edit'])->name('users.edit');
    Route::put('/updateusers/{id}',  [\App\Http\Controllers\UserController::class, 'update'])->name('users.update');
    // Route::get('/delete_user/{id}', [\App\Http\Controllers\UserController::class, 'destroy'])->name('users.delete');
    Route::get('delete-user/{id}', [UserController::class, 'delete']);


    //nasabah
    Route::get('/nasabah', [\App\Http\Controllers\NasabahController::class, 'index'])->name('nasabah');
    Route::get('/addnasabah', [\App\Http\Controllers\NasabahController::class, 'create'])->name('createNasabah');
    Route::post('/addnasabah', [\App\Http\Controllers\NasabahController::class, 'store'])->name('storeNasabah');
    Route::get('/editnasabah/{id}',  [\App\Http\Controllers\NasabahController::class, 'edit'])->name('nasabah.edit');
    Route::put('/updatenasabah/{id}',  [\App\Http\Controllers\NasabahController::class, 'update'])->name('nasabah.update');
    Route::delete('/nasabah/{id}', [\App\Http\Controllers\NasabahController::class, 'destroy'])->name('nasabah.destroy');
    Route::get('/detail_nasabah/{id}', [\App\Http\Controllers\NasabahController::class, 'show'])->name('nasabah.show');

    //simpanan
    Route::get('/simpanan', [\App\Http\Controllers\SimpananController::class, 'index'])->name('simpanan');
    Route::get('/simpanan/create', [\App\Http\Controllers\SimpananController::class, 'create'])->name('simpanan.create');
    Route::post('/simpanan/store', [\App\Http\Controllers\SimpananController::class, 'store'])->name('simpanan.store');
    Route::get('/detail_simpanan/{id}', [\App\Http\Controllers\SimpananController::class, 'show'])->name('simpanan.show');
    Route::get('/editsimpanan/{id}/edit', [\App\Http\Controllers\SimpananController::class, 'edit'])->name('simpanan.edit');
    Route::put('/updatesimpanan/{id}', [\App\Http\Controllers\SimpananController::class, 'update'])->name('simpanan.update');
    Route::delete('/simpanan/{id}', [\App\Http\Controllers\SimpananController::class, 'destroy'])->name('simpanan.destroy');
    
    
    //pinjaman
    Route::get('/pinjaman', [\App\Http\Controllers\PinjamanController::class, 'index'])->name('pinjaman');
    Route::get('/pinjaman/create', [\App\Http\Controllers\PinjamanController::class, 'create'])->name('pinjaman.create');
    Route::post('/pinjaman/store', [\App\Http\Controllers\PinjamanController::class, 'store'])->name('pinjaman.store');
    Route::get('/detail_pinjaman/{id}', [\App\Http\Controllers\PinjamanController::class, 'show'])->name('pinjaman.show');
    // Route::get('/editpinjaman/{id}/edit', [\App\Http\Controllers\PinjamanController::class, 'edit'])->name('pinjaman.edit');
    Route::put('/pinjaman/{id}', [\App\Http\Controllers\PinjamanController::class, 'update'])->name('pinjaman.update');

    Route::delete('/pinjaman/{id}', [\App\Http\Controllers\PinjamanController::class, 'destroy'])->name('pinjaman.destroy');
    Route::get('/terima_pengajuan/{id}', [\App\Http\Controllers\PinjamanController::class, 'terimapengajuan'])->name('terima_pengajuan');
    Route::post('/tolak_pengajuan/{id}', [\App\Http\Controllers\PinjamanController::class, 'tolakPengajuan'])->name('tolak_pengajuan');
    Route::get('angsuran/{id}', [\App\Http\Controllers\PinjamanController::class, 'showAngsuran'])->name('angsuran.show');
    
    //laporan
    Route::get('/simpanan/cetak', [\App\Http\Controllers\SimpananController::class, 'cetak'])->name('simpanan.cetak');
    Route::get('/pinjaman/cetak', [\App\Http\Controllers\LaporanController::class, 'laporanPinjaman'])->name('pinjaman.cetak');
    Route::get('/laporan-angsuran/{id}', [\App\Http\Controllers\LaporanController::class, 'laporanAngsuran'])->name('laporan.angsuran');
    Route::get('/penarikan/cetak', [\App\Http\Controllers\LaporanController::class, 'laporanPenarikan'])->name('penarikan.cetak');

    //laporan index
    Route::get('/laporanPenarikan', [\App\Http\Controllers\LaporanController::class, 'indexPenarikan'])->name('laporanPenarikan');
    Route::get('/laporanSimpanan', [\App\Http\Controllers\LaporanController::class, 'indexSimpanan'])->name('laporanSimpanan');
    Route::get('/laporanPinjaman', [\App\Http\Controllers\LaporanController::class, 'indexPinjaman'])->name('laporanPinjaman');




    
    //angsuran
    Route::post('/pinjaman/{pinjaman_id}/angsuran', [\App\Http\Controllers\AngsuranController::class, 'bayarAngsuran'])->name('angsuran.bayar');
    // routes/web.php
    Route::put('/angsuran/{id}', [\App\Http\Controllers\AngsuranController::class, 'update'])->name('angsuran.update');

    //angsuran
    Route::get('/angsuran', [\App\Http\Controllers\AngsuranController::class, 'index'])->name('angsuran');
    Route::get('/angsuran/create', [\App\Http\Controllers\AngsuranController::class, 'create'])->name('angsuran.create');
    Route::post('/angsuran/store', [\App\Http\Controllers\AngsuranController::class, 'store'])->name('angsuran.store');
    Route::get('/editangsuran/{id}/edit', [\App\Http\Controllers\AngsuranController::class, 'edit'])->name('angsuran.edit');
    // Route::put('/updateangsuran/{id}', [\App\Http\Controllers\AngsuranController::class, 'update'])->name('angsuran.update');
    Route::get('/angsuran/cetak', [\App\Http\Controllers\AngsuranController::class, 'cetak'])->name('angsuran.cetak');
    Route::delete('/angsuran/{id}', [\App\Http\Controllers\AngsuranController::class, 'destroy'])->name('angsuran.destroy');


    //penarikan
    Route::get('/penarikan', [\App\Http\Controllers\PenarikanController::class, 'index'])->name('penarikan');
    Route::post('/penarikan/store', [\App\Http\Controllers\PenarikanController::class, 'store'])->name('penarikan.store');
    Route::get('/detail_penarikan/{id}', [\App\Http\Controllers\PenarikanController::class, 'show'])->name('penarikan.show');
    Route::get('/editPenarikan/{id}/edit', [\App\Http\Controllers\PenarikanController::class, 'edit'])->name('penarikan.edit');
    Route::put('/penarikan/{id}', [\App\Http\Controllers\PenarikanController::class, 'update'])->name('penarikan.update');
    Route::delete('/penarikan/{id}', [\App\Http\Controllers\PenarikanController::class, 'destroy'])->name('penarikan.destroy');




    // Route::delete('/pinjaman/{id}', [\App\Http\Controllers\PinjamanController::class, 'destroyAngsuran'])->name('angsuran.destroy');
    // Route::delete('/angsuran/{id}', [\App\Http\Controllers\AngsuranController::class, 'destroyAngsuran'])->name('angsuran.destroy');

  
    //role&permission
    Route::get('show-roles', [RoleAndPermissionController::class, 'show']);
    Route::get('create-roles', [RoleAndPermissionController::class, 'createRole']);
    Route::post('add-role', [RoleAndPermissionController::class, 'create']);
    Route::get('edit-role/{id}', [RoleAndPermissionController::class, 'editRole']);
    Route::post('update-role', [RoleAndPermissionController::class, 'updateRole']);
    Route::get('delete-role/{id}', [RoleAndPermissionController::class, 'delete']);
});

Auth::routes();
