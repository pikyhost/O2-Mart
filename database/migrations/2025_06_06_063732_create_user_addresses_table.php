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
        Schema::create('user_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('session_id')->nullable()->index(); // For guest users
            $table->string('label'); // e.g., 'WORK', 'Home', 'GARAGE'
            $table->string('full_name');
            $table->string('phone')->nullable();
            $table->text('address_line_1');
            $table->text('address_line_2')->nullable();

            // Location foreign keys
            $table->foreignId('country_id')->constrained()->onDelete('cascade');
            $table->foreignId('governorate_id')->constrained()->onDelete('cascade');
            $table->foreignId('city_id')->constrained()->onDelete('cascade');
            $table->foreignId('area_id')->nullable()->constrained()->onDelete('cascade');

            $table->string('postal_code')->nullable();
            $table->text('additional_info')->nullable(); // Extra delivery instructions
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            // Ensure either user_id or session_id is present
            $table->index(['user_id', 'session_id']);
            $table->index(['user_id', 'is_primary']);
            $table->index(['session_id', 'is_primary']);

            // Unique constraint for session_id when user_id is null
            $table->unique(['session_id', 'label']); // Prevent duplicate labels for same session
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_addresses');
    }
};
