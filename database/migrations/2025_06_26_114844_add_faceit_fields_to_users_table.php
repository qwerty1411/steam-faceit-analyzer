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
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('faceit_id')->unique()->nullable();
            $table->string('faceit_nickname')->nullable();
            $table->string('faceit_avatar')->nullable();
            $table->string('faceit_access_token')->nullable();
            $table->string('faceit_refresh_token')->nullable();

        });
    }
};
