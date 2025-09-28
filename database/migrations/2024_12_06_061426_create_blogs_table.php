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
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blog_category_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('author_id')->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->date('published_at')->nullable();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('content');
            $table->timestamps();

            $table->boolean('is_active')->default(false)->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};
