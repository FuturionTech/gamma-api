<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Convert remaining translatable varchar columns to text (for JSON storage)
 * and wrap existing plain-text values in {"en": "value"} JSON.
 *
 * Tables already handled by the previous migration:
 *   services (title, description, short_description)
 *   solutions (title, subtitle, description)
 *   industries (title, description, short_description)
 */
return new class extends Migration
{
    /**
     * Tables and their translatable varchar columns that need conversion to text.
     * Columns already text/longText are included in the wrap step but not the schema change.
     */
    private array $schemaChanges = [
        'faqs'               => ['question'],
        'stats'              => ['label'],
        'blog_posts'         => ['title'],
        'banners'            => ['title', 'cta_text'],
        'teams'              => ['role'],
        'testimonials'       => ['position'],
        'process_steps'      => ['title'],
        'process_step_items' => ['title'],
        'job_positions'      => ['title'],
        'projects'           => ['title'],
        'certifications'     => ['title'],
        'service_features'   => ['title'],
        'service_benefits'   => ['title'],
        'solution_features'  => ['title'],
        'solution_benefits'  => ['title'],
    ];

    /**
     * ALL translatable columns per table (varchar + text) that need JSON wrapping.
     */
    private array $wrapColumns = [
        'faqs'               => ['question', 'answer'],
        'stats'              => ['label'],
        'blog_posts'         => ['title', 'excerpt', 'content'],
        'banners'            => ['title', 'subtitle', 'cta_text'],
        'teams'              => ['role', 'biography'],
        'testimonials'       => ['content', 'position'],
        'process_steps'      => ['title', 'description', 'short_description'],
        'process_step_items' => ['title', 'description'],
        'job_positions'      => ['title', 'summary', 'description'],
        'projects'           => ['title', 'description', 'challenge', 'solution', 'results'],
        'certifications'     => ['title'],
        'service_features'   => ['title', 'description'],
        'service_benefits'   => ['title', 'description'],
        'solution_features'  => ['title', 'description'],
        'solution_benefits'  => ['title', 'description'],
    ];

    public function up(): void
    {
        // 1. Convert varchar columns to text so they can hold JSON
        foreach ($this->schemaChanges as $table => $columns) {
            Schema::table($table, function (Blueprint $blueprint) use ($columns) {
                foreach ($columns as $column) {
                    $blueprint->text($column)->nullable()->change();
                }
            });
        }

        // 2. Wrap existing plain-text values in {"en": "value"} JSON
        foreach ($this->wrapColumns as $table => $columns) {
            DB::table($table)->orderBy('id')->each(function ($row) use ($table, $columns) {
                $updates = [];
                foreach ($columns as $column) {
                    $value = $row->{$column};
                    if ($value !== null && !$this->isJson($value)) {
                        $updates[$column] = json_encode(['en' => $value], JSON_UNESCAPED_UNICODE);
                    }
                }
                if (!empty($updates)) {
                    DB::table($table)->where('id', $row->id)->update($updates);
                }
            });
        }
    }

    public function down(): void
    {
        // 1. Unwrap JSON back to plain text
        foreach ($this->wrapColumns as $table => $columns) {
            DB::table($table)->orderBy('id')->each(function ($row) use ($table, $columns) {
                $updates = [];
                foreach ($columns as $column) {
                    $value = $row->{$column};
                    if ($value !== null && $this->isJson($value)) {
                        $decoded = json_decode($value, true);
                        $updates[$column] = $decoded['en'] ?? $value;
                    }
                }
                if (!empty($updates)) {
                    DB::table($table)->where('id', $row->id)->update($updates);
                }
            });
        }

        // 2. Convert text columns back to varchar(255)
        foreach ($this->schemaChanges as $table => $columns) {
            Schema::table($table, function (Blueprint $blueprint) use ($columns) {
                foreach ($columns as $column) {
                    $blueprint->string($column, 255)->nullable()->change();
                }
            });
        }
    }

    private function isJson(string $value): bool
    {
        json_decode($value);
        return json_last_error() === JSON_ERROR_NONE;
    }
};
