<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('linkedin_profiles', function (Blueprint $table) {
            $table->text('access_token')->change();
            $table->text('refresh_token')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('linkedin_profiles', function (Blueprint $table) {
            $table->string('access_token')->change();
            $table->string('refresh_token')->nullable()->change();
        });
    }
};