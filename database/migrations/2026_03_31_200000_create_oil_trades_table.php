<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('oil_trades', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['buy', 'sell']);
            $table->decimal('quantity_kg', 10, 3);
            $table->decimal('price_per_kg', 10, 3);
            $table->decimal('total_amount', 10, 3);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('oil_trades');
    }
};
