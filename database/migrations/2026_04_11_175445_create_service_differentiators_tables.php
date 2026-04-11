<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_differentiators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->string('icon')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
            $table->index(['service_id', 'order']);
        });

        Schema::create('service_differentiator_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_differentiator_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->unique(['service_differentiator_id', 'locale'], 'sd_trans_diff_locale_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_differentiator_translations');
        Schema::dropIfExists('service_differentiators');
    }
};
