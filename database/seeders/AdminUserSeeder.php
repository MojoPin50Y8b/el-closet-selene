<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
// use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Crea (o toma) el usuario admin por defecto
        $user = User::firstOrCreate(
            ['email' => 'closetdeselene@gmail.com'],
            [
                'name' => 'Selene Admin',
                'password' => Hash::make('Admin123!'),
                'phone' => '2461414244',
                'is_active' => true,
            ]
        );

        // Asegura el rol admin (asumiendo que RoleSeeder ya lo creó)
        if (!$user->hasRole('admin')) {
            $user->assignRole('admin');
        }
    }
}
