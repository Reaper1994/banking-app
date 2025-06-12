<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->unique();
            $table->foreignId('sender_account_id')->constrained('savings_accounts');
            $table->foreignId('recipient_account_id')->constrained('savings_accounts');
            $table->decimal('amount', 15, 2);
            $table->string('status');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('reference_number');
            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transfers');
    }
}; 