<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_approach_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->string('icon')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
            $table->index(['service_id', 'order']);
        });

        Schema::create('service_approach_step_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_approach_step_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->unique(['service_approach_step_id', 'locale'], 'sas_trans_step_locale_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_approach_step_translations');
        Schema::dropIfExists('service_approach_steps');
    }
};
