<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        // First drop any existing indexes
        Schema::table('savings_accounts', function (Blueprint $table) {
            $table->dropIndex(['account_number']);
            $table->dropIndex(['balance']);
            $table->dropIndex(['is_active']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['first_name']);
            $table->dropIndex(['last_name']);
            $table->dropIndex(['email']);
        });

        // Then create new indexes
        Schema::table('savings_accounts', function (Blueprint $table) {
            $table->index('account_number');
            $table->index('balance');
            $table->index('is_active');
            $table->index('created_at');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index('first_name');
            $table->index('last_name');
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::table('savings_accounts', function (Blueprint $table) {
            $table->dropIndex(['account_number']);
            $table->dropIndex(['balance']);
            $table->dropIndex(['is_active']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['first_name']);
            $table->dropIndex(['last_name']);
            $table->dropIndex(['email']);
        });
    }

    private function createIndexIfNotExists(string $table, string $column): void
    {
        $indexName = "{$table}_{$column}_index";
        $indexes = collect(DB::select("SHOW INDEXES FROM {$table}"))->pluck('Key_name');

        if (! $indexes->contains($indexName)) {
            Schema::table($table, function (Blueprint $table) use ($column) {
                $table->index($column);
            });
        }
    }

    private function dropIndexIfExists(string $table, string $column): void
    {
        $indexName = "{$table}_{$column}_index";
        $indexes = collect(DB::select("SHOW INDEXES FROM {$table}"))->pluck('Key_name');

        if ($indexes->contains($indexName)) {
            Schema::table($table, function (Blueprint $table) use ($indexName) {
                $table->dropIndex($indexName);
            });
        }
    }
};
