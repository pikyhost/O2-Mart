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
            // Add email_needed column if it doesn't exist
            if (!Schema::hasColumn('popups', 'email_needed')) {
                $table->boolean('email_needed')->default(false)->after('is_active');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('popups', function (Blueprint $table) {
            if (Schema::hasColumn('popups', 'email_needed')) {
                $table->dropColumn('email_needed');
            }
        });
    }
};
