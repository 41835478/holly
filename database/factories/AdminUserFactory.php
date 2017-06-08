<?php

use App\Models\AdminUser;

/* @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(AdminUser::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'email' => $faker->unique()->safeEmail,
        'username' => $faker->name,
        'avatar' => asset_from($faker->imageUrl(640, 640), ''),
        'password' => $password ?: $password = bcrypt(md5('secret')),
        'remember_token' => str_random(10),
    ];
});
