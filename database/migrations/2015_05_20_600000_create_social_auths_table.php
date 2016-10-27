<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->tinyInteger('social_type')->unsigned();
            $table->unsignedInteger('user_id');
            $table->string('access_token', 520);
            $table->string('refresh_token', 520)->nullable();
            $table->string('uid', 40)->nullable();
            // May store other vendor information, e.g. Weixin UnionID
            $table->string('vendor', 200)->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index(['social_type', 'user_id']);
            $table->index(['social_type', 'access_token', 'uid', 'vendor']);
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
