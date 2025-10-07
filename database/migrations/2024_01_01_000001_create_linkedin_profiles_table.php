<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('linkedin_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('linkedin_id')->unique();
            $table->string('access_token')->nullable();
            $table->string('refresh_token')->nullable();
            $table->timestamp('token_expires_at')->nullable();
            $table->json('profile_data')->nullable();
            $table->boolean('auto_accept_connections')->default(true);
            $table->boolean('post_automation_enabled')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('linkedin_profiles');
    }
};