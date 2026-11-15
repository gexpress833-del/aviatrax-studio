<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class PermissionRoleSeeder extends Seeder
{
    /**
    * Seed roles, permissions metadata and default settings.
     */
    public function run(): void
    {
        Role::where('name', 'admin')->delete();

        $roles = [
            [
                'name' => 'administrateur',
                'description' => 'Contrôle total de la plateforme et de la distribution globale.',
                'permissions' => [
                    'manage_settings',
                    'manage_users',
                    'validate_artists',
                    'manage_projects',
                    'approve_distribution',
                    'manage_finances',
                    'manage_withdrawals',
                ],
            ],
            [
                'name' => 'secretariat',
                'description' => 'Validation des artistes et gestion administrative des dossiers.',
                'permissions' => [
                    'view_dashboard',
                    'manage_artists',
                    'validate_artist_profiles',
                    'view_project_reports',
                ],
            ],
            [
                'name' => 'responsable_studio',
                'description' => 'Suivi des projets musicaux et validation du workflow de production.',
                'permissions' => [
                    'manage_projects',
                    'upload_project_files',
                    'advance_project_stage',
                    'reply_to_comments',
                ],
            ],
            [
                'name' => 'artiste',
                'description' => 'Consultation des projets, commentaires, wallet et release hub.',
                'permissions' => [
                    'view_own_projects',
                    'comment_on_project',
                    'upload_work_files',
                    'view_wallet',
                    'request_withdrawal',
                ],
            ],
        ];

        foreach ($roles as $role) {
            $createdRole = Role::firstOrCreate(
                ['name' => $role['name']],
                [
                    'description' => $role['description'],
                ]
            );

            $createdRole->setAttribute('permissions_meta', $role['permissions']);
        }

        $defaultSettings = [
            [
                'key' => 'points_conversion_rate',
                'value' => [
                    'points_per_dollar' => 100,
                    'label' => '100 points = 1 USD',
                    'currency' => 'USD',
                ],
            ],
            [
                'key' => 'minimum_withdrawal_points',
                'value' => [
                    'threshold' => 5000,
                    'currency' => 'points',
                ],
            ],
            [
                'key' => 'currency_exchange_rate',
                'value' => [
                    'cdf_per_usd' => 0,
                    'base_currency' => 'USD',
                    'target_currency' => 'CDF',
                ],
            ],
            [
                'key' => 'default_platforms',
                'value' => [
                    'spotify',
                    'apple_music',
                    'boomplay',
                    'audiomack',
                    'youtube_music',
                    'deezer',
                    'tiktok',
                ],
            ],
            [
                'key' => 'studio_distribution_status',
                'value' => [
                    'pending' => 'en_attente',
                    'online' => 'en_ligne',
                ],
            ],
        ];

        foreach ($defaultSettings as $setting) {
            Setting::firstOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value']]
            );
        }

    }
}
