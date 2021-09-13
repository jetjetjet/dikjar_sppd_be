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

		$user = User::find('1');
		$user2 = User::find('2');
		$user->assignRole('Super Admin');
		$user2->assignRole('Super Admin');
		$role_first = Role::first();

		$permissions = [
			[
				'name'       => 'dashboard',
				'guard_name' => 'sanctum',
				'action'     => ['view'],
			],
			[
				'name'       => 'setting',
				'guard_name' => 'sanctum',
				'action'     => ['view', 'add', 'edit', 'delete'],
			],
			[
				'name'       => 'role',
				'guard_name' => 'sanctum',
				'action'     => ['view', 'add', 'edit', 'delete'],
			],
			[
				'name'       => 'user',
				'guard_name' => 'sanctum',
				'action'     => ['view', 'add', 'edit', 'delete'],
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
