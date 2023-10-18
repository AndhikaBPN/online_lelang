<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
Use App\Models\Lelang;
Use App\Models\Barang;
Use App\Models\History;
Use App\Models\User;
use DB;
use DateTime;

class LelangC
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $data = DB::table('lelang')
                    ->select('*')
                    ->where('lelang.deleted', 0)
                    ->where('status', 'dibuka')
                    ->join('barang', 'lelang.id_barang', 'barang.id_barang')
                    ->get();
            return response()->json(['status'=>'success', 'data'=>['content'=>$data]], 200);
        } catch (Exception $e) {
            return response()->json(['status'=>'failed', 'data'=>['mesage'=>$e]], 500);
        }
    }

    public function index2(){
        try {
            $data = DB::table('lelang')->get()->where('deleted', 0);
            return response()->json(['status'=>'success', 'data'=>['content'=>$data]], 200);
        } catch (Exception $e) {
            return response()->json(['status'=>'failed', 'data'=>['mesage'=>$e]], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $checkId = DB::table('lelang')
                        ->select('id_barang')
                        ->where('deleted', 0)
                        ->where('id_barang', $request->id_barang)
                        ->first();

        if($checkId!=null){
            return response()->json(['status'=>'failed', 'data'=>['message'=>'Data sudah pernah di input']], 400);
        }

        $validate = Validator::make($request->all(), [
            'id_barang' => 'required|numeric|gte:1',
            'end_date' => 'required|numeric|gte:1'
        ]);

        if($validate->fails()){
            return response()->json(['status'=>'failed', 'data'=>['message'=>'ID Barang tidak dapat ditemukan']], 400);
        } else {
            try {
                $data = new Lelang;
                $data->id_barang = $request->id_barang;
                $data->tgl_lelang = Carbon::now();
                $data->end_at = Carbon::parse($data->end_at)->addDays($request->end_date);
                $data->id_pengguna = null;
                $data->id_petugas = auth()->user()->id;
                $data->status = 'dibuka';
                $data->created_by = auth()->user()->id;
                $data->created_at = Carbon::now();
                $data->updated_by = 0;
                $data->updated_at = Carbon::now();
                $data->save();
                return response()->json(['status'=>'success', 'data' => [
                    'message' => 'Data berhasil ditambah'
                ]], 200);
            } catch (Exception $e) {
                return response()->json(['error'=>'Data gagal ditambah','message'=>$e], 500);
            }

        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $detailLelang = DB::table('lelang')
                            ->join('barang', 'lelang.id_barang', 'barang.id_barang')
                            ->leftjoin('users AS pengguna', 'lelang.id_pengguna', 'pengguna.id')
                            ->leftjoin('users AS petugas', 'lelang.id_petugas', 'petugas.id')
                            ->select('barang.nama_barang', 
                                     'barang.gambar', 
                                     'barang.harga_awal', 
                                     'barang.deskripsi_barang',
                                     'lelang.harga_akhir',
                                     'lelang.tgl_lelang',
                                     'lelang.end_at',
                                     'pengguna.nama AS nama_pengguna',
                                     'petugas.nama AS nama_petugas',
                                     'lelang.created_by',
                                     'lelang.created_at',
                                     'lelang.updated_by',
                                     'lelang.updated_at')
                            ->where('lelang.id_lelang', $id)
                            ->where('lelang.deleted', 0)
                            ->get();
            
            return response()->json(['status'=>'success', 'data'=>[
                'content'=>$detailLelang
            ]], 200);
        } catch (Exception $e) {
            return response()->json(['status'=>'failed', 'data'=>[
                'content'=>'Tidak ada data'
            ]], 400);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $data = Lelang::find($id);
            $data->deleted = 1;
            $data->updated_by = auth()->user()->id;
            $data->updated_at = Carbon::now();
            $data->update();
            return response()->json(['status'=>'success', 'data'=>['message'=>'Data berhasil dihapus']], 200);
        } catch (Exception $e) {
            return response()->json(['status'=>'failed', 'data'=>['message'=>'Data gagal dihapus', 'error'=>$e]], 500);
        }
    }

    public function changeStatus($id){
        try {
            $getUserByPrice = DB::table('history')
                                ->select('*')
                                ->where('id_lelang', $id)
                                ->orderBy('penawaran_harga', 'desc')
                                ->first();
    
            $changeWinStatus = DB::table('history')
                                ->where('id_lelang', $id)
                                ->where('id_history', $getUserByPrice->id_history)
                                ->update(['status_pemenang'=>'menang']);
    
            $changeLoseStatus = DB::table('history')
                                ->where('id_lelang', $id)
                                ->where('status_pemenang', 'proses')
                                ->update(['status_pemenang'=>'kalah']);
    
            $updateLelang = DB::table('lelang')
                            ->where('id_lelang', $id)
                            ->update([
                                'harga_akhir'=>$getUserByPrice->penawaran_harga,
                                'id_pengguna'=>$getUserByPrice->id_pengguna,
                                'status'=>'ditutup'
                            ]);
            
            $data = Lelang::find($id);
            $data->updated_by = auth()->user()->id;
            $data->updated_at = Carbon::now();
            $data->update();
            return response()->json(['status'=>'success', 'data'=>['content'=>'Data berhasil diubah']], 200);
        } catch (Exception $e) {
            return response()->json(['status'=>'success', 'data'=>['content'=>'Data gagal diubah']], 500);
        }
        
    }
}
