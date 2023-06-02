<?php

namespace Database\Seeders;

use Botble\Base\Supports\BaseSeeder;
use Botble\RealEstate\Models\Account;
use Botble\RealEstate\Models\Property;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AccountSeeder extends BaseSeeder
{
    public function run(): void
    {
        Account::query()->truncate();

        $files = $this->uploadFiles('accounts');

        $faker = fake();

        Account::query()->create([
            'first_name' => $faker->firstName(),
            'last_name' => $faker->lastName(),
            'email' => 'john.smith@botble.com',
            'username' => Str::slug($faker->unique()->userName()),
            'password' => Hash::make('12345678'),
            'dob' => $faker->dateTime(),
            'phone' => $faker->e164PhoneNumber(),
            'description' => $faker->realText(30),
            'credits' => 10,
            'confirmed_at' => Carbon::now(),
            'avatar_id' => $faker->randomElements($files)[0]['data']->id,
        ]);

        for ($i = 0; $i < 10; $i++) {
            Account::query()->create([
                'first_name' => $faker->firstName(),
                'last_name' => $faker->lastName(),
                'email' => $faker->email(),
                'username' => Str::slug($faker->unique()->userName()),
                'password' => Hash::make($faker->password()),
                'dob' => $faker->dateTime(),
                'phone' => $faker->e164PhoneNumber(),
                'description' => $faker->realText(30),
                'credits' => $faker->numberBetween(1, 10),
                'confirmed_at' => Carbon::now(),
                'avatar_id' => $faker->randomElements($files)[0]['data']->id,
            ]);
        }

        foreach (Account::query()->get() as $account) {
            if (is_int($account->id) && $account->id % 2 == 0) {
                $account->is_featured = true;
                $account->save();
            }
        }

        $properties = Property::query()->get();

        foreach ($properties as $property) {
            $property->author_id = Account::query()->inRandomOrder()->value('id');
            $property->author_type = Account::class;
            $property->save();
        }
    }
}
