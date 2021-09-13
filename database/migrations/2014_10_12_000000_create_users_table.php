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
            $table->id();
            // $table->foreignId('position_id')->nullable();
            $table->string('nip',20);
            // $table->string('username',50);
            $table->string('email');
            $table->string('full_name', 100)->nullable();
            $table->string('path_foto')->nullable();
            $table->string('password', 100);
            $table->date('tgl_lahir')->nullable();
            $table->string('jenis_kelamin');
            $table->string('phone', 15)->nullable();
            $table->string('address', 400)->nullable();
            $table->text('ttd')->nullable();
            $table->dateTime('last_login')->nullable();
            $table->boolean('is_bendahara')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->bigInteger('created_by');
            $table->bigInteger('updated_by')->nullable();
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
