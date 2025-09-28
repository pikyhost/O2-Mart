<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update Day records to standard week mapping
        DB::table('days')->where('id', 1)->update(['name' => 'Sunday']);
        DB::table('days')->where('id', 2)->update(['name' => 'Monday']);
        DB::table('days')->where('id', 3)->update(['name' => 'Tuesday']);
        DB::table('days')->where('id', 4)->update(['name' => 'Wednesday']);
        DB::table('days')->where('id', 5)->update(['name' => 'Thursday']);
        DB::table('days')->where('id', 6)->update(['name' => 'Friday']);
        DB::table('days')->where('id', 7)->update(['name' => 'Saturday']);

        // Update working_hours records to match new mapping
        // Old: 1=Mon, 2=Tue, 3=Wed, 4=Thu, 5=Fri, 6=Sat, 7=Sun
        // New: 1=Sun, 2=Mon, 3=Tue, 4=Wed, 5=Thu, 6=Fri, 7=Sat
        DB::statement('UPDATE working_hours SET day_id = CASE 
            WHEN day_id = 1 THEN 2  -- Mon: 1->2
            WHEN day_id = 2 THEN 3  -- Tue: 2->3  
            WHEN day_id = 3 THEN 4  -- Wed: 3->4
            WHEN day_id = 4 THEN 5  -- Thu: 4->5
            WHEN day_id = 5 THEN 6  -- Fri: 5->6
            WHEN day_id = 6 THEN 7  -- Sat: 6->7
            WHEN day_id = 7 THEN 1  -- Sun: 7->1
            ELSE day_id END');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert Day records to old mapping
        DB::table('days')->where('id', 1)->update(['name' => 'Monday']);
        DB::table('days')->where('id', 2)->update(['name' => 'Tuesday']);
        DB::table('days')->where('id', 3)->update(['name' => 'Wednesday']);
        DB::table('days')->where('id', 4)->update(['name' => 'Thursday']);
        DB::table('days')->where('id', 5)->update(['name' => 'Friday']);
        DB::table('days')->where('id', 6)->update(['name' => 'Saturday']);
        DB::table('days')->where('id', 7)->update(['name' => 'Sunday']);

        // Revert working_hours records
        DB::statement('UPDATE working_hours SET day_id = CASE 
            WHEN day_id = 2 THEN 1  -- Mon: 2->1
            WHEN day_id = 3 THEN 2  -- Tue: 3->2  
            WHEN day_id = 4 THEN 3  -- Wed: 4->3
            WHEN day_id = 5 THEN 4  -- Thu: 5->4
            WHEN day_id = 6 THEN 5  -- Fri: 6->5
            WHEN day_id = 7 THEN 6  -- Sat: 7->6
            WHEN day_id = 1 THEN 7  -- Sun: 1->7
            ELSE day_id END');
    }
};