<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDevicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('devices')) {
            return;
        }

        Schema::create('devices', function (Blueprint $table) {
            $table->increments('id');
            $table->string('tdid', 40)->unique();
            $table->string('did', 40)->nullable();
            $table->string('acid', 40);
            $table->string('os', 10);
            $table->string('os_version', 20);
            $table->string('platform', 20);
            $table->string('model', 20)->nullable();
            $table->string('name', 150)->nullable();
            $table->boolean('is_jailbroken')->default(0);
            $table->string('carrier', 16)->nullable();
            $table->string('locale', 16)->nullable();
            $table->string('network', 8)->nullable();
            $table->string('ssid', 30)->nullable();
            $table->string('push_token', 64)->nullable();
            $table->string('idfa', 40)->nullable();
            $table->string('idfv', 40)->nullable();
            $table->unsignedInteger('screen_width')->default(0);
            $table->unsignedInteger('screen_height')->default(0);
            $table->float('screen_scale')->default(0.00);
            $table->integer('timezone_gmt')->default(0);
            // 登陆次数
            $table->unsignedInteger('login_count')->default(0);
            // 上次登陆时间
            $table->timestamp('last_login_at')->nullable();
            // 上次登陆IP
            $table->string('last_login_ip', 45)->nullable();
            // 注册时的IP
            $table->string('registered_ip', 45)->nullable();
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
        Schema::dropIfExists('devices');
    }
}