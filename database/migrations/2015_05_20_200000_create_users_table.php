<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->string('email', 100)->nullable()->unique();
            $table->string('phone', 20)->nullable()->unique();
            $table->string('password')->nullable();
            $table->string('username', 20)->nullable();
            $table->string('avatar')->nullable();
            $table->string('original_avatar')->nullable();
            $table->unsignedInteger('status')->default(User::STATUS_NORMAL);
            $table->unsignedInteger('login_count')->default(0);
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip', 45)->nullable();
            $table->string('registered_ip', 45)->nullable();
            $table->rememberToken();
            $table->timestamps();
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
