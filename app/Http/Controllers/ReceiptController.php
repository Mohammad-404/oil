<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\OilTrade;
use App\Models\Owner;
use App\Models\Receipt;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Barryvdh\DomPDF\Facade\Pdf;

class ReceiptController extends Controller
{
    public function index(Request $request)
    {
        $activeYear = $this->getActiveYear();
        $query = $this->applyYearFilter(Receipt::with('owner')->latest(), $activeYear);

        if ($request->filled('search') && $request->user()->role === 'user') {
            $this->applyUserReceiptSearch($query, trim($request->input('search')));
        } elseif ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('receipt_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('owner', fn($o) => $o->where('name', 'like', '%' . $request->search . '%'));
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->from_date) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->to_date) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $receipts = $query->paginate(15)->withQueryString();

        // ── ملخص (للكاشير فقط) — يعكس الفلتر المطبّق ──
        $summary = null;
        if (auth()->check() && auth()->user()->isCashier()) {
            $baseQuery = $this->applyYearFilter(Receipt::query(), $activeYear);
            if ($request->search) {
                $baseQuery->where(function ($q) use ($request) {
                    $q->where('receipt_number', 'like', '%' . $request->search . '%')
                      ->orWhereHas('owner', fn($o) => $o->where('name', 'like', '%' . $request->search . '%'));
                });
            }
            if ($request->status)    { $baseQuery->where('status', $request->status); }
            if ($request->from_date) { $baseQuery->whereDate('created_at', '>=', $request->from_date); }
            if ($request->to_date)   { $baseQuery->whereDate('created_at', '<=', $request->to_date); }

            $oilBase = fn($type) => $this->oilCashQuery($type, $request, $activeYear);
            $oilBuyTotal  = $oilBase('buy')->sum('total_amount');
            $oilSellTotal = $oilBase('sell')->sum('total_amount');
            $expBase = fn($type) => $this->applyYearFilter(Expense::where('type', $type), $activeYear)
                ->when($request->from_date, fn($q) => $q->whereDate('created_at', '>=', $request->from_date))
                ->when($request->to_date,   fn($q) => $q->whereDate('created_at', '<=', $request->to_date));
            $expenseTotal = $expBase('expense')->sum('amount');
            $incomeTotal  = $expBase('income')->sum('amount');

            // الأجرة النقدية = إيصالات غير دين وغير رديات زيت
            // + مبلغ التنك فقط لإيصالات رديات الزيت التي تنكها ليست مسواة بالرديات
            $cashFees = (clone $baseQuery)
                            ->where('status', '!=', 'debt')
                            ->where('payment_method', '!=', 'oil_return')
                            ->sum('total_fee')
                        + (clone $baseQuery)
                            ->where('status', '!=', 'debt')
                            ->where('payment_method', 'oil_return')
                            ->where('cans_in_return', false)
                            ->sum('discounted_can_total');

            $summary = [
                'totalReceipts' => (clone $baseQuery)->count(),
                'totalOlive'    => (clone $baseQuery)->sum('olive_weight_kg'),
                'totalOil'      => (clone $baseQuery)->sum('oil_weight_kg'),
                'cashFees'      => $cashFees,
                'netCashFees'   => $cashFees - $oilBuyTotal + $oilSellTotal - $expenseTotal + $incomeTotal,
                'oilBuyTotal'   => $oilBuyTotal,
                'oilSellTotal'  => $oilSellTotal,
                'expenseTotal'  => $expenseTotal,
                'incomeTotal'   => $incomeTotal,
                // رديات الزيت: فقط المكتملة (تم استلام الزيت فعلاً)
                'oilReturnKg'      => (clone $baseQuery)->where('payment_method', 'oil_return')->where('status', '!=', 'debt')->sum('oil_return_kg'),
                'debtCount'        => (clone $baseQuery)->where('status', 'debt')->count(),
                // الديون المالية فقط (نستثني ديون رديات الزيت)
                'debtTotal'        => (clone $baseQuery)->where('status', 'debt')->where('payment_method', '!=', 'oil_return')->sum('total_fee'),
                // ديون رديات الزيت (بالكيلو)
                'debtOilKg'        => (clone $baseQuery)->where('status', 'debt')->where('payment_method', 'oil_return')->sum('oil_return_kg'),
                'debtPaidCount'    => (clone $baseQuery)->whereNotNull('debt_paid_at')->count(),
                // نقداً فقط — رديات الزيت المسددة لا تُحسب في الأجور النقدية
                'debtPaidTotal'    => (clone $baseQuery)->whereNotNull('debt_paid_at')->where('payment_method', '!=', 'oil_return')->sum('total_fee'),
                'pendingCount'     => (clone $baseQuery)->where('status', 'pending')->count(),
                'processingCount'  => (clone $baseQuery)->where('status', 'processing')->count(),
                'completedCount'   => (clone $baseQuery)->where('status', 'completed')->count(),
                'oilStock'         => OilTrade::totalOilStock($activeYear),
                'oilBuyKg'         => $this->applyYearFilter(OilTrade::where('type', 'buy'), $activeYear)->sum('quantity_kg'),
                'oilSellKg'        => $this->applyYearFilter(OilTrade::where('type', 'sell'), $activeYear)->sum('quantity_kg'),
                'isFiltered'       => $request->hasAny(['search', 'status', 'from_date', 'to_date']),
            ];
        }

        // AJAX filter: skip lock check, return rendered partial as JSON
        if ($request->expectsJson()) {
            return response()->json([
                'html' => view('receipts._results', compact('receipts', 'summary'))->render(),
            ]);
        }

        // حماية التقارير بكلمة مرور للكاشير
        if (auth()->user()->isCashier() && !session('reports_unlocked')) {
            return view('receipts.index', ['locked' => true, 'receipts' => $receipts, 'summary' => null]);
        }
        session()->forget('reports_unlocked');

        return view('receipts.index', compact('receipts', 'summary'));
    }

    public function unlockReports(Request $request)
    {
        $savedPassword = \DB::table('settings')->where('key', 'cashier_delete_password')->value('value');
        if ($savedPassword && $request->input('reports_password') === $savedPassword) {
            session(['reports_unlocked' => true]);
            return redirect()->route('receipts.index');
        }
        return back()->withErrors(['reports_password' => 'كلمة المرور غير صحيحة']);
    }

    public function create(Request $request)
    {
        $owners = Owner::orderBy('name')->get();
        $fee_per_kg = \DB::table('settings')->where('key', 'fee_per_kg')->value('value') ?? 2;
        $selectedOwner = $request->owner_id ? Owner::find($request->owner_id) : null;
        return view('receipts.create', compact('owners', 'fee_per_kg', 'selectedOwner'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'owner_id'        => 'required|exists:owners,id',
            'carrier_name'    => 'required|string|max:255',
            'olive_weight_kg' => 'required|numeric|min:0.1',
            'floor_number'    => 'nullable|integer',
            'fee_per_kg'      => 'required|numeric|min:0',
            'notes'           => 'nullable|string',
        ], [
            'owner_id.required'        => 'يرجى اختيار صاحب الزيتون',
            'carrier_name.required'    => 'اسم حامل الزيتون مطلوب',
            'olive_weight_kg.required' => 'كمية الزيتون مطلوبة',
            'fee_per_kg.required'      => 'سعر العصر مطلوب',
        ]);

        $validated['status'] = 'pending'; // كمية الزيت تُدخل لاحقاً عند الكاشير

        $receipt = Receipt::create($validated);

        // يوزر عادي → طباعة الإيصال مباشرة، كاشير → صفحة التفاصيل
        if (auth()->user()->role === 'user') {
            return redirect()->route('receipts.print', $receipt)
                ->with('success', 'تم إنشاء الإيصال بنجاح - رقم: ' . $receipt->receipt_number);
        }

        return redirect()->route('receipts.show', $receipt)
            ->with('success', 'تم إنشاء الإيصال بنجاح - رقم: ' . $receipt->receipt_number);
    }

    public function show(Receipt $receipt)
    {
        $receipt->load('owner');
        return view('receipts.show', compact('receipt'));
    }

    public function edit(Receipt $receipt)
    {
        // الكاشير يحتاج إلى كلمة مرور للتعديل
        if (auth()->user()->isCashier() && !session('receipt_edit_unlocked_' . $receipt->id)) {
            return redirect()->route('receipts.index')
                ->with('edit_required', $receipt->id);
        }
        $owners = Owner::orderBy('name')->get();
        return view('receipts.edit', compact('receipt', 'owners'));
    }

    public function update(Request $request, Receipt $receipt)
    {
        $validated = $request->validate([
            'owner_id'        => 'required|exists:owners,id',
            'carrier_name'    => 'required|string|max:255',
            'olive_weight_kg' => 'required|numeric|min:0',
            'oil_weight_kg'   => 'nullable|numeric|min:0',
            'oil_weight_gram' => 'nullable|numeric|min:0',
            'floor_number'    => 'nullable|integer',
            'fee_per_kg'      => 'required|numeric|min:0',
            'driver_fee'      => 'nullable|numeric|min:0',
            'status'          => 'required|in:pending,processing,completed,debt',
            'notes'           => 'nullable|string',
        ]);

        $validated['driver_fee'] = $validated['driver_fee'] ?? 0;
        $receipt->update($validated);

        session()->forget('receipt_edit_unlocked_' . $receipt->id);

        return redirect()->route('receipts.show', $receipt)
            ->with('success', 'تم تحديث الإيصال بنجاح');
    }

    public function requestEdit(Request $request, Receipt $receipt)
    {
        $savedPassword = \DB::table('settings')->where('key', 'cashier_delete_password')->value('value');
        if ($savedPassword && $request->input('edit_password') === $savedPassword) {
            session(['receipt_edit_unlocked_' . $receipt->id => true]);
            return redirect()->route('receipts.edit', $receipt);
        }
        return back()->with('edit_error', $receipt->id);
    }

    public function destroy(Receipt $receipt)
    {
        if ($receipt->status !== 'pending') {
            return redirect()->route('receipts.index')
                ->with('error', 'لا يمكن حذف الإيصال رقم ' . $receipt->receipt_number . ' — يُسمح بالحذف فقط للإيصالات في حالة "انتظار"');
        }
        $num = $receipt->receipt_number;
        $receipt->delete();
        return redirect()->route('receipts.index')
            ->with('success', 'تم حذف الإيصال رقم ' . $num);
    }

    public function userReceipts(Request $request)
    {
        $query = Receipt::with('owner')->latest();

        if ($request->filled('search')) {
            $this->applyUserReceiptSearch($query, trim($request->input('search')));
        }

        $receipts = $query->paginate(20)->withQueryString();
        return view('user.receipts', compact('receipts'));
    }

    private function applyUserReceiptSearch(\Illuminate\Database\Eloquent\Builder $query, string $search): void
    {
        $query->where(function ($q) use ($search) {
            $q->whereHas('owner', fn($owner) => $owner->where('name', 'like', '%' . $search . '%'));
            if (ctype_digit($search)) {
                $q->orWhere('floor_number', $search);
            }
        });
    }

    private function oilCashQuery(string $type, Request $request, ?int $activeYear): \Illuminate\Database\Eloquent\Builder
    {
        $query = OilTrade::where('type', $type);
        $hasPaymentStatus = Schema::hasColumn('oil_trades', 'payment_status');
        if ($hasPaymentStatus) {
            $query->where('payment_status', 'paid');
        }

        $cashDate = $hasPaymentStatus && Schema::hasColumn('oil_trades', 'debt_paid_at')
            ? DB::raw('COALESCE(debt_paid_at, created_at)')
            : 'created_at';

        if ($request->from_date) $query->whereDate($cashDate, '>=', $request->from_date);
        if ($request->to_date) $query->whereDate($cashDate, '<=', $request->to_date);
        if ($activeYear) $query->whereYear($cashDate, $activeYear);

        return $query;
    }

    public function printReceipt(Receipt $receipt)
    {
        $receipt->load('owner');
        $pressName  = \DB::table('settings')->where('key', 'press_name')->value('value')  ?? 'معصرة الزيتون';
        $pressPhone = \DB::table('settings')->where('key', 'press_phone')->value('value') ?? '';
        $canPrices  = \App\Models\Receipt::getCanPrices();
        return view('receipts.print', compact('receipt', 'pressName', 'pressPhone', 'canPrices'));
    }

    public function printRow(Receipt $receipt)
    {
        $receipt->load('owner');
        $pressName = \DB::table('settings')->where('key', 'press_name')->value('value') ?? 'معصرة الزيتون';
        $canPrices = \App\Models\Receipt::getCanPrices();
        return view('receipts.print-row', compact('receipt', 'pressName', 'canPrices'));
    }

    public function printTable(Request $request)
    {
        $activeYear = $this->getActiveYear();
        $query = $this->applyYearFilter(Receipt::with('owner')->latest(), $activeYear);
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('receipt_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('owner', fn($o) => $o->where('name', 'like', '%' . $request->search . '%'));
            });
        }
        if ($request->status)    { $query->where('status', $request->status); }
        if ($request->from_date) { $query->whereDate('created_at', '>=', $request->from_date); }
        if ($request->to_date)   { $query->whereDate('created_at', '<=', $request->to_date); }
        $receipts     = $query->get();
        $pressName    = \DB::table('settings')->where('key', 'press_name')->value('value') ?? 'معصرة الزيتون';
        $pressPhone   = \DB::table('settings')->where('key', 'press_phone')->value('value') ?? '';
        $cashBaseQuery = $this->applyYearFilter(Receipt::query(), $activeYear);
        if ($request->search) {
            $cashBaseQuery->where(function ($q) use ($request) {
                $q->where('receipt_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('owner', fn($o) => $o->where('name', 'like', '%' . $request->search . '%'));
            });
        }
        if ($request->status)    { $cashBaseQuery->where('status', $request->status); }
        if ($request->from_date) { $cashBaseQuery->whereDate('created_at', '>=', $request->from_date); }
        if ($request->to_date)   { $cashBaseQuery->whereDate('created_at', '<=', $request->to_date); }
        $cashTotal    = (clone $cashBaseQuery)->where('status', '!=', 'debt')->where('payment_method', '!=', 'oil_return')->sum('total_fee')
                      + (clone $cashBaseQuery)->where('status', '!=', 'debt')->where('payment_method', 'oil_return')->where('cans_in_return', false)->sum('discounted_can_total');
        $oilBase      = fn($type) => $this->oilCashQuery($type, $request, $activeYear);
        $oilBuyTotal  = $oilBase('buy')->sum('total_amount');
        $oilSellTotal = $oilBase('sell')->sum('total_amount');
        $expBase      = fn($type) => $this->applyYearFilter(Expense::where('type', $type), $activeYear)
                            ->when($request->from_date, fn($q) => $q->whereDate('created_at', '>=', $request->from_date))
                            ->when($request->to_date,   fn($q) => $q->whereDate('created_at', '<=', $request->to_date));
        $expenseTotal = $expBase('expense')->sum('amount');
        $incomeTotal  = $expBase('income')->sum('amount');
        $netCashFees  = $cashTotal - $oilBuyTotal + $oilSellTotal - $expenseTotal + $incomeTotal;
        $oilRetKg     = $receipts->where('payment_method', 'oil_return')->where('status', '!=', 'debt')->sum('oil_return_kg');
        $debtTotal    = $receipts->where('status', 'debt')->where('payment_method', '!=', 'oil_return')->sum('total_fee');
        $debtOilKg    = $receipts->where('status', 'debt')->where('payment_method', 'oil_return')->sum('oil_return_kg');
        $oliveTotal   = $receipts->sum('olive_weight_kg');
        $oilTotal     = $receipts->sum('oil_weight_kg');
        $canPrices    = \App\Models\Receipt::getCanPrices();
        return view('receipts.print-table', compact('receipts', 'pressName', 'pressPhone', 'cashTotal', 'netCashFees', 'oilBuyTotal', 'oilSellTotal', 'expenseTotal', 'incomeTotal', 'oilRetKg', 'debtTotal', 'debtOilKg', 'oliveTotal', 'oilTotal', 'canPrices'));
    }

    public function cashier(Receipt $receipt)
    {
        $receipt->load('owner');
        return view('cashier.show', compact('receipt'));
    }

    public function updateOil(Request $request, Receipt $receipt)
    {
        $validated = $request->validate([
            'oil_weight_kg'   => 'required|numeric|min:0',
            'oil_weight_gram' => 'nullable|numeric|min:0',
            'status'          => 'required|in:pending,processing,completed,debt',
        ]);

        $receipt->update($validated);

        return redirect()->route('receipts.show', $receipt)
            ->with('success', 'تم تحديث كمية الزيت بنجاح');
    }

    public function exportPdf(Receipt $receipt)
    {
        $receipt->load('owner');
        $pressName = \DB::table('settings')->where('key', 'press_name')->value('value') ?? 'معصرة الزيتون';
        $pdf = Pdf::loadView('receipts.pdf', compact('receipt', 'pressName'));
        $pdf->setPaper('A5');
        return $pdf->download('receipt-' . $receipt->receipt_number . '.pdf');
    }
}
