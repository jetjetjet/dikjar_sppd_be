<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnPengAnggaranToSpt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('spt', function (Blueprint $table) {
            $table->bigInteger('pengguna_anggaran_id')->nullable();
            $table->dateTime('finished_at')->nullable();
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
        Schema::table('spt', function (Blueprint $table) {
            $table->dropColumn(['pengguna_anggaran_id', 'finished_at', 'finished_by']);
        });
    }
}
