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
        // cashier = صلاحيات كاملة | user = إضافة مالك وإيصالات فقط
        \DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('cashier','user') NOT NULL DEFAULT 'user'");
        // اليوزرز الحاليين يصيرون cashier
        \DB::table('users')->update(['role' => 'cashier']);
    }

    public function down(): void
    {
        \DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','cashier') NOT NULL DEFAULT 'admin'");
    }
};
