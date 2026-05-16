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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('author_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->boolean('human_written')->default(false);
            $table->string('title');
            $table->string('links_title')->nullable();
            $table->string('meta_title');
            $table->string('meta_description');
            $table->string('links_group')->nullable();
            $table->text('intro')->nullable();
            $table->text('conclusion')->nullable();
            $table->string('slug'); // can not add unique cause of soft-deletes
            $table->smallInteger('status');
            $table->smallInteger('tc_style');
            $table->unsignedInteger('views')->default(0);
            $table->json('related')->nullable();
            $table->timestamp('published_at')->nullable();
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
        Schema::dropIfExists('posts');
    }
};
