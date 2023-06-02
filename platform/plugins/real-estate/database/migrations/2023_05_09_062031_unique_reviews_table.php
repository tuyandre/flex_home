<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration
{
    public function up(): void
    {
        DB::statement('CREATE TABLE IF NOT EXISTS re_reviews_tmp LIKE re_reviews');
        DB::statement('TRUNCATE TABLE re_reviews_tmp');
        DB::statement('INSERT re_reviews_tmp SELECT * FROM re_reviews');
        DB::statement('TRUNCATE TABLE re_reviews');

        Schema::table('re_reviews', function (Blueprint $table) {
            $table->unique(['account_id', 'reviewable_id', 'reviewable_type'], 'reviews_unique');
        });


        DB::table('re_reviews_tmp')->oldest()->chunk(1000, function ($chunked) {
            DB::table('re_reviews')->insertOrIgnore(array_map(fn($item) => (array) $item, $chunked->toArray()));
        });

         Schema::dropIfExists('re_reviews_tmp');
    }

    public function down(): void
    {
        Schema::table('re_reviews', function (Blueprint $table) {
            $table->dropUnique('reviews_unique');
        });
    }
};
