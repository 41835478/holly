<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->string('os', 10);
            $table->string('os_version', 20);
            $table->string('platform', 20);
            $table->string('model', 20)->nullable();
            $table->string('name', 150)->nullable();
            $table->boolean('jailbroken')->default(0);
            $table->string('carrier', 20)->nullable();
            $table->string('locale', 20)->nullable();
            $table->string('network', 8)->nullable();
            $table->string('ssid', 30)->nullable();
            $table->string('push_token', 64)->nullable();
            $table->string('idfa', 40)->nullable();
            $table->string('idfv', 40)->nullable();
            $table->unsignedInteger('screen_width')->default(0);
            $table->unsignedInteger('screen_height')->default(0);
            $table->float('screen_scale')->default(0.00);
            $table->integer('timezone_gmt')->default(0);
            $table->unsignedInteger('login_count')->default(0);
            $table->timestamp('last_login_at')->nullable();
            $table->ipAddress('last_login_ip')->nullable();
            $table->ipAddress('registered_ip')->nullable();
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
