<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (! Schema::hasColumn('re_properties', 'views')) {
            Schema::table('re_properties', function (Blueprint $table) {
                $table->integer('views')->unsigned()->default(0);
            });
        }

        if (! Schema::hasColumn('re_projects', 'views')) {
            Schema::table('re_projects', function (Blueprint $table) {
                $table->integer('views')->unsigned()->default(0);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('re_properties', 'views')) {
            Schema::table('re_properties', function (Blueprint $table) {
                $table->dropColumn('views');
            });
        }

        if (Schema::hasColumn('re_projects', 'views')) {
            Schema::table('re_projects', function (Blueprint $table) {
                $table->dropColumn('views');
            });
        }
    }
};
