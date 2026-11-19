<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->char('currency', 3)->default('USD');
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->string('status')->default('pending');
            $table->string('payment_reference')->unique();
            $table->string('order_number')->nullable()->unique();
            $table->string('provider_reference')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status', 'ends_at']);
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->string('project_type')->default('studio_production')->after('slug');
            $table->date('release_date')->nullable()->after('current_stage');
            $table->string('distribution_status')->default('pending')->after('release_date');
            $table->index(['project_type', 'release_date']);
        });

        Schema::table('projets', function (Blueprint $table) {
            $table->date('release_date')->nullable()->after('date_fin_prevue');
        });
    }

    public function down(): void
    {
        Schema::table('projets', function (Blueprint $table) {
            $table->dropColumn('release_date');
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->dropIndex(['project_type', 'release_date']);
            $table->dropColumn(['project_type', 'release_date', 'distribution_status']);
        });

        Schema::dropIfExists('subscriptions');
    }
};