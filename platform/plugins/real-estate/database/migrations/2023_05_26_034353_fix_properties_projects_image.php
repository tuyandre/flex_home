<?php

use Botble\RealEstate\Models\Project;
use Botble\RealEstate\Models\Property;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration
{
    public function up(): void
    {
        foreach (Property::query()->get() as $project) {
            $project->images = ! is_array($project->images) ? json_decode($project->images, true) : $project->images;
            $project->save();
        }

        foreach (Project::query()->get() as $project) {
            $project->images = ! is_array($project->images) ? json_decode($project->images, true) : $project->images;
            $project->save();
        }
    }
};
