<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePejabatTtdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pejabat_ttd', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->bigInteger('pegawai_id')->unsigned();
            $table->bigInteger('anggaran_id')->unsigned()->nullable();
            $table->string('autorisasi');
            $table->string('autorisasi_code');
            $table->boolean('is_active');
            $table->bigInteger('created_by')->nullable();
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
        Schema::dropIfExists('pejabat_ttd');
    }
}
