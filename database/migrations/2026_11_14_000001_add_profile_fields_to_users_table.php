<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('stage_name')->nullable()->after('name');
            $table->string('phone')->nullable()->after('email');
            $table->string('avatar_url')->nullable()->after('password');
            $table->string('status')->default('en_attente_validation')->after('password');
            $table->string('role_name')->nullable()->after('status');
            $table->index(['status', 'role_id']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['status', 'role_id']);
            $table->dropColumn(['stage_name', 'phone', 'avatar_url', 'status', 'role_name']);
        });
    }
};
