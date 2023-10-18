<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use App\Models\History;
use App\Models\Lelang;
use App\Models\Barang;
use App\Models\User;
use DB;

class HistoryC
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $listBid = DB::table('history')
                        ->join('users', 'history.id_pengguna', 'users.id')
                        ->select('users.nama', 'history.penawaran_harga', 'history.status_pemenang')
                        ->where('history.deleted', 0)
                        ->get();
        
        return response()->json(['status'=>'success', 'data'=>['content'=>$listBid]], 200);
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
    public function store(Request $request, $id)
    {
        $validate = Validator::make($request->all(), [
            'penawaran_harga' => 'required|numeric'
        ]);

        if($validate->fails()){
            return response()->json($validate->errors()->toJson(), 400);
        }
        
        $hargaAwal = DB::table('lelang')
                        ->join('barang', 'lelang.id_barang', '=', 'barang.id_barang')
                        ->select('barang.harga_awal', 'lelang.id_lelang')
                        ->where('lelang.deleted', 0)
                        ->where('lelang.id_lelang', $id)
                        ->first();
        
        $penawaranHarga = DB::table('history')
                            ->where('deleted', 0)
                            ->where('id_lelang', $id)
                            ->select('penawaran_harga')
                            ->orderBy('penawaran_harga', 'desc')
                            ->first();

        $dateRange = DB::table('lelang')
                    ->select('end_at')
                    ->where('id_lelang', $id)
                    ->where('deleted', 0)
                    ->get();

        if(Carbon::now() > $dateRange->end_at){
            return response()->json(['status'=>'success', 'data' => ['message' => 'Sudah melewati tanggal lelang']], 200);
        }

        if($penawaranHarga!=null){
            if($request->penawaran_harga <= $penawaranHarga->penawaran_harga){
                return response()->json(['data'=>"Bid kurang dari penawaran harga tertinggi"], 400);
            }
        }

        if($request->penawaran_harga<=$hargaAwal->harga_awal){
            return response()->json(['data'=>"Bid kurang dari harga awal"], 400);
        }
        
        try {
            $data = new History;
            $data->id_lelang = $hargaAwal->id_lelang;
            $data->id_pengguna = auth()->user()->id;
            $data->penawaran_harga = $request->penawaran_harga;
            $data->status_pemenang = 'proses';
            $data->created_by = auth()->user()->id;
            $data->created_at = Carbon::now();
            $data->updated_by = 0;
            $data->updated_at = Carbon::now();
            $data->save();
            return response()->json(['status'=>'success', 'data' => ['message' => 'Data berhasil di tambah']], 200);
        } catch (Exception $e) {
            return response()->json(['data'=>"Terjadi kesalahan"], 500);
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
        $listTawaran = DB::table('history')
                    ->join('users', 'history.id_pengguna AS hi', 'users.id_pengguna AS user')
                    ->where('id_lelang', $id)
                    ->where('deleted', 0)
                    ->select('user.nama', 'hi.penawaran_harga')
                    ->orderBy('penawaran_harga', 'desc')
                    ->get();

        return response()->json(['data'=>$listTawaran], 200);
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
        //
    }
}
