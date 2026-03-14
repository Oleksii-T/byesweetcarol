<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('meta_title');
            $table->string('meta_description');
            $table->smallInteger('rating')->nullable();
            $table->smallInteger('metacritic')->nullable();
            $table->float('users_score', 3, 1)->nullable();
            $table->date('release_date')->nullable();
            $table->string('developer');
            $table->string('publisher')->nullable();
            $table->string('platforms');
            $table->string('ganres');
            $table->string('esbr');
            $table->string('slug')->unique();
            $table->json('hours');
            $table->text('description');
            $table->text('summary');
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
        Schema::dropIfExists('games');
    }
};
