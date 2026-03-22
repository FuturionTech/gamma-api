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
        Schema::create('page_views', function (Blueprint $table) {
            $table->id();
            $table->string('session_id', 64);
            $table->string('path', 500);
            $table->string('referrer', 500)->nullable();
            $table->string('utm_source', 100)->nullable();
            $table->string('utm_medium', 100)->nullable();
            $table->string('utm_campaign', 100)->nullable();
            $table->string('device_type', 20)->nullable();
            $table->string('browser', 50)->nullable();
            $table->string('os', 50)->nullable();
            $table->integer('screen_width')->nullable();
            $table->string('language', 10)->nullable();
            $table->string('country', 2)->nullable();
            $table->string('city', 100)->nullable();
            $table->integer('duration_ms')->nullable();
            $table->boolean('is_bot')->default(false);
            $table->string('bot_name', 100)->nullable();
            $table->string('browser_version', 20)->nullable();
            $table->string('os_version', 20)->nullable();
            $table->string('device_brand', 50)->nullable();
            $table->string('device_model', 50)->nullable();
            $table->string('timezone', 50)->nullable();
            $table->integer('page_load_ms')->nullable();
            $table->string('connection_type', 20)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('session_id');
            $table->index('path');
            $table->index('created_at');
        });

        Schema::create('analytics_events', function (Blueprint $table) {
            $table->id();
            $table->string('session_id', 64);
            $table->string('event_name', 100);
            $table->jsonb('event_data')->nullable();
            $table->string('path', 500);
            $table->timestamp('created_at')->useCurrent();

            $table->index('session_id');
            $table->index('event_name');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analytics_events');
        Schema::dropIfExists('page_views');
    }
};
