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
        // Schema::create('users', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('nip',20);
        //     $table->string('password', 100);
        //     $table->dateTime('last_login')->nullable();
        //     $table->rememberToken();
        //     $table->timestamps();
        //     $table->bigInteger('created_by');
        //     $table->bigInteger('updated_by')->nullable();
        //     $table->softDeletes();
        //     $table->bigInteger('deleted_by')->nullable();

        //     $table->foreign('nip')->references('nip')->on('pegawai')->onUpdate('cascade')->onDelete('cascade');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::dropIfExists('users');
    }
}
