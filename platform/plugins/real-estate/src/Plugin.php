<?php

namespace Botble\RealEstate;

use Botble\PluginManagement\Abstracts\PluginOperationAbstract;
use Illuminate\Support\Facades\Schema;

class Plugin extends PluginOperationAbstract
{
    public static function activated(): void
    {
        app('migrator')->run(database_path('migrations'));
    }

    public static function remove(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('re_consults');
        Schema::dropIfExists('re_investors');
        Schema::dropIfExists('re_property_categories');
        Schema::dropIfExists('re_project_categories');
        Schema::dropIfExists('re_projects');
        Schema::dropIfExists('re_properties');
        Schema::dropIfExists('re_features');
        Schema::dropIfExists('re_property_features');
        Schema::dropIfExists('re_categories');
        Schema::dropIfExists('re_currencies');
        Schema::dropIfExists('re_facilities_distances');
        Schema::dropIfExists('re_facilities');
        Schema::dropIfExists('re_accounts');
        Schema::dropIfExists('re_account_password_resets');
        Schema::dropIfExists('re_account_activity_logs');
        Schema::dropIfExists('re_packages');
        Schema::dropIfExists('re_account_packages');
        Schema::dropIfExists('re_properties_translations');
        Schema::dropIfExists('re_projects_translations');
        Schema::dropIfExists('re_features_translations');
        Schema::dropIfExists('re_facilities_translations');
        Schema::dropIfExists('re_categories_translations');
        Schema::dropIfExists('re_packages_translations');
        Schema::dropIfExists('re_custom_fields');
        Schema::dropIfExists('re_custom_field_values');
        Schema::dropIfExists('re_custom_field_options');
        Schema::dropIfExists('re_custom_fields_translations');
        Schema::dropIfExists('re_custom_field_options_translations');
        Schema::dropIfExists('re_custom_field_values_translations');
        Schema::dropIfExists('re_invoices');
        Schema::dropIfExists('re_invoice_items');
        Schema::dropIfExists('re_account_packages');
    }
}
