<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use app\Model\Setting;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = [
            'press_name'  => Setting::get('press_name', 'معصرة الزيتون'),
            'press_phone' => Setting::get('press_phone', ''),
            'fee_per_kg'  => Setting::get('fee_per_kg', 0.100),
        ];
        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        Setting::set('press_name',  $request->press_name,  'اسم المعصرة');
        Setting::set('press_phone', $request->press_phone, 'رقم هاتف المعصرة');
        Setting::set('fee_per_kg',  $request->fee_per_kg,  'سعر الأجرة/كغم');
        return back()->with('success', 'تم حفظ الإعدادات بنجاح');
    }
}
