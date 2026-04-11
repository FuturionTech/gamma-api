<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_benefit_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_benefit_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['service_benefit_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_benefit_translations');
    }
};
