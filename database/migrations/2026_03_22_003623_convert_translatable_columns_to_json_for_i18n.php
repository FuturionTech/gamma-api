<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Convert translatable columns to JSON for spatie/laravel-translatable.
 *
 * - Converts varchar title/subtitle columns to text (to hold JSON)
 * - Migrates existing plain-text values into {"en": "value"} JSON
 * - Drops the old _fr columns (their data was never populated in production)
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── services ─────────────────────────────────────────────
        // 1. Convert title to text so it can hold JSON
        Schema::table('services', function (Blueprint $table) {
            $table->text('title')->change();
        });

        // 2. Wrap existing English values in JSON
        DB::table('services')->orderBy('id')->each(function ($row) {
            DB::table('services')->where('id', $row->id)->update([
                'title'             => json_encode(['en' => $row->title], JSON_UNESCAPED_UNICODE),
                'description'       => $row->description ? json_encode(['en' => $row->description], JSON_UNESCAPED_UNICODE) : null,
                'short_description' => $row->short_description ? json_encode(['en' => $row->short_description], JSON_UNESCAPED_UNICODE) : null,
            ]);
        });

        // 3. Drop _fr columns (only if they exist)
        Schema::table('services', function (Blueprint $table) {
            $columns = array_filter(
                ['title_fr', 'description_fr', 'short_description_fr'],
                fn ($col) => Schema::hasColumn('services', $col)
            );
            if ($columns) {
                $table->dropColumn($columns);
            }
        });

        // ── solutions ────────────────────────────────────────────
        Schema::table('solutions', function (Blueprint $table) {
            $table->text('title')->change();
            $table->text('subtitle')->nullable()->change();
        });

        DB::table('solutions')->orderBy('id')->each(function ($row) {
            DB::table('solutions')->where('id', $row->id)->update([
                'title'       => json_encode(['en' => $row->title], JSON_UNESCAPED_UNICODE),
                'subtitle'    => $row->subtitle ? json_encode(['en' => $row->subtitle], JSON_UNESCAPED_UNICODE) : null,
                'description' => $row->description ? json_encode(['en' => $row->description], JSON_UNESCAPED_UNICODE) : null,
            ]);
        });

        Schema::table('solutions', function (Blueprint $table) {
            $columns = array_filter(
                ['title_fr', 'subtitle_fr', 'description_fr'],
                fn ($col) => Schema::hasColumn('solutions', $col)
            );
            if ($columns) {
                $table->dropColumn($columns);
            }
        });

        // ── industries ───────────────────────────────────────────
        Schema::table('industries', function (Blueprint $table) {
            $table->text('title')->change();
        });

        DB::table('industries')->orderBy('id')->each(function ($row) {
            DB::table('industries')->where('id', $row->id)->update([
                'title'             => json_encode(['en' => $row->title], JSON_UNESCAPED_UNICODE),
                'description'       => $row->description ? json_encode(['en' => $row->description], JSON_UNESCAPED_UNICODE) : null,
                'short_description' => $row->short_description ? json_encode(['en' => $row->short_description], JSON_UNESCAPED_UNICODE) : null,
            ]);
        });
    }

    public function down(): void
    {
        // ── industries (reverse) ─────────────────────────────────
        DB::table('industries')->orderBy('id')->each(function ($row) {
            $title = json_decode($row->title, true);
            $desc = $row->description ? json_decode($row->description, true) : null;
            $short = $row->short_description ? json_decode($row->short_description, true) : null;

            DB::table('industries')->where('id', $row->id)->update([
                'title'             => $title['en'] ?? $row->title,
                'description'       => $desc['en'] ?? $row->description,
                'short_description' => $short['en'] ?? $row->short_description,
            ]);
        });

        Schema::table('industries', function (Blueprint $table) {
            $table->string('title', 255)->change();
        });

        // ── solutions (reverse) ──────────────────────────────────
        Schema::table('solutions', function (Blueprint $table) {
            $table->string('title_fr', 255)->nullable();
            $table->string('subtitle_fr', 255)->nullable();
            $table->text('description_fr')->nullable();
        });

        DB::table('solutions')->orderBy('id')->each(function ($row) {
            $title = json_decode($row->title, true);
            $subtitle = $row->subtitle ? json_decode($row->subtitle, true) : null;
            $desc = $row->description ? json_decode($row->description, true) : null;

            DB::table('solutions')->where('id', $row->id)->update([
                'title'       => $title['en'] ?? $row->title,
                'subtitle'    => $subtitle['en'] ?? $row->subtitle,
                'description' => $desc['en'] ?? $row->description,
            ]);
        });

        Schema::table('solutions', function (Blueprint $table) {
            $table->string('title', 255)->change();
            $table->string('subtitle', 255)->nullable()->change();
        });

        // ── services (reverse) ───────────────────────────────────
        Schema::table('services', function (Blueprint $table) {
            $table->string('title_fr', 255)->nullable();
            $table->text('description_fr')->nullable();
            $table->string('short_description_fr', 255)->nullable();
        });

        DB::table('services')->orderBy('id')->each(function ($row) {
            $title = json_decode($row->title, true);
            $desc = $row->description ? json_decode($row->description, true) : null;
            $short = $row->short_description ? json_decode($row->short_description, true) : null;

            DB::table('services')->where('id', $row->id)->update([
                'title'             => $title['en'] ?? $row->title,
                'description'       => $desc['en'] ?? $row->description,
                'short_description' => $short['en'] ?? $row->short_description,
            ]);
        });

        Schema::table('services', function (Blueprint $table) {
            $table->string('title', 255)->change();
        });
    }
};
