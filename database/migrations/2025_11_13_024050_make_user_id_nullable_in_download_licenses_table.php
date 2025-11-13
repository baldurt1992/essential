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
        Schema::table('download_licenses', function (Blueprint $table) {
            // Primero eliminar la foreign key constraint
            $table->dropForeign(['user_id']);
            // Hacer user_id nullable para permitir licencias de invitados
            $table->foreignId('user_id')->nullable()->change();
            // Reagregar la foreign key constraint con onDelete('set null')
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('download_licenses', function (Blueprint $table) {
            // Eliminar la foreign key constraint
            $table->dropForeign(['user_id']);
            // Hacer user_id NOT NULL nuevamente
            $table->foreignId('user_id')->nullable(false)->change();
            // Reagregar la foreign key constraint original
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }
};
