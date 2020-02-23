<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Miracuthbert\Royalty\Models\Point;
use Faker\Generator as Faker;

$factory->define(Point::class, function (Faker $faker) {
    return [
        'name' => $name = $faker->unique()->colorName,
        'key' => \Illuminate\Support\Str::slug($name),
        'points' => $faker->randomElement(range(10, 100, 20)),
        'description' => $faker->text(60),
    ];
});
