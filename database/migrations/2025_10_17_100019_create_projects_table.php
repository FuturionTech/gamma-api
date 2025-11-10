<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('challenge')->nullable();
            $table->text('solution')->nullable();
            $table->text('results')->nullable();
            $table->string('featured_image_url')->nullable();
            $table->json('gallery_images')->nullable();
            $table->string('client_name')->nullable();
            $table->string('industry')->nullable();
            $table->json('technologies')->nullable();
            $table->string('status')->default('draft'); // draft, published
            $table->date('completion_date')->nullable();
            $table->timestamps();
            
            $table->index(['application_id', 'status']);
            $table->index(['application_id', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};

