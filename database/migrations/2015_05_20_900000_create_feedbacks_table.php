<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFeedbacksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('feedbacks')) {
            return;
        }

        Schema::create('feedbacks', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('device_id')->nullable();
            $table->string('os', 10);
            $table->string('os_version', 20)->nullable();
            $table->string('platform', 20)->nullable();
            $table->string('network', 8)->nullable();
            $table->string('ip', 45)->nullable();
            $table->text('content');
            $table->string('contact', 100)->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('feedbacks');
    }
}
