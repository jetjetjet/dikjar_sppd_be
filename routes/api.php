<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AnggaranController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UnitKerjaController;

use App\Http\Controllers\TujuanDalamController;
use App\Http\Controllers\JenisTransportController;
use App\Http\Controllers\KategoriPengeluaranController;
use App\Http\Controllers\SatuanController;

use App\Http\Controllers\JabatanController;
use App\Http\Controllers\PejabatTtdController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WilayahController;

use App\Http\Controllers\BiayaController;
use App\Http\Controllers\InapController;
use App\Http\Controllers\TransportController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\SPTController;
use App\Http\Controllers\SPPDController;
use App\Http\Controllers\SPTLogController;
use App\Http\Controllers\CekSPTController;

use App\Http\Controllers\NotifController;

use App\Http\Controllers\ReportController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::get('/spt/verifikasi', [CekSPTController::class, 'verifikasi']);
Route::get('/tesa', [SPPDController::class, 'tesa']);

Route::post('login', [AuthController::class, 'login'])->name('login');
Route::get('/report/spt/selesai/export', [ReportController::class, 'exportFinishedSPT']);

Route::group(['middleware' => 'auth:sanctum'], function() {
	Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

	//MASTER DATA
	Route::get('/anggaran-grid', [AnggaranController::class, 'grid'])->middleware('can:anggaran-view');
	Route::get('/role-search', [AnggaranController::class, 'searchRole']);
	Route::get('/anggaran-search', [AnggaranController::class, 'search']);
	Route::get('/anggaran/{id}', [AnggaranController::class, 'show'])->middleware('can:anggaran-view');
	Route::post('/anggaran', [AnggaranController::class, 'store'])->middleware('can:anggaran-add');
	Route::put('/anggaran/{id}', [AnggaranController::class, 'update'])->middleware('can:anggaran-edit');
	Route::delete('/anggaran/{id}', [AnggaranController::class, 'destroy'])->middleware('can:anggaran-delete');

	
	Route::get('/dashboard-anggaran', [DashboardController::class, 'anggaran']);
	Route::get('/dashboard-pegawai', [DashboardController::class, 'pegawaiDinas']);

	Route::get('/unit-kerja-grid', [UnitKerjaController::class, 'grid']);
	Route::get('/unit-kerja-search', [UnitKerjaController::class, 'search']);
	Route::get('/unit-kerja/{id}', [UnitKerjaController::class, 'show']);
	Route::post('/unit-kerja', [UnitKerjaController::class, 'store']);
	Route::put('/unit-kerja/{id}', [UnitKerjaController::class, 'update']);
	Route::delete('/unit-kerja/{id}', [UnitKerjaController::class, 'destroy']);

	Route::get('/jabatan-grid', [JabatanController::class, 'grid'])->middleware('can:jabatan-view');
	Route::get('/jabatan-search', [JabatanController::class, 'search']);
	Route::get('/jabatan-parent', [JabatanController::class, 'parent']);
	Route::get('/jabatan/{id}', [JabatanController::class, 'show'])->middleware('can:jabatan-view');
	Route::post('/jabatan', [JabatanController::class, 'store'])->middleware('can:jabatan-add');
	Route::put('/jabatan/{id}', [JabatanController::class, 'update'])->middleware('can:jabatan-edit');
	Route::delete('/jabatan/{id}', [JabatanController::class, 'destroy'])->middleware('can:jabatan-delete');

	Route::get('/jenis-transport-grid', [JenisTransportController::class, 'grid']);
	Route::get('/jenis-transport-search', [JenisTransportController::class, 'search']);
	Route::get('/jenis-transport/{id}', [JenisTransportController::class, 'show']);
	Route::post('/jenis-transport', [JenisTransportController::class, 'store']);
	Route::put('/jenis-transport/{id}', [JenisTransportController::class, 'update']);
	Route::delete('/jenis-transport/{id}', [JenisTransportController::class, 'destroy']);

	Route::get('/daerah-grid', [TujuanDalamController::class, 'grid']);
	Route::get('/daerah-search', [TujuanDalamController::class, 'search']);
	Route::get('/daerah/{id}', [TujuanDalamController::class, 'show']);
	Route::post('/daerah', [TujuanDalamController::class, 'store']);
	Route::put('/daerah/{id}', [TujuanDalamController::class, 'update']);
	Route::delete('/daerah/{id}', [TujuanDalamController::class, 'destroy']);

	Route::get('/kategori-pengeluaran-grid', [KategoriPengeluaranController::class, 'grid']);
	Route::get('/kategori-pengeluaran-search', [KategoriPengeluaranController::class, 'search']);
	Route::get('/kategori-pengeluaran/{id}', [KategoriPengeluaranController::class, 'show']);
	Route::post('/kategori-pengeluaran', [KategoriPengeluaranController::class, 'store']);
	Route::put('/kategori-pengeluaran/{id}', [KategoriPengeluaranController::class, 'update']);
	Route::delete('/kategori-pengeluaran/{id}', [KategoriPengeluaranController::class, 'destroy']);
	
	Route::get('/satuan-grid', [SatuanController::class, 'grid']);
	Route::get('/satuan-search', [SatuanController::class, 'search']);
	Route::get('/satuan/{id}', [SatuanController::class, 'show']);
	Route::post('/satuan', [SatuanController::class, 'store']);
	Route::put('/satuan/{id}', [SatuanController::class, 'update']);
	Route::delete('/satuan/{id}', [SatuanController::class, 'destroy']);
	
	Route::get('/pegawai-grid', [PegawaiController::class, 'grid'])->middleware('can:pegawai-view');
	Route::get('/pegawai-search', [PegawaiController::class, 'search']);
	Route::get('/pegawai/{id}', [PegawaiController::class, 'show']);
	Route::post('/pegawai', [PegawaiController::class, 'store'])->middleware('can:pegawai-add');
	Route::put('/pegawai/{id}', [PegawaiController::class, 'update'])->middleware('can:pegawai-edit');
	Route::put('/pegawai/{id}/change-password', [PegawaiController::class, 'changePassword'])->middleware('can:pegawai-edit');
	Route::put('/pegawai/{id}/change-photo', [PegawaiController::class, 'changePhoto'])->middleware('can:pegawai-edit');
	Route::delete('/pegawai/{id}', [PegawaiController::class, 'destroy'])->middleware('can:pegawai-delete');
	
	Route::get('/pejabat-grid', [PejabatTtdController::class, 'grid'])->middleware('can:pejabat-view');
	Route::get('/pejabat-search', [PejabatTtdController::class, 'search']);
	Route::get('/pejabat/{id}', [PejabatTtdController::class, 'show'])->middleware('can:pejabat-view');
	Route::post('/pejabat', [PejabatTtdController::class, 'store'])->middleware('can:pejabat-add');
	Route::put('/pejabat/{id}', [PejabatTtdController::class, 'update'])->middleware('can:pejabat-edit');
	Route::put('/pejabat/{id}/active', [PejabatTtdController::class, 'setActive'])->middleware('can:pejabat-edit');
	Route::delete('/pejabat/{id}', [PejabatTtdController::class, 'destroy'])->middleware('can:pejabat-delete');
	
	Route::get('/profile/{id}', [ProfileController::class, 'show']);
	Route::put('/profile/{id}', [ProfileController::class, 'update']);
	Route::put('/profile/{id}/change-password', [ProfileController::class, 'changePassword']);
	Route::put('/profile/{id}/change-photo', [ProfileController::class, 'changePhoto']);

	Route::get('/role-grid', [RoleController::class, 'grid'])->middleware('can:peran-view');
	Route::get('/role-permissions', [RoleController::class, 'getPermission']);
	Route::get('/role/{id}', [RoleController::class, 'show'])->middleware('can:peran-view');
	Route::post('/role', [RoleController::class, 'store'])->middleware('can:peran-add');
	Route::put('/role/{id}', [RoleController::class, 'update'])->middleware('can:peran-edit');
	Route::delete('/role/{id}',  [RoleController::class, 'destroy'])->middleware('can:peran-delete');

	Route::get('/user-grid', [UserController::class, 'grid'])->middleware('can:user-view');
	Route::get('/user-search', [UserController::class, 'search']);
	Route::get('/user-sptsearch', [UserController::class, 'sptSearch']);
	Route::get('/user/{id}', [UserController::class, 'show'])->middleware('can:user-view');
	Route::post('/user', [UserController::class, 'store'])->middleware('can:user-add');
	Route::put('/user/{id}', [UserController::class, 'update'])->middleware('can:user-edit');
	Route::put('/user/{id}/change-password', [UserController::class, 'changePassword'])->middleware('can:user-edit');
	Route::put('/user/{id}/change-photo', [UserController::class, 'changePhoto'])->middleware('can:user-edit');
	Route::delete('/user/{id}', [UserController::class, 'destroy'])->middleware('can:user-delete');
	
	Route::get('/wilayah/provinsi', [WilayahController::class, 'getProvinsi']);
	Route::get('/wilayah/kota', [WilayahController::class, 'getKabupaten']);
	Route::get('/wilayah/kecamatan', [WilayahController::class, 'getKecamatan']);
	Route::get('/wilayah/desa', [WilayahController::class, 'getDesa']);

	//SPT
	Route::get('/spt-grid', [SPTController::class, 'grid'])->middleware('can:spt-view');
	Route::get('/spt/{id}', [SPTController::class, 'show'])->middleware('can:spt-view');
	Route::get('/spt/{id}/lihat', [SPTController::class, 'getSPT'])->middleware('can:spt-view');
	Route::get('/spt/{id}/cetak', [SPTController::class, 'cetakSPT'])->middleware('can:spt-generate');
	Route::post('/spt', [SPTController::class, 'store'])->middleware('can:spt-add');
	Route::post('/spt/{id}/void', [SPTController::class, 'void'])->middleware('can:spt-void');
	Route::put('/spt/{id}', [SPTController::class, 'update'])->middleware('can:spt-edit');
	Route::patch('/spt/{id}/selesai', [SPTController::class, 'finish'])->middleware('can:spt-finish');
	Route::patch('/spt/{id}/proses', [SPTController::class, 'proses'])->middleware('can:spt-edit');
	Route::delete('/spt/{id}', [SPTController::class, 'destroy'])->middleware('can:spt-delete');

	//SPPD
	Route::get('/spt/{id}/sppd-grid', [SPPDController::class, 'grid']);
	Route::get('/spt/cetak-sppd/{id}', [SPPDController::class, 'cetakSPPD']);
	Route::get('/spt/{id}/sppd/{sptDetailId}/{pegawaiId}', [SPPDController::class, 'show']);
	Route::get('/spt/lihat-sppd/{sptDetailId}/{pegawaiId}', [SPPDController::class, 'getSPPD']);

	//Log
	Route::get('/spt-log', [SPTLogController::class, 'grid'])->middleware('can:spt-log');

	Route::get('/biaya/grid/{id}/{pegawaiId}', [BiayaController::class, 'grid']);
	Route::post('/biaya', [BiayaController::class, 'store']);
	Route::put('/biaya/{id}', [BiayaController::class, 'update']);

	Route::get('/inap/{biayaId}/{pegawaiId}', [InapController::class, 'grid']);
	Route::get('/inap/{id}', [InapController::class, 'show']);
	Route::post('/inap', [InapController::class, 'store']);
	Route::put('/inap/{id}', [InapController::class, 'update']);
	Route::put('/inap/{id}/checkout', [InapController::class, 'checkout']);
	Route::put('/inap/{id}/upload', [InapController::class, 'uploadFile']);
	Route::delete('/inap/{id}/{biayaId}/{pegawaiId}', [InapController::class, 'destroy']);
	
	Route::get('/transport/{id}', [TransportController::class, 'show']);
	Route::post('/transport', [TransportController::class, 'store']);
	Route::put('/transport/{id}', [TransportController::class, 'update']);
	Route::put('/transport/{id}/upload', [TransportController::class, 'uploadFile']);
	Route::delete('/transport/{id}/{biayaId}/{pegawaiId}', [TransportController::class, 'destroy']);
	
	Route::get('/pengeluaran/{id}', [PengeluaranController::class, 'show']);
	Route::post('/pengeluaran', [PengeluaranController::class, 'store']);
	Route::put('/pengeluaran/{id}', [PengeluaranController::class, 'update']);
	Route::put('/pengeluaran/{id}/upload', [PengeluaranController::class, 'uploadFile']);
	Route::delete('/pengeluaran/{id}/{biayaId}/{pegawaiId}', [PengeluaranController::class, 'destroy']);

	Route::get('/report/spt/selesai', [ReportController::class, 'reportByFinishedSPT'])->middleware('can:laporan-tahunan');
	Route::post('/report/spt/pegawai', [ReportController::class, 'reportByPegawai'])->middleware('can:laporan-pegawai');
	
	Route::get('/spt/{id}/kwitansi', [SPPDController::class, 'cetakKwitansi']);
	Route::put('/spt/{id}/laporan', [SPPDController::class, 'cetakLaporan']);
	Route::get('/spt/{id}/biaya/{biayaId}/rumming/{pegawaiId}', [SPPDController::class, 'cetakRumming']);
	
	Route::get('/notif', [NotifController::class, 'notif']);
	Route::get('/notif/view', [NotifController::class, 'viewNotif']);
	Route::get('/notif/grid', [NotifController::class, 'listNotif']);
});