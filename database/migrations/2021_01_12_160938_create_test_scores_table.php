<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTestScoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('test_scores', function (Blueprint $table) {
            $table->id();
            $table->string('time')->nullable();
            $table->string('score')->nullable();
            $table->string('user_id')->nullable();
            $table->string('topic_id')->nullable();
            $table->string('course_id')->nullable();
            $table->enum('type', ['mcq','test','flashcard'])->nullable();
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
        Schema::dropIfExists('test_scores');
    }
}
