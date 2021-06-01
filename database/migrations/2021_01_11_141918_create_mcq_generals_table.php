<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMcqGeneralsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mcq_generals', function (Blueprint $table) {
            $table->id();
            $table->text('question');
            $table->string('opt_a');
            $table->string('opt_b');
            $table->string('opt_c');
            $table->string('opt_d');
            $table->string('answer');
            $table->text('reason');
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
        Schema::dropIfExists('mcq_generals');
    }
}
