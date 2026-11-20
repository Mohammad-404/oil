<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('oil_trades', function (Blueprint $table) {
            $table->string('payment_status', 16)->default('paid');
            $table->string('counterparty_name', 150)->nullable();
            $table->timestamp('debt_paid_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('oil_trades', function (Blueprint $table) {
            $table->dropColumn(['payment_status', 'counterparty_name', 'debt_paid_at']);
        });
    }
};
