<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Task 4 of Plan B — swap Service, ServiceFeature, and ServiceBenefit models
     * from spatie HasTranslations to astrotomic Translatable. Translation data
     * lives in the dedicated `*_translations` tables (populated in Tasks 1 and 3),
     * so the legacy JSON columns on the parent tables must no longer be required.
     * Task 5 will drop them entirely.
     */
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->text('title')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->text('title')->nullable(false)->change();
        });
    }
};
