<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $artistRoleId = DB::table('roles')->where('name', 'artiste')->value('id');

        if (! $artistRoleId) {
            return;
        }

        DB::table('users')
            ->where('role_id', $artistRoleId)
            ->where(function ($query) {
                $query->where('status', 'en_attente_validation')
                    ->orWhere('statut', 'en_attente_validation');
            })
            ->update([
                'status' => 'valide',
                'statut' => 'valide',
            ]);
    }

    public function down(): void
    {
        // Les comptes activés automatiquement ne peuvent pas être distingués des comptes validés auparavant.
    }
};
