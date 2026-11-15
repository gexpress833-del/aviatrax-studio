<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestAccountsSeeder extends Seeder
{
    /**
     * Seed demo accounts for testing all user states and roles.
     */
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            return;
        }

        $roles = [
            'administrateur' => Role::where('name', 'administrateur')->first(),
            'secretariat' => Role::where('name', 'secretariat')->first(),
            'responsable_studio' => Role::where('name', 'responsable_studio')->first(),
            'artiste' => Role::where('name', 'artiste')->first(),
        ];

        $accounts = [
            [
                'name' => 'Admin Test',
                'email' => 'admin.test@aviatrax-studio.com',
                'telephone' => '0100000001',
                'password' => 'password',
                'statut' => 'valide',
                'role' => 'administrateur',
            ],
            [
                'name' => 'Secrétariat Test',
                'email' => 'secretariat.test@aviatrax-studio.com',
                'telephone' => '0100000002',
                'password' => 'password',
                'statut' => 'valide',
                'role' => 'secretariat',
            ],
            [
                'name' => 'Responsable Studio Test',
                'email' => 'responsable.test@aviatrax-studio.com',
                'telephone' => '0100000003',
                'password' => 'password',
                'statut' => 'valide',
                'role' => 'responsable_studio',
            ],
            [
                'name' => 'Artiste Test',
                'email' => 'artiste.test@aviatrax-studio.com',
                'telephone' => '0100000004',
                'password' => 'password',
                'statut' => 'valide',
                'role' => 'artiste',
            ],
            [
                'name' => 'Artiste en attente',
                'email' => 'artiste.attente@aviatrax-studio.com',
                'telephone' => '0100000005',
                'password' => 'password',
                'statut' => 'en_attente_validation',
                'role' => 'artiste',
            ],
            [
                'name' => 'Artiste suspendu',
                'email' => 'artiste.suspendu@aviatrax-studio.com',
                'telephone' => '0100000006',
                'password' => 'password',
                'statut' => 'suspendu',
                'role' => 'artiste',
            ],
        ];

        foreach ($accounts as $account) {
            $role = $roles[$account['role']] ?? null;

            if (! $role) {
                continue;
            }

            $user = User::firstOrCreate(
                ['email' => $account['email']],
                [
                    'name' => $account['name'],
                    'telephone' => $account['telephone'],
                    'password' => Hash::make($account['password']),
                    'statut' => $account['statut'],
                    'role_id' => $role->id,
                ]
            );

            $user->update([
                'name' => $account['name'],
                'telephone' => $account['telephone'],
                'password' => Hash::make($account['password']),
                'statut' => $account['statut'],
                'role_id' => $role->id,
            ]);
        }
    }
}
