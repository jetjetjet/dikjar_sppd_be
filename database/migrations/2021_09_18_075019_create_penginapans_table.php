<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePenginapansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inap', function (Blueprint $table) {
            $table->id();
            // $table->bigInteger('user_id');
            $table->bigInteger('spt_id');

            $table->string('hotel');
            $table->string('room', 25);
            $table->date('tgl_checkin');
            $table->date('tgl_checkout');
            $table->integer('jml_hari');
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
        Schema::dropIfExists('inap');
    }
}
