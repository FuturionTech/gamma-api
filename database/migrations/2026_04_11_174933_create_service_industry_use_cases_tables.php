<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_industry_use_cases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_industry_application_id')
                ->constrained('service_industry_applications')
                ->cascadeOnDelete();
            $table->integer('order')->default(0);
            $table->timestamps();
            $table->index(['service_industry_application_id', 'order'], 'siuc_app_order_idx');
        });

        Schema::create('service_industry_use_case_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_industry_use_case_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 5);
            $table->text('text');
            $table->timestamps();
            $table->unique(['service_industry_use_case_id', 'locale'], 'siuc_trans_usecase_locale_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_industry_use_case_translations');
        Schema::dropIfExists('service_industry_use_cases');
    }
};
