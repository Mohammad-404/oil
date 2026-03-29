<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Owner;
use App\Models\Receipt;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // بيانات تجريبية - أصحاب الزيتون
        $owners = [
            ['name' => 'أحمد محمد الخالدي',  'city' => 'عجلون',   'street' => 'شارع الملك عبدالله', 'phone' => '0791234567', 'floor_number' => 1],
            ['name' => 'محمود عبد الرحمن',    'city' => 'إربد',    'street' => 'شارع الجامعة',        'phone' => '0792345678', 'floor_number' => 2],
            ['name' => 'خالد يوسف النمر',     'city' => 'جرش',     'street' => 'شارع الرسالة',        'phone' => '0793456789', 'floor_number' => null],
            ['name' => 'سامي إبراهيم العمر',  'city' => 'عجلون',   'street' => 'شارع الزيتون',        'phone' => '0794567890', 'floor_number' => 3],
            ['name' => 'عمر سالم الحسن',      'city' => 'عمّان',   'street' => 'شارع المدينة',        'phone' => '0795678901', 'floor_number' => null],
        ];

        foreach ($owners as $ownerData) {
            $owner = Owner::create($ownerData);

            // إنشاء 2-3 إيصالات لكل صاحب
            $statuses = ['pending', 'processing', 'completed'];
            for ($i = 0; $i < rand(1, 3); $i++) {
                $kg = rand(50, 500);
                $fee = 2.00;
                Receipt::create([
                    'owner_id'        => $owner->id,
                    'carrier_name'    => 'أبو ' . explode(' ', $owner->name)[0],
                    'olive_weight_kg' => $kg,
                    'oil_weight_kg'   => $statuses[$i % 3] === 'completed' ? round($kg * 0.2, 1) : null,
                    'oil_weight_gram' => $statuses[$i % 3] === 'completed' ? rand(0, 900) : null,
                    'floor_number'    => $owner->floor_number,
                    'fee_per_kg'      => $fee,
                    'total_fee'       => $kg * $fee,
                    'status'          => $statuses[$i % 3],
                    'notes'           => null,
                ]);
            }
        }
    }
}
