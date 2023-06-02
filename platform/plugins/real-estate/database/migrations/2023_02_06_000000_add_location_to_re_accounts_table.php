<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (! Schema::hasColumn('re_accounts', 'country_id')) {
            Schema::table('re_accounts', function (Blueprint $table) {
                $table->foreignId('country_id')->nullable();
                $table->foreignId('state_id')->nullable();
                $table->foreignId('city_id')->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::table('re_accounts', function (Blueprint $table) {
            $table->dropColumn(['country_id', 'state_id', 'city_id']);
        });
    }
};
