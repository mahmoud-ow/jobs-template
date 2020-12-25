<?php

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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            $table->string('phone')->nullable();
            $table->string('user_type')->default('user')->comment('can be admin');
            $table->integer('last_known_notification')->default(0)->comment('last time the user clicked the notifications icon , latest notification id');
            $table->integer('state')->default(1);
            $table->integer('verified')->default(0);
            $table->string('auth_token')->nullable();
            $table->string('verification_key')->nullable();
            $table->string('language')->nullable();

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