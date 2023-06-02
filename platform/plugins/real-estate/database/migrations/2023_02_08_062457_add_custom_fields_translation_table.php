<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('re_custom_fields_translations')) {
            Schema::create('re_custom_fields_translations', function (Blueprint $table) {
                $table->string('lang_code');
                $table->foreignId('re_custom_fields_id');
                $table->string('name', 255)->nullable();
                $table->string('type', 60)->nullable();

                $table->primary(['lang_code', 're_custom_fields_id'], 're_custom_fields_translations_primary');
            });
        }

        if (! Schema::hasTable('re_custom_field_options_translations')) {
            Schema::create('re_custom_field_options_translations', function (Blueprint $table) {
                $table->string('lang_code');
                $table->foreignId('re_custom_field_options_id');
                $table->string('label', 255)->nullable();
                $table->string('value', 255)->nullable();

                $table->primary(['lang_code', 're_custom_field_options_id'], 're_custom_field_options_translations_primary');
            });
        }

        if (! Schema::hasTable('re_custom_field_values_translations')) {
            Schema::create('re_custom_field_values_translations', function (Blueprint $table) {
                $table->string('lang_code');
                $table->foreignId('re_custom_field_values_id');
                $table->string('name', 255)->nullable();
                $table->string('value', 255)->nullable();

                $table->primary(['lang_code', 're_custom_field_values_id'], 're_custom_field_values_translations_primary');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('re_custom_fields_translations');
        Schema::dropIfExists('re_custom_field_options_translations');
        Schema::dropIfExists('re_custom_field_values_translations');
    }
};
