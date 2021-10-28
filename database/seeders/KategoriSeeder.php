<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KategoriPengeluaran;
use App\Models\Satuan;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        KategoriPengeluaran::truncate();
        Satuan::truncate();

        $kategori = array('Uang Saku', 'Uang Makan', 'Uang Representasi');
        foreach($kategori as $kat) {
            KategoriPengeluaran::create(['name' => $kat, 'created_by' => 1]);
        }

        $satuan = array('Hari', 'Unit', 'pcs', 'Item');
        foreach($satuan as $sat) {
            Satuan::create(['name' => $sat, 'created_by' => 1]);
        }

    }
}
