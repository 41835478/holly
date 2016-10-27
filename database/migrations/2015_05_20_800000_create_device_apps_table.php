<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeviceAppsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('device_apps')) {
            return;
        }

        Schema::create('device_apps', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('device_id');
            $table->string('identifier', 191);
            $table->string('version', 20);
            $table->string('channel', 20);

            $table->index(['device_id', 'identifier']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('device_apps');
    }
}
