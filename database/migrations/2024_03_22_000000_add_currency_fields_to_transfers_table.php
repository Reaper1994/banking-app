<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transfers', function (Blueprint $table) {
            $table->string('currency', 3)->after('amount');
            $table->string('recipient_currency', 3)->after('currency');
            $table->decimal('converted_amount', 10, 2)->after('recipient_currency');
        });
    }

    public function down(): void
    {
        Schema::table('transfers', function (Blueprint $table) {
            $table->dropColumn(['currency', 'recipient_currency', 'converted_amount']);
        });
    }
}; 