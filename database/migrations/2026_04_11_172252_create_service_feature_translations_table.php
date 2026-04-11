<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_feature_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_feature_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['service_feature_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_feature_translations');
    }
};
