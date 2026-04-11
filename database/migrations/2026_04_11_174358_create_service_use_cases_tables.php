<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_use_cases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->integer('order')->default(0);
            $table->timestamps();
            $table->index(['service_id', 'order']);
        });

        Schema::create('service_use_case_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_use_case_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 5);
            $table->text('text');
            $table->timestamps();
            $table->unique(['service_use_case_id', 'locale'], 'suc_trans_usecase_locale_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_use_case_translations');
        Schema::dropIfExists('service_use_cases');
    }
};
