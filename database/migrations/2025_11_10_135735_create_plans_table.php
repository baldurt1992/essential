<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('billing_interval', 32);
            $table->unsignedTinyInteger('billing_interval_count')->default(1);
            $table->unsignedBigInteger('price_cents');
            $table->string('currency', 3)->default('usd');
            $table->string('stripe_product_id')->nullable();
            $table->string('stripe_price_id')->nullable()->unique();
            $table->boolean('is_active')->default(true);
            $table->json('features')->nullable();
            $table->json('metadata')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
