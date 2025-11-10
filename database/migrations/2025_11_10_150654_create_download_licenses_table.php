<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('download_licenses', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->morphs('source'); // purchase or subscription
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('template_id')->constrained('templates')->cascadeOnDelete();
            $table->string('purchase_code')->nullable()->index();
            $table->unsignedInteger('download_count')->default(0);
            $table->unsignedInteger('download_limit')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('last_downloaded_at')->nullable();
            $table->timestamps();

            $table->unique(['source_type', 'source_id', 'template_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('download_licenses');
    }
};
