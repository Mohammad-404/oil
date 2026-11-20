<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // مشاركة إعدادات المعصرة مع جميع الـ views
        View::composer('*', function ($view) {
            try {
                $settings   = DB::table('settings')->pluck('value', 'key');
                $pressName  = $settings['press_name']  ?? 'معصرة ربوع الشجرة الحديث';
                $pressPhone = $settings['press_phone'] ?? '0785099961 / 0785365419 / 0785766009';
            } catch (\Exception $e) {
                $pressName  = 'معصرة ربوع الشجرة الحديث';
                $pressPhone = '';
            }
            $view->with(compact('pressName', 'pressPhone'));
        });
    }
}
