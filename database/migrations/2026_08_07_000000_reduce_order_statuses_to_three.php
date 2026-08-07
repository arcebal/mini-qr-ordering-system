<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('orders')->where('status', 'ready')->update(['status' => 'preparing']);
        DB::table('orders')->where('status', 'cancelled')->update(['status' => 'accepted']);

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE orders MODIFY status ENUM('accepted', 'preparing', 'completed') NOT NULL DEFAULT 'accepted'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE orders MODIFY status ENUM('pending', 'accepted', 'preparing', 'ready', 'completed', 'cancelled') NOT NULL DEFAULT 'pending'");
        }
    }
};
