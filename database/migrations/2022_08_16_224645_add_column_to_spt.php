<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToSpt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('spt', function (Blueprint $table) {
            $table->string('void_remark')->nullable();
            $table->bigInteger('voided_by')->nullable();
            $table->timestamp('voided_at')->nullable();
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
            $table->dropColumn(['voided_by', 'voided_at', 'void_remark']);
        });
    }
}
