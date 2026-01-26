<?php

namespace Database\Seeders;

use App\Libraries\Helpers;

use Illuminate\Database\Seeder;
use App\ApiKey;

use Faker\Factory as Faker;

class ApiKeyTableSeeder extends Seeder
{
    private $settings = [

    ];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        \DB::table('api_keys')->delete();

        ## Api Keys
        factory(ApiKey::class)->create([
            'key'          => 'paypal_username',
            'value'         => Helpers::getEnvWithFallback('PAYPAL_USERNAME', null),
        ]);
       factory(ApiKey::class)->create([
            'key'          => 'paypal_password',
            'value'         => Helpers::getEnvWithFallback('PAYPAL_PASSWORD', null),
        ]);
        factory(ApiKey::class)->create([
            'key'          => 'paypal_signature',
            'value'         => Helpers::getEnvWithFallback('PAYPAL_SIGNATURE', null),
        ]);
        factory(ApiKey::class)->create([
            'key'          => 'stripe_public_key',
            'value'         => Helpers::getEnvWithFallback('STRIPE_PUBLIC_KEY', null),
        ]);
        factory(ApiKey::class)->create([
            'key'          => 'stripe_secret_key',
            'value'         => Helpers::getEnvWithFallback('STRIPE_SECRET_KEY', null),
        ]);
        factory(ApiKey::class)->create([
            'key'          => 'challonge_api_key',
            'value'         => Helpers::getEnvWithFallback('CHALLONGE_API_KEY', null),
        ]);
        factory(ApiKey::class)->create([
            'key'          => 'steam_api_key',
            'value'         => Helpers::getEnvWithFallback('STEAM_API_KEY', null),
        ]);
    }
}
