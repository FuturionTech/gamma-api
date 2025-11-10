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
        Schema::create('administrators', function (Blueprint $table) {
            $table->id();
            $table->string('employee_number')->unique();
            $table->string('email')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone_number', 20)->nullable()->unique();
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('phone_verified_at')->nullable();
            $table->boolean('two_factor_enabled')->default(false);
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip')->nullable();
            $table->unsignedTinyInteger('failed_login_attempts')->default(0);
            $table->timestamp('locked_until')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('administrators')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('administrators')->nullOnDelete();
            $table->rememberToken();
            $table->timestamps();

            // Indexes for performance
            $table->index('status');
            $table->index('email_verified_at');
            $table->index('phone_verified_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('administrators');
    }
};

