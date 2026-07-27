<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['student', 'faculty'] as $description) {
            Role::firstOrCreate(['description' => $description]);
        }
    }
}
