<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('primary_color')->nullable();
            $table->string('secondary_color')->nullable();

            $table->string('site_name')->nullable();

            $table->foreignId('country_id')->nullable();
            $table->foreignId('currency_id')->nullable()->constrained('currencies')->onDelete('cascade');

            $table->string('logo')->nullable();
            $table->string('dark_logo')->nullable();

            $table->string('favicon')->nullable();
            $table->timestamps();

            $table->string('phone')->nullable();
            $table->string('email')->nullable();

            $table->string('facebook')->nullable();
            $table->string('youtube')->nullable();
            $table->string('instagram')->nullable();
            $table->string('x')->nullable();
            $table->string('snapchat')->nullable();
            $table->string('tiktok')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
