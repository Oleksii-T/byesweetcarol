<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->smallInteger('status')->after('comment_id');
            $table->foreignId('post_id')->after('id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->after('id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('comment_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            //
        });
    }
};
