<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\OilTrade;
use App\Models\Receipt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $day = today()->toDateString();
        $base = fn() => Receipt::query()->whereDate('created_at', $day);
        $todayTrades = fn(string $type) => OilTrade::where('type', $type)->whereDate('created_at', $day);

        $todayPendingCount    = $base()->where('status', 'pending')->count();
        $todayProcessingCount = $base()->where('status', 'processing')->count();
        $todayCompletedCount  = $base()->where('status', 'completed')->count();
        $todayOlive       = $base()->sum('olive_weight_kg');
        $todayOil         = $base()->sum('oil_weight_kg');
        $todayOilReturnKg = $base()->where('payment_method', 'oil_return')->where('status', '!=', 'debt')->sum('oil_return_kg');
        $todayCount       = $base()->count();
        $todayFees        = $base()->where('status', '!=', 'debt')->where('payment_method', '!=', 'oil_return')->sum('total_fee')
                          + $base()->where('status', '!=', 'debt')->where('payment_method', 'oil_return')->where('cans_in_return', false)->sum('discounted_can_total');
        $todayOilBuyTotal  = OilTrade::where('type', 'buy')->cashBetween($day, $day)->sum('total_amount');
        $todayOilSellTotal = OilTrade::where('type', 'sell')->cashBetween($day, $day)->sum('total_amount');
        $todayOilBuyDebt   = $todayTrades('buy')->outstandingDebt()->sum('total_amount');
        $todayOilSellDebt  = $todayTrades('sell')->outstandingDebt()->sum('total_amount');
        $todayExpenseTotal = Expense::where('type', 'expense')->whereDate('created_at', $day)->sum('amount');
        $todayIncomeTotal  = Expense::where('type', 'income')->whereDate('created_at', $day)->sum('amount');
        $todayNetFees      = $todayFees - $todayOilBuyTotal + $todayOilSellTotal - $todayExpenseTotal + $todayIncomeTotal;
        $todayDebtTotal    = $base()->where('status', 'debt')->where('payment_method', '!=', 'oil_return')->sum('total_fee');
        $todayDebtCount    = $base()->where('status', 'debt')->count();
        $todayDebtOilKg    = $base()->where('status', 'debt')->where('payment_method', 'oil_return')->sum('oil_return_kg');

        // حركة زيت هذا اليوم فقط؛ المخزون الفعلي يبقى محسوباً في صفحة بيع وشراء الزيت.
        $oilReturnKg = $todayOilReturnKg;
        $oilBuyKg    = $todayTrades('buy')->sum('quantity_kg');
        $oilSellKg   = $todayTrades('sell')->sum('quantity_kg');
        $netOilKg    = $oilReturnKg + $oilBuyKg - $oilSellKg;

        // ديون مسددة اليوم — نقداً فقط (رديات الزيت لا تُحسب في الأجور النقدية)
        $todayDebtPaidTotal = Receipt::query()
                                ->whereDate('debt_paid_at', $day)
                                ->where('payment_method', '!=', 'oil_return')
                                ->sum('total_fee');
        $todayDebtPaidCount = Receipt::query()->whereDate('debt_paid_at', $day)->count();
        // الديون المسددة لا تُحسب في صافي الأجور النقدية

        $recentReceipts   = $base()->with('owner')->latest()->get();
        $tableCashTotal   = $recentReceipts->filter(fn($r) => $r->status !== 'debt' && $r->payment_method !== 'oil_return')->sum('total_fee')
                          + $recentReceipts->filter(fn($r) => $r->status !== 'debt' && $r->payment_method === 'oil_return' && !$r->cans_in_return)->sum('discounted_can_total');
        $tableOilRetTotal = $recentReceipts->filter(fn($r) => $r->payment_method === 'oil_return' && $r->status !== 'debt')->sum('oil_return_kg');
        $tableDebtTotal   = $recentReceipts->filter(fn($r) => $r->status === 'debt' && $r->payment_method !== 'oil_return')->sum('total_fee');
        $tableDebtOilKg   = $recentReceipts->filter(fn($r) => $r->status === 'debt' && $r->payment_method === 'oil_return')->sum('oil_return_kg');

        // ديون مسددة اليوم من أيام سابقة (لإظهارها في جدول منفصل)
        $todayPaidDebts = Receipt::query()
                            ->with('owner')
                            ->whereDate('debt_paid_at', $day)
                            ->whereDate('created_at', '<', $day)
                            ->latest('debt_paid_at')
                            ->get();

        return view('dashboard', compact(
            'day', 'oilReturnKg', 'oilBuyKg', 'oilSellKg', 'netOilKg',
            'todayOlive', 'todayOil', 'todayOilReturnKg',
            'todayCount', 'todayFees', 'todayNetFees',
            'todayOilBuyTotal', 'todayOilSellTotal',
            'todayOilBuyDebt', 'todayOilSellDebt',
            'todayExpenseTotal', 'todayIncomeTotal',
            'todayDebtTotal', 'todayDebtCount', 'todayDebtOilKg',
            'todayDebtPaidTotal', 'todayDebtPaidCount',
            'todayPendingCount', 'todayProcessingCount', 'todayCompletedCount',
            'recentReceipts',
            'tableCashTotal', 'tableOilRetTotal', 'tableDebtTotal', 'tableDebtOilKg',
            'todayPaidDebts'
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

        $receipts      = $query->latest()->get();
        $totalOlive    = $receipts->sum('olive_weight_kg');
        $totalOil      = $receipts->sum('oil_weight_kg');
        $totalFees     = $receipts->where('status', '!=', 'debt')->where('payment_method', '!=', 'oil_return')->sum('total_fee')
                       + $receipts->where('status', '!=', 'debt')->where('payment_method', 'oil_return')->where('cans_in_return', false)->sum('discounted_can_total');
        $totalCount    = $receipts->count();
        $oilBuyTotal   = OilTrade::where('type', 'buy')->paid()->sum('total_amount');
        $oilSellTotal  = OilTrade::where('type', 'sell')->paid()->sum('total_amount');
        $oilBuyDebt    = OilTrade::where('type', 'buy')->outstandingDebt()->sum('total_amount');
        $oilSellDebt   = OilTrade::where('type', 'sell')->outstandingDebt()->sum('total_amount');
        $oilBuyKg      = OilTrade::where('type', 'buy')->sum('quantity_kg');
        $oilSellKg     = OilTrade::where('type', 'sell')->sum('quantity_kg');
        $totalNetFees  = $totalFees - $oilBuyTotal + $oilSellTotal;
        $debtTotal     = $receipts->where('status', 'debt')->where('payment_method', '!=', 'oil_return')->sum('total_fee');
        $debtOilKg     = $receipts->where('status', 'debt')->where('payment_method', 'oil_return')->sum('oil_return_kg');
        $oilReturnKg   = $receipts->where('payment_method', 'oil_return')->where('status', '!=', 'debt')->sum('oil_return_kg');
        $netOilKg      = $oilReturnKg + $oilBuyKg - $oilSellKg;

        return view('reports.index', compact(
            'receipts', 'totalOlive', 'totalOil', 'totalFees', 'totalCount',
            'totalNetFees', 'oilBuyTotal', 'oilSellTotal',
            'oilBuyDebt', 'oilSellDebt',
            'oilBuyKg', 'oilSellKg', 'netOilKg',
            'debtTotal', 'debtOilKg', 'oilReturnKg'
        ));
    }

    public function financialReport(Request $request)
    {
        // AJAX filter: skip lock check, return rendered partial as JSON
        if ($request->expectsJson()) {
            $data = $this->buildFinancialData($request);
            return response()->json([
                'html' => view('reports._financial_results', $data)->render(),
            ]);
        }

        if (!session('financial_unlocked')) {
            return view('reports.financial', ['locked' => true]);
        }
        session()->forget('financial_unlocked');

        $data = $this->buildFinancialData($request);
        return view('reports.financial', array_merge($data, ['locked' => false]));
    }

    private function buildFinancialData(Request $request): array
    {
        $activeYear = $this->getActiveYear();
        $fromDate   = $request->input('from_date', today()->toDateString());
        $toDate     = $request->input('to_date',   today()->toDateString());

        // الأجرة النقدية: نفس منطق لوحة التحكم — كل إيصال status != debt
        // (لا يوجد فلتر على debt_paid_at لأن المدفوعة تصبح status=completed وتُحسب هنا)
        $feesByDate = $this->applyYearFilter(Receipt::query(), $activeYear)
            ->whereDate('created_at', '>=', $fromDate)->whereDate('created_at', '<=', $toDate)
            ->where('status', '!=', 'debt')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as receipt_count'),
                DB::raw('SUM(CASE WHEN payment_method != "oil_return" THEN total_fee ELSE 0 END) as direct_fees'),
                DB::raw('SUM(CASE WHEN payment_method = "oil_return" AND cans_in_return = 0 THEN discounted_can_total ELSE 0 END) as can_fees'))
            ->groupBy('date')->get()->keyBy('date');

        // ديون مسددة نقداً فقط: رديات الزيت لا تُحسب في الأجور النقدية
        $paidDebtsByDate = $this->applyYearFilter(Receipt::query(), $activeYear)
            ->whereDate('debt_paid_at', '>=', $fromDate)->whereDate('debt_paid_at', '<=', $toDate)
            ->whereNotNull('debt_paid_at')
            ->where('payment_method', '!=', 'oil_return')
            ->select(DB::raw('DATE(debt_paid_at) as date'), DB::raw('COUNT(*) as paid_count'),
                DB::raw('SUM(total_fee) as paid_total'))
            ->groupBy('date')->get()->keyBy('date');

        $debtsByDate = $this->applyYearFilter(Receipt::query(), $activeYear)
            ->whereDate('created_at', '>=', $fromDate)->whereDate('created_at', '<=', $toDate)
            ->where('status', 'debt')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as debt_count'), DB::raw('SUM(total_fee) as debt_total'))
            ->groupBy('date')->get()->keyBy('date');

        $expByDate = $this->applyYearFilter(Expense::query(), $activeYear)
            ->whereDate('created_at', '>=', $fromDate)->whereDate('created_at', '<=', $toDate)
            ->select(DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(CASE WHEN type = "income"  THEN amount ELSE 0 END) as income_total'),
                DB::raw('SUM(CASE WHEN type = "expense" THEN amount ELSE 0 END) as expense_total'))
            ->groupBy('date')->get()->keyBy('date');

        $oilByDate = OilTrade::cashBetween($fromDate, $toDate, $activeYear)
            ->select(DB::raw('DATE(' . OilTrade::CASH_DATE . ') as date'),
                DB::raw('SUM(CASE WHEN type = "buy"  THEN total_amount ELSE 0 END) as buy_total'),
                DB::raw('SUM(CASE WHEN type = "sell" THEN total_amount ELSE 0 END) as sell_total'))
            ->groupBy('date')->get()->keyBy('date');

        $oilDebtsByDate = $this->applyYearFilter(OilTrade::outstandingDebt(), $activeYear)
            ->whereDate('created_at', '>=', $fromDate)->whereDate('created_at', '<=', $toDate)
            ->select(DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(CASE WHEN type = "buy" THEN total_amount ELSE 0 END) as buy_debt'),
                DB::raw('SUM(CASE WHEN type = "sell" THEN total_amount ELSE 0 END) as sell_debt'))
            ->groupBy('date')->get()->keyBy('date');

        $allDates = collect()
            ->merge($feesByDate->keys())->merge($debtsByDate->keys())
            ->merge($expByDate->keys())->merge($oilByDate->keys())
            ->merge($oilDebtsByDate->keys())
            ->merge($paidDebtsByDate->keys())
            ->unique()->sort()->values();

        $rows = $allDates->map(function ($date) use ($feesByDate, $debtsByDate, $expByDate, $oilByDate, $paidDebtsByDate, $oilDebtsByDate) {
            $f = $feesByDate->get($date); $d = $debtsByDate->get($date);
            $e = $expByDate->get($date);  $o = $oilByDate->get($date);
            $p = $paidDebtsByDate->get($date);
            $oilDebt = $oilDebtsByDate->get($date);
            $paidDebts = $p->paid_total ?? 0;
            $cashFees  = ($f->direct_fees ?? 0) + ($f->can_fees ?? 0);
            $income    = $e->income_total  ?? 0; $oilSell = $o->sell_total ?? 0;
            $expense   = $e->expense_total ?? 0; $oilBuy  = $o->buy_total  ?? 0;
            $totalIn   = $cashFees + $income + $oilSell;
            $totalOut  = $expense + $oilBuy;
            return ['date' => $date, 'receipt_count' => $f->receipt_count ?? 0,
                'debt_count' => $d->debt_count ?? 0, 'debt_total' => $d->debt_total ?? 0,
                'paid_debt_count' => $p->paid_count ?? 0, 'paid_debt_total' => $paidDebts,
                'cash_fees' => $cashFees, 'income' => $income, 'oil_sell' => $oilSell,
                'expense' => $expense, 'oil_buy' => $oilBuy,
                'oil_buy_debt' => $oilDebt->buy_debt ?? 0, 'oil_sell_debt' => $oilDebt->sell_debt ?? 0,
                'total_in' => $totalIn, 'total_out' => $totalOut, 'net' => $totalIn - $totalOut];
        });

        $totals = ['receipt_count' => $rows->sum('receipt_count'), 'debt_count' => $rows->sum('debt_count'),
            'debt_total' => $rows->sum('debt_total'), 'paid_debt_count' => $rows->sum('paid_debt_count'),
            'paid_debt_total' => $rows->sum('paid_debt_total'), 'cash_fees' => $rows->sum('cash_fees'),
            'income' => $rows->sum('income'), 'oil_sell' => $rows->sum('oil_sell'),
            'expense' => $rows->sum('expense'), 'oil_buy' => $rows->sum('oil_buy'),
            'oil_buy_debt' => $rows->sum('oil_buy_debt'), 'oil_sell_debt' => $rows->sum('oil_sell_debt'),
            'total_in' => $rows->sum('total_in'), 'total_out' => $rows->sum('total_out'),
            'net' => $rows->sum('net')];

        return compact('rows', 'totals', 'fromDate', 'toDate');
    }

    public function unlockFinancial(Request $request)
    {
        $savedPassword = DB::table('settings')->where('key', 'cashier_delete_password')->value('value');
        if ($savedPassword && $request->input('financial_password') === $savedPassword) {
            session(['financial_unlocked' => true]);
            return redirect()->route('reports.financial');
        }
        return back()->withErrors(['financial_password' => 'كلمة المرور غير صحيحة']);
    }

    public function settings()
    {
        if (!session('settings_unlocked')) {
            return view('settings', ['locked' => true, 'settings' => collect()]);
        }
        session()->forget('settings_unlocked');

        $settings   = DB::table('settings')->pluck('value', 'key');
        $activeYear = $this->getActiveYear();
        return view('settings', ['locked' => false, 'settings' => $settings, 'activeYear' => $activeYear]);
    }

    public function unlockSettings(Request $request)
    {
        $savedPassword = DB::table('settings')->where('key', 'cashier_delete_password')->value('value');
        if ($savedPassword && $request->input('settings_password') === $savedPassword) {
            session(['settings_unlocked' => true]);
            return redirect()->route('settings');
        }
        return back()->withErrors(['settings_password' => 'كلمة المرور غير صحيحة']);
    }

    public function updateSettings(Request $request)
    {
        $fields = ['press_name', 'fee_per_kg', 'press_phone', 'press_address', 'can_16_price', 'can_8_price', 'can_4_price', 'cashier_delete_password', 'active_year'];
        foreach ($fields as $field) {
            if ($request->has($field)) {
                DB::table('settings')->updateOrInsert(
                    ['key' => $field],
                    ['value' => $request->$field, 'updated_at' => now()]
                );
            }
        }
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'تم حفظ الإعدادات بنجاح']);
        }
        return redirect()->route('dashboard')->with('success', 'تم حفظ الإعدادات بنجاح');
    }

    public function bulkDelete(Request $request)
    {
        // التحقق من كلمة المرور
        $isJson = $request->expectsJson();
        $savedPassword = DB::table('settings')->where('key', 'cashier_delete_password')->value('value');
        if (!$savedPassword || $request->input('confirm_password') !== $savedPassword) {
            return $isJson
                ? response()->json(['success' => false, 'message' => 'كلمة المرور غير صحيحة — لم يتم الحذف'], 422)
                : back()->with('bulk_delete_error', 'كلمة المرور غير صحيحة — لم يتم الحذف');
        }

        $fromDate = $request->input('from_date');
        $toDate   = $request->input('to_date');

        if (!$fromDate || !$toDate) {
            return $isJson
                ? response()->json(['success' => false, 'message' => 'يرجى تحديد تاريخ البداية والنهاية'], 422)
                : back()->with('bulk_delete_error', 'يرجى تحديد تاريخ البداية والنهاية');
        }

        if ($fromDate > $toDate) {
            return $isJson
                ? response()->json(['success' => false, 'message' => 'تاريخ البداية يجب أن يكون قبل تاريخ النهاية'], 422)
                : back()->with('bulk_delete_error', 'تاريخ البداية يجب أن يكون قبل تاريخ النهاية');
        }

        $deleted = [];

        if ($request->boolean('del_receipts')) {
            $count = \App\Models\Receipt::whereDate('created_at', '>=', $fromDate)
                                        ->whereDate('created_at', '<=', $toDate)->count();
            \App\Models\Receipt::whereDate('created_at', '>=', $fromDate)
                               ->whereDate('created_at', '<=', $toDate)->delete();
            $deleted[] = "{$count} إيصال";
        }

        if ($request->boolean('del_oil_trades')) {
            $count = \App\Models\OilTrade::whereDate('created_at', '>=', $fromDate)
                                         ->whereDate('created_at', '<=', $toDate)->count();
            \App\Models\OilTrade::whereDate('created_at', '>=', $fromDate)
                                ->whereDate('created_at', '<=', $toDate)->delete();
            $deleted[] = "{$count} سجل بيع/شراء زيت";
        }

        if ($request->boolean('del_expenses')) {
            $count = \App\Models\Expense::whereDate('created_at', '>=', $fromDate)
                                         ->whereDate('created_at', '<=', $toDate)->count();
            \App\Models\Expense::whereDate('created_at', '>=', $fromDate)
                               ->whereDate('created_at', '<=', $toDate)->delete();
            $deleted[] = "{$count} مصروف/وارد";
        }

        if (empty($deleted)) {
            return $isJson
                ? response()->json(['success' => false, 'message' => 'لم تختر أي جدول للحذف'], 422)
                : back()->with('bulk_delete_error', 'لم تختر أي جدول للحذف');
        }

        $msg = 'تم الحذف بنجاح للفترة من ' . $fromDate . ' إلى ' . $toDate . ' — ' . implode(' | ', $deleted);
        return $isJson
            ? response()->json(['success' => true, 'message' => $msg])
            : redirect()->route('dashboard')->with('bulk_delete_success', $msg);
    }
}
