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
        Schema::create('suppliers_page', function (Blueprint $table) {
            $table->id();
            $table->string('title_become_supplier');
            $table->text('desc_become_supplier');
            $table->string('why_auto_title');
            $table->text('why_auto_desc');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers_page');
    }
};
