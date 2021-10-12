<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePengeluaransTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pengeluaran', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('biaya_id');
            $table->bigInteger('pegawai_id');
            $table->date('tgl');
            $table->string('jenis_pengeluaran');
            $table->string('catatan')->nullable();
            $table->string('jenis_satuan');
            $table->integer('jml');
            $table->integer('total_hari');
            $table->decimal('total_biaya',16,0);
            $table->bigInteger('file_id')->nullable();

            $table->timestamps();
            $table->softDeletes();
            $table->string('created_by');
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pengeluaran');
    }
}
