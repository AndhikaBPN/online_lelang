<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
Use App\Models\Lelang;
Use App\Models\Barang;
use DB;

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
        // $check = DB::table('lelang')
        //         ->join('barang', 'lelang.id_barang', 'barang.id_barang')
        //         ->where('status', 'dibuka')
        //         ->where('lelang.id_barang', $request->id_barang)
        //         ->get();
        
        // $count = count($check);

        $validate = Validator::make($request->all(), [
            'id_barang' => 'required|numeric|gte:1|unique:lelang'
        ]);

        if($validate->fails()){
            return response()->json(['status'=>'failed', 'data'=>['message'=>'Data sudah pernah di input']], 400);
        } else {
            try {
                $data = new Lelang;
                $data->id_barang = $request->id_barang;
                $data->tgl_lelang = Carbon::now();
                $data->id_pengguna = null;
                $data->id_petugas = auth()->user()->id;
                $data->status = 'dibuka';
                $data->created_by = auth()->user()->id;
                $data->created_at = Carbon::now();
                $data->updated_by = 0;
                $data->updated_at = null;
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
        //
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
            $data->update();
            return response()->json(['status'=>'success', 'data'=>['message'=>'Data berhasil dihapus']], 200);
        } catch (Exception $e) {
            return response()->json(['status'=>'failed', 'data'=>['message'=>'Data berhasil dihapus', 'error'=>$e]], 500);
        }
    }
}
