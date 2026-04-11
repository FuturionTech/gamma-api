<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->string('icon')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->index(['service_id', 'order']);
        });

        Schema::create('service_stat_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_stat_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('value', 100);
            $table->string('label', 255);
            $table->timestamps();

            $table->unique(['service_stat_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_stat_translations');
        Schema::dropIfExists('service_stats');
    }
};
