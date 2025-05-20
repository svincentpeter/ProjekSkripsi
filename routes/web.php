<?php

use App\Http\Controllers\NasabahController;
use App\Http\Controllers\RoleAndPermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AngsuranController;
use App\Http\Controllers\AuditLogController;
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
    Route::resource('nasabah', \App\Http\Controllers\NasabahController::class);

    //simpanan
    Route::resource('simpanan', \App\Http\Controllers\SimpananController::class);

    //pinjaman
    Route::get('/pinjaman', [\App\Http\Controllers\PinjamanController::class, 'index'])->name('pinjaman');
    Route::get('/pinjaman/create', [\App\Http\Controllers\PinjamanController::class, 'create'])->name('pinjaman.create');
    Route::post('/pinjaman/store', [\App\Http\Controllers\PinjamanController::class, 'store'])->name('pinjaman.store');
    Route::get('/detail_pinjaman/{id}', [\App\Http\Controllers\PinjamanController::class, 'show'])->name('pinjaman.show');
    // Route::get('/editpinjaman/{id}/edit', [\App\Http\Controllers\PinjamanController::class, 'edit'])->name('pinjaman.edit');
    Route::put('/pinjaman/{id}', [\App\Http\Controllers\PinjamanController::class, 'update'])->name('pinjaman.update');
    Route::delete('/pinjaman/{id}', [\App\Http\Controllers\PinjamanController::class, 'destroy'])->name('pinjaman.destroy');
    Route::get('/terima_pengajuan/{id}', [\App\Http\Controllers\PinjamanController::class, 'terima'])->name('terima_pengajuan');
    Route::post('/tolak_pengajuan/{id}', [\App\Http\Controllers\PinjamanController::class, 'tolak'])->name('tolak_pengajuan');

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
    Route::resource('angsuran', AngsuranController::class);
    Route::post('/pinjaman/{pinjaman}/angsuran', [App\Http\Controllers\AngsuranController::class, 'store'])->name('angsuran.bayar');
    Route::get('/angsuran/{id}/cetak', [App\Http\Controllers\AngsuranController::class, 'cetak'])->name('angsuran.cetak');
// NEW: Detail AJAX (for modal show)
Route::get('/penarikan/show/{id}', [\App\Http\Controllers\PenarikanController::class, 'showAjax'])->name('penarikan.showAjax');

// NEW: Export Excel
Route::get('/penarikan/excel', [\App\Http\Controllers\PenarikanController::class, 'exportExcel'])->name('penarikan.excel');

// NEW: Riwayat audit log/history
Route::get('/penarikan/{id}/history', [\App\Http\Controllers\PenarikanController::class, 'history'])->name('penarikan.history');


    //penarikan
    Route::get('/penarikan', [\App\Http\Controllers\PenarikanController::class, 'index'])->name('penarikan');
    Route::post('/penarikan/store', [\App\Http\Controllers\PenarikanController::class, 'store'])->name('penarikan.store');
    Route::get('/detail_penarikan/{id}', [\App\Http\Controllers\PenarikanController::class, 'show'])->name('penarikan.show');
    Route::get('/editPenarikan/{id}/edit', [\App\Http\Controllers\PenarikanController::class, 'edit'])->name('penarikan.edit');
    Route::put('/penarikan/{id}', [\App\Http\Controllers\PenarikanController::class, 'update'])->name('penarikan.update');
    Route::delete('/penarikan/{id}', [\App\Http\Controllers\PenarikanController::class, 'destroy'])->name('penarikan.destroy');

Route::get('/audit-log', [AuditLogController::class, 'index'])
    ->middleware('permission:audit-log-list')
    ->name('auditlog.index');



    // Route::delete('/pinjaman/{id}', [\App\Http\Controllers\PinjamanController::class, 'destroyAngsuran'])->name('angsuran.destroy');
    // Route::delete('/angsuran/{id}', [\App\Http\Controllers\AngsuranController::class, 'destroyAngsuran'])->name('angsuran.destroy');


    //role&permission
    Route::get('/roles', [RoleAndPermissionController::class, 'show'])->name('roles.index');
Route::get('/roles/create', [RoleAndPermissionController::class, 'createRole'])->name('roles.create');
Route::post('/roles', [RoleAndPermissionController::class, 'create'])->name('roles.store');
Route::get('/roles/{id}/edit', [RoleAndPermissionController::class, 'editRole'])->name('roles.edit');
Route::put('/roles/{id}', [RoleAndPermissionController::class, 'updateRole'])->name('roles.update');
Route::delete('/roles/{id}', [RoleAndPermissionController::class, 'delete'])->name('roles.destroy');
});

Auth::routes();
