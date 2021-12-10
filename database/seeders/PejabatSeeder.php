<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PejabatTtd;

class PejabatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $pejabat = [
            [
                'pegawai_id' => 2,
                'autorisasi' => 'Petugas Tanda Tangan',
                'autorisasi_code' => 'PTTD',
                'is_active' => '1',
                'created_at' => now()->toDateTimeString(),
                'created_by' => 1
            ], 
            [
                'pegawai_id' => 3,
                'autorisasi' => 'Petugas Tanda Tangan',
                'autorisasi_code' => 'PTTD',
                'is_active' => '1',
                'created_at' => now()->toDateTimeString(),
                'created_by' => 1
            ], 
            [
                'pegawai_id' => 4,
                'autorisasi' => 'Petugas Tanda Tangan',
                'autorisasi_code' => 'PTTD',
                'is_active' => '1',
                'created_at' => now()->toDateTimeString(),
                'created_by' => 1
            ], 
            [
                'pegawai_id' => 5,
                'autorisasi' => 'Petugas Tanda Tangan',
                'autorisasi_code' => 'PTTD',
                'is_active' => '1',
                'created_at' => now()->toDateTimeString(),
                'created_by' => 1
            ], 
            [
                'pegawai_id' => 6,
                'autorisasi' => 'Petugas Tanda Tangan',
                'autorisasi_code' => 'PTTD',
                'is_active' => '1',
                'created_at' => now()->toDateTimeString(),
                'created_by' => 1
            ]
        ];

        PejabatTtd::truncate();
        PejabatTtd::insert($pejabat);
    }
}
