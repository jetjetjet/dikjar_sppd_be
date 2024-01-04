<?php

namespace App\Console\Commands;

use App\Models\Anggaran;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AnggaranTahunBaruCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'anggaran:copy {--tahun=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $year_param = $this->option('tahun');
            $date = new Carbon('01-01-' . $year_param);
            $tahun = (int) $date->addYear()->format('Y');
            $anggarans = Anggaran::where('periode', $year_param)->get();
            $bar = $this->output->createProgressBar(count($anggaran));
            $bar->start();

            foreach ($anggarans as $anggaran) {
                $this->performTask($anggaran);
                Anggaran::create([
                    'kode_rekening' => $anggaran->kode_rekening,
                    'nama_rekening' => $anggaran->nama_rekening,
                    'bidang'        => $anggaran->bidang,
                    'uraian'        => $anggaran->uraian,
                    'pagu'          => $anggaran->pagu,
                    'periode'       => $tahun,
                    'bendahara_id'  => $anggaran->bendahara_id,
                    'pptk_id'       => $anggaran->pptk_id,
                    'pengguna_id'   => $anggaran->pengguna_id,
                    'created_by'    => 1,
                    'created_at'    => now(),
                ]);
                $bar->advance();
            }
            $bar->finish();
        } catch (\Throwable $th) {
            // dd($th);
        }
    }
}
