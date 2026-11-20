<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('receipts', function (Blueprint $table) {
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->decimal('discount_amount', 10, 3)->default(0);
            $table->decimal('discounted_can_total', 10, 3)->default(0);
        });

        // Existing receipts have no discount, so their payable can amount stays the same.
        DB::table('receipts')->update(['discounted_can_total' => DB::raw('can_total')]);
    }

    public function down(): void
    {
        Schema::table('receipts', function (Blueprint $table) {
            $table->dropColumn(['discount_percent', 'discount_amount', 'discounted_can_total']);
        });
    }
};
