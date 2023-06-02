<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (! Schema::hasColumn('re_categories', 'parent_id')) {
            Schema::table('re_categories', function (Blueprint $table) {
                $table->foreignId('parent_id')->default(0);
            });
        }

        if (! Schema::hasTable('re_property_categories')) {
            Schema::create('re_property_categories', function (Blueprint $table) {
                $table->foreignId('property_id');
                $table->foreignId('category_id');
                $table->primary(['property_id', 'category_id'], 'property_categories_primary');
            });
        }

        if (! Schema::hasTable('re_project_categories')) {
            Schema::create('re_project_categories', function (Blueprint $table) {
                $table->foreignId('project_id');
                $table->foreignId('category_id');

                $table->primary(['project_id', 'category_id'], 'project_categories_primary');
            });
        }

        $properties = DB::table('re_properties')->get();
        foreach ($properties as $property) {
            DB::table('re_property_categories')->insert([
                'property_id' => $property->id,
                'category_id' => $property->category_id,
            ]);
        }

        $projects = DB::table('re_projects')->get();
        foreach ($projects as $project) {
            DB::table('re_project_categories')->insert([
                'project_id' => $project->id,
                'category_id' => $project->category_id,
            ]);
        }

        if (Schema::hasColumn('re_properties', 'category_id')) {
            Schema::table('re_properties', function (Blueprint $table) {
                $table->dropColumn('category_id');
            });
        }

        if (Schema::hasColumn('re_projects', 'category_id')) {
            Schema::table('re_projects', function (Blueprint $table) {
                $table->dropColumn('category_id');
            });
        }
    }

    public function down(): void
    {
        Schema::table('re_properties', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable();
        });

        Schema::table('re_projects', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable();
        });

        $propertyCategories = DB::table('re_property_categories')->get();
        foreach ($propertyCategories as $propertyCategory) {
            DB::table('re_properties')
                ->where('id', $propertyCategory->property_id)
                ->update([
                    'category_id' => $propertyCategory->category_id,
                ]);
        }

        $projectCategories = DB::table('re_project_categories')->get();
        foreach ($projectCategories as $projectCategory) {
            DB::table('re_projects')
                ->where('id', $projectCategory->project_id)
                ->update([
                    'category_id' => $projectCategory->category_id,
                ]);
        }

        Schema::dropIfExists('re_property_categories');
        Schema::dropIfExists('re_project_categories');
        Schema::table('re_categories', function (Blueprint $table) {
            $table->dropColumn('parent_id');
        });
    }
};
