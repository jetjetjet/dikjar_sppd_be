<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Role::truncate();
		$role = Role::insert([
			'name'       => 'Super Admin',
			'guard_name' => 'sanctum',
			'created_at' => \Carbon\Carbon::now(),
		]);
		
		$role1 = Role::insert([
			'name'       => 'KADIN',
			'guard_name' => 'sanctum',
			'created_at' => \Carbon\Carbon::now(),
		]);
		
		$role2 = Role::insert([
			'name'       => 'Sekretaris',
			'guard_name' => 'sanctum',
			'created_at' => \Carbon\Carbon::now(),
		]);

		$role2 = Role::insert([
			'name'       => 'Pegawai',
			'guard_name' => 'sanctum',
			'created_at' => \Carbon\Carbon::now(),
		]);

		$Sekretariat = Role::insert([
			'name'       => 'Staf Sekretariat',
			'guard_name' => 'sanctum',
			'created_at' => \Carbon\Carbon::now(),
		]);
		
		$paudpnf = Role::insert([
			'name'       => 'Staf Pembinaan PAUD dan PNF',
			'guard_name' => 'sanctum',
			'created_at' => \Carbon\Carbon::now(),
		]);

		$sd = Role::insert([
			'name'       => 'Staf Pembinaan Sekolah Dasar',
			'guard_name' => 'sanctum',
			'created_at' => \Carbon\Carbon::now(),
		]);

		$smp = Role::insert([
			'name'       => 'Staf Pembinaan SMP',
			'guard_name' => 'sanctum',
			'created_at' => \Carbon\Carbon::now(),
		]);
		
		$ptk = Role::insert([
			'name'       => 'Staf Pembinaan Pendidik dan Tenaga Kependidikan',
			'guard_name' => 'sanctum',
			'created_at' => \Carbon\Carbon::now(),
		]);

		$user = User::find(1);
		$user2 = User::find(2);
		$user5 = User::find(2);
		$user->assignRole('Super Admin');
		$user2->assignRole('Super Admin');
		$user5->assignRole('KADIN');
		$role_first = Role::first();

		$permissions = [
			[
				'name'       => 'dashboard',
				'guard_name' => 'sanctum',
				'action'     => ['view'],
			],
			[
				'name'       => 'anggaran',
				'guard_name' => 'sanctum',
				'action'     => ['view', 'add', 'edit', 'delete'],
			],
			[
				'name'       => 'pejabat',
				'guard_name' => 'sanctum',
				'action'     => ['view', 'add', 'edit', 'delete'],
			],
			[
				'name'       => 'peran',
				'guard_name' => 'sanctum',
				'action'     => ['view', 'add', 'edit', 'delete'],
			],
			// [
			// 	'name'       => 'jabatan',
			// 	'guard_name' => 'sanctum',
			// 	'action'     => ['view', 'add', 'edit', 'delete'],
			// ],
			[
				'name'       => 'setting',
				'guard_name' => 'sanctum',
				'action'     => ['view', 'add', 'edit', 'delete'],
			],
			[
				'name'       => 'pegawai',
				'guard_name' => 'sanctum',
				'action'     => ['view', 'add', 'edit', 'delete'],
			],
			[
				'name'       => 'spt',
				'guard_name' => 'sanctum',
				'action'     => ['view', 'add', 'edit', 'delete', 'finish', 'proses', 'generate', 'kwitansi'],
			],
			[
				'name'       => 'sppd',
				'guard_name' => 'sanctum',
				'action'     => ['edit', 'delete', ],
			],
			[
				'name'       => 'laporan',
				'guard_name' => 'sanctum',
				'action'     => ['view', 'anggaran', 'pegawai', 'tahunan', 'export'],
			]
		];

		Permission::truncate();
		foreach ($permissions as $row) {
			foreach ($row['action'] as $key => $val) {
				$temp = [
					'name'       => $row['name'].'-'.$val,
					'guard_name' => $row['guard_name']
				];	
				// create permission and assign to role
				$perms = Permission::create($temp);
				$role_first->givePermissionTo($perms);
			}
		}
	}
}
