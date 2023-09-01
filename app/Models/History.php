<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    use HasFactory;

    public $timestamps=false;

    protected $table="history";
    protected $primaryKey="id_history";
    protected $fillable=[
        'id_lelang',
        'id_pengguna',
        'penawaran_harga',
        'status_pemenang',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at'
    ];

    public function history()
    {
        return $this->belongsto('App\Models\lelang', 'id_lelang', 'id_lelang');
    }

    public function barang()
    {
        return $this->belongsto('App\Models\barang', 'id_lelang', 'id_barang');
    }   

    public function user()
    {
        return $this->belongsto('App\Models\User', 'id_pengguna', 'id');
    }
}
