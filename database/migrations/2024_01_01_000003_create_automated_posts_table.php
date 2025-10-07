<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('automated_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('content');
            $table->string('status')->default('pending'); // pending, posted, failed
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('posted_at')->nullable();
            $table->json('engagement_data')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('automated_posts');
    }
};