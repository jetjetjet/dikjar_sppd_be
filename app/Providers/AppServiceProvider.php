<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // config('app.locale') (default 'id') sendiri tidak otomatis dipakai Carbon —
        // wajib di-set eksplisit supaya isoFormat() (dipakai DocumentGeneratorService/
        // DocumentMapperService saat generate & isi ulang dokumen SPT/SPPD/kwitansi/
        // laporan/rumming) menghasilkan nama bulan & hari Indonesia (mis. "25 Agustus
        // 2026"), bukan default Carbon ("25 August 2026").
        Carbon::setLocale(config('app.locale'));
    }
}
