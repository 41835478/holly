<?php

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if ($initial_user_id = config('var.initial_user_id')) {
            if (! User::whereKey($initial_user_id)->exists()) {
                factory(User::class)->create([
                    (new User)->getKeyName() => $initial_user_id,
                ]);
            }
        }

        if (App::isLocal()) {
            factory(User::class, 300)->create();
        }
    }
}
