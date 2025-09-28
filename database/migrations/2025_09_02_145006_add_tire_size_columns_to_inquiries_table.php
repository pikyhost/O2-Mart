<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // All columns already exist from previous migrations:
        // - front_width, front_height, front_diameter, rear_tyres added in 2025_08_04_181702
        // - rim_size_id added in 2025_08_04_173510
        // Nothing to do here
    }

    public function down(): void
    {
        // Nothing to rollback since no columns were added
    }
};