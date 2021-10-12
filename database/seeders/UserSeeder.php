<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Pegawai;
use App\Models\Jabatan;
// use App\Models\Bidang

class UserSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		//Bidang
		// Bidang::truncate();
		// $bidang = Bidang::create([
		// 	'code' => 'DINAS PENDIDIKAN',
		// 	''
		// ])

		//Jabatan
		Jabatan::truncate();
		$bupati = Jabatan::create([
			'name' => 'Bupati Kerinci',
			'golongan' => '-',
			'is_parent' => '1',
			'parent_id' => null,
			'created_at' => now()->toDateTimeString(),
			'created_by' => '1'
		]);
		$wabup = Jabatan::create([
			'name' => 'Wakil Bupati Kerinci',
			'golongan' => '-',
			'is_parent' => '1',
			'parent_id' => $bupati->id,
			'created_at' => now()->toDateTimeString(),
			'created_by' => '1'
		]);
		$sekda = Jabatan::create([
			'name' => 'Sekretaris Daerah Kabupaten Kerinci',
			'golongan' => '-',
			'is_parent' => '1',
			'parent_id' => $bupati->id,
			'created_at' => now()->toDateTimeString(),
			'created_by' => '1'
		]);

		$kadin = Jabatan::create([
			'name' => 'Kepala Dinas',
			'golongan' => 'Pembina Tk. I',
			'is_parent' => '1',
			'parent_id' => $bupati->id,
			'created_at' => now()->toDateTimeString(),
			'created_by' => '1'
		]);

		
		$sekretaris = Jabatan::create([
			'name' => 'Sekretaris',
			'golongan' => 'Gol. ',
			'is_parent' => '1',
			'parent_id' => null,
			'created_at' => now()->toDateTimeString(),
			'created_by' => '1'
		]);

		$sumum = Jabatan::create([
			'name' => 'Kasubbag Umum dan Kepegawaian',
			'golongan' => 'Gol. ',
			'is_parent' => '0',
			'parent_id' => $sekretaris->id,
			'created_at' => now()->toDateTimeString(),
			'created_by' => '1'
		]);

		$skeu = Jabatan::create([
			'name' => 'Kasubbag Keuangan',
			'golongan' => 'Gol. ',
			'is_parent' => '0',
			'parent_id' => $sekretaris->id,
			'created_at' => now()->toDateTimeString(),
			'created_by' => '1'
		]);

		$spep = Jabatan::create([
			'name' => 'Perencanaan, Evaluasi dan Pelaporan',
			'golongan' => 'Gol. ',
			'is_parent' => '0',
			'parent_id' => $sekretaris->id,
			'created_at' => now()->toDateTimeString(),
			'created_by' => '1'
		]);

		$kabpd = Jabatan::create([
			'name' => 'Kepala Bidang Pendidikan Sekolah Dasar',
			'golongan' => 'Gol. ',
			'is_parent' => '1',
			'parent_id' => null,
			'created_at' => now()->toDateTimeString(),
			'created_by' => '1'
		]);

		$kasismp = Jabatan::create([
			'name' => 'Kasi Kurikulum SMP',
			'golongan' => 'Gol. ',
			'is_parent' => '0',
			'parent_id' => $kabpd->id,
			'created_at' => now()->toDateTimeString(),
			'created_by' => '1'
		]);

		$kasisd = Jabatan::create([
			'name' => 'Kasi Kurikulum SD',
			'golongan' => 'Gol. ',
			'is_parent' => '0',
			'parent_id' => $kabpd->id,
			'created_at' => now()->toDateTimeString(),
			'created_by' => '1'
		]);

		$kasimp = Jabatan::create([
			'name' => 'Kasi Penjaminan Mutu Pendidikan',
			'golongan' => 'Gol. ',
			'is_parent' => '0',
			'parent_id' => $kabpd->id,
			'created_at' => now()->toDateTimeString(),
			'created_by' => '1'
		]);

		$kagtk = Jabatan::create([
			'name' => 'Kepala Bidang GTK',
			'golongan' => 'Gol. ',
			'is_parent' => '1',
			'parent_id' => null,
			'created_at' => now()->toDateTimeString(),
			'created_by' => '1'
		]);

		$kasik = Jabatan::create([
			'name' => 'Kasi Kesejahteraan',
			'golongan' => 'Gol. ',
			'is_parent' => '0',
			'parent_id' => $kagtk->id,
			'created_at' => now()->toDateTimeString(),
			'created_by' => '1'
		]);

		$kasisdm = Jabatan::create([
			'name' => 'Kasi SDM',
			'golongan' => 'Gol. ',
			'is_parent' => '0',
			'parent_id' => $kagtk->id,
			'created_at' => now()->toDateTimeString(),
			'created_by' => '1'
		]);

		$kasimp = Jabatan::create([
			'name' => 'Kasi Mutasi Promosi',
			'golongan' => 'Gol. ',
			'is_parent' => '0',
			'parent_id' => $kagtk->id,
			'created_at' => now()->toDateTimeString(),
			'created_by' => '1'
		]);

		$kapaud = Jabatan::create([
			'name' => 'Kepala Bidang PAUD',
			'golongan' => 'Gol. ',
			'is_parent' => '1',
			'parent_id' => null,
			'created_at' => now()->toDateTimeString(),
			'created_by' => '1'
		]);

		$kasipaud = Jabatan::create([
			'name' => 'Kasi PAUD',
			'golongan' => 'Gol. ',
			'is_parent' => '0',
			'parent_id' => $kapaud->id,
			'created_at' => now()->toDateTimeString(),
			'created_by' => '1'
		]);

		$kasiseja = Jabatan::create([
			'name' => 'Kasi Kesejahteraan',
			'golongan' => 'Gol. ',
			'is_parent' => '0',
			'parent_id' => $kapaud->id,
			'created_at' => now()->toDateTimeString(),
			'created_by' => '1'
		]);

		$kasipm = Jabatan::create([
			'name' => 'Kasi Pendidikan Masyarakat',
			'golongan' => 'Gol. ',
			'is_parent' => '0',
			'parent_id' => $kapaud->id,
			'created_at' => now()->toDateTimeString(),
			'created_by' => '1'
		]);

		$kabsp = Jabatan::create([
			'name' => 'Kepala Bidang Sarana dan Prasarana',
			'golongan' => 'Gol. ',
			'is_parent' => '1',
			'parent_id' => null,
			'created_at' => now()->toDateTimeString(),
			'created_by' => '1'
		]);

		$kasissmp = Jabatan::create([
			'name' => 'Kasi Sarana SMP',
			'golongan' => 'Gol. ',
			'is_parent' => '0',
			'parent_id' => $kabsp->id,
			'created_at' => now()->toDateTimeString(),
			'created_by' => '1'
		]);

		$kasissd = Jabatan::create([
			'name' => 'Kasi Sarana SD',
			'golongan' => 'Gol. ',
			'is_parent' => '0',
			'parent_id' => $kabsp->id,
			'created_at' => now()->toDateTimeString(),
			'created_by' => '1'
		]);

		$kasispaud = Jabatan::create([
			'name' => 'Kasi Sarana PAUD',
			'golongan' => 'Gol. ',
			'is_parent' => '0',
			'parent_id' => $kabsp->id,
			'created_at' => now()->toDateTimeString(),
			'created_by' => '1'
		]);
	
		//User
		Pegawai::truncate();
		User::truncate();

		$admin = Pegawai::create([
			'nip' => '12345678',
			'full_name' => 'Super Admin',
			'jenis_kelamin' => 'Laki-laki',
			'email' => 'admin@disdikkerinci.id',
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		]);
		User::create([
			'nip' => '12345678',
			'password' => bcrypt('admin'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		]);

		Pegawai::create([
			'jabatan_id' => $bupati->id,
			'nip' => '00000000',
			'full_name' => 'Dr. H. Adirozal M.Si.',
			'jenis_kelamin' => 'Laki-laki',
			'email' => 'dummy@disdikkerinci.id',
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		]);

		Pegawai::create([
			'jabatan_id' => $wabup->id,
			'nip' => '00000001',
			'full_name' => 'Ir. H. Ami Taher',
			'jenis_kelamin' => 'Laki-laki',
			'email' => 'dummy@disdikkerinci.id',
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		]);

		Pegawai::create([
			'jabatan_id' => $sekda->id,
			'nip' => '00000002',
			'full_name' => 'nama_sekda',
			'jenis_kelamin' => 'Laki-laki',
			'email' => 'dummy@disdikkerinci.id',
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		]);
		
		$murison = Pegawai::create([
			'jabatan_id' => $kadin->id,
			'nip' => '196505291990031007',
			'full_name' => 'H. MURISON, S.Sos, S.Pd, M.Si',
			'jenis_kelamin' => 'Laki-laki',
			'email' => 'admin1@disdikkerinci.id',
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		]);
		User::create([
			'nip' => '196505291990031007',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		]);
		
		$romui = Pegawai::create([
			'jabatan_id' => $sekretaris->id,
			'nip' => '196409161986021001',
			'full_name' => 'ROMUI ELADI,S.Pd, MM',
			'jenis_kelamin' => 'Laki-laki',
			'email' => 'admin1@disdikkerinci.id',
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		]);
		User::create([
			'nip' => '196409161986021001',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		]);
		
		$yanto = Pegawai::create([
			'jabatan_id' => $sumum->id,
			'nip' => '196912171998031004',
			'full_name' => 'YANTO DIUM, S.ST,Par, M.Si',
			'jenis_kelamin' => 'Laki-laki',
			'email' => 'admin1@disdikkerinci.id',
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		]);
		User::create([
			'nip' => '196912171998031004',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		]);
		
		$hakimi = Pegawai::create([
			'jabatan_id' => $kabpd->id,
			'nip' => '196507081992031008',
			'full_name' => 'HAKIMI,S.Pd',
			'jenis_kelamin' => 'Laki-laki',
			'email' => 'admin1@disdikkerinci.id',
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		]);
		User::create([
			'nip' => '196507081992031008',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		]);
		
		$eka = Pegawai::create([
			'jabatan_id' => $spep->id,
			'nip' => '197705252012122001',
			'full_name' => 'EKA WADIANTI,S.Pd',
			'jenis_kelamin' => 'Perempuan',
			'email' => 'admin1@disdikkerinci.id',
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		]);
		User::create([
			'nip' => '197705252012122001',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		]);
		
		$sil = Pegawai::create([
			'jabatan_id' => $kasisd->id,
			'nip' => '198204292007011004',
			'full_name' => 'SILISMAN, S.Sos',
			'jenis_kelamin' => 'Laki-laki',
			'email' => 'admin1@disdikkerinci.id',
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		]);
		User::create([
			'nip' => '198204292007011004',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		]);

	}
}
