<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SPT;
use App\Models\Biaya;
use App\Models\Anggaran;

class SetupBiayaAnggaranCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup:biaya_anggaran';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $all_biaya = Biaya::all();
            $bar = $this->output->createProgressBar(count($all_biaya));
            $bar->start();

            foreach ($all_biaya as $biaya) {
                $spt = SPT::where('id', $biaya->spt_id)->first();
                $biaya->anggaran_id = $spt->anggaran_id;
                $biaya->save();
                $bar->advance();
            }
            $bar->finish();
        } catch (\Throwable $th) {
            dd($th);
        }
    }
}
