<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table): void {
            $table->renameColumn('friendly_name', 'translation_key');
        });
        Schema::table('plans', function (Blueprint $table): void {
            $table->renameColumn('description', 'description_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table): void {
            $table->renameColumn('description_key', 'description');
        });
        Schema::table('plans', function (Blueprint $table): void {
            $table->renameColumn('translation_key', 'friendly_name');
        });
    }
};
