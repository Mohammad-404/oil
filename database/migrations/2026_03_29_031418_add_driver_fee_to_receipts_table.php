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
            // أجرة السائق — يدوية منفصلة عن أجرة العصر
            $table->decimal('driver_fee', 10, 2)->default(0);
            // أجرة العصر = olive_weight_kg × fee_per_kg (محسوبة تلقائياً)
            $table->decimal('press_fee', 10, 2)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('receipts', function (Blueprint $table) {
            $table->dropColumn(['driver_fee', 'press_fee']);
        });
    }
};
