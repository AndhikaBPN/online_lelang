<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->BigIncrements('id');
            $table->string('nama');
            $table->string('alamat')->nullable();
            $table->String('no_hp');
            $table->String('email');
            $table->string('username');
            $table->string('password');
            $table->enum('role', ['admin', 'petugas', 'pengguna']);
            $table->bigInteger('created_by')->default(0);
            $table->dateTime('created_at')->default(DB::raw('CURRENT_TIMESTAMP'))->nullable();
            $table->bigInteger('updated_by')->default(0);
            $table->dateTime('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'))->nullable();
            $table->boolean('deleted')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
