<?php

use App\Models\User;
use App\Support\Helper;

/* @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'email' => $email = $faker->unique()->safeEmail,
        'phone' => $faker->unique()->phoneNumber,
        'password' => $password ?: $password = bcrypt(md5('secret')),
        'username' => $faker->userName,
        'avatar' => $avatar = Helper::gravatar($email, 640),
        'original_avatar' => $avatar,
        'login_count' => $faker->numberBetween(1, 100000),
        'last_login_at' => $faker->dateTime(),
        'last_login_ip' => $faker->ipv4,
        'registered_ip' => $faker->ipv4,
        'remember_token' => str_random(10),
    ];
});
