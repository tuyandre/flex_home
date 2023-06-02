<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('re_account_packages')) {
            Schema::create('re_account_packages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('account_id');
                $table->foreignId('package_id');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('re_account_packages')) {
            Schema::dropIfExists('re_account_packages');
        }
    }
};
