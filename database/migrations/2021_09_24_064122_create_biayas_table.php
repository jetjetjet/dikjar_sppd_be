<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBiayasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('biaya', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('spt_id');
            $table->bigInteger('pegawai_id');

            $table->decimal('total_biaya_lainnya',16,0)->nullable();
            $table->decimal('total_biaya_inap',16,0)->nullable();
            $table->decimal('total_biaya_travel',16,0)->nullable();
            $table->decimal('total_biaya_pesawat',16,0)->nullable();
            $table->decimal('total_biaya',16,0);

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
        Schema::dropIfExists('biaya');
    }
}
