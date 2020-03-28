<?php

use Kasitaw\ApiKey\ApiKey;
use Illuminate\Support\Str;
use Faker\Generator as Faker;
use Kasitaw\ApiKey\Tests\TestModel\User;

/* @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(ApiKey::class, function (Faker $faker) {
    $user = factory(User::class)->create();

    return [
        'uuid' => Str::uuid()->toString(),
        'model_type' => get_class($user),
        'model_id' => $user->id,
        'key' => Str::random(80),
        'status' => true,
        'last_access_at' => now(),
    ];
});
