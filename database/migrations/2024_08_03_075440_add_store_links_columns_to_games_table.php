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
        Schema::table('games', function (Blueprint $table) {
            $table->string('official_site')->after('description')->nullable();
            $table->string('steam')->after('description')->nullable();
            $table->string('playstation_store')->after('description')->nullable();
            $table->string('xbox_store')->after('description')->nullable();
            $table->string('nintendo_store')->after('description')->nullable();
            $table->text('summary')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('games', function (Blueprint $table) {
            //
        });
    }
};
