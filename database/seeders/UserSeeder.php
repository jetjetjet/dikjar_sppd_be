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
		//User
		Pegawai::truncate();
		User::truncate();

		$dataPegawai = [
			[
				'jabatan' => 'Bupati Kerinci',
				'pangkat' => null,
				'golongan' => null,
				'nip' => null,
				'pegawai_app' => '0',
				'full_name' => 'Dr. H. Adirozal M.Si.',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'dummy1@disdikkerinci.id',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Wakil Bupati Kerinci',
				'pangkat' => null,
				'golongan' => null,
				'nip' => null,
				'full_name' => 'Ir. H. Ami Taher',
				'pegawai_app' => '0',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'dummy2@disdikkerinci.id',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Sekretaris Daerah Kerinci',
				'nip' => null,
				'pangkat' => null,
				'golongan' => null,
				'full_name' => 'nama_sekda',
				'jenis_kelamin' => 'Laki-laki',
				'pegawai_app' => '0',
				'email' => 'dummy3@disdikkerinci.id',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Kepala Dinas Pendidikan',
				'pangkat' => 'Pembina Utama Muda',
				'golongan' => 'IV/c',
				'nip' => '196505291990031007',
				'full_name' => 'H.MURISON,S.Pd,S.Sos,M.Si',
				'jenis_kelamin' => 'Laki-laki',
				'pegawai_app' => '1',
				'email' => 'murison@disdikkerinci.id',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			], 
			[
				'jabatan' => 'Sekretaris Dinas Pendidikan',
				'pangkat' => 'Pembina Tingkat I',
				'golongan' => 'IV/b',
				'nip' => '196409161986021001',
				'full_name' => 'ROMUI ELADI,S.Pd.MM',
				'pegawai_app' => '1',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'romui_eladi@disdikkerinci.id',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Kepala Sub Bagian Perencanaan, Evaluasi dan Pelaporan',
				'pangkat' => 'Penata',
				'golongan' => 'III/c',
				'nip' => '198112042006041010',
				'full_name' => 'DELVI SYOFRIADI,S.Sos, M.E.',
				'jenis_kelamin' => 'Laki-laki',
				'pegawai_app' => '1',
				'email' => 'delvi_syofriadi@disdikkerinci.id',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Kepala Sub Bagian Keuangan dan BMD',
				'pangkat' => 'Penata',
				'golongan' => 'III/c',
				'nip' => '197907092006041015',
				'full_name' => 'DENI YANTO,SE. MM',
				'jenis_kelamin' => 'Laki-laki',
				'pegawai_app' => '1',
				'email' => 'deni_yanto@disdikkerinci.id',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Verifikator Keuangan',
				'pangkat' => 'Penata Tingkat I',
				'golongan' => 'III/d',
				'nip' => '196506171986022002',
				'full_name' => 'LISMI,S.Pd',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'lismi@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Kepala Sub Bagian Umum Dan Kepegawaian',
				'pangkat' => 'Pembina',
				'golongan' => 'IV/a',
				'nip' => '196912171998031004',
				'full_name' => 'YANTO DIUM,S.ST, Par. M.Si',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'yanto_dium@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Pengadministrasi Persuratan',
				'pangkat' => 'Pembina',
				'golongan' => 'IV/a',
				'nip' => '196710061995121002',
				'full_name' => 'Drs. MAT AWAL',
				'jenis_kelamin' => 'Laki-laki',
				'pegawai_app' => '1',
				'email' => 'mat_awal@disdikkerinci.id',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Pengadministrasi Umum',
				'pangkat' => 'Penata Muda Tk. I',
				'golongan' => 'III/b',
				'nip' => '197507312014081001',
				'full_name' => 'EDWAR,S.T.',
				'jenis_kelamin' => 'Laki-laki',
				'pegawai_app' => '1',
				'email' => 'edwar@disdikkerinci.id',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Kepala Bidang Pembinaan Paud dan Pendidikan non Formal',
				'pangkat' => 'Penata',
				'golongan' => 'III/c',
				'nip' => '198006022010011005',
				'full_name' => 'ADLIZAR,S.Pd',
				'jenis_kelamin' => 'Laki-laki',
				'pegawai_app' => '1',
				'email' => 'adlizar@disdikkerinci.id',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Kepala Seksi Kurikulum PAUD dan PNF',
				'pangkat' => 'Penata',
				'golongan' => 'III/c',
				'nip' => '198005252009012007',
				'full_name' => 'EVONI LUKIAWATI,S.E.',
				'jenis_kelamin' => 'Laki-laki',
				'pegawai_app' => '1',
				'email' => 'evoni@disdikkerinci.id',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Kepala Seksi Sarana dan Prasarana PAUD dan PNF',
				'pangkat' => 'Penata Muda Tk. I',
				'golongan' => 'III/b',
				'nip' => '197704242012121001',
				'full_name' => 'SALPANDI,S.T.',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'salpandi@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Kepala Seksi Kelembagaan dan Peserta Didik PAUD dan PNF',
				'pangkat' => 'Penata Muda',
				'golongan' => 'III/a',
				'nip' => '198909292011011003',
				'full_name' => 'RENGKI,S.Pdi',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'rengki@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Kepala Bidang Pembinaan Sekolah Dasar',
				'pangkat' => 'Pembina',
				'golongan' => 'IV/a',
				'nip' => '196507081992031008',
				'full_name' => 'HAKIMI,S.Pd',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'hakimi@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Kepala Seksi Kurikulum Dan Penilaian SD',
				'pangkat' => 'Pembina Tingkat I',
				'golongan' => 'IV/b',
				'nip' => '197012151990071002',
				'full_name' => 'SAPREL,S.Pd',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'saprel@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Kepala Seksi Sarana dan Prasarana SD',
				'pangkat' => 'Penata',
				'golongan' => 'III/c',
				'nip' => '198204292007011004',
				'full_name' => 'SILISMAN,S.Sos',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'silisman@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Pengelola Data Sarana dan Prasarana Pendidikan',
				'pangkat' => 'Pengatur',
				'golongan' => 'II/c',
				'nip' => '197501112010011010',
				'full_name' => 'ASWARUDIN',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'aswarudin@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Kepala Seksi Kelembagaan dan Peserta Didik Sekolah Dasar',
				'pangkat' => 'Pembina Tingkat I',
				'golongan' => 'IV/b',
				'nip' => '196404041989021002',
				'full_name' => 'Drs. MAT AGUSSALIM,M.Pd',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'mat_agussalim@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Kepala Bidang Pembinaan Pendidik dan Tenaga Kependidikan',
				'pangkat' => 'Penata',
				'golongan' => 'III/c',
				'nip' => '198509112008031001',
				'full_name' => 'EFRI DONAL,S.Pd. M.PdI',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'efri_donal@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Kepala Seksi PTK PAUD dan PNF',
				'pangkat' => 'Penata Muda',
				'golongan' => 'III/a',
				'nip' => '197307092011012001',
				'full_name' => 'SILISWATI,S.E.',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'siliswati@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Kepala Seksi PTK SMP',
				'pangkat' => 'Penata Muda Tk. I',
				'golongan' => 'III/b',
				'nip' => '196905102012121002',
				'full_name' => 'AINUL DAHRI,S.Pd',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'ainul_dahri@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Kepala Seksi PTK SD',
				'pangkat' => 'Penata Muda Tk. I',
				'golongan' => 'III/b',
				'nip' => '197706192009031003',
				'full_name' => 'TAUFIKA OFIANDRI,S.Pd',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'taufika_ofiandri@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Kepala Bidang Pembinaan Sekolah Menengah Pertama',
				'pangkat' => 'Penata Tingkat I',
				'golongan' => 'III/d',
				'nip' => '196501072005021002',
				'full_name' => 'KHAIRUL BAHRI,S.Pd.SD.,   M.M.',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'khairul_bahri@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Kepala Seksi Sarana Prasarana SMP',
				'pangkat' => 'Penata Tingkat I',
				'golongan' => 'III/d',
				'nip' => '197407042007011005',
				'full_name' => 'YOSKA MARDIZAL,S.E.',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'yoska_mardizal@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Kepala Seksi Kurikulum dan Penilaian Sekolah Menengah Pertama',
				'pangkat' => 'Penata Muda Tk. I',
				'golongan' => 'III/b',
				'nip' => '196709292012121001',
				'full_name' => 'MUKHTAR,S.Pd',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'mukhtar@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Kepala Seksi Kelembagaan dan Peserta Didik  SMP',
				'pangkat' => 'Penata Muda Tk. I',
				'golongan' => 'III/b',
				'nip' => '196701092012121001',
				'full_name' => 'PERLIUS,S.Pd',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'perlius@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Pengadministrasi Keuangan',
				'pangkat' => 'Pengatur',
				'golongan' => 'II/c',
				'nip' => '198906172010012002',
				'full_name' => 'TIA YUNITA',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'tia_yunita@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Pengadministrasi Keuangan',
				'pangkat' => 'Pengatur Muda Tk.I',
				'golongan' => 'II/b',
				'nip' => '197512312014081001',
				'full_name' => 'IKHWAN',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'ikhwan@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Pengelola Kepegawaian',
				'pangkat' => 'Penata Muda Tk. I',
				'golongan' => 'III/b',
				'nip' => '196810102012122003',
				'full_name' => 'ROHANIAH,S.Pd',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'rohaniah@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Pengelola Data Pendidikan dan evaluasi',
				'pangkat' => 'Penata',
				'golongan' => 'III/c',
				'nip' => '197705252012122001',
				'full_name' => 'EKA WADIANTI,S.Pd',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'eka_wadianti@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Pengelola Pendidikan ',
				'pangkat' => 'Penata Muda',
				'golongan' => 'III/a',
				'nip' => '198607022014081002',
				'full_name' => 'JUL HERDI WIJAYA,S.PdI',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'jul_herdi@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Analis Manajemen Perkantoran ',
				'pangkat' => 'Penata Muda',
				'golongan' => 'III/a',
				'nip' => '199311202020121008',
				'full_name' => 'ENDANG SUBRATA,SE',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'endang_subrata@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Analis Keuangan ',
				'pangkat' => 'Penata Muda',
				'golongan' => 'III/a',
				'nip' => '199604012020122021',
				'full_name' => 'RIKE MONIKA FAUZIAH,SE',
				'jenis_kelamin' => 'Perempuan',
				'email' => 'rike_monika@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Analis Manajemen Perkantoran',
				'pangkat' => 'Penata Muda',
				'golongan' => 'III/a',
				'nip' => '199305112020122021',
				'full_name' => 'ANINDYA REZA MONICA,S.AP',
				'jenis_kelamin' => 'Perempuan',
				'email' => 'anindya_reza@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Analis Keuangan',
				'pangkat' => 'Penata Muda',
				'golongan' => 'III/a',
				'nip' => '199312032020121013',
				'full_name' => 'TRI OKA PUTRA,SE',
				'jenis_kelamin' => 'Perempuan',
				'email' => 'tri_oka@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Analis Pendidikan',
				'pangkat' => 'Penata Muda',
				'golongan' => 'III/a',
				'nip' => '199506122020122028',
				'full_name' => 'AISYIAH ANGGUN PURNAMA,S.Pd',
				'jenis_kelamin' => 'Perempuan',
				'email' => 'aisyiah_anggun@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			]
		];

		$staffData = [
			[
				'jabatan' => 'Staf Perencanaan, Evaluasi dan Pelaporan',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => null,
				'full_name' => 'OVIEN VALERIE, S.Kom',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'ovien@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Staf Perencanaan, Evaluasi dan Pelaporan',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => null,
				'full_name' => 'RIZKI SHANDRA H, S.Kom',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'rizki_shandra@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Staf Perencanaan, Evaluasi dan Pelaporan',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => null,
				'full_name' => 'FAUZIAH, S.Pd',
				'jenis_kelamin' => 'Perempuan',
				'email' => 'fauziah@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Staf Perencanaan, Evaluasi dan Pelaporan',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => null,
				'full_name' => 'ADIANTO, SH',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'adianto@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Staf Perencanaan, Evaluasi dan Pelaporan',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => null,
				'full_name' => 'DEBI PRADINATA, S.PdI',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'debi_pradinata@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Staf Keuangan dan BMD',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => null,
				'full_name' => 'FENSI KOSVETAL, S.Sy',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'fensi_kosvetal@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Staf Keuangan dan BMD',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => null,
				'full_name' => 'YASIR HABIBI, SE',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'yasir_habibi@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Staf Keuangan dan BMD',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => null,
				'full_name' => 'EFNIKO SUPRATAMA, S.Kom',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'efniko_supratama@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Staf Keuangan dan BMD',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => null,
				'full_name' => 'ELIZAR, A.Md',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'elizar@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Staf Keuangan dan BMD',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => null,
				'full_name' => 'EFROL DONI, S.PdI',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'efrol_doni@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Staf Umum dan Kepegawaian',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => null,
				'full_name' => 'LEO AMELZAL ZAMETA',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'leo_amelzal@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Staf Umum dan Kepegawaian',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => null,
				'full_name' => 'ATENG NADA SURYA, A.Md',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'ateng_nada@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Staf Umum dan Kepegawaian',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => null,
				'full_name' => 'SETIA PUTRI, SE',
				'jenis_kelamin' => 'Perempuan',
				'email' => 'setia_putri@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Staf Umum dan Kepegawaian',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => null,
				'full_name' => 'ILMAIDA,SPd',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'ilmaida@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Staf Umum dan Kepegawaian',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => null,
				'full_name' => 'MOH.THORIQ KUNNASIHIN',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'thoriq@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Staf Umum dan Kepegawaian',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => null,
				'full_name' => 'YOKO WAHYU,S.Sos',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'yoko_wahyu@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Staf Umum dan Kepegawaian',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => null,
				'full_name' => 'REVA KURNIA DEWI, SE',
				'jenis_kelamin' => 'Perempuan',
				'email' => 'reva_kurnia@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Staf Umum dan Kepegawaian',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => null,
				'full_name' => 'NILDA AFRIL YANSA, S.Hum.',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'nilda_afril@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Staf Umum dan Kepegawaian',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => null,
				'full_name' => 'NUAIMAN, S.Pd',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'nuaiman@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			]
		];

		$staffPaudPnf = [
			[
				'jabatan' => 'Staf Bidang PAUD dan PNF',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => null,
				'full_name' => 'HIKMAH FADILA, S.Pd ',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'hikmah_fadila@disdikkerinci.id',
				'pegawai_app' => '1',
					'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Staf Bidang PAUD dan PNF',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => null,
				'full_name' => 'SUSMILIA, A.Md',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'susmilia@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Staf Bidang PAUD dan PNF',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => null,
				'full_name' => 'REREN AGNES MALDA, S.Kom',
				'jenis_kelamin' => 'Perempuan',
				'email' => 'reren_agnes@disdikkerinci.id',
				'pegawai_app' => '1',
					'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Staf Bidang PAUD dan PNF',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => null,
				'full_name' => 'RIA ANGRAINI, S.Pd',
				'jenis_kelamin' => 'Perempuan',
				'email' => 'ria_angraini@disdikkerinci.id',
				'pegawai_app' => '1',
					'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Staf Bidang PAUD dan PNF',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => null, 
				'full_name' => 'DORIS, S.Pd',
				'jenis_kelamin' => 'Perempuan',
				'email' => 'doris@disdikkerinci.id',
				'pegawai_app' => '1',
					'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Staf Bidang PAUD dan PNF',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => null,
				'full_name' => 'OCKI HEIDY NOFRA, S.Pd',
				'jenis_kelamin' => 'Perempuan',
				'email' => 'ocki_heidy@disdikkerinci.id',
				'pegawai_app' => '1',
					'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Staf Bidang PAUD dan PNF',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => null,
				'full_name' => 'TIO SONGGA TAMAMILE,S,Ap',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'tio_songga@disdikkerinci.id',
				'pegawai_app' => '1',
					'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Staf Bidang PAUD dan PNF',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => null,
				'full_name' => 'TESMIZAR ALMI,SE',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'tesmizar@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Staf Bidang PAUD dan PNF',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => null,
				'full_name' => 'RISE AFRIANDI,SE',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'rise_afriandi@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Staf Bidang PAUD dan PNF',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => null,
				'full_name' => 'AYZELA PUPUTRI,S.Pd',
				'jenis_kelamin' => 'Perempuan',
				'email' => 'ayzela@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Staf Bidang PAUD dan PNF',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => null,
				'full_name' => 'ADYA PRAWIRA ASRIL, S.Kom.',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'adya_prawira@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			]
		];

		$staffSd = [
			[
				'jabatan' => 'Staf Bidang SD',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => null,
				'full_name' => 'ADIA MELAWATI, A.Md',
				'jenis_kelamin' => 'Perempuan',
				'email' => 'adia_melawati@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Staf Bidang SD',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => null,
				'full_name' => 'ELLA RAMADHANA, S.Pd',
				'jenis_kelamin' => 'Perempuan',
				'email' => 'ella_ramadhana@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Staf Bidang SD',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => null,
				'full_name' => 'NORA MATALANTHAU, S.Pd',
				'jenis_kelamin' => 'Perempuan',
				'email' => 'nora_matalanthau@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Staf Bidang SD',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => null,
				'full_name' => 'HENGKI PERNANDO, A.Md',
				'jenis_kelamin' => 'Perempuan',
				'email' => 'hengki_pernando@disdikkerinci.id',
				'pegawai_app' => '1',
					'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Staf Bidang SD',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => null,
				'full_name' => 'ANISA PEBRIANI,S.Pd',
				'jenis_kelamin' => 'Perempuan',
				'email' => 'anisa_pebriani@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Staf Bidang SD',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => null,
				'full_name' => 'WIWIN ARDIA,S.Sn',
				'jenis_kelamin' => 'Perempuan',
				'email' => 'wiwin_ardia@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Staf Bidang SD',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => null,
				'full_name' => 'DEPIAL KUNTARA, S.Sos',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'depial_kuntara@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Staf Bidang SD',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => null,
				'full_name' => 'ANIFRA, A.Md',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'anifra@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Staf Bidang SD',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => null,
				'full_name' => 'MERI SILVIA, A.Md',
				'jenis_kelamin' => 'Perempuan',
				'email' => 'meri_silvia@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Staf Bidang SD',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => null,
				'full_name' => 'ZULPIA EFENDI, S.PdI',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'zulpia_efendi@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Staf Bidang SD',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => null,
				'full_name' => 'ASENG YOPIN BESKA, S.E.',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'aseng_yopin@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			]
		];

		$staffSmp = [
			[
				'jabatan' => 'Staf Bidang SMP',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => null,
				'full_name' => 'DESI OKTARINA, S.Sy',
				'jenis_kelamin' => 'Perempuan',
				'email' => 'desi_oktarina@disdikkerinci.id',
				'pegawai_app' => '1',
					'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Staf Bidang SMP',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => null,
				'full_name' => 'CANDRA AFNOZA, S.Sy',
				'jenis_kelamin' => 'Perempuan',
				'email' => 'candra_afnoza@disdikkerinci.id',
				'pegawai_app' => '1',
					'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Staf Bidang SMP',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => null,
				'full_name' => 'REFNI, SE',
				'jenis_kelamin' => 'Perempuan',
				'email' => 'refni@disdikkerinci.id',
				'pegawai_app' => '1',
					'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Staf Bidang SMP',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => null,
				'full_name' => 'SYUFYANDI, SE',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'syufyandi@disdikkerinci.id',
				'pegawai_app' => '1',
					'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Staf Bidang SMP',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => null,
				'full_name' => 'EVAL TRANISA, A.Md',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'eval_tranisa@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Staf Bidang SMP',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => null,
				'full_name' => 'ROMI SATRIA, S.Sos',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'romi_satria@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Staf Bidang SMP',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => null,
				'full_name' => 'RAIMIKA TIARA, S.PdI',
				'jenis_kelamin' => 'Perempuan',
				'email' => 'raimika_tiara@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Staf Bidang SMP',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => null,
				'full_name' => 'FANEL MARTA ROZA,SE',
				'jenis_kelamin' => 'Perempuan',
				'email' => 'fanel_marta@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Staf Bidang SMP',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => null,
				'full_name' => 'HERI KURNIAWAN',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'heri_kurniawan@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Staf Bidang SMP',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => null,
				'full_name' => 'IMELDA SANTIA,SE',
				'jenis_kelamin' => 'Perempuan',
				'email' => 'imelda_santia@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Staf Bidang SMP',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => null,
				'full_name' => 'VINTIA GERI SYAFITRI, S.AP',
				'jenis_kelamin' => 'Perempuan',
				'email' => 'vintia@disdikkerinci.id',
				'pegawai_app' => '1',
					'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			]
		];

		$staffPTK = [
			[
				'jabatan' => 'Staf Bidang PTK',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => null,
				'full_name' => 'BENI YULIYANTO',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'beni_yuliyanto@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Staf Bidang PTK',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => null,
				'full_name' => 'DILA PRADINI, S.Pd',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'dila_pradini@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Staf Bidang PTK',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => null,
				'full_name' => 'DEPI HELMIYANTO, S.Sos',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'depi_helmiyanto@disdikkerinci.id',
				'pegawai_app' => '1',
					'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Staf Bidang PTK',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => null,
				'full_name' => 'KARLENA, A.Md',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'karlena@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Staf Bidang PTK',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => null,
				'full_name' => 'RESI NOPITA, S.Pd',
				'jenis_kelamin' => 'Perempuan',
				'email' => 'resi_nopita@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Staf Bidang PTK',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => null,
				'full_name' => 'FORDI SUSANTO, A.Md',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'fordi_susanto@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Staf Bidang PTK',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => null,
				'full_name' => 'BETA INDO PUTRA, S.Pd',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'beta_indo@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Staf Bidang PTK',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => null,
				'full_name' => 'NOLA ANGGELA,A.md',
				'jenis_kelamin' => 'Perempuan',
				'email' => 'nola_anggela@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Staf Bidang PTK',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => null,
				'full_name' => 'POFINSA ELANDA, A.Md',
				'jenis_kelamin' => 'Perempuan',
				'email' => 'pofinsa_elanda@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Staf Bidang PTK',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => null,
				'full_name' => 'DICKY MAHENDRA, S.Pd',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'dicky_mahendra@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Staf Bidang PTK',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => null,
				'full_name' => 'ROMIS LIADI,SE',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'romis_liadi@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Staf Bidang PTK',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => null,
				'full_name' => 'ANDROMICO,S.Pd',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'andromico@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			]
		];

		Pegawai::insert(
		[
			'jabatan' => 'admin aplikasi',
			'nip' => null,
			'full_name' => 'Super Admin',
			'jenis_kelamin' => 'Laki-laki',
			'email' => 'admin@disdikkerinci.id',
			'pegawai_app' => '1',
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		]);

		Pegawai::insert($dataPegawai);
		Pegawai::insert($staffData);
		Pegawai::insert($staffPaudPnf);
		Pegawai::insert($staffSd);
		Pegawai::insert($staffSmp);
		Pegawai::insert($staffPTK);

		// User
		sleep(3);

		$pegawai = Pegawai::where('pegawai_app', '1')->where('id', '>', 4)->where('email', '!=', 'ovien@disdikkerinci.id')->get();
		User::create([
			'email' => 'admin@disdikkerinci.id',
			'password' => bcrypt('admin'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		]);
		User::create(
		[
			'email' => 'ovien@disdikkerinci.id',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		]);

		// $temp = array();
		// foreach($pegawai as $peg) {
		// 	$data = array(
		// 		'email' => $peg->email,
		// 		'password' => bcrypt('password'),
		// 		'created_at' => now()->toDateTimeString(),
		// 		'created_by' => 1
		// 	);
		// 	array_push($temp, $data);
		// }

		// User::insert($temp);

	}
}
