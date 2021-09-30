<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AnggaranController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BidangController;
use App\Http\Controllers\JabatanController;
use App\Http\Controllers\PejabatTtdController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WilayahController;

use App\Http\Controllers\BiayaController;
use App\Http\Controllers\InapController;
use App\Http\Controllers\TransportController;
use App\Http\Controllers\SPTController;
use App\Http\Controllers\SPPDController;

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
Route::get('/tgl', [SPTController::class, 'tgl']);

Route::post('login', [AuthController::class, 'login'])->name('login');

Route::group(['middleware' => 'auth:sanctum'], function() {
	Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

	//MASTER DATA
	Route::get('/anggaran-grid', [AnggaranController::class, 'grid']);
	Route::get('/anggaran-search', [AnggaranController::class, 'search']);
	Route::get('/anggaran/{id}', [AnggaranController::class, 'show']);
	Route::post('/anggaran', [AnggaranController::class, 'store']);
	Route::put('/anggaran/{id}', [AnggaranController::class, 'update']);
	Route::delete('/anggaran/{id}', [AnggaranController::class, 'destroy']);

	// Route::get('/bidang-grid', [BidangController::class, 'grid']);
	// Route::get('/bidang-search', [BidangController::class, 'search']);
	// Route::get('/bidang/{id}', [BidangController::class, 'show']);
	// Route::post('/bidang', [BidangController::class, 'store']);
	// Route::put('/bidang/{id}', [BidangController::class, 'update']);
	// Route::delete('/bidang/{id}', [BidangController::class, 'destroy']);

	Route::get('/jabatan-grid', [JabatanController::class, 'grid']);
	Route::get('/jabatan-search', [JabatanController::class, 'search']);
	Route::get('/jabatan-parent', [JabatanController::class, 'parent']);
	Route::get('/jabatan/{id}', [JabatanController::class, 'show']);
	Route::post('/jabatan', [JabatanController::class, 'store']);
	Route::put('/jabatan/{id}', [JabatanController::class, 'update']);
	Route::delete('/jabatan/{id}', [JabatanController::class, 'destroy']);
	
	Route::get('/pejabat-grid', [PejabatTtdController::class, 'grid']);
	Route::get('/pejabat-search', [PejabatTtdController::class, 'search']);
	Route::get('/pejabat/{id}', [PejabatTtdController::class, 'show']);
	Route::post('/pejabat', [PejabatTtdController::class, 'store']);
	Route::put('/pejabat/{id}', [PejabatTtdController::class, 'update']);
	Route::put('/pejabat/{id}/active', [PejabatTtdController::class, 'setActive']);
	Route::delete('/pejabat/{id}', [PejabatTtdController::class, 'destroy']);
	
	Route::get('/profile/{id}', [ProfileController::class, 'show']);
	Route::put('/profile/{id}', [ProfileController::class, 'update']);
	Route::put('/profile/{id}/change-password', [ProfileController::class, 'changePassword']);
	Route::put('/profile/{id}/change-photo', [ProfileController::class, 'updateProfilePhoto']);

	Route::get('/role-grid', [RoleController::class, 'grid']);
	Route::get('/role-permissions', [RoleController::class, 'getPermission']);
	Route::get('/role/{id}', [RoleController::class, 'show']);
	Route::post('/role', [RoleController::class, 'store']);
	Route::put('/role/{id}', [RoleController::class, 'update']);
	Route::delete('/role/{id}',  [RoleController::class, 'destroy']);

	Route::get('/user-grid', [UserController::class, 'grid']);
	Route::get('/user-search', [UserController::class, 'search']);
	Route::get('/user-sptsearch', [UserController::class, 'sptSearch']);
	Route::get('/user/{id}', [UserController::class, 'show']);
	Route::post('/user', [UserController::class, 'store']);
	Route::put('/user/{id}', [UserController::class, 'update']);
	Route::put('/user/{id}/change-password', [UserController::class, 'changePassword']);
	Route::put('/user/{id}/change-photo', [UserController::class, 'changePhoto']);
	Route::delete('/user/{id}', [UserController::class, 'destroy']);
	
	Route::get('/wilayah/provinsi', [WilayahController::class, 'getProvinsi']);
	Route::get('/wilayah/kota', [WilayahController::class, 'getKabupaten']);
	Route::get('/wilayah/kecamatan', [WilayahController::class, 'getKecamatan']);
	Route::get('/wilayah/desa', [WilayahController::class, 'getDesa']);

	//SPT
	Route::get('/spt-grid', [SPTController::class, 'grid']);
	Route::get('/spt/{id}', [SPTController::class, 'show']);
	Route::get('/spt/{id}/lihat-spt', [SPTController::class, 'getSPT']);
	Route::post('/spt', [SPTController::class, 'store']);
	Route::post('/spt/{id}/cetak-spt', [SPTController::class, 'cetakSPT']);
	Route::post('/spt/{id}/selesai', [SPTController::class, 'finish']);
	Route::put('/spt/{id}', [SPTController::class, 'update']);
	Route::delete('/spt/{id}', [SPTController::class, 'destroy']);

	//SPPD
	Route::get('/spt/{id}/sppd-grid', [SPPDController::class, 'grid']);
	Route::get('/spt/{id}/sppd/{sptDetailId}/{userId}', [SPPDController::class, 'show']);
	Route::get('/spt/lihat-sppd/{sptDetailId}/{userId}', [SPPDController::class, 'getSPPD']);
	Route::post('/spt/{id}/cetak-sppd/{sptDetailId}/{userId}', [SPPDController::class, 'cetakSPPD']);

	Route::post('/biaya', [BiayaController::class, 'store']);
	Route::put('/biaya/{id}', [BiayaController::class, 'update']);

	Route::get('/inap/{biayaId}/{userId}', [InapController::class, 'grid']);
	Route::post('/inap', [InapController::class, 'store']);
	Route::put('/inap/{id}', [InapController::class, 'update']);
	Route::put('/inap/{id}/checkout', [InapController::class, 'checkout']);
	Route::put('/inap/{id}/upload-file', [InapController::class, 'uploadFile']);
	Route::delete('/inap/{id}/{biayaId}/{userId}', [InapController::class, 'destroy']);
	
	Route::get('/transport/{biayaId}/{userId}', [TransportController::class, 'grid']);
	Route::post('/transport', [TransportController::class, 'store']);
	Route::put('/transport/{id}', [TransportController::class, 'update']);
	Route::put('/transport/{id}/upload-file', [TransportController::class, 'uploadFile']);
	Route::delete('/transport/{id}/{biayaId}/{userId}', [TransportController::class, 'destroy']);
	
});