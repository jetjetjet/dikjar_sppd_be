<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = 'admin@disdikkerinci.id';
        $newPassword = 'admin';

        $updated = DB::table('users')
            ->where('email', $email)
            ->whereNull('deleted_at')
            ->update(['password' => Hash::make($newPassword)]);

        if ($updated) {
            $this->command->info("[AdminSeeder] Password '$email' berhasil direset.");
        } else {
            $this->command->warn("[AdminSeeder] User '$email' tidak ditemukan — skip.");
        }
    }
}
