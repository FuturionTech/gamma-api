<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_technologies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->string('icon')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
            $table->index(['service_id', 'order']);
        });

        Schema::create('service_technology_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_technology_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('name');
            $table->timestamps();
            $table->unique(['service_technology_id', 'locale'], 'st_trans_tech_locale_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_technology_translations');
        Schema::dropIfExists('service_technologies');
    }
};
