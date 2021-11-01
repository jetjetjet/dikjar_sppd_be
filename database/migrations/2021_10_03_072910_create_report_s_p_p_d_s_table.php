<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportSPPDSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_sppd', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('pegawai_id');
            $table->bigInteger('spt_id');
            $table->bigInteger('spt_detail_id');
            $table->bigInteger('biaya_id');
            $table->string('nama_pelaksana');
            $table->string('jabatan');
            $table->string('no_pku')->nullable();
            $table->string('no_spt');
            $table->string('no_sppd')->nullable();
            $table->string('kegiatan');
            $table->string('penyelenggara')->nullable();
            $table->string('lok_asal');
            $table->string('lok_tujuan');
            $table->date('tgl_berangkat');
            $table->date('tgl_kembali');
            $table->decimal('uang_saku', 16,0)->nullable();
            $table->decimal('uang_makan', 16,0)->nullable();
            $table->decimal('uang_representasi', 16,0)->nullable();
            $table->decimal('uang_penginapan', 16,0)->nullable();
            $table->decimal('uang_travel', 16,0)->nullable();
            $table->decimal('uang_pesawat', 16,0)->nullable();
            $table->decimal('uang_total', 16,0)->nullable();
            
            $table->string('inap_hotel')->nullable();
            $table->string('inap_room')->nullable();
            $table->date('inap_checkin')->nullable();
            $table->date('inap_checkout')->nullable();
            $table->integer('inap_jml_hari')->nullable();
            $table->decimal('inap_per_malam', 16,0)->nullable();
            $table->decimal('inap_jumlah', 16,0)->nullable();
            
            $table->string('pesbrgkt_maskapai')->nullable();
            $table->string('pesbrgkt_no_tiket')->nullable();
            $table->string('pesbrgkt_kode_booking')->nullable();
            $table->string('pesbrgkt_no_penerbangan')->nullable();
            $table->date('pesbrgkt_tgl')->nullable();
            $table->decimal('pesbrgkt_jumlah', 16,0)->nullable();

            $table->string('peskmbl_maskapai')->nullable();
            $table->string('peskmbl_no_tiket')->nullable();
            $table->string('peskmbl_kode_booking')->nullable();
            $table->string('peskmbl_no_penerbangan')->nullable();
            $table->date('peskmbl_tgl')->nullable();
            $table->decimal('peskmbl_jumlah', 16,0)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('report_sppd');
    }
}
