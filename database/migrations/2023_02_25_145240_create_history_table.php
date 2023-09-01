<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('history', function (Blueprint $table) {
            $table->BigIncrements('id_history');
            $table->unsignedBigInteger('id_lelang');
            $table->unsignedBigInteger('id_pengguna');
            $table->integer('penawaran_harga');
            $table->enum('status_pemenang', ['proses', 'menang', 'kalah']);
            $table->bigInteger('created_by')->default(0);
            $table->dateTime('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->bigInteger('updated_by')->default(0);
            $table->dateTime('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'))->nullable();
            $table->boolean('deleted')->default(0);
            
            $table->foreign('id_lelang')->references('id_lelang')->on('lelang');
            $table->foreign('id_pengguna')->references('id')->on('users');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('history');
    }
}
