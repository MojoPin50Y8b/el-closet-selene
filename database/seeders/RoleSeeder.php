<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $cliente = Role::firstOrCreate(['name' => 'cliente', 'guard_name' => 'web']);

        // Asigna admin a un usuario existente por email
        $user = User::where('email', 'closetdeselene@gmail.com')->first();
        if ($user) {
            $user->assignRole($admin);
        }
    }
}
