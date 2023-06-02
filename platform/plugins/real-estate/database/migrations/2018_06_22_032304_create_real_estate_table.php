<?php

use Botble\ACL\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        $this->down();

        Schema::create('re_projects', function (Blueprint $table) {
            $table->id();
            $table->string('name', 300);
            $table->string('description', 400)->nullable();
            $table->longText('content')->nullable();
            $table->string('images')->nullable();
            $table->string('location')->nullable();
            $table->foreignId('investor_id');
            $table->integer('number_block')->nullable();
            $table->smallInteger('number_floor')->nullable();
            $table->smallInteger('number_flat')->nullable();
            $table->boolean('is_featured')->default(0);
            $table->date('date_finish')->nullable();
            $table->date('date_sell')->nullable();
            $table->decimal('price_from', 15, 0)->nullable();
            $table->decimal('price_to', 15, 0)->nullable();
            $table->foreignId('currency_id')->nullable();
            $table->foreignId('city_id')->nullable();
            $table->foreignId('category_id')->nullable();
            $table->string('status', 60)->default('selling');
            $table->foreignId('author_id')->nullable();
            $table->string('author_type', 255)->default(addslashes(User::class));
            $table->timestamps();
        });

        Schema::create('re_properties', function (Blueprint $table) {
            $table->id();
            $table->string('name', 300);
            $table->string('type', 20)->default('sale');
            $table->string('description', 400)->nullable();
            $table->longText('content')->nullable();
            $table->string('location')->nullable();
            $table->string('images')->nullable();
            $table->foreignId('project_id')->default(0);
            $table->integer('number_bedroom')->nullable();
            $table->integer('number_bathroom')->nullable();
            $table->integer('number_floor')->nullable();
            $table->integer('square')->nullable();
            $table->decimal('price', 15, 0)->nullable();
            $table->foreignId('currency_id')->nullable();
            $table->boolean('is_featured')->default(0);
            $table->foreignId('city_id')->nullable();
            $table->string('period', 30)->default('month');
            $table->string('status', 60)->default('selling');
            $table->foreignId('category_id')->nullable();
            $table->foreignId('author_id')->nullable();
            $table->string('author_type', 255)->default(addslashes(User::class));
            $table->string('moderation_status', 60)->default('pending');
            $table->date('expire_date')->nullable();
            $table->boolean('auto_renew')->default(false);
            $table->boolean('never_expired')->default(false);
            $table->timestamps();
        });

        Schema::create('re_features', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('icon', 60)->nullable();
            $table->string('status', 60)->default('published');
        });

        Schema::create('re_property_features', function (Blueprint $table) {
            $table->foreignId('property_id');
            $table->foreignId('feature_id');
        });

        Schema::create('re_project_features', function (Blueprint $table) {
            $table->foreignId('project_id');
            $table->foreignId('feature_id');
        });

        Schema::create('re_investors', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('status', 60)->default('published');
            $table->timestamps();
        });

        Schema::create('re_currencies', function (Blueprint $table) {
            $table->id();
            $table->string('title', 60);
            $table->string('symbol', 10);
            $table->tinyInteger('is_prefix_symbol')->unsigned()->default(0);
            $table->tinyInteger('decimals')->unsigned()->default(0);
            $table->integer('order')->default(0)->unsigned();
            $table->tinyInteger('is_default')->default(0);
            $table->double('exchange_rate')->default(1);
            $table->timestamps();
        });

        Schema::create('re_consults', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('email', 60);
            $table->string('phone', 60);
            $table->foreignId('project_id')->nullable();
            $table->foreignId('property_id')->nullable();
            $table->longText('content')->nullable();
            $table->string('status', 60)->default('unread');
            $table->timestamps();
        });

        Schema::create('re_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 120);
            $table->string('last_name', 120);
            $table->text('description')->nullable();
            $table->string('gender', 20)->nullable();
            $table->string('email')->unique();
            $table->string('username', 60)->unique()->nullable();
            $table->string('password');
            $table->foreignId('avatar_id')->nullable();
            $table->date('dob')->nullable();
            $table->string('phone', 25)->nullable();
            $table->integer('credits')->unsigned()->nullable();
            $table->dateTime('confirmed_at')->nullable();
            $table->string('email_verify_token', 120)->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('re_account_password_resets', function (Blueprint $table) {
            $table->string('email')->index();
            $table->string('token')->index();
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('re_account_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->string('action', 120);
            $table->text('user_agent')->nullable();
            $table->string('reference_url', 255)->nullable();
            $table->string('reference_name', 255)->nullable();
            $table->string('ip_address', 25)->nullable();
            $table->foreignId('account_id')->index();
            $table->timestamps();
        });

        Schema::create('re_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->double('price', 15, 2)->unsigned();
            $table->foreignId('currency_id');
            $table->integer('percent_save')->unsigned()->default(0);
            $table->integer('number_of_listings')->unsigned();
            $table->integer('account_limit')->unsigned()->nullable();
            $table->tinyInteger('order')->default(0);
            $table->tinyInteger('is_default')->unsigned()->default(0);
            $table->string('status', 60)->default('published');
            $table->timestamps();
        });

        Schema::create('re_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('description', 400)->nullable();
            $table->string('status', 60)->default('published');
            $table->integer('order')->default(0)->unsigned();
            $table->tinyInteger('is_default')->default(0);
            $table->timestamps();
        });

        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->integer('credits')->unsigned();
            $table->string('description', 255)->nullable();
            $table->foreignId('user_id')->nullable();
            $table->foreignId('account_id')->nullable();
            $table->string('type')->default('add');

            $table->foreignId('payment_id')->nullable();
            $table->timestamps();
        });

        Schema::create('re_facilities', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('icon', 60)->nullable();
            $table->string('status', 60)->default('published');
            $table->timestamps();
        });

        Schema::create('re_facilities_distances', function (Blueprint $table) {
            $table->foreignId('facility_id');
            $table->foreignId('reference_id');
            $table->string('reference_type', 255);
            $table->string('distance', 255)->nullable();

            $table->primary(['facility_id', 'reference_id'], 'facilities_distances_primary');
        });
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('re_investors');
        Schema::dropIfExists('re_projects');
        Schema::dropIfExists('re_properties');
        Schema::dropIfExists('re_features');
        Schema::dropIfExists('re_property_features');
        Schema::dropIfExists('re_project_features');
        Schema::dropIfExists('re_currencies');
        Schema::dropIfExists('re_consults');
        Schema::dropIfExists('re_account_activity_logs');
        Schema::dropIfExists('re_account_password_resets');
        Schema::dropIfExists('re_accounts');
        Schema::dropIfExists('re_packages');
        Schema::dropIfExists('re_categories');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('re_facilities');
        Schema::dropIfExists('re_facilities_distances');
    }
};
