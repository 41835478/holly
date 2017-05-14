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
        if (! AdminUser::whereKey(1)->exists()) {
            factory(AdminUser::class, 'super')->create();
        }

        if (App::isLocal()) {
            factory(AdminUser::class, 100)->create();
        }
    }
}
