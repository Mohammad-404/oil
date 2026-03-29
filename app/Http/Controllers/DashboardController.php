<?php

namespace App\Http\Controllers;

use App\Models\Owner;
use App\Models\Receipt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalOwners    = Owner::count();
        $totalReceipts  = Receipt::count();
        $todayReceipts  = Receipt::whereDate('created_at', today())->count();
        $totalOlive     = Receipt::sum('olive_weight_kg');
        $totalOil       = Receipt::sum('oil_weight_kg');
        $totalFees      = Receipt::sum('total_fee');
        $pendingCount   = Receipt::where('status', 'pending')->count();
        $completedCount = Receipt::where('status', 'completed')->count();

        $recentReceipts = Receipt::with('owner')->latest()->take(10)->get();

        // إحصائيات آخر 7 أيام
        $weeklyStats = Receipt::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(olive_weight_kg) as total_olive')
            )
            ->whereBetween('created_at', [now()->subDays(6), now()])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('dashboard', compact(
            'totalOwners', 'totalReceipts', 'todayReceipts',
            'totalOlive', 'totalOil', 'totalFees',
            'pendingCount', 'completedCount',
            'recentReceipts', 'weeklyStats'
        ));
    }

    public function reports(Request $request)
    {
        $query = Receipt::with('owner');

        if ($request->from_date) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->to_date) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $receipts    = $query->latest()->get();
        $totalOlive  = $receipts->sum('olive_weight_kg');
        $totalOil    = $receipts->sum('oil_weight_kg');
        $totalFees   = $receipts->sum('total_fee');
        $totalCount  = $receipts->count();

        return view('reports.index', compact(
            'receipts', 'totalOlive', 'totalOil', 'totalFees', 'totalCount'
        ));
    }

    public function settings()
    {
        $settings = DB::table('settings')->pluck('value', 'key');
        return view('settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $fields = ['press_name', 'fee_per_kg', 'press_phone', 'press_address'];
        foreach ($fields as $field) {
            if ($request->has($field)) {
                DB::table('settings')->updateOrInsert(
                    ['key' => $field],
                    ['value' => $request->$field, 'updated_at' => now()]
                );
            }
        }
        return redirect()->route('settings')->with('success', 'تم حفظ الإعدادات بنجاح');
    }
}
