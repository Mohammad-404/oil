<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // إدراج الإعدادات الافتراضية
        DB::table('settings')->insert([
            ['key' => 'press_name', 'value' => 'معصرة الزيتون', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'fee_per_kg', 'value' => '2.00', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'press_phone', 'value' => '', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'press_address', 'value' => '', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
