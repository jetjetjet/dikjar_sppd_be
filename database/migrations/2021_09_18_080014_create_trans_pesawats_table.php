<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransPesawatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trans_pesawat', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('spt_id');
            $table->string('type');
            $table->string('maskapai');
            $table->string('no_tiket');
            $table->string('kode_booking')->nullable();
            $table->string('no_penerbangan')->nullable();
            $table->date('tgl');
            $table->decimal('jml_bayar',16,2);

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
        Schema::dropIfExists('trans_pesawat');
    }
}
