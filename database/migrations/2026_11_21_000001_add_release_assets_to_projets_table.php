<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projets', function (Blueprint $table) {
            $table->string('cover_art_path')->nullable()->after('release_date');
            $table->string('cover_art_url')->nullable()->after('cover_art_path');
            $table->string('cover_art_nom_original')->nullable()->after('cover_art_url');
            $table->string('cover_art_public_id')->nullable()->after('cover_art_nom_original');
            $table->json('platform_links')->nullable()->after('cover_art_public_id');
            $table->string('distribution_status')->default('pending')->after('platform_links');
        });
    }

    public function down(): void
    {
        Schema::table('projets', function (Blueprint $table) {
            $table->dropColumn([
                'cover_art_path',
                'cover_art_url',
                'cover_art_nom_original',
                'cover_art_public_id',
                'platform_links',
                'distribution_status',
            ]);
        });
    }
};
