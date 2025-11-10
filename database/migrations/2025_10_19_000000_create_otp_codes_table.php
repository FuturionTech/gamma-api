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
        Schema::create('otp_codes', function (Blueprint $table) {
            $table->id();
            $table->string('identifier'); // Email or phone number
            $table->string('code', 6); // 6-digit OTP code
            $table->enum('delivery_method', ['EMAIL', 'SMS'])->default('EMAIL');
            $table->string('language', 2)->default('FR'); // FR or EN
            $table->timestamp('expires_at')->index();
            $table->timestamp('verified_at')->nullable();
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->timestamps();

            // Index for faster lookups
            $table->index(['identifier', 'delivery_method', 'verified_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otp_codes');
    }
};
