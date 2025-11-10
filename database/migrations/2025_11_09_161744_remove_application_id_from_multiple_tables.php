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
        // Drop indexes that include application_id BEFORE dropping the column
        Schema::table('faqs', function (Blueprint $table) {
            $table->dropIndex(['application_id', 'is_active']);
            $table->dropIndex(['application_id', 'category']);
        });

        Schema::table('blog_posts', function (Blueprint $table) {
            $table->dropIndex(['application_id', 'status']);
            $table->dropIndex(['application_id', 'slug']);
        });

        Schema::table('job_positions', function (Blueprint $table) {
            $table->dropIndex(['application_id', 'status']);
        });

        Schema::table('contact_requests', function (Blueprint $table) {
            $table->dropIndex(['application_id', 'status']);
        });

        // Now drop the application_id columns
        $tables = [
            'partners', 'clients', 'testimonials', 'banners', 'teams',
            'certifications', 'job_positions', 'contact_requests', 'faqs',
            'blog_posts', 'projects', 'stats'
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->dropForeign(['application_id']);
                $blueprint->dropColumn('application_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'partners', 'clients', 'testimonials', 'banners', 'teams',
            'certifications', 'job_positions', 'contact_requests', 'faqs',
            'blog_posts', 'projects', 'stats'
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->foreignId('application_id')->after('id')->constrained()->cascadeOnDelete();
            });
        }

        // Recreate indexes
        Schema::table('faqs', function (Blueprint $table) {
            $table->index(['application_id', 'is_active']);
            $table->index(['application_id', 'category']);
        });

        Schema::table('blog_posts', function (Blueprint $table) {
            $table->index(['application_id', 'status']);
            $table->index(['application_id', 'slug']);
        });

        Schema::table('job_positions', function (Blueprint $table) {
            $table->index(['application_id', 'status']);
        });

        Schema::table('contact_requests', function (Blueprint $table) {
            $table->index(['application_id', 'status']);
        });
    }
};
