<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            // Primero eliminar la foreign key constraint
            $table->dropForeign(['user_id']);
            // Hacer user_id nullable para permitir compras de invitados
            $table->foreignId('user_id')->nullable()->change();
            // Reagregar la foreign key constraint con onDelete('set null')
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            // Agregar campo guest_email para compras de invitados
            $table->string('guest_email')->nullable()->after('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropColumn('guest_email');
            // Nota: No revertimos user_id a NOT NULL para evitar problemas con datos existentes
        });
    }
};
