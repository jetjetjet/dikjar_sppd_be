<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSPTSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('spt', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('spt_file_id')->nullable()->unsigned();
            // $table->bigInteger('bidang_id')->nullable();
            $table->integer('periode');
            $table->string('jenis_dinas');
            $table->bigInteger('anggaran_id');
            $table->bigInteger('pttd_user_id');
            $table->integer('no_index');
            $table->string('no_spt');
            $table->string('dasar_pelaksana');
            $table->string('untuk');
            $table->string('status');
            $table->string('transportasi');
            $table->string('provinsi_asal')->nullable();
            $table->string('kota_asal')->nullable();
            $table->string('kec_asal')->nullable();
            $table->string('provinsi_tujuan')->nullable();
            $table->string('kota_tujuan')->nullable();
            $table->string('kec_tujuan')->nullable();
            $table->date('tgl_berangkat');
            $table->date('tgl_kembali');
            $table->timestamps();
            $table->softDeletes();
            $table->bigInteger('created_by');
            $table->bigInteger('updated_by')->nullable();
            $table->bigInteger('deleted_by')->nullable();
            $table->dateTime('spt_generated_at')->nullable();
            $table->bigInteger('spt_generated_by')->nullable();
            $table->dateTime('finished_at')->nullable();
            $table->bigInteger('finished_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('spt');
    }
}
