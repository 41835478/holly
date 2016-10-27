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
                123456 => [
                    'email' => 'test@example.com',
                    'username' => 'Test User',
                ],
                520000 => [
                    'email' => 'test1@example.com',
                    'username' => 'Test1 User',
                ],
            ] as $id => $attributes
        ) {
            User::firstOrCreate(compact('id'), $attributes);
        }
    }
}
