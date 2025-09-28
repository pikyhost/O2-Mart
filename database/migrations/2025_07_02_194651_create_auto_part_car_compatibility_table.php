<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('auto_part_car_compatibility', function (Blueprint $table) {
            $table->id();

            $table->foreignId('auto_part_id')->constrained()->onDelete('cascade');
            $table->foreignId('car_model_id')->constrained()->onDelete('cascade');

            $table->year('year_from');
            $table->year('year_to')->nullable();

            $table->boolean('is_verified')->default(false);
            $table->string('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auto_part_car_compatibility');
    }
};
