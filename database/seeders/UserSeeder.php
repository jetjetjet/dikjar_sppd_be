<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		User::truncate();
		User::create([
			'nip' => '12345678',
			'full_name' => 'Super Admin',
			'jenis_kelamin' => 'Laki-laki',
			'password' => bcrypt('admin'),
			'email' => 'admin@disdikkerinci.id',
			'full_name' => 'admin',
			'created_at' => date('Y-m-d H:i:s'),
			'created_by' => 0
		]);

		
		User::create([
			'nip' => '1234567890',
			'full_name' => 'Super Admin1',
			'jenis_kelamin' => 'Laki-laki',
			'password' => bcrypt('admin'),
			'email' => 'admin1@disdikkerinci.id',
			'full_name' => 'admin ss',
			'created_at' => date('Y-m-d H:i:s'),
			'created_by' => 0
		]);
	}
}
