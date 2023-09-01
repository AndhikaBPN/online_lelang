<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLelangTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lelang', function (Blueprint $table) {
            $table->BigIncrements('id_lelang');
            $table->unsignedBigInteger('id_barang');
            $table->date('tgl_lelang');
            $table->integer('harga_akhir')->nullable();
            $table->unsignedBigInteger('id_pengguna')->nullable();
            $table->unsignedBigInteger('id_petugas');
            $table->enum('status', ['dibuka', 'ditutup']);
            $table->bigInteger('created_by')->default(0);
            $table->dateTime('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->bigInteger('updated_by')->default(0);
            $table->dateTime('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'))->nullable();
            $table->boolean('deleted')->default(0);
            
            $table->foreign('id_barang')->references('id_barang')->on('barang');
            $table->foreign('id_pengguna')->references('id')->on('users');
            $table->foreign('id_petugas')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lelang');
    }
}
