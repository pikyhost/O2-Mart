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
        Schema::table('popups', function (Blueprint $table) {
            // Drop the old boolean column
            $table->dropColumn('email_needed');
            
            // Add new enum column for email input mode
            $table->enum('email_input_mode', ['hidden', 'optional', 'required'])
                ->default('hidden')
                ->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('popups', function (Blueprint $table) {
            // Restore the old boolean column
            $table->boolean('email_needed')->default(false)->after('is_active');
            
            // Drop the enum column
            $table->dropColumn('email_input_mode');
        });
    }
};
