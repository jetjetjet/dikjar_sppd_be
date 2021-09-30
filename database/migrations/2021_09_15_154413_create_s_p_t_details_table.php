<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSPTDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('spt_detail', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('spt_id');
            $table->bigInteger('user_id');
            $table->bigInteger('sppd_file_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->bigInteger('created_by');
            $table->bigInteger('updated_by')->nullable();
            $table->bigInteger('deleted_by')->nullable();
            $table->timestamp('sppd_generated_at')->nullable();
            $table->bigInteger('sppd_generated_by')->nullable();
            $table->timestamp('finished_at')->nullable();
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
        Schema::dropIfExists('spt_detail');
    }
}
