<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('re_custom_fields')) {
            Schema::create('re_custom_fields', function (Blueprint $table) {
                $table->id();
                $table->string('name', 255);
                $table->string('type', 60);
                $table->integer('order')->default(999);
                $table->boolean('is_global')->default(false);
                $table->morphs('authorable');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('re_custom_field_options')) {
            Schema::create('re_custom_field_options', function (Blueprint $table) {
                $table->id();
                $table->foreignId('custom_field_id');
                $table->string('label')->nullable();
                $table->string('value');
                $table->integer('order')->default(999);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('re_custom_field_values')) {
            Schema::create('re_custom_field_values', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->string('value')->nullable();
                $table->morphs('reference');
                $table->foreignId('custom_field_id')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('re_custom_fields');
        Schema::dropIfExists('re_custom_field_values');
        Schema::dropIfExists('re_custom_field_options');
    }
};
