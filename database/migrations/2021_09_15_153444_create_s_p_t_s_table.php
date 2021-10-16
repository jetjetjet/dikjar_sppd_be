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
            $table->bigInteger('pelaksana_id');
            $table->bigInteger('pttd_id');
            $table->date('tgl_spt');
            $table->integer('no_index');
            $table->string('no_spt');
            $table->string('dasar_pelaksana');
            $table->string('untuk');
            $table->string('status');
            $table->string('transportasi');
            $table->string('daerah_asal')->nullable();
            $table->string('daerah_tujuan')->nullable();
            $table->date('tgl_berangkat');
            $table->date('tgl_kembali');
            $table->dateTime('spt_generated_at')->nullable();
            $table->bigInteger('spt_generated_by')->nullable();
            $table->dateTime('finished_at')->nullable();
            $table->bigInteger('finished_by')->nullable();

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
        Schema::dropIfExists('spt');
    }
}
