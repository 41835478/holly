<?php

use App\Models\AdminUser;

/* @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->defineAs(AdminUser::class, 'super', function (Faker\Generator $faker) {
    return [
        'email' => config('var.super_admin_email') ?: $faker->unique()->safeEmail,
        'username' => $faker->name,
        'avatar' => $faker->imageUrl(640, 640),
        'password' => bcrypt(md5('admin')),
        'remember_token' => str_random(10),
    ];
});

/* @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(AdminUser::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'email' => $faker->unique()->safeEmail,
        'username' => $faker->name,
        'avatar' => $faker->imageUrl(640, 640),
        'password' => $password ?: $password = bcrypt(md5('secret')),
        'remember_token' => str_random(10),
    ];
});
