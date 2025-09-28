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
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mobile_van_service_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('installer_shop_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('address')->nullable();
            $table->string('google_map_link')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};
