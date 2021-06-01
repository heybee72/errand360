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
            $table->string('name');
            $table->enum('user_plan', ['free','basic','deluxe','premium'])->default('free');
            $table->integer('notification_status')->default(1);
            $table->string('ref_code')->nullable();
            $table->string('email')->unique();
            $table->string('profile_image')->nullable(); 
            $table->string('campus')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password', 60);
            $table->enum('role', ['user'])->default('user');
            $table->ipAddress('user_ip');
            $table->string('api_token', 60)->unique();
            $table->rememberToken('name');
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
