<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\User;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('users')) {
            return;
        }

        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email', 150)->nullable()->unique();
            $table->string('phone', 20)->nullable()->unique();
            $table->string('password', 60)->nullable();
            $table->string('username', 10)->nullable();
            $table->string('avatar_path', 300)->nullable();
            $table->string('small_avatar_path', 300)->nullable();
            // 用户的状态. 可以用于存放一些状态值
            $table->unsignedInteger('status')->default(User::STATUS_NORMAL);
            // 金币(积分)
            // $table->unsignedInteger('coins')->default(0);
            $table->timestamp('vip_expired_at')->nullable();
            // 登陆次数
            $table->unsignedInteger('login_count')->default(0);
            // 上次登陆时间
            $table->timestamp('last_login_at')->nullable();
            // 上次登陆IP
            $table->string('last_login_ip', 45)->nullable();
            // 注册时的IP
            $table->string('registered_ip', 45)->nullable();
            // 注册时间
            $table->timestamp('registered_at')->nullable();
            // 记住登陆状态
            $table->rememberToken();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
