<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeedbackTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('feedback')) {
            return;
        }

        Schema::create('feedback', function (Blueprint $table) {
            $table->increments('id');
            $table->text('content');
            $table->string('contact', 100)->nullable();
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('device_id')->nullable();
            $table->string('os', 10);
            $table->string('os_version', 20)->nullable();
            $table->string('platform', 20)->nullable();
            $table->string('network', 8)->nullable();
            $table->string('ip', 45);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('feedback');
    }
}
