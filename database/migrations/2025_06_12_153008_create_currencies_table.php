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
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('code', 3)->unique(); // ISO 4217 code (e.g., USD, EUR, GBP)
            $table->string('name'); // Full name (e.g., US Dollar, Euro, British Pound)
            $table->string('symbol', 5); // Currency symbol (e.g., $, €, £)
            $table->decimal('exchange_rate', 10, 4)->default(1.0000); // Exchange rate relative to USD
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
