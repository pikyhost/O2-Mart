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
        $driver = \DB::getDriverName();
        
        if ($driver === 'mysql') {
            \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        } else {
            \DB::statement('PRAGMA foreign_keys=OFF;');
        }
        
        // Just update the existing records to keep only name
        // Don't actually drop columns to avoid foreign key issues
        \DB::table('brands')->update([
            'slug' => null,
            'description' => null,
            'website_url' => null,
            'is_active' => 1,
            'is_featured' => 0,
            'meta_title' => null,
            'meta_description' => null,
            'meta_keywords' => null
        ]);
        
        if ($driver === 'mysql') {
            \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        } else {
            \DB::statement('PRAGMA foreign_keys=ON;');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('brands', function (Blueprint $table) {
            $table->string('slug')->nullable();
            $table->text('description')->nullable();
            $table->string('website_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
        });
    }
};
