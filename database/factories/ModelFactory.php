<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
    ];
});

$factory->define(App\Models\Article::class, function (Faker\Generator $faker) {
    $infoWords = $faker->numberBetween(50,150);
    return [
        'user_id' => 1,
        'category_id' => 1,
        'title' => $faker->title,
        'info' => $faker->text($infoWords),
        'date_from' => $faker->date(),
        'date_to' => $faker->dateTimeBetween('now', '30 years')->format('Y-m-d'),
        'time_from' => $faker->time(),
        'time_to' => $faker->time(),
        'is_private' => 0,
        'number_extra_tickets' => $faker->numberBetween(0, 20),
        'address' =>  $faker->country . ' ' . $faker->city . ' ' . $faker->address,
        'lat' => $faker->latitude(),
        'lng' => $faker->longitude(),
        'status' => 1,
    ];
});
