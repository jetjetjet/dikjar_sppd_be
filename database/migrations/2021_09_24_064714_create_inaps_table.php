<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInapsTable extends Migration
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
            $table->bigInteger('user_id');
            $table->bigInteger('biaya_id');

            $table->string('hotel');
            $table->string('room', 25);
            $table->decimal('harga',16,0);
            $table->date('tgl_checkin');
            $table->date('tgl_checkout')->nullable();
            $table->integer('jml_hari')->nullable();
            $table->decimal('jml_bayar',16,0)->nullable();
            $table->string('catatan')->nullable();
            $table->bigInteger('file_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->bigInteger('created_by');
            $table->bigInteger('updated_by')->nullable();
            $table->bigInteger('deleted_by')->nullable();
            $table->dateTime('checkout_at')->nullable();
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
