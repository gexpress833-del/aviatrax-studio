<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['en_attente_validation', 'valide', 'suspendu'] as $status) {
            DB::table('users')
                ->where('statut', $status)
                ->update(['status' => $status]);
        }
    }

    public function down(): void
    {
    }
};