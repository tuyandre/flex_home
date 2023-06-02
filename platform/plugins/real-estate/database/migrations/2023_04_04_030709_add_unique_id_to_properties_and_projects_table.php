<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('re_properties', function (Blueprint $table) {
            $table->string('unique_id')->nullable()->unique();
        });

        Schema::table('re_projects', function (Blueprint $table) {
            $table->string('unique_id')->nullable()->unique();
        });
    }

    public function down(): void
    {
        Schema::table('re_properties', function (Blueprint $table) {
            $table->dropColumn('unique_id');
        });

        Schema::table('re_projects', function (Blueprint $table) {
            $table->dropColumn('unique_id');
        });
    }
};
