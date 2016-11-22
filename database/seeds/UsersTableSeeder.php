<?php

use Illuminate\Database\Seeder;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ([

            655300 => [
                'email' => 'test@example.com',
                'username' => 'Test User',
            ],

        ] as $id => $attributes) {
            User::firstOrCreate(compact('id'), $attributes);
        }
    }
}
