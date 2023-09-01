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

Route::group(['middleware'=>['jwt.verify']], function() {
    Route::post('/logout', [UserC::class,'logout']);
});

Route::post('/registerAdmin', [UserC::class,'registerAdmin']);
Route::post('registerPengguna', [UserC::class,'registerPengguna']);
Route::post('/login', [UserC::class,'login']);

// ADMIN
Route::group(['middleware'=>['api.admin']], function(){
    // ADD PETUGAS
    Route::post('/registerPetugas', [UserC::class,'registerPetugas']);

    // BARANG
    Route::get('/getbarang', [BarangC::class, 'index']);
    Route::get('/getbarang/{id}', [BarangC::class, 'show']);
    Route::post('/insertbarang', [BarangC::class,'store']);
    Route::put('/editbarang/{id}', [BarangC::class,'update']);
    Route::put('/hapusbarang/{id}', [BarangC::class,'destroy']);

    // LELANG
    Route::get('/getlelang', [LelangC::class,'index']);
    Route::get('/allLelang', [LelangC::class,'index2']);
    Route::post('/insertlelang', [LelangC::class, 'store']);
    Route::put('/deletelelang/{id}', [LelangC::class,'destroy']);

    // HISTORY
    Route::get('/gethistory', [HistoryC::class,'index']);
    Route::get('/getDetailhistory/{id}', [HistoryC::class,'show']);
});

Route::group(['middleware'=>['api.pengguna']], function() {
    Route::get('/getDetailhistory/{id}', [HistoryC::class,'show']);
    Route::get('/gethistory', [HistoryC::class,'index']);
});