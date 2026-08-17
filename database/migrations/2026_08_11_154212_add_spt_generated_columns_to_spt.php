<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('spt', function (Blueprint $table) {
            $table->timestamp('spt_generated_at')->nullable();
            $table->bigInteger('spt_generated_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('spt', function (Blueprint $table) {
            $table->dropColumn(['spt_generated_at', 'spt_generated_by']);
        });
    }
};
