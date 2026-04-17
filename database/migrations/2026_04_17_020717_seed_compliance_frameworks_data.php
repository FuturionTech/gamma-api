<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        $frameworks = [
            ['name' => 'OSFI', 'slug' => 'osfi', 'order' => 0],
            ['name' => 'PIPEDA', 'slug' => 'pipeda', 'order' => 1],
            ['name' => 'PHIPA', 'slug' => 'phipa', 'order' => 2],
            ['name' => 'SOC 2', 'slug' => 'soc-2', 'order' => 3],
            ['name' => 'ISO 27001', 'slug' => 'iso-27001', 'order' => 4],
            ['name' => 'GDPR', 'slug' => 'gdpr', 'order' => 5],
            ['name' => 'HIPAA', 'slug' => 'hipaa', 'order' => 6],
        ];

        foreach ($frameworks as $fw) {
            DB::table('compliance_frameworks')->updateOrInsert(
                ['slug' => $fw['slug']],
                array_merge($fw, [
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }

    public function down(): void
    {
        DB::table('compliance_frameworks')->whereIn('slug', [
            'osfi', 'pipeda', 'phipa', 'soc-2', 'iso-27001', 'gdpr', 'hipaa',
        ])->delete();
    }
};
