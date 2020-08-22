<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faithgen\Discussions\Models\Discussion;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(Discussion::class, function (Faker $faker) {
    return [
        'id'         => Str::uuid()->toString(),
        'title'      => $faker->sentence,
        'url'        => $faker->url,
        'discussion' => $faker->sentence(70),
        'approved'   => true,
    ];
});
