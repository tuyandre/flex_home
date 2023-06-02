<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\Artisan;
use Botble\Base\Supports\BaseSeeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends BaseSeeder
{
    public function run(): void
    {
        Artisan::call('db:wipe');

        DB::purge();
        DB::unprepared('USE `' . config('database.connections.mysql.database') . '`');
        DB::connection()->setDatabaseName(config('database.connections.mysql.database'));
        DB::getSchemaBuilder()->dropAllTables();
        DB::unprepared(file_get_contents(base_path('database.sql')));

        $this->activateAllPlugins();

        $this->call(LanguageSeeder::class);
        $this->call(CurrencySeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(FacilitySeeder::class);
        $this->call(FeatureSeeder::class);
        $this->call(PackageSeeder::class);
        $this->call(AccountSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(SettingSeeder::class);
        $this->call(PageSeeder::class);
        $this->call(LatLongSeeder::class);
        $this->call(MenuSeeder::class);
        $this->call(ThemeOptionSeeder::class);
        $this->call(BlogSeeder::class);
        $this->call(CareerSeeder::class);
        $this->call(ProjectSeeder::class);
        $this->call(PropertySeeder::class);
        $this->call(LocationSeeder::class);
        $this->call(ReviewSeeder::class);

        $this->uploadFiles('banner');
        $this->uploadFiles('cities');
        $this->uploadFiles('logo');
        $this->uploadFiles('projects');
        $this->uploadFiles('properties');
        $this->uploadFiles('users');

        $this->finished();
    }
}
