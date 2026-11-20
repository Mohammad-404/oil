<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('receipts', function (Blueprint $table) {
            // طريقة الدفع: نقدي / رديات زيت / بدل تنكة
            $table->enum('payment_method', ['cash', 'oil_return', 'can'])
                  ->default('cash')->after('total_fee');

            // رديات الزيت — كمية الزيت التي يأخذها الزبون بدل المبلغ
            $table->decimal('oil_return_kg', 10, 2)->default(0)->after('payment_method');
            $table->decimal('oil_return_gram', 10, 2)->default(0)->after('oil_return_kg');

            // بدل التنكة — عدد التنك لكل حجم
            $table->integer('can_16_count')->default(0)->after('oil_return_gram'); // 2.000 د.أ لكل واحدة
            $table->integer('can_8_count')->default(0)->after('can_16_count');    // 1.500 د.أ لكل واحدة
            $table->integer('can_4_count')->default(0)->after('can_8_count');    // 0.750 د.أ لكل واحدة
            $table->decimal('can_total', 10, 3)->default(0)->after('can_4_count'); // مجموع التنك
        });

        // driver_fee موجود مسبقاً — نبقيها لكنها معلومة داخلية للمعصرة فقط
        // ولا تُدخل في total_fee بعد الآن
    }

    public function down(): void
    {
        Schema::table('receipts', function (Blueprint $table) {
            $table->dropColumn([
                'payment_method', 'oil_return_kg', 'oil_return_gram',
                'can_16_count', 'can_8_count', 'can_4_count', 'can_total',
            ]);
        });
    }
};
