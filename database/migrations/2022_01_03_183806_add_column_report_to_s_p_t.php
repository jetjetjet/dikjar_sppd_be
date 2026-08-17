<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnReportToSPT extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('spt', function (Blueprint $table) {
            $table->text('hasil')->nullable();
            $table->text('saran')->nullable();
            $table->bigInteger('laporan_file_id')->nullable();
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
            $table->dropColumn(['hasil', 'saran', 'laporan_file_id']);
        });
    }
}
