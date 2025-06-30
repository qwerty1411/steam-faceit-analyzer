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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            // Email может быть отсутствовать для Faceit-only аккаунтов
            $table->string('email')->nullable()->unique();
            $table->timestamp('email_verified_at')->nullable();
            // Пароль необязателен при входе через Faceit
            $table->string('password')->nullable();
            $table->rememberToken();

            // Поля для интеграции с Faceit
            $table->string('faceit_id')->nullable()->unique();
            $table->string('faceit_nickname')->nullable();
            $table->string('faceit_avatar')->nullable();
            $table->text('faceit_access_token')->nullable();
            $table->text('faceit_refresh_token')->nullable();

            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
