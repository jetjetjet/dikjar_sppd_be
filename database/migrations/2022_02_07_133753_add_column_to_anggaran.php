<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToAnggaran extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('anggaran', function (Blueprint $table) {
            $table->bigInteger('bendahara_id')->nullable();
            $table->bigInteger('pptk_id')->nullable();
            $table->bigInteger('pengguna_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('anggaran', function (Blueprint $table) {
            $table->dropColumn(['bendahara_id', 'pptk_id', 'pengguna_id']);
        });
    }
}
