<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Final cleanup: drops the JSON columns used by spatie/laravel-translatable
     * on services, service_features, and service_benefits. All content has
     * already been migrated to the astrotomic translation tables by the
     * preceding data migrations, and the model traits have been swapped
     * so nothing reads from these columns anymore.
     */
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['title', 'description', 'short_description']);
        });

        Schema::table('service_features', function (Blueprint $table) {
            $table->dropColumn(['title', 'description']);
        });

        Schema::table('service_benefits', function (Blueprint $table) {
            $table->dropColumn(['title', 'description']);
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->text('title')->nullable();
            $table->text('description')->nullable();
            $table->text('short_description')->nullable();
        });

        Schema::table('service_features', function (Blueprint $table) {
            $table->text('title')->nullable();
            $table->text('description')->nullable();
        });

        Schema::table('service_benefits', function (Blueprint $table) {
            $table->text('title')->nullable();
            $table->text('description')->nullable();
        });
    }
};
