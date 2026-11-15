<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->foreignId('artist_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('studio_manager_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('current_stage')->default('enregistrement');
            $table->date('start_date')->nullable();
            $table->date('expected_end_date')->nullable();
            $table->string('cover_art_url')->nullable();
            $table->string('isrc_code')->nullable();
            $table->string('upc_code')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['artist_id', 'studio_manager_id']);
            $table->index('current_stage');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
