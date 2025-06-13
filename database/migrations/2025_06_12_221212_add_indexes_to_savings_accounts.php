<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up()
    {
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

    public function down()
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
};
