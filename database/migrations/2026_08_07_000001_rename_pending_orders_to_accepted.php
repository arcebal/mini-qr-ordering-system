<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('orders')->where('status', 'pending')->update(['status' => 'accepted']);

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE orders MODIFY status ENUM('accepted', 'preparing', 'completed') NOT NULL DEFAULT 'accepted'");
        }
    }

    public function down(): void
    {
        DB::table('orders')->where('status', 'accepted')->update(['status' => 'pending']);

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE orders MODIFY status ENUM('pending', 'preparing', 'completed') NOT NULL DEFAULT 'pending'");
        }
    }
};
