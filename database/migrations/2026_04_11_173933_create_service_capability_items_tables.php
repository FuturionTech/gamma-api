<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_capability_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_capability_group_id')
                ->constrained('service_capability_groups')
                ->cascadeOnDelete();
            $table->integer('order')->default(0);
            $table->timestamps();
            $table->index(['service_capability_group_id', 'order'], 'sci_group_order_idx');
        });

        Schema::create('service_capability_item_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_capability_item_id')
                ->constrained('service_capability_items')
                ->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('name', 255);
            $table->timestamps();
            $table->unique(['service_capability_item_id', 'locale'], 'sci_trans_item_locale_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_capability_item_translations');
        Schema::dropIfExists('service_capability_items');
    }
};
