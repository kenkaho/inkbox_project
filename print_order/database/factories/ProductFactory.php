<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Product;
use Faker\Generator as Faker;

$factory->define(Product::class, function (Faker $faker) {
    return [
            'title' => $faker->text($maxNbChars = 100),
            'price' => $faker->randomFloat($nbMaxDecimals = 2, $min = 1, $max = 3000),
            'inventory_quantity' => $faker->numberBetween(0,2000),
            'sku' => $faker->unique()->randomNumber($nbDigits = 8),
            'design_url' => $faker->domainName(),
            'vendor' => $faker->company(),
    ];
});