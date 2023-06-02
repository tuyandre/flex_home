<?php

use Botble\Page\Models\Page;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration
{
    public function up(): void
    {

        if (! is_plugin_active('real-estate')) {
            return;
        }

        $propertiesPageId = theme_option('properties_list_page_id');

        if ($propertiesPageId) {
            $page = Page::query()->find($propertiesPageId);

            if ($page) {
                $page->content = str_replace('[properties-list number_of_properties_per_page="12"][/properties-list]', '[properties-list title="' . __('Discover our properties') . '" description="' . theme_option('properties_description') . '"][/properties-list]', $page->content);

                $page->save();
            }
        }

        $propertiesPageId = theme_option('projects_list_page_id');

        if ($propertiesPageId) {
            $page = Page::query()->find($propertiesPageId);

            if ($page) {
                $page->content = str_replace('[projects-list number_of_projects_per_page="12"][/projects-list]', '[projects-list title="' . __('Discover our projects') . '" description="' . theme_option('home_project_description') . '"][/projects-list]', $page->content);

                $page->save();
            }
        }
    }
};
