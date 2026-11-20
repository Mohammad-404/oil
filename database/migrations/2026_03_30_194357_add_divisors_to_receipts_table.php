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
        Schema::table('receipts', function (Blueprint $table) {
            // N₁: مقسوم رديات الزيت بدون تنك → (زيت × سعر) ÷ N₁ = قيمة الرديات بالدينار
            $table->decimal('oil_return_divisor', 8, 3)->default(7);
            // N₂: مقسوم رديات الزيت مع التنك → (تنك + زيت × سعر) ÷ N₂ = قيمة الرديات بالدينار
            $table->decimal('can_return_divisor', 8, 3)->default(7);
        });
    }

    public function down(): void
    {
        Schema::table('receipts', function (Blueprint $table) {
            $table->dropColumn(['oil_return_divisor', 'can_return_divisor']);
        });
    }
};
