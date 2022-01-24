<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DaerahDalamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            [
				'name'       => 'jenis_transport',
				'guard_name' => 'sanctum',
				'action'     => ['view', 'add', 'edit', 'delete'],
			],
			[
				'name'       => 'kategori_pengeluaran',
				'guard_name' => 'sanctum',
				'action'     => ['view', 'add', 'edit', 'delete'],
			],
			[
				'name'       => 'satuan',
				'guard_name' => 'sanctum',
				'action'     => ['view', 'add', 'edit', 'delete'],
			]
        ];

        $role_first = Role::first();
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
