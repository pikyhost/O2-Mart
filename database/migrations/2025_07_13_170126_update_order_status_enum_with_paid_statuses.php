<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateOrderStatusEnumWithPaidStatuses extends Migration
{
    public function up()
    {
        // For SQLite compatibility, we'll just ensure the column exists
        // SQLite doesn't enforce ENUM constraints anyway
        if (DB::getDriverName() === 'mysql') {
            DB::statement("
                ALTER TABLE orders 
                MODIFY COLUMN status 
                ENUM('pending', 'preparing', 'shipping', 'delayed', 'refund', 'cancelled', 'completed', 'paid', 'payment_failed') 
                NOT NULL DEFAULT 'pending'
            ");
        }
        // For SQLite, the column already exists and can accept any string value
    }

    public function down()
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("
                ALTER TABLE orders 
                MODIFY COLUMN status 
                ENUM('pending', 'preparing', 'shipping', 'delayed', 'refund', 'cancelled', 'completed') 
                NOT NULL DEFAULT 'pending'
            ");
        }
        // For SQLite, no action needed
    }
}
