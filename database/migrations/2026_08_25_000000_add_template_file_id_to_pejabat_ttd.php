<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pejabat_ttd', function (Blueprint $table) {
            // Template Word (.docx) khusus pejabat ini, hasil upload lewat FileStorageService
            // (files.id). Nullable — pejabat tanpa template custom pakai fallback lama di
            // DocumentGeneratorService::resolveSptTemplate(). Lihat PLAN_TEMPLATE_PER_PEJABAT.md.
            $table->bigInteger('template_file_id')->unsigned()->nullable()->after('anggaran_id');
        });
    }

    public function down(): void
    {
        Schema::table('pejabat_ttd', function (Blueprint $table) {
            $table->dropColumn('template_file_id');
        });
    }
};
