<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * السنة النشطة من الإعدادات — null يعني عرض كل السنوات
     */
    protected function getActiveYear(): ?int
    {
        $year = DB::table('settings')->where('key', 'active_year')->value('value');
        return ($year && $year !== 'all') ? (int)$year : null;
    }

    /**
     * تطبيق فلتر السنة على أي query builder
     */
    protected function applyYearFilter($query, ?int $year)
    {
        if ($year) {
            $query->whereYear('created_at', $year);
        }
        return $query;
    }
}
