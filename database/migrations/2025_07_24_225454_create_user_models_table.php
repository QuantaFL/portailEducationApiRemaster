<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_models', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('birthday');
            $table->enum('gender', ['M','F']);
            $table->string('email')->unique();
            $table->string('password');
            $table->string('adress');
            $table->string('phone')->unique();
            $table->string('profile_picture_url')->nullable();
            $table->string('nationality')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_models');
    }
};
