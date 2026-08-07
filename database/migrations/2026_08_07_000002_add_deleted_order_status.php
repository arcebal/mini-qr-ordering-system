<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE orders MODIFY status ENUM('accepted', 'preparing', 'completed', 'deleted') NOT NULL DEFAULT 'accepted'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::table('orders')->where('status', 'deleted')->update(['status' => 'accepted']);
            DB::statement("ALTER TABLE orders MODIFY status ENUM('accepted', 'preparing', 'completed') NOT NULL DEFAULT 'accepted'");
        }
    }
};
