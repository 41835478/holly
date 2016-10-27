<?php

use App\Models\AdminUser;
use Illuminate\Database\Seeder;

class AdminUsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ([
                'admin@example.com' => 'Super Admin',
            ] as $email => $username
        ) {
            AdminUser::firstOrCreate(compact('email'), compact('username') + [
                'password' => bcrypt(md5('admin')),
            ]);
        }
    }
}
