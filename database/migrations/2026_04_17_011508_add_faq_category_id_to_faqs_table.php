<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add nullable FK column
        Schema::table('faqs', function (Blueprint $table) {
            $table->foreignId('faq_category_id')->nullable()->after('category')
                  ->constrained('faq_categories')->nullOnDelete();
        });

        // Backfill: match existing category strings to faq_categories by slug
        $categories = DB::table('faq_categories')->get();
        foreach ($categories as $cat) {
            DB::table('faqs')
                ->whereRaw('LOWER(category) = ?', [strtolower($cat->name)])
                ->update(['faq_category_id' => $cat->id]);
        }
    }

    public function down(): void
    {
        Schema::table('faqs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('faq_category_id');
        });
    }
};
