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
        Schema::table('users', function (Blueprint $table) {
            $table->string('password_recovery_otp', 6)->nullable()->after('otp_expires_at');
            $table->timestamp('password_recovery_otp_expires_at')->nullable()->after('password_recovery_otp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['password_recovery_otp', 'password_recovery_otp_expires_at']);
        });
    }
};
