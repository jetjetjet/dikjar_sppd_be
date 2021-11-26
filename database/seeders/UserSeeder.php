<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Pegawai;
// use App\Models\Jabatan;
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
		// Jabatan::truncate();
		// $bupati = Jabatan::create([
		// 	'name' => 'Bupati Kerinci',
		// 	'is_parent' => '1',
		// 	'parent_id' => null,
		// 	'created_at' => now()->toDateTimeString(),
		// 	'created_by' => '1'
		// ]);
		// $wabup = Jabatan::create([
		// 	'name' => 'Wakil Bupati Kerinci',
		// 	'is_parent' => '1',
		// 	'parent_id' => $bupati->id,
		// 	'created_at' => now()->toDateTimeString(),
		// 	'created_by' => '1'
		// ]);
		// $sekda = Jabatan::create([
		// 	'name' => 'Sekretaris Daerah Kabupaten Kerinci',
		// 	'is_parent' => '1',
		// 	'parent_id' => $bupati->id,
		// 	'created_at' => now()->toDateTimeString(),
		// 	'created_by' => '1'
		// ]);

		// $kadin = Jabatan::create([
		// 	'name' => 'Kepala Dinas',
		// 	'is_parent' => '1',
		// 	'parent_id' => $bupati->id,
		// 	'created_at' => now()->toDateTimeString(),
		// 	'created_by' => '1'
		// ]);
	
		//User
		Pegawai::truncate();
		User::truncate();

		$admin = Pegawai::create([
			'jabatan' => 'admin aplikasi',
			'nip' => '12345678',
			'full_name' => 'Super Admin',
			'jenis_kelamin' => 'Laki-laki',
			'email' => 'admin@disdikkerinci.id',
			'pegawai_app' => '1',
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
			'jabatan' => 'Bupati Kerinci',
			'pangkat' => null,
			'golongan' => null,
			'nip' => '00000000',
			'pegawai_app' => '0',
			'full_name' => 'Dr. H. Adirozal M.Si.',
			'jenis_kelamin' => 'Laki-laki',
			'email' => 'dummy@disdikkerinci.id',
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		]);

		Pegawai::create([
			'jabatan' => 'Wakil Bupati Kerinci',
			'nip' => '00000001',
			'full_name' => 'Ir. H. Ami Taher',
			'pegawai_app' => '0',
			'jenis_kelamin' => 'Laki-laki',
			'email' => 'dummy@disdikkerinci.id',
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		]);

		Pegawai::create([
			'jabatan' => 'Sekretaris Daerah Kerinci',
			'nip' => '00000002',
			'full_name' => 'nama_sekda',
			'jenis_kelamin' => 'Laki-laki',
			'pegawai_app' => '0',
			'email' => 'dummy@disdikkerinci.id',
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		]);
		
		Pegawai::create([
			'jabatan' => 'Kepala Dinas Pendidikan',
			'pangkat' => 'Pembina Utama Muda',
			'golongan' => 'IV/c',
			'nip' => '196505291990031007',
			'full_name' => 'H.MURISON,S.Pd,S.Sos,M.Si',
			'jenis_kelamin' => 'Laki-laki',
			'pegawai_app' => '1',
			'email' => 'dummy@disdikkerinci.id',
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
	]);

Pegawai::create([
			'jabatan' => 'Sekretaris Dinas Pendidikan',
			'pangkat' => 'Pembina Tingkat I',
			'golongan' => 'IV/b',
			'nip' => '196409161986021001',
			'full_name' => 'ROMUI ELADI,S.Pd.MM',
			'pegawai_app' => '1',
			'jenis_kelamin' => 'Laki-laki',
			'email' => 'dummy@disdikkerinci.id',
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
	]);

Pegawai::create([
			'jabatan' => 'Kepala Sub Bagian Perencanaan, Evaluasi dan Pelaporan',
			'pangkat' => 'Penata',
			'golongan' => 'III/c',
			'nip' => '198112042006041010',
			'full_name' => 'DELVI SYOFRIADI,S.Sos, M.E.',
			'jenis_kelamin' => 'Laki-laki',
			'pegawai_app' => '1',
			'email' => 'dummy@disdikkerinci.id',
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
	]);

Pegawai::create([
			'jabatan' => 'Kepala Sub Bagian Keuangan dan BMD',
			'pangkat' => 'Penata',
			'golongan' => 'III/c',
			'nip' => '197907092006041015',
			'full_name' => 'DENI YANTO,SE. MM',
			'pegawai_app' => '1',
			'jenis_kelamin' => 'Laki-laki',
			'email' => 'dummy@disdikkerinci.id',
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
	]);

Pegawai::create([
			'jabatan' => 'Verifikator Keuangan',
			'pangkat' => 'Penata Tingkat I',
			'golongan' => 'III/d',
			'nip' => '196506171986022002',
			'full_name' => 'LISMI,S.Pd',
			'pegawai_app' => '1',
			'jenis_kelamin' => 'Laki-laki',
			'email' => 'dummy@disdikkerinci.id',
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
	]);

Pegawai::create([
			'jabatan' => 'Kepala Sub Bagian Umum Dan Kepegawaian',
			'pangkat' => 'Pembina',
			'golongan' => 'IV/a',
			'nip' => '196912171998031004',
			'pegawai_app' => '1',
			'full_name' => 'YANTO DIUM,S.ST, Par. M.Si',
			'jenis_kelamin' => 'Laki-laki',
			'email' => 'dummy@disdikkerinci.id',
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
	]);

Pegawai::create([
			'jabatan' => 'Pengadministrasi Persuratan',
			'pangkat' => 'Pembina',
			'golongan' => 'IV/a',
			'nip' => '196710061995121002',
			'full_name' => 'Drs. MAT AWAL',
			'pegawai_app' => '1',
			'jenis_kelamin' => 'Laki-laki',
			'email' => 'dummy@disdikkerinci.id',
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
	]);

Pegawai::create([
			'jabatan' => 'Pengadministrasi Umum',
			'pangkat' => 'Penata Muda Tk. I',
			'golongan' => 'III/b',
			'nip' => '197507312014081001',
			'pegawai_app' => '1',
			'full_name' => 'EDWAR,S.T.',
			'jenis_kelamin' => 'Laki-laki',
			'email' => 'dummy@disdikkerinci.id',
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
	]);

Pegawai::create([
			'jabatan' => 'Kepala Bidang Pembinaan Paud dan Pendidikan non Formal',
			'pangkat' => 'Penata',
			'golongan' => 'III/c',
			'pegawai_app' => '1',
			'nip' => '198006022010011005',
			'full_name' => 'ADLIZAR,S.Pd',
			'jenis_kelamin' => 'Laki-laki',
			'email' => 'dummy@disdikkerinci.id',
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
	]);

Pegawai::create([
			'jabatan' => 'Kepala Seksi Kurikulum PAUD dan PNF',
			'pangkat' => 'Penata',
			'golongan' => 'III/c',
			'pegawai_app' => '1',
			'nip' => '198005252009012007',
			'full_name' => 'EVONI LUKIAWATI,S.E.',
			'jenis_kelamin' => 'Laki-laki',
			'email' => 'dummy@disdikkerinci.id',
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
	]);

Pegawai::create([
			'jabatan' => 'Kepala Seksi Sarana dan Prasarana PAUD dan PNF',
			'pangkat' => 'Penata Muda Tk. I',
			'golongan' => 'III/b',
			'nip' => '197704242012121001',
			'pegawai_app' => '1',
			'full_name' => 'SALPANDI,S.T.',
			'jenis_kelamin' => 'Laki-laki',
			'email' => 'dummy@disdikkerinci.id',
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
	]);

Pegawai::create([
			'jabatan' => 'Kepala Seksi Kelembagaan dan Peserta Didik PAUD dan PNF',
			'pangkat' => 'Penata Muda',
			'golongan' => 'III/a',
			'nip' => '198909292011011003',
			'full_name' => 'RENGKI,S.Pdi',
			'jenis_kelamin' => 'Laki-laki',
			'pegawai_app' => '1',
			'email' => 'dummy@disdikkerinci.id',
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
	]);

Pegawai::create([
			'jabatan' => 'Kepala Bidang Pembinaan Sekolah Dasar',
			'pangkat' => 'Pembina',
			'golongan' => 'IV/a',
			'nip' => '196507081992031008',
			'full_name' => 'HAKIMI,S.Pd',
			'pegawai_app' => '1',
			'jenis_kelamin' => 'Laki-laki',
			'email' => 'dummy@disdikkerinci.id',
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
	]);

Pegawai::create([
			'jabatan' => 'Kepala Seksi Kurikulum Dan Penilaian SD',
			'pangkat' => 'Pembina Tingkat I',
			'golongan' => 'IV/b',
			'nip' => '197012151990071002',
			'full_name' => 'SAPREL,S.Pd',
			'jenis_kelamin' => 'Laki-laki',
			'pegawai_app' => '1',
			'email' => 'dummy@disdikkerinci.id',
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
	]);

Pegawai::create([
			'jabatan' => 'Kepala Seksi Sarana dan Prasarana SD',
			'pangkat' => 'Penata',
			'golongan' => 'III/c',
			'nip' => '198204292007011004',
			'full_name' => 'SILISMAN,S.Sos',
			'pegawai_app' => '1',
			'jenis_kelamin' => 'Laki-laki',
			'email' => 'dummy@disdikkerinci.id',
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
	]);

Pegawai::create([
			'jabatan' => 'Pengelola Data Sarana dan Prasarana Pendidikan',
			'pangkat' => 'Pengatur',
			'golongan' => 'II/c',
			'nip' => '197501112010011010',
			'full_name' => 'ASWARUDIN',
			'jenis_kelamin' => 'Laki-laki',
			'pegawai_app' => '1',
			'email' => 'dummy@disdikkerinci.id',
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
	]);

Pegawai::create([
			'jabatan' => 'Kepala Seksi Kelembagaan dan Peserta Didik Sekolah Dasar',
			'pangkat' => 'Pembina Tingkat I',
			'golongan' => 'IV/b',
			'nip' => '196404041989021002',
			'full_name' => 'Drs. MAT AGUSSALIM,M.Pd',
			'pegawai_app' => '1',
			'jenis_kelamin' => 'Laki-laki',
			'email' => 'dummy@disdikkerinci.id',
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
	]);

Pegawai::create([
			'jabatan' => 'Kepala Bidang Pembinaan Pendidik dan Tenaga Kependidikan',
			'pangkat' => 'Penata',
			'golongan' => 'III/c',
			'nip' => '198509112008031001',
			'pegawai_app' => '1',
			'full_name' => 'EFRI DONAL,S.Pd. M.PdI',
			'jenis_kelamin' => 'Laki-laki',
			'email' => 'dummy@disdikkerinci.id',
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
	]);

Pegawai::create([
			'jabatan' => 'Kepala Seksi PTK PAUD dan PNF',
			'pangkat' => 'Penata Muda',
			'golongan' => 'III/a',
			'nip' => '197307092011012001',
			'pegawai_app' => '1',
			'full_name' => 'SILISWATI,S.E.',
			'jenis_kelamin' => 'Laki-laki',
			'email' => 'dummy@disdikkerinci.id',
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
	]);

Pegawai::create([
			'jabatan' => 'Kepala Seksi PTK SMP',
			'pangkat' => 'Penata Muda Tk. I',
			'golongan' => 'III/b',
			'nip' => '196905102012121002',
			'pegawai_app' => '1',
			'full_name' => 'AINUL DAHRI,S.Pd',
			'jenis_kelamin' => 'Laki-laki',
			'email' => 'dummy@disdikkerinci.id',
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
	]);

Pegawai::create([
			'jabatan' => 'Kepala Seksi PTK SD',
			'pangkat' => 'Penata Muda Tk. I',
			'golongan' => 'III/b',
			'nip' => '197706192009031003',
			'full_name' => 'TAUFIKA OFIANDRI,S.Pd',
			'pegawai_app' => '1',
			'jenis_kelamin' => 'Laki-laki',
			'email' => 'dummy@disdikkerinci.id',
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
	]);

Pegawai::create([
			'jabatan' => 'Kepala Bidang Pembinaan Sekolah Menengah Pertama',
			'pangkat' => 'Penata Tingkat I',
			'golongan' => 'III/d',
			'nip' => '196501072005021002',
			'full_name' => 'KHAIRUL BAHRI,S.Pd.SD., M.M.',
			'jenis_kelamin' => 'Laki-laki',
			'pegawai_app' => '1',
			'email' => 'dummy@disdikkerinci.id',
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
	]);

Pegawai::create([
			'jabatan' => 'Kepala Seksi Sarana Prasarana SMP',
			'pangkat' => 'Penata Tingkat I',
			'golongan' => 'III/d',
			'nip' => '197407042007011005',
			'full_name' => 'YOSKA MARDIZAL,S.E.',
			'pegawai_app' => '1',
			'jenis_kelamin' => 'Laki-laki',
			'email' => 'dummy@disdikkerinci.id',
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
	]);

Pegawai::create([
			'jabatan' => 'Kepala Seksi Kurikulum dan Penilaian Sekolah Menengah Pertama',
			'pangkat' => 'Penata Muda Tk. I',
			'golongan' => 'III/b',
			'nip' => '196709292012121001',
			'full_name' => 'MUKHTAR,S.Pd',
			'jenis_kelamin' => 'Laki-laki',
			'pegawai_app' => '1',
			'email' => 'dummy@disdikkerinci.id',
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
	]);

Pegawai::create([
			'jabatan' => 'Kepala Seksi Kelembagaan dan Peserta Didik  SMP',
			'pangkat' => 'Penata Muda Tk. I',
			'golongan' => 'III/b',
			'nip' => '196701092012121001',
			'full_name' => 'PERLIUS,S.Pd',
			'jenis_kelamin' => 'Laki-laki',
			'pegawai_app' => '1',
			'email' => 'dummy@disdikkerinci.id',
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
	]);

Pegawai::create([
			'jabatan' => 'Pengadministrasi Keuangan',
			'pangkat' => 'Pengatur',
			'golongan' => 'II/c',
			'nip' => '198906172010012002',
			'full_name' => 'TIA YUNITA',
			'jenis_kelamin' => 'Laki-laki',
			'pegawai_app' => '1',
			'email' => 'dummy@disdikkerinci.id',
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
	]);

Pegawai::create([
			'jabatan' => 'Pengadministrasi Keuangan',
			'pangkat' => 'Pengatur Muda Tk.I',
			'golongan' => 'II/b',
			'nip' => '197512312014081001',
			'full_name' => 'IKHWAN',
			'pegawai_app' => '1',
			'jenis_kelamin' => 'Laki-laki',
			'email' => 'dummy@disdikkerinci.id',
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
	]);

Pegawai::create([
			'jabatan' => 'Pengelola Kepegawaian',
			'pangkat' => 'Penata Muda Tk. I',
			'golongan' => 'III/b',
			'nip' => '196810102012122003',
			'full_name' => 'ROHANIAH,S.Pd',
			'jenis_kelamin' => 'Laki-laki',
			'pegawai_app' => '1',
			'email' => 'dummy@disdikkerinci.id',
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
	]);

Pegawai::create([
			'jabatan' => 'Pengelola Data Pendidikan dan evaluasi',
			'pangkat' => 'Penata',
			'golongan' => 'III/c',
			'nip' => '197705252012122001',
			'full_name' => 'EKA WADIANTI,S.Pd',
			'jenis_kelamin' => 'Laki-laki',
			'pegawai_app' => '1',
			'email' => 'dummy@disdikkerinci.id',
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
	]);

Pegawai::create([
			'jabatan' => 'Pengelola Pendidikan ',
			'pangkat' => 'Penata Muda',
			'golongan' => 'III/a',
			'nip' => '198607022014081002',
			'full_name' => 'JUL HERDI WIJAYA,S.PdI',
			'pegawai_app' => '1',
			'jenis_kelamin' => 'Laki-laki',
			'email' => 'dummy@disdikkerinci.id',
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
	]);

Pegawai::create([
			'jabatan' => 'Analis Manajemen Perkantoran ',
			'pangkat' => 'Penata Muda',
			'golongan' => 'III/a',
			'nip' => '199311202020121008',
			'full_name' => 'ENDANG SUBRATA,SE',
			'jenis_kelamin' => 'Laki-laki',
			'pegawai_app' => '1',
			'pegawai_app' => '1',
			'email' => 'dummy@disdikkerinci.id',
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
	]);

Pegawai::create([
			'jabatan' => 'Analis Keuangan ',
			'pangkat' => 'Penata Muda',
			'golongan' => 'III/a',
			'nip' => '199604012020122021',
			'full_name' => 'RIKE MONIKA FAUZIAH,SE',
			'pegawai_app' => '1',
			'jenis_kelamin' => 'Perempuan',
			'email' => 'dummy@disdikkerinci.id',
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
	]);

Pegawai::create([
			'jabatan' => 'Analis Manajemen Perkantoran',
			'pangkat' => 'Penata Muda',
			'golongan' => 'III/a',
			'nip' => '199305112020122021',
			'full_name' => 'ANINDYA REZA MONICA,S.AP',
			'jenis_kelamin' => 'Perempuan',
			'pegawai_app' => '1',
			'email' => 'dummy@disdikkerinci.id',
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
	]);

Pegawai::create([
			'jabatan' => 'Analis Keuangan',
			'pangkat' => 'Penata Muda',
			'golongan' => 'III/a',
			'nip' => '199312032020121013',
			'full_name' => 'TRI OKA PUTRA,SE',
			'pegawai_app' => '1',
			'jenis_kelamin' => 'Perempuan',
			'email' => 'dummy@disdikkerinci.id',
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
	]);

Pegawai::create([
			'jabatan' => 'Analis Pendidikan',
			'pangkat' => 'Penata Muda',
			'golongan' => 'III/a',
			'nip' => '199506122020122028',
			'full_name' => 'AISYIAH ANGGUN PURNAMA,S.Pd',
			'pegawai_app' => '1',
			'jenis_kelamin' => 'Perempuan',
			'email' => 'dummy@disdikkerinci.id',
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
	]);

		
		User::create([
			'nip' => '196912171998031004',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		]);
		User::create([
			'nip' => '196507081992031008',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		]);
		User::create([
			'nip' => '197705252012122001',
			'password' => bcrypt('password'),
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
