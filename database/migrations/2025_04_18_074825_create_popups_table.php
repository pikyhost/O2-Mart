<?php

use App\Models\Popup;
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
        Schema::create('popups', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->text('description');

            $table->string('image_path')->nullable();
            $table->string('cta_text');
            $table->string('cta_link');

            $table->boolean('is_active')->default(true);
            $table->boolean('email_needed')->default(false);

            $table->enum('display_rules', [
                'all_pages',
                'specific_pages',
                'page_group',
                'all_except_specific',
                'all_except_group'
            ])->default('all_pages');
            $table->json('specific_pages')->nullable();

            $table->unsignedInteger('popup_order')->default(0);
            $table->unsignedInteger('show_interval_minutes')->default(60);
            $table->unsignedInteger('delay_seconds')->default(60);
            $table->unsignedInteger('duration_seconds')->default(60);
            $table->unsignedInteger('dont_show_again_days')->default(7);


            $table->boolean('is_join_us')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('popups');
    }
};
