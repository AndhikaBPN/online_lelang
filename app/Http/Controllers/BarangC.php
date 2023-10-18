<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use App\models\Barang;
use DB;

class BarangC
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $barangs = DB::table('barang')
                    ->select('*')
                    ->where('deleted', 0)
                    ->get();
        return response()->json(['status'=>'Success', 'data'=>['content'=>$barangs]], 200);
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
        $validate = Validator::make($request->all(), [
            "nama_barang" => 'required|string|max:50',
            "gambar" => 'mimes:jpeg,png,jpg|max:2048',
            "harga_awal" => 'required|numeric|min:1',
            "deskripsi_barang" => 'required|string|max:500',
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors()->toJson(), 400);
        } else {
            $input = $request->input();
            try {
                $data = new Barang;
                $data->nama_barang = $request->nama_barang;
                $data->gambar = $request->gambar;
                $data->tgl_daftar = Carbon::now();
                $data->harga_awal = $request->harga_awal;
                $data->deskripsi_barang = $request->deskripsi_barang;
                $data->created_by = auth()->user()->id;
                $data->created_at = Carbon::now();
                $data->updated_by = 0;
                $data->updated_at = Carbon::now();

                if($request->file('gambar')){
                    $gambar = $request->gambar;
                    $name = $gambar->getClientOriginalName();
                    $gambar->move('images/post', $name);
                    $data->gambar = $name;
                }

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
        // $detailBarang = Barang::find($id);
        $detailBarang = DB::table('barang')->where('id_barang', $id)->get();
        return response()->json(['status'=>'success', 'data'=>[
            'content' => $detailBarang
        ]], 200);
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
        $validate = Validator::make($request->all(), [
            'nama_barang' => 'required|string|max:50',
            'gambar' => 'mimes:jpeg,png,jpg|max:2048',
            'harga_awal' => 'required|numeric|min:1',
            'deskripsi_barang' => 'required|string|max:500',
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors()->toJson(), 400);
        } else {
            try {
                $data = Barang::find($id);
                $data->nama_barang = $request->nama_barang;
                $data->gambar = $request->gambar != null ? $request->gambar : $data->gambar;
                $data->tgl_daftar = Carbon::now();
                $data->harga_awal = $request->harga_awal;
                $data->deskripsi_barang = $request->deskripsi_barang;
                $data->updated_by = auth()->user()->id;
                $data->updated_at = Carbon::now();

                if($request->has('gambar')){
                    $gambar = $request->gambar;
                    $name = $gambar->getClientOriginalName();
                    $gambar->move('images/post', $name);
                    $data->gambar = $name;
                }

                $data->update();
                return response()->json(['status'=>'success', 'data' => [
                    'message' => 'Data berhasil di update'
                ]], 200);
            } catch (Exception $e) {
                return response()->json(['error'=>'Data gagal di update','message'=>$e], 500);
            }

        }
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
            $data = Barang::find($id);
            $data->updated_by = auth()->user()->id;
            $data->updated_at = Carbon::now();
            $data->deleted = 1;
            $data->update();
            return response()->json(['status'=>'success', 'data'=>['message'=>'Data berhasil dihapus']], 200);
        } catch (Exception $e) {
            return response()->json(['status'=>'failed', 'data'=>['message'=>'Data gagal dihapus', 'error'=>$e]], 500);
        }
    }
}
