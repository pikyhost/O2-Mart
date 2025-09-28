<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('days', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Monday", "Tuesday", etc.
            $table->timestamps();
        });

        // Seed initial data (alternative to separate seeder)
        $days = [
            ['name' => 'Monday', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Tuesday', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Wednesday', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Thursday', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Friday', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Saturday', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Sunday', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('days')->insert($days);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('days');
    }
};
