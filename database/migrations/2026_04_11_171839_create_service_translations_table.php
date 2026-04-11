<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 5);

            // Card / page fields
            $table->string('title');
            $table->string('short_description', 500)->nullable();
            $table->text('description')->nullable();

            // SEO meta
            $table->string('meta_title')->nullable();
            $table->string('meta_description', 500)->nullable();
            $table->string('meta_keywords', 500)->nullable();

            // Hero section
            $table->string('hero_tagline')->nullable();
            $table->string('hero_headline', 500)->nullable();
            $table->text('hero_subheadline')->nullable();
            $table->string('hero_cta_primary_label', 100)->nullable();
            $table->string('hero_cta_secondary_label', 100)->nullable();

            // Challenge section
            $table->string('challenge_title')->nullable();
            $table->text('challenge_description')->nullable();

            // How-we-deliver section
            $table->string('delivery_title')->nullable();
            $table->text('delivery_description')->nullable();

            // Capabilities section (title only; groups are a separate table)
            $table->string('capabilities_title')->nullable();

            // Key use cases section
            $table->string('use_cases_title')->nullable();
            $table->text('use_cases_description')->nullable();

            // Our approach section
            $table->string('approach_title')->nullable();
            $table->text('approach_description')->nullable();

            // Industry applications section
            $table->string('industry_title')->nullable();
            $table->text('industry_description')->nullable();

            // Technologies section
            $table->string('technologies_title')->nullable();
            $table->text('technologies_description')->nullable();

            // Business impact section
            $table->string('business_impact_title')->nullable();
            $table->text('business_impact_description')->nullable();

            // Differentiators section (title only; points are a separate table)
            $table->string('differentiators_title')->nullable();

            // Closing CTA
            $table->string('closing_title')->nullable();
            $table->text('closing_subtitle')->nullable();

            // Per-locale publish
            $table->timestamp('published_at')->nullable();

            $table->timestamps();

            $table->unique(['service_id', 'locale']);
            $table->index(['service_id', 'published_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_translations');
    }
};
