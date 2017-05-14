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
        if ($superEmail = config('var.super_admin_email')) {
            if (! AdminUser::where(['email' => $superEmail])->exists()) {
                factory(AdminUser::class)->create([
                    'email' => $superEmail,
                    'password' => bcrypt(md5('admin')),
                ]);
            }
        }

        if (App::isLocal()) {
            factory(AdminUser::class, 100)->create();
        }
    }
}
