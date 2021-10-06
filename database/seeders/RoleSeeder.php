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
			'name'       => 'SUPER ADMIN',
			'guard_name' => 'sanctum',
			'created_at' => \Carbon\Carbon::now(),
		]);
		
		$role1 = Role::insert([
			'name'       => 'KADIN',
			'guard_name' => 'sanctum',
			'created_at' => \Carbon\Carbon::now(),
		]);
		
		$role2 = Role::insert([
			'name'       => 'SEKRETARIS',
			'guard_name' => 'sanctum',
			'created_at' => \Carbon\Carbon::now(),
		]);

		$role2 = Role::insert([
			'name'       => 'KASUBBAG',
			'guard_name' => 'sanctum',
			'created_at' => \Carbon\Carbon::now(),
		]);

		$role2 = Role::insert([
			'name'       => 'STAFF',
			'guard_name' => 'sanctum',
			'created_at' => \Carbon\Carbon::now(),
		]);

		$user = User::find('1');
		$user2 = User::find('2');
		$user->assignRole('SUPER ADMIN');
		$user2->assignRole('SUPER ADMIN');
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
			// [
			// 	'name'       => 'bidang',
			// 	'guard_name' => 'sanctum',
			// 	'action'     => ['view', 'add', 'edit', 'delete'],
			// ],
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
			[
				'name'       => 'jabatan',
				'guard_name' => 'sanctum',
				'action'     => ['view', 'add', 'edit', 'delete'],
			],
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
			// [
			// 	'name'       => 'wilayah',
			// 	'guard_name' => 'sanctum',
			// 	'action'     => ['view'],
			// ],
			[
				'name'       => 'spt',
				'guard_name' => 'sanctum',
				'action'     => ['view', 'add', 'edit', 'delete', 'finish', 'generate', 'generate_SPPD'],
			],
			[
				'name'       => 'sppd',
				'guard_name' => 'sanctum',
				'action'     => ['edit', 'delete', 'generate'],
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
