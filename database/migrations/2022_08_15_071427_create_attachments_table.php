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
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('alt')->nullable();
            $table->string('title')->nullable();
            $table->string('original_name');
            $table->string('type')->nullable();
            $table->double('size')->nullable();
            $table->string('group')->nullable();
            $table->bigInteger('attachmentable_id')->nullable();
            $table->string('attachmentable_type')->nullable();
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
        Schema::dropIfExists('attachments');
    }
};
