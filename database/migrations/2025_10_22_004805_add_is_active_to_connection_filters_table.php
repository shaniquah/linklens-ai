<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('connection_filters', function (Blueprint $table) {
            if (!Schema::hasColumn('connection_filters', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('criteria');
            }
        });
    }

    public function down(): void
    {
        Schema::table('connection_filters', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }
};