<?php

use Botble\Location\Models\City;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (Schema::hasColumn('cities', 'slug')) {
            return;
        }

        Schema::table('cities', function (Blueprint $table) {
            $table->string('slug', 120)->unique()->nullable();
            $table->tinyInteger('is_featured')->default(0);
            $table->string('image', 255)->nullable();
        });

        $cities = City::query()->get();

        foreach ($cities as $city) {
            $city->slug = Str::slug($city->name);
            $city->save();
        }
    }

    public function down(): void
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->dropColumn(['slug', 'is_featured', 'image']);
        });
    }
};
