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
        $email = config('admin.email');
        $password = config('admin.password');

        if (! $email || ! $password) {
            $this->command?->warn('Admin non créé : configurez ADMIN_EMAIL et ADMIN_PASSWORD dans l’environnement.');

            return;
        }

        $adminRole = Role::firstOrCreate(
            ['name' => 'administrateur'],
            ['description' => 'Contrôle total, valide les projets et rôles']
        );

        $admin = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => 'Administrateur',
                'telephone' => '0123456789',
                'password' => Hash::make($password),
                'status' => 'valide',
                'statut' => 'valide',
                'role_id' => $adminRole->id,
            ]
        );

        if ($admin->role?->id !== $adminRole->id || $admin->status !== 'valide' || $admin->statut !== 'valide') {
            $admin->update([
                'role_id' => $adminRole->id,
                'status' => 'valide',
                'statut' => 'valide',
            ]);
        }
    }
} 