<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameColumnTransportToBiaya extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('biaya', 'total_biaya_travel'))
        {
            Schema::table('biaya', function (Blueprint $table)
            {
                $table->renameColumn('total_biaya_travel', 'total_biaya_transport');
            });
        }

        if (Schema::hasColumn('report_sppd', 'uang_travel'))
        {
            Schema::table('report_sppd', function (Blueprint $table)
            {
                $table->renameColumn('uang_travel', 'uang_transport');
                $table->decimal('uang_lain', 16,0)->nullable();
                $table->decimal('uang_dinas_dlm', 16,0)->nullable();
                $table->decimal('uang_dinas_luar', 16,0)->nullable();
                $table->string('nama_rekening')->nullable();
                $table->string('kode_rekening')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('biaya', 'total_biaya_transport'))
        {
            Schema::table('biaya', function (Blueprint $table)
            {
                $table->renameColumn('total_biaya_transport', 'total_biaya_travel');
            });
        }

        if (Schema::hasColumn('report_sppd', 'uang_transport'))
        {
            Schema::table('report_sppd', function (Blueprint $table)
            {
                $table->renameColumn('uang_transport', 'uang_travel');
                $table->dropColumn(['uang_lain', 'uang_dinas_dlm', 'uang_dinas_luar', 'nama_rekening','kode_rekening']);
            });
        }
    }
}
