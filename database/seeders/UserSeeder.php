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
				'nip' => '00000000',
				'pegawai_app' => '0',
				'full_name' => 'Dr. H. Adirozal M.Si.',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'dummy@disdikkerinci.id',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Wakil Bupati Kerinci',
				'pangkat' => null,
				'golongan' => null,
				'nip' => '00000001',
				'full_name' => 'Ir. H. Ami Taher',
				'pegawai_app' => '0',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'dummy@disdikkerinci.id',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Sekretaris Daerah Kerinci',
				'nip' => '00000002',
				'pangkat' => null,
				'golongan' => null,
				'full_name' => 'nama_sekda',
				'jenis_kelamin' => 'Laki-laki',
				'pegawai_app' => '0',
				'email' => 'dummy@disdikkerinci.id',
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
				'email' => 'dummy@disdikkerinci.id',
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
				'email' => 'dummy@disdikkerinci.id',
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
				'email' => 'dummy@disdikkerinci.id',
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
				'email' => 'dummy@disdikkerinci.id',
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
				'email' => 'dummy@disdikkerinci.id',
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
				'email' => 'dummy@disdikkerinci.id',
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
				'email' => 'dummy@disdikkerinci.id',
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
				'email' => 'dummy@disdikkerinci.id',
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
				'email' => 'dummy@disdikkerinci.id',
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
				'email' => 'dummy@disdikkerinci.id',
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
				'email' => 'dummy@disdikkerinci.id',
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
				'email' => 'dummy@disdikkerinci.id',
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
				'email' => 'dummy@disdikkerinci.id',
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
				'email' => 'dummy@disdikkerinci.id',
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
				'email' => 'dummy@disdikkerinci.id',
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
				'email' => 'dummy@disdikkerinci.id',
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
				'email' => 'dummy@disdikkerinci.id',
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
				'email' => 'dummy@disdikkerinci.id',
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
				'email' => 'dummy@disdikkerinci.id',
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
				'email' => 'dummy@disdikkerinci.id',
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
				'email' => 'dummy@disdikkerinci.id',
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
				'email' => 'dummy@disdikkerinci.id',
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
				'email' => 'dummy@disdikkerinci.id',
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
				'email' => 'dummy@disdikkerinci.id',
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
				'email' => 'dummy@disdikkerinci.id',
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
				'email' => 'dummy@disdikkerinci.id',
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
				'email' => 'dummy@disdikkerinci.id',
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
				'email' => 'dummy@disdikkerinci.id',
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
				'email' => 'dummy@disdikkerinci.id',
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
				'email' => 'dummy@disdikkerinci.id',
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
				'email' => 'dummy@disdikkerinci.id',
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
				'email' => 'dummy@disdikkerinci.id',
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
				'email' => 'dummy@disdikkerinci.id',
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
				'email' => 'dummy@disdikkerinci.id',
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
				'email' => 'dummy@disdikkerinci.id',
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
				'nip' => '910120140006',
				'full_name' => 'OVIEN VALERIE, S.Kom',
				'jenis_kelamin' => 'Perempuan',
				'email' => 'dummy@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Staf Perencanaan, Evaluasi dan Pelaporan',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => '910120180012',
				'full_name' => 'FAUZIAH, S.Pd',
				'jenis_kelamin' => 'Perempuan',
				'email' => 'dummy@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Staf Perencanaan, Evaluasi dan Pelaporan',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => '9101201600021',
				'full_name' => 'ADIANTO, SH',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'dummy@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Staf Perencanaan, Evaluasi dan Pelaporan',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => '910120160008',
				'full_name' => 'DEBI PRADINATA, S.PdI',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'dummy@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Staf Keuangan dan BMD',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => '910120150001',
				'full_name' => 'FENSI KOSVETAL, S.Sy',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'dummy@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Staf Keuangan dan BMD',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => '910120120006',
				'full_name' => 'YASIR HABIBI, SE',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'dummy@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Staf Keuangan dan BMD',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => '910120130004',
				'full_name' => 'EFNIKO SUPRATAMA, S.Kom',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'dummy@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Staf Keuangan dan BMD',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => '910120140015',
				'full_name' => 'ELIZAR, A.Md',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'dummy@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Staf Keuangan dan BMD',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => '910120141000',
				'full_name' => 'EFROL DONI, S.PdI',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'dummy@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Staf Umum dan Kepegawaian',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => '910120181033',
				'full_name' => 'LEO AMELZAL ZAMETA',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'dummy@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Staf Umum dan Kepegawaian',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => '910120120003',
				'full_name' => 'ATENG NADA SURYA, A.Md',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'dummy@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Staf Umum dan Kepegawaian',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => '910120200008',
				'full_name' => 'SETIA PUTRI, SE',
				'jenis_kelamin' => 'Perempuan',
				'email' => 'dummy@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Staf Umum dan Kepegawaian',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => '910120200010',
				'full_name' => 'ILMAIDA,SPd',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'dummy@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Staf Umum dan Kepegawaian',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => '910120200009',
				'full_name' => 'MOH.THORIQ KUNNASIHIN',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'dummy@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Staf Umum dan Kepegawaian',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => '910120120012',
				'full_name' => 'YOKO WAHYU,S.Sos',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'dummy@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Staf Umum dan Kepegawaian',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => '9101201500311',
				'full_name' => 'REVA KURNIA DEWI, SE',
				'jenis_kelamin' => 'Perempuan',
				'email' => 'dummy@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Staf Umum dan Kepegawaian',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => '910120210001',
				'full_name' => 'NILDA AFRIL YANSA, S.Hum.',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'dummy@disdikkerinci.id',
				'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
				'created_by' => 1
			],
			[
				'jabatan' => 'Staf Umum dan Kepegawaian',
				'pangkat' => '-',
				'golongan' => '-',
				'nip' => '910120210003',
				'full_name' => 'NUAIMAN, S.Pd',
				'jenis_kelamin' => 'Laki-laki',
				'email' => 'dummy@disdikkerinci.id',
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
			'nip' => '910120120002',
			'full_name' => 'HIKMAH FADILA, S.Pd ',
			'jenis_kelamin' => 'Laki-laki',
			'email' => 'dummy@disdikkerinci.id',
			'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'jabatan' => 'Staf Bidang PAUD dan PNF',
			'pangkat' => '-',
			'golongan' => '-',
			'nip' => '9101201200251',
			'full_name' => 'SUSMILIA, A.Md',
			'jenis_kelamin' => 'Laki-laki',
			'email' => 'dummy@disdikkerinci.id',
			'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'jabatan' => 'Staf Bidang PAUD dan PNF',
			'pangkat' => '-',
			'golongan' => '-',
			'nip' => '910120140013',
			'full_name' => 'REREN AGNES MALDA, S.Kom',
			'jenis_kelamin' => 'Perempuan',
			'email' => 'dummy@disdikkerinci.id',
			'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'jabatan' => 'Staf Bidang PAUD dan PNF',
			'pangkat' => '-',
			'golongan' => '-',
			'nip' => '910120140001',
			'full_name' => 'RIA ANGRAINI, S.Pd',
			'jenis_kelamin' => 'Perempuan',
			'email' => 'dummy@disdikkerinci.id',
			'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'jabatan' => 'Staf Bidang PAUD dan PNF',
			'pangkat' => '-',
			'golongan' => '-',
			'nip' => '910120160002', 
			'full_name' => 'DORIS, S.Pd',
			'jenis_kelamin' => 'Perempuan',
			'email' => 'dummy@disdikkerinci.id',
			'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'jabatan' => 'Staf Bidang PAUD dan PNF',
			'pangkat' => '-',
			'golongan' => '-',
			'nip' => '910120160007',
			'full_name' => 'OCKI HEIDY NOFRA, S.Pd',
			'jenis_kelamin' => 'Perempuan',
			'email' => 'dummy@disdikkerinci.id',
			'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'jabatan' => 'Staf Bidang PAUD dan PNF',
			'pangkat' => '-',
			'golongan' => '-',
			'nip' => '910120190004',
			'full_name' => 'TIO SONGGA TAMAMILE,S,Ap',
			'jenis_kelamin' => 'Laki-laki',
			'email' => 'dummy@disdikkerinci.id',
			'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'jabatan' => 'Staf Bidang PAUD dan PNF',
			'pangkat' => '-',
			'golongan' => '-',
			'nip' => '910120171003',
			'full_name' => 'TESMIZAR ALMI,SE',
			'jenis_kelamin' => 'Laki-laki',
			'email' => 'dummy@disdikkerinci.id',
			'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'jabatan' => 'Staf Bidang PAUD dan PNF',
			'pangkat' => '-',
			'golongan' => '-',
			'nip' => '910120200003',
			'full_name' => 'RISE AFRIANDI,SE',
			'jenis_kelamin' => 'Laki-laki',
			'email' => 'dummy@disdikkerinci.id',
			'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'jabatan' => 'Staf Bidang PAUD dan PNF',
			'pangkat' => '-',
			'golongan' => '-',
			'nip' => '910120200013',
			'full_name' => 'AYZELA PUPUTRI,S.Pd',
			'jenis_kelamin' => 'Perempuan',
			'email' => 'dummy@disdikkerinci.id',
			'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'jabatan' => 'Staf Bidang PAUD dan PNF',
			'pangkat' => '-',
			'golongan' => '-',
			'nip' => '910120210002',
			'full_name' => 'ADYA PRAWIRA ASRIL, S.Kom.',
			'jenis_kelamin' => 'Laki-laki',
			'email' => 'dummy@disdikkerinci.id',
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
			'nip' => '910120120010',
			'full_name' => 'ADIA MELAWATI, A.Md',
			'jenis_kelamin' => 'Perempuan',
			'email' => 'dummy@disdikkerinci.id',
			'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'jabatan' => 'Staf Bidang SD',
			'pangkat' => '-',
			'golongan' => '-',
			'nip' => '910120150029',
			'full_name' => 'ELLA RAMADHANA, S.Pd',
			'jenis_kelamin' => 'Perempuan',
			'email' => 'dummy@disdikkerinci.id',
			'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'jabatan' => 'Staf Bidang SD',
			'pangkat' => '-',
			'golongan' => '-',
			'nip' => '910120120017',
			'full_name' => 'NORA MATALANTHAU, S.Pd',
			'jenis_kelamin' => 'Perempuan',
			'email' => 'dummy@disdikkerinci.id',
			'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'jabatan' => 'Staf Bidang SD',
			'pangkat' => '-',
			'golongan' => '-',
			'nip' => '910120171005',
			'full_name' => 'HENGKI PERNANDO, A.Md',
			'jenis_kelamin' => 'Perempuan',
			'email' => 'dummy@disdikkerinci.id',
			'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'jabatan' => 'Staf Bidang SD',
			'pangkat' => '-',
			'golongan' => '-',
			'nip' => '910120200012',
			'full_name' => 'ANISA PEBRIANI,S.Pd',
			'jenis_kelamin' => 'Perempuan',
			'email' => 'dummy@disdikkerinci.id',
			'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'jabatan' => 'Staf Bidang SD',
			'pangkat' => '-',
			'golongan' => '-',
			'nip' => '910120190003',
			'full_name' => 'WIWIN ARDIA,S.Sn',
			'jenis_kelamin' => 'Perempuan',
			'email' => 'dummy@disdikkerinci.id',
			'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'jabatan' => 'Staf Bidang SD',
			'pangkat' => '-',
			'golongan' => '-',
			'nip' => '910120140019',
			'full_name' => 'DEPIAL KUNTARA, S.Sos',
			'jenis_kelamin' => 'Laki-laki',
			'email' => 'dummy@disdikkerinci.id',
			'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'jabatan' => 'Staf Bidang SD',
			'pangkat' => '-',
			'golongan' => '-',
			'nip' => '910120130001',
			'full_name' => 'ANIFRA, A.Md',
			'jenis_kelamin' => 'Laki-laki',
			'email' => 'dummy@disdikkerinci.id',
			'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'jabatan' => 'Staf Bidang SD',
			'pangkat' => '-',
			'golongan' => '-',
			'nip' => '910120120018',
			'full_name' => 'MERI SILVIA, A.Md',
			'jenis_kelamin' => 'Perempuan',
			'email' => 'dummy@disdikkerinci.id',
			'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'jabatan' => 'Staf Bidang SD',
			'pangkat' => '-',
			'golongan' => '-',
			'nip' => '910120140009',
			'full_name' => 'ZULPIA EFENDI, S.PdI',
			'jenis_kelamin' => 'Laki-laki',
			'email' => 'dummy@disdikkerinci.id',
			'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'jabatan' => 'Staf Bidang SD',
			'pangkat' => '-',
			'golongan' => '-',
			'nip' => '910120210004',
			'full_name' => 'ASENG YOPIN BESKA, S.E.',
			'jenis_kelamin' => 'Laki-laki',
			'email' => 'dummy@disdikkerinci.id',
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
			'nip' => '910120140008',
			'full_name' => 'DESI OKTARINA, S.Sy',
			'jenis_kelamin' => 'Perempuan',
			'email' => 'dummy@disdikkerinci.id',
			'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'jabatan' => 'Staf Bidang SMP',
			'pangkat' => '-',
			'golongan' => '-',
			'nip' => '910120140026',
			'full_name' => 'CANDRA AFNOZA, S.Sy',
			'jenis_kelamin' => 'Perempuan',
			'email' => 'dummy@disdikkerinci.id',
			'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'jabatan' => 'Staf Bidang SMP',
			'pangkat' => '-',
			'golongan' => '-',
			'nip' => '910120120020',
			'full_name' => 'REFNI, SE',
			'jenis_kelamin' => 'Perempuan',
			'email' => 'dummy@disdikkerinci.id',
			'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'jabatan' => 'Staf Bidang SMP',
			'pangkat' => '-',
			'golongan' => '-',
			'nip' => '910120120026',
			'full_name' => 'SYUFYANDI, SE',
			'jenis_kelamin' => 'Laki-laki',
			'email' => 'dummy@disdikkerinci.id',
			'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'jabatan' => 'Staf Bidang SMP',
			'pangkat' => '-',
			'golongan' => '-',
			'nip' => '910120120028',
			'full_name' => 'EVAL TRANISA, A.Md',
			'jenis_kelamin' => 'Laki-laki',
			'email' => 'dummy@disdikkerinci.id',
			'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'jabatan' => 'Staf Bidang SMP',
			'pangkat' => '-',
			'golongan' => '-',
			'nip' => '910120171007',
			'full_name' => 'ROMI SATRIA, S.Sos',
			'jenis_kelamin' => 'Laki-laki',
			'email' => 'dummy@disdikkerinci.id',
			'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'jabatan' => 'Staf Bidang SMP',
			'pangkat' => '-',
			'golongan' => '-',
			'nip' => '910120150031',
			'full_name' => 'RAIMIKA TIARA, S.PdI',
			'jenis_kelamin' => 'Perempuan',
			'email' => 'dummy@disdikkerinci.id',
			'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'jabatan' => 'Staf Bidang SMP',
			'pangkat' => '-',
			'golongan' => '-',
			'nip' => '910120200002',
			'full_name' => 'FANEL MARTA ROZA,SE',
			'jenis_kelamin' => 'Perempuan',
			'email' => 'dummy@disdikkerinci.id',
			'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'jabatan' => 'Staf Bidang SMP',
			'pangkat' => '-',
			'golongan' => '-',
			'nip' => '910120120029',
			'full_name' => 'HERI KURNIAWAN',
			'jenis_kelamin' => 'Laki-laki',
			'email' => 'dummy@disdikkerinci.id',
			'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'jabatan' => 'Staf Bidang SMP',
			'pangkat' => '-',
			'golongan' => '-',
			'nip' => '910120200006',
			'full_name' => 'IMELDA SANTIA,SE',
			'jenis_kelamin' => 'Perempuan',
			'email' => 'dummy@disdikkerinci.id',
			'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'jabatan' => 'Staf Bidang SMP',
			'pangkat' => '-',
			'golongan' => '-',
			'nip' => '910120210005',
			'full_name' => 'VINTIA GERI SYAFITRI, S.AP',
			'jenis_kelamin' => 'Perempuan',
			'email' => 'dummy@disdikkerinci.id',
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
			'nip' => '910120120001',
			'full_name' => 'BENI YULIYANTO',
			'jenis_kelamin' => 'Laki-laki',
			'email' => 'dummy@disdikkerinci.id',
			'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'jabatan' => 'Staf Bidang PTK',
			'pangkat' => '-',
			'golongan' => '-',
			'nip' => '910120150032',
			'full_name' => 'DILA PRADINI, S.Pd',
			'jenis_kelamin' => 'Laki-laki',
			'email' => 'dummy@disdikkerinci.id',
			'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'jabatan' => 'Staf Bidang PTK',
			'pangkat' => '-',
			'golongan' => '-',
			'nip' => '910120120025',
			'full_name' => 'DEPI HELMIYANTO, S.Sos',
			'jenis_kelamin' => 'Laki-laki',
			'email' => 'dummy@disdikkerinci.id',
			'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'jabatan' => 'Staf Bidang PTK',
			'pangkat' => '-',
			'golongan' => '-',
			'nip' => '910120141001',
			'full_name' => 'KARLENA, A.Md',
			'jenis_kelamin' => 'Laki-laki',
			'email' => 'dummy@disdikkerinci.id',
			'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'jabatan' => 'Staf Bidang PTK',
			'pangkat' => '-',
			'golongan' => '-',
			'nip' => '9101201500111',
			'full_name' => 'RESI NOPITA, S.Pd',
			'jenis_kelamin' => 'Perempuan',
			'email' => 'dummy@disdikkerinci.id',
			'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'jabatan' => 'Staf Bidang PTK',
			'pangkat' => '-',
			'golongan' => '-',
			'nip' => '910120140012',
			'full_name' => 'FORDI SUSANTO, A.Md',
			'jenis_kelamin' => 'Laki-laki',
			'email' => 'dummy@disdikkerinci.id',
			'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'jabatan' => 'Staf Bidang PTK',
			'pangkat' => '-',
			'golongan' => '-',
			'nip' => '910120190002',
			'full_name' => 'BETA INDO PUTRA, S.Pd',
			'jenis_kelamin' => 'Laki-laki',
			'email' => 'dummy@disdikkerinci.id',
			'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'jabatan' => 'Staf Bidang PTK',
			'pangkat' => '-',
			'golongan' => '-',
			'nip' => '910120130150',
			'full_name' => 'NOLA ANGGELA,A.md',
			'jenis_kelamin' => 'Perempuan',
			'email' => 'dummy@disdikkerinci.id',
			'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'jabatan' => 'Staf Bidang PTK',
			'pangkat' => '-',
			'golongan' => '-',
			'nip' => '910120180015',
			'full_name' => 'POFINSA ELANDA, A.Md',
			'jenis_kelamin' => 'Perempuan',
			'email' => 'dummy@disdikkerinci.id',
			'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'jabatan' => 'Staf Bidang PTK',
			'pangkat' => '-',
			'golongan' => '-',
			'nip' => '910120200001',
			'full_name' => 'DICKY MAHENDRA, S.Pd',
			'jenis_kelamin' => 'Laki-laki',
			'email' => 'dummy@disdikkerinci.id',
			'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'jabatan' => 'Staf Bidang PTK',
			'pangkat' => '-',
			'golongan' => '-',
			'nip' => '910120200004',
			'full_name' => 'ROMIS LIADI,SE',
			'jenis_kelamin' => 'Laki-laki',
			'email' => 'dummy@disdikkerinci.id',
			'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'jabatan' => 'Staf Bidang PTK',
			'pangkat' => '-',
			'golongan' => '-',
			'nip' => '910120200007',
			'full_name' => 'ANDROMICO,S.Pd',
			'jenis_kelamin' => 'Laki-laki',
			'email' => 'dummy@disdikkerinci.id',
			'pegawai_app' => '1',
				'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		]
	];

	Pegawai::insert(
		[
			'jabatan' => 'admin aplikasi',
			'nip' => '12345678',
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

	$userPegawai = [
		[
			'nip' => '196505291990031007',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '196409161986021001',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '198112042006041010',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '197907092006041015',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '196506171986022002',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '196912171998031004',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '196710061995121002',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '197507312014081001',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '198006022010011005',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '198005252009012007',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '197704242012121001',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '198909292011011003',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '196507081992031008',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '197012151990071002',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '198204292007011004',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '197501112010011010',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '196404041989021002',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '198509112008031001',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '197307092011012001',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '196905102012121002',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '197706192009031003',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '196501072005021002',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '197407042007011005',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '196709292012121001',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '196701092012121001',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '198906172010012002',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '197512312014081001',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '196810102012122003',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '197705252012122001',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '198607022014081002',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '199311202020121008',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '199604012020122021',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '199305112020122021',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '199312032020121013',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '199506122020122028',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		]
	];

	$userSekretariat = [
		[
			'nip' => '910120180012',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '9101201600021',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '910120160008',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '910120150001',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '910120120006',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '910120130004',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '910120140015',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '910120141000',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '910120181033',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '910120120003',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '910120200008',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '910120200010',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '910120200009',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '910120120012',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '9101201500311',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '910120210001',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '910120210003',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		]
	];

	$stafPaud = [
		[
			'nip' => '910120120002',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '9101201200251',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '910120140013',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '910120140001',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '910120160002',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '910120160007',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '910120190004',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '910120171003',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '910120200003',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '910120200013',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '910120210002',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '910120200007',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		]
	];

	$stafSD = [
		[
			'nip' => '910120120010',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '910120150029',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '910120120017',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '910120171005',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '910120200012',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '910120190003',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '910120140019',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '910120130001',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '910120120018',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '910120140009',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '910120210004',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		]
	];

	$stafSmp= [
		[
			'nip' => '910120140008',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '910120120020',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '910120120026',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '910120120028',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '910120171007',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '910120150031',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '910120200002',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '910120120029',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '910120200006',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '910120210005',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		]
	];

	$userStafPTK = [
		[
			'nip' => '910120120001',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '910120150032',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '910120120025',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '910120141001',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '9101201500111',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '910120140012',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '910120190002',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '910120130150',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '910120180015',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '910120200001',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '910120200004',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		],
		[
			'nip' => '910120200007',
			'password' => bcrypt('password'),
			'created_at' => now()->toDateTimeString(),
			'created_by' => 1
		]
	];
	
	User::create([
		'nip' => '12345678',
		'password' => bcrypt('admin'),
		'created_at' => now()->toDateTimeString(),
		'created_by' => 1
	]);
	User::create(
	[
		'nip' => '910120140006',
		'password' => bcrypt('password'),
		'created_at' => now()->toDateTimeString(),
		'created_by' => 1
	]);
	User::insert($userPegawai);
	User::insert($userSekretariat);
	User::insert($stafPaud);
	User::insert($stafSD);
	User::insert($stafSmp);
	User::insert($userStafPTK);

	}
}
