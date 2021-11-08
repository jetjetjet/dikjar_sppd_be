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
            $table->bigInteger('pegawai_id');
            $table->boolean('is_pelaksana');
            $table->bigInteger('sppd_file_id')->nullable();
            // $table->boolean('is')
            
            $table->timestamp('sppd_generated_at')->nullable();
            $table->bigInteger('sppd_generated_by')->nullable();
            $table->timestamp('settled_at')->nullable();
            $table->bigInteger('settled_by')->nullable();
            
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
        Schema::dropIfExists('spt_detail');
    }
}
