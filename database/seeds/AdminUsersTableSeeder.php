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
        $email = 'admin@example.com';
        $username = 'Super Admin';
        $password = bcrypt(md5('admin'));

        AdminUser::firstOrCreate(
            compact('email'),
            compact('username', 'password')
        );
    }
}
