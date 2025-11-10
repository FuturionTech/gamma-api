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
        Schema::table('services', function (Blueprint $table) {
            $table->dropForeign(['application_id']);
            $table->dropIndex(['application_id', 'slug']);
            $table->dropIndex(['application_id', 'is_active']);
            $table->dropColumn('application_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->foreignId('application_id')->after('id')->constrained()->cascadeOnDelete();
            $table->index(['application_id', 'slug']);
            $table->index(['application_id', 'is_active']);
        });
    }
};
