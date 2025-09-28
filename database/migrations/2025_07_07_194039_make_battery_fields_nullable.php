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
    Schema::table('batteries', function (Blueprint $table) {
        $table->string('item_code')->nullable()->change();
        $table->string('image_url')->nullable()->change();
        $table->decimal('regular_price', 10, 2)->nullable()->change();
        $table->decimal('discount_percentage', 5, 2)->nullable()->change();
        $table->decimal('discounted_price', 10, 2)->nullable()->change();
        $table->foreignId('capacity_id')->nullable()->change();
        $table->foreignId('dimension_id')->nullable()->change();
        $table->text('description')->nullable()->change();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
