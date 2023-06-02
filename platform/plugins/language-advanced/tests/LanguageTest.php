<?php

namespace Botble\LanguageAdvanced\Tests;

use Botble\ACL\Models\User;
use Botble\ACL\Repositories\Interfaces\ActivationInterface;
use Botble\Language\Models\Language;
use Botble\Language\Models\LanguageMeta;
use Botble\Page\Models\Page;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class LanguageTest extends TestCase
{
    public function testTranslatable(): void
    {
        $this->createLanguages();

        $this->assertTrue(is_plugin_active('language') && is_plugin_active('language-advanced'));

        $user = $this->createUser();

        $this->be(User::first());

        $page = Page::create([
            'name' => 'This is a page in English',
            'user_id' => $user->id,
        ]);

        $this->get(route('pages.edit', $page->id))
            ->assertSee('This is a page in English');

        DB::table('pages_translations')->truncate();
        DB::table('pages_translations')->insert([
            'lang_code' => 'vi',
            'pages_id' => $page->id,
            'name' => 'This is a page in Vietnamese',
        ]);

        $this->call('GET', route('pages.edit', $page->id), ['ref_lang' => 'vi']);
        //->assertSee('This is a page in Vietnamese');

        $page->delete();

        $this->assertDatabaseHas(
            'pages_translations',
            [
                'lang_code' => 'vi',
                'pages_id' => $page->id,
                'name' => 'This is a page in Vietnamese',
            ]
        );
    }

    protected function createUser(): User
    {
        Schema::disableForeignKeyConstraints();

        User::truncate();

        $user = new User();
        $user->first_name = 'System';
        $user->last_name = 'Admin';
        $user->email = 'admin@botble.com';
        $user->username = 'botble';
        $user->password = Hash::make('159357');
        $user->super_user = 1;
        $user->manage_supers = 1;
        $user->save();

        $activationRepository = app(ActivationInterface::class);

        $activation = $activationRepository->createUser($user);

        $activationRepository->complete($user, $activation->code);

        return $user;
    }

    protected function createLanguages(): void
    {
        $languages = [
            [
                'lang_name' => 'English',
                'lang_locale' => 'en',
                'lang_is_default' => true,
                'lang_code' => 'en_US',
                'lang_is_rtl' => false,
                'lang_flag' => 'us',
                'lang_order' => 0,
            ],
            [
                'lang_name' => 'Tiếng Việt',
                'lang_locale' => 'vi',
                'lang_is_default' => false,
                'lang_code' => 'vi',
                'lang_is_rtl' => false,
                'lang_flag' => 'vn',
                'lang_order' => 0,
            ],
        ];

        Language::truncate();
        LanguageMeta::truncate();

        foreach ($languages as $item) {
            Language::create($item);
        }
    }
}
