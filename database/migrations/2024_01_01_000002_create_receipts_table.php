<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_number')->unique(); // رقم الإيصال
            $table->foreignId('owner_id')->constrained()->onDelete('cascade'); // صاحب الزيتون
            $table->string('carrier_name');           // اسم حامل الزيتون
            $table->decimal('olive_weight_kg', 10, 2);  // كمية الزيتون (كيلو)
            $table->decimal('oil_weight_kg', 10, 2)->nullable();    // كمية الزيت المعصور (كيلو)
            $table->decimal('oil_weight_gram', 10, 2)->nullable();  // كمية الزيت (غرام)
            $table->integer('floor_number')->nullable(); // رقم الدور
            $table->decimal('fee_per_kg', 8, 2)->default(2.00);     // الأجرة لكل كيلو
            $table->decimal('total_fee', 10, 2)->nullable();         // المجموع الكلي للأجرة
            $table->enum('status', ['pending', 'processing', 'completed'])->default('pending'); // الحالة
            $table->text('notes')->nullable();        // ملاحظات
            $table->timestamp('received_at')->useCurrent(); // تاريخ الاستلام
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('receipts');
    }
};
