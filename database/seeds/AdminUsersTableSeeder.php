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
        if ($super_admin_email = config('var.super_admin_email')) {
            if (! AdminUser::where(['email' => $super_admin_email])->exists()) {
                factory(AdminUser::class)->create([
                    'email' => $super_admin_email,
                    'password' => bcrypt(md5('admin')),
                ]);
            }
        }

        if (App::isLocal()) {
            factory(AdminUser::class, 100)->create();
        }
    }
}
