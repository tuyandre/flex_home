<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('re_account_activity_logs', function (Blueprint $table) {
            $table->string('ip_address', 39)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('re_account_activity_logs', function (Blueprint $table) {
            $table->string('ip_address', 25)->nullable()->change();
        });
    }
};
