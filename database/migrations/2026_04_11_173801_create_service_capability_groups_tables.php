<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_capability_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->string('icon')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
            $table->index(['service_id', 'order']);
        });

        Schema::create('service_capability_group_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_capability_group_id')
                ->constrained('service_capability_groups')
                ->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('name', 255);
            $table->timestamps();
            $table->unique(['service_capability_group_id', 'locale'], 'scg_trans_group_locale_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_capability_group_translations');
        Schema::dropIfExists('service_capability_groups');
    }
};
