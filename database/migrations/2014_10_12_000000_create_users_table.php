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
        Schema::create('users', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('name');
            $table->string('surname');
            $table->string('bio')->nullable();
            $table->string('location')->nullable();
            $table->string('phone_prefix');
            $table->string('phone');
            $table->string('picture')->nullable();
            $table->boolean('show_reviews');
            $table->integer('rating');
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->string('auth_token', 360)->nullable();
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
};
