<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('re_reviews')) {
            Schema::create('re_reviews', function (Blueprint $table) {
                $table->id();
                $table->foreignId('account_id');
                $table->morphs('reviewable');
                $table->tinyInteger('star');
                $table->string('content', 500);
                $table->string('status', 60)->default('approved');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('re_reviews');
    }
};
