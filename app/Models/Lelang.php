<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lelang extends Model
{
    use HasFactory;

    public $timestamps=false;

    protected $table="lelang";
    protected $primaryKey="id_lelang";
    protected $fillable=[
        'id_barang',
        'tgl_lelang',
        'harga_akhir',
        'id_pengguna',
        'id_petugas',
        'status',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at'
    ];

    public function barang()
    {
        return $this->belongsto('App\Models\barang', 'id_barang', 'id_barang');
    }

    public function user()
    {
        return $this->belongsto('App\Models\User', 'id_pengguna', 'id');
    }
}
