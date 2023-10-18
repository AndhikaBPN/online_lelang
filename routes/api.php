<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BarangC;
Use App\Http\Controllers\LelangC;
use App\Http\Controllers\UserC;
use App\Http\Controllers\HistoryC;

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

Route::post('/registerAdmin', [UserC::class,'registerAdmin']);
Route::post('registerPengguna', [UserC::class,'registerPengguna']);
Route::post('/registerPetugas', [UserC::class,'registerPetugas'])->middleware('role:admin');
Route::post('/login', [UserC::class,'login']);

Route::group(['middleware'=>['jwt.verify']], function() {
    Route::post('/logout', [UserC::class,'logout']);
});

// BARANG
Route::get('barang', [BarangC::class,'index'])->middleware('role:admin,petugas,pengguna');
Route::get('barang/{id_barang}', [BarangC::class,'show'])->middleware('role:admin,petugas,pengguna');
Route::post('barang', [BarangC::class,'store'])->middleware('role:admin,petugas');
Route::put('barang/{id_barang}', [BarangC::class,'update'])->middleware('role:admin,petugas');
Route::delete('barang/{id_barang}', [BarangC::class,'destroy'])->middleware('role:admin,petugas');

// Lelang
Route::get('lelang', [LelangC::class,'index'])->middleware('role:admin,petugas,pengguna');
Route::get('lelang/{id_lelang}', [LelangC::class,'show'])->middleware('role:admin,petugas,pengguna');
Route::post('lelang', [LelangC::class,'store'])->middleware('role:admin,petugas,pengguna');
Route::post('lelang/close/{id_lelang}', [LelangC::class,'changeStatus'])->middleware('role:admin,petugas');

// HISTORY
Route::get('history', [HistoryC::class, 'index'])->middleware('role:pengguna,admin,petugas');
Route::get('history/{id_history}', [HistoryC::class,'show'])->middleware('role:admin,petugas,pengguna');
Route::post('history/{id_lelang}', [HistoryC::class, 'store'])->middleware('role:pengguna');