<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transport', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('biaya_id');
            $table->bigInteger('pegawai_id');
            $table->string('jenis_transport');
            $table->string('catatan')->nullable();
            $table->string('perjalanan');
            $table->string('agen');
            $table->string('no_tiket');
            $table->string('kode_booking')->nullable();
            $table->string('no_penerbangan')->nullable();
            $table->bigInteger('file_id')->nullable();
            $table->date('tgl');
            $table->decimal('total_bayar',16,0);

            $table->timestamps();
            $table->softDeletes();
            $table->bigInteger('created_by');
            $table->bigInteger('updated_by')->nullable();
            $table->bigInteger('deleted_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transport');
    }
}
