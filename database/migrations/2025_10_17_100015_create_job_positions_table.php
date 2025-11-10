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
        Schema::create('job_positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('department')->nullable();
            $table->string('location')->nullable();
            $table->string('job_type')->default('full_time'); // full_time, part_time, contract
            $table->boolean('is_remote')->default(false);
            $table->string('salary_range')->nullable();
            $table->string('experience_required')->nullable();
            $table->text('summary')->nullable();
            $table->text('description')->nullable();
            $table->json('responsibilities')->nullable();
            $table->json('requirements')->nullable();
            $table->json('nice_to_have')->nullable();
            $table->json('benefits')->nullable();
            $table->json('skills')->nullable();
            $table->date('posted_date')->nullable();
            $table->string('status')->default('active'); // active, closed
            $table->timestamps();
            
            $table->index(['application_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_positions');
    }
};

