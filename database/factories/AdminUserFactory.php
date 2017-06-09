<?php

use App\Models\AdminUser;
use App\Support\Helper;

/* @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(AdminUser::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'email' => $email = $faker->unique()->safeEmail,
        'username' => $faker->name,
        'avatar' => Helper::gravatar($email, 640),
        'password' => $password ?: $password = bcrypt(md5('secret')),
        'remember_token' => str_random(10),
    ];
});
