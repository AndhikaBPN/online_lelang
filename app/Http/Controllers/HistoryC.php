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
        // $data = DB::table('history')->get();
        // return response()->json(['status'=>'success','data'=>['content'=>$data]], 200);

        $cekHarga = DB::table('history')
                    ->where('id_pengguna', auth()->user()->id)
                    ->select('*')
                    ->orderBy('penawaran_harga', 'desc')
                    ->get();
        
        $cekPenawaranHarga = DB::table('history')
                            // ->where('id_pengguna', auth()->user()->id)
                            // ->select('penawaran_harga')
                            // ->orderBy('penawaran_harga', 'desc')
                            ->get();


        if($cekPenawaranHarga->isNotEmpty()){
            foreach ($cekPenawaranHarga as $cph) {
                if($cph->penawaran_harga <= 70000){
                    return response()->json(['data'=>$cph], 200);
                }else{
                    return response()->json(['data'=>'gaadaaaa'], 400);
                }
            }
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
        $cekHarga = DB::table('history')
                    ->where('id_lelang', $request->id_lelang)
                    ->where('id_pengguna', auth()->user()->id)
                    ->first()
                    ->orderBy('penawaran_harga', 'desc');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $cekHarga = DB::table('history')
                    ->where('id_history', $id)
                    ->where('id_pengguna', 3)
                    ->select('*')
                    ->orderBy('penawaran_harga', 'desc')
                    ->get();

        return response()->json(['data'=>$cekHarga], 200);
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
