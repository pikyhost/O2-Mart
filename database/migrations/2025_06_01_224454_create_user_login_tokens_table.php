<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_login_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->text('token')->nullable();
            $table->text('session_id')->nullable();
            $table->boolean('is_login');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_login_tokens');
    }
};
