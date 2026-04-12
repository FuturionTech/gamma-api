<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Remove all sample job positions seeded by the previous migration.
        // Real positions will be added via the admin API when actual roles open.
        DB::table('job_positions')->delete();
    }

    public function down(): void
    {
        // Intentionally empty
    }
};
