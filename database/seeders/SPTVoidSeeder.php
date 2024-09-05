<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SPTVoidSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // create permission and assign to role
		$role_first = Role::first();
        $perms = Permission::create([
            'name'       => 'spt-void',
            'guard_name' => 'sanctum'
        ]);
        $role_first->givePermissionTo($perms);
    }
}
