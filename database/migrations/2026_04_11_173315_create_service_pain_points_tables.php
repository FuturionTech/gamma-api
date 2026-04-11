<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_pain_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->index(['service_id', 'order']);
        });

        Schema::create('service_pain_point_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_pain_point_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 5);
            $table->text('text');
            $table->timestamps();

            $table->unique(['service_pain_point_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_pain_point_translations');
        Schema::dropIfExists('service_pain_points');
    }
};
