<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('careers_translations')) {
            Schema::create('careers_translations', function (Blueprint $table) {
                $table->string('lang_code');
                $table->foreignId('careers_id');
                $table->string('name', 255)->nullable();
                $table->string('location', 255)->nullable();
                $table->string('salary', 255)->nullable();
                $table->longText('description')->nullable();

                $table->primary(['lang_code', 'careers_id'], 'careers_translations_primary');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('careers_translations');
    }
};
