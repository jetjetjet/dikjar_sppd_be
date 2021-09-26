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
            $table->bigInteger('user_id');

            $table->decimal('uang_saku',16,0)->nullable();
            $table->decimal('uang_representasi',16,0)->nullable();
            $table->decimal('uang_makan',16,0)->nullable();
            $table->decimal('uang_inap',16,0)->nullable();
            $table->decimal('uang_travel',16,0)->nullable();
            $table->decimal('uang_pesawat',16,0)->nullable();
            $table->decimal('jml_biaya',16,0);
            // $table->boolean('is_inap');
            // $table->boolean('is_transport');

            $table->timestamps();
            $table->bigInteger('created_by');
            $table->bigInteger('updated_by')->nullable();
            $table->softDeletes();
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
