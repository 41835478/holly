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
