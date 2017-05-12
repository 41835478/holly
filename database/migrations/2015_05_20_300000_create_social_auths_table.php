<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSocialAuthsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('social_auths')) {
            return;
        }

        Schema::create('social_auths', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('social_type');
            $table->unsignedInteger('user_id');
            $table->string('access_token', 550);
            $table->string('refresh_token', 550)->nullable();
            $table->string('uid', 40)->nullable();
            // May store other vendor information, like Weixin UnionID
            $table->string('vendor')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index(['social_type', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('social_auths');
    }
}
