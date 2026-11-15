<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_EMAIL');
        $password = env('ADMIN_PASSWORD');

        if (! $email || ! $password) {
            $this->command?->warn('Admin non créé : configurez ADMIN_EMAIL et ADMIN_PASSWORD dans l’environnement.');

            return;
        }

        $adminRole = Role::where('name', 'administrateur')->first();

        if ($adminRole) {
            $admin = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => 'Administrateur',
                    'telephone' => '0123456789',
                    'password' => Hash::make($password),
                    'statut' => 'valide',
                    'role_id' => $adminRole->id,
                ]
            );

            if (!$admin->role || $admin->role->id !== $adminRole->id) {
                $admin->update(['role_id' => $adminRole->id, 'statut' => 'valide']);
            }
        }
    }
} 