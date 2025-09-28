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
        Schema::create('inquiries', function (Blueprint $table) {
            $table->id();

            // Inquiry Classification
            $table->enum('type', ['rims', 'auto_parts', 'battery', 'tires'])->index();
            $table->enum('status', ['pending', 'processing', 'quoted', 'completed', 'cancelled'])
                ->default('pending')->index();
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');

            // Customer Information
            $table->string('full_name', 100);
            $table->string('phone_number', 20);
            $table->string('email', 150)->index();

            // Vehicle Information
            $table->string('car_make', 50)->nullable();
            $table->string('car_model', 50)->nullable();
            $table->year('car_year')->nullable();
            $table->string('vin_chassis_number', 50)->nullable()->index();

            // Inquiry Details
            $table->json('required_parts')->nullable();
            $table->unsignedSmallInteger('quantity')->default(1);
            $table->text('battery_specs')->nullable();
            $table->text('description')->nullable();

            // File Management
            $table->json('car_license_photos')->nullable();
            $table->json('part_photos')->nullable();

            // Administrative
            $table->text('admin_notes')->nullable();
            $table->decimal('quoted_price', 10, 2)->nullable();
            $table->timestamp('quoted_at')->nullable();
            $table->unsignedBigInteger('assigned_to')->nullable();

            // Metadata
            $table->string('source', 50)->default('website'); // website, mobile_app, phone, etc.
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent', 500)->nullable();

            $table->timestamps();

            // Indexes for performance
            $table->index(['type', 'status']);
            $table->index(['created_at', 'type']);
            $table->index('assigned_to');

            // Foreign key for assigned user (optional)
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inquiries');
    }
};
