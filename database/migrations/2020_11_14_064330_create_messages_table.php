<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();

            $table->integer('conversation_id');
            $table->integer('to_user_id');
            $table->integer('from_user_id');
            $table->longText('message');
            $table->integer('seen')->default(false);

            /* $table->integer('from_user_id')->nullable()->comment('Null = Can Be A Guest');
            $table->integer('to_user_id')->default(0)->comment('Null = Can Be A Guest');
            $table->string('phone')->nullable()->comment('Null = Can Be A Guest');
            $table->string('email')->nullable()->comment('Null = Can Be A Guest');
            $table->longText('content', 2000);
            $table->integer('seen')->default(false); */
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
        Schema::dropIfExists('messages');
    }
}
