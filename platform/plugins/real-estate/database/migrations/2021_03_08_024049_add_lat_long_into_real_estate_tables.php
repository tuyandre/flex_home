<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('re_projects', function ($table) {
            $table->string('latitude', 25)->nullable();
            $table->string('longitude', 25)->nullable();
        });

        Schema::table('re_properties', function ($table) {
            $table->string('latitude', 25)->nullable();
            $table->string('longitude', 25)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('re_projects', function ($table) {
            $table->dropColumn(['latitude', 'longitude']);
        });

        Schema::table('re_properties', function ($table) {
            $table->dropColumn(['latitude', 'longitude']);
        });
    }
};
