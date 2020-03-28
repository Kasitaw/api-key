<?php

use Faker\Generator as Faker;
use Kasitaw\ApiKey\Tests\TestModel\User;

/* @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(User::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'created_at' => now(),
        'updated_at' => now(),
    ];
});
