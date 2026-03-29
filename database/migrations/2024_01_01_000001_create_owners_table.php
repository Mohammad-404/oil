<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('owners', function (Blueprint $table) {
            $table->id();
            $table->string('name');           // اسم صاحب الزيتون
            $table->string('city');           // المدينة
            $table->string('street');         // الشارع
            $table->string('phone');          // رقم الهاتف
            $table->integer('floor_number')->nullable(); // رقم الدور
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('owners');
    }
};
