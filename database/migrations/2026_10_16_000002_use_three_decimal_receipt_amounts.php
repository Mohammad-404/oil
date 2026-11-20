<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('receipts', function (Blueprint $table) {
            $table->decimal('fee_per_kg', 8, 3)->default(2)->change();
            $table->decimal('press_fee', 10, 3)->nullable()->change();
            $table->decimal('total_fee', 10, 3)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('receipts', function (Blueprint $table) {
            $table->decimal('fee_per_kg', 8, 2)->default(2)->change();
            $table->decimal('press_fee', 10, 2)->nullable()->change();
            $table->decimal('total_fee', 10, 2)->nullable()->change();
        });
    }
};
