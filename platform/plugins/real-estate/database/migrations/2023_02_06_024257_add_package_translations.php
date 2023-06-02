<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('re_packages_translations')) {
            Schema::create('re_packages_translations', function (Blueprint $table) {
                $table->string('lang_code');
                $table->foreignId('re_packages_id');
                $table->string('name', 255)->nullable();

                $table->primary(['lang_code', 're_packages_id'], 're_packages_translations_primary');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('re_packages_translations');
    }
};
