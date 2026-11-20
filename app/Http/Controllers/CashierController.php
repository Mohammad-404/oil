<?php

namespace App\Http\Controllers;

use App\Models\Owner;
use App\Models\OilTrade;
use App\Models\Receipt;
use Illuminate\Http\Request;

class CashierController extends Controller
{
    public function index(Request $request)
    {
        $owner    = null;
        $receipts = collect();
        $receiptNotFound = false;

        $statusFilter = $request->input('status', '');

        if ($request->filled('receipt_number')) {
            // بحث برقم الإيصال → يعرض هذا الإيصال فقط
            $found = Receipt::with('owner')->where('receipt_number', trim($request->receipt_number))->first();
            if ($found) {
                $owner    = $found->owner;
                $receipts = collect([$found]);
            } else {
                $receiptNotFound = true;
            }
        } elseif ($request->filled('floor_number')) {
            $owner = Owner::where('floor_number', $request->floor_number)
                          ->with(['receipts' => function($q) use ($statusFilter) {
                              if ($statusFilter) $q->where('status', $statusFilter);
                              $q->latest();
                          }])
                          ->first();

            if ($owner) {
                $receipts = $owner->receipts;
            }
        }

        $canPrices = \App\Models\Receipt::getCanPrices();
        return view('cashier.index', compact('owner', 'receipts', 'canPrices', 'statusFilter', 'receiptNotFound'));
    }

    /**
     * تحديث بيانات العصر والدفع من الكاشير
     */
    public function updateOil(Request $request, Receipt $receipt)
    {
        $validated = $request->validate([
            'oil_weight_kg'   => 'required|numeric|min:0',
            'oil_weight_gram' => 'nullable|numeric|min:0|max:999',
            'fee_per_kg'      => 'required|numeric|min:0',
            'payment_method'  => 'required|in:cash,oil_return,can',
            'oil_return_kg'   => 'nullable|numeric|min:0',
            'oil_return_gram' => 'nullable|numeric|min:0|max:999',
            'can_16_count'    => 'nullable|integer|min:0',
            'can_8_count'     => 'nullable|integer|min:0',
            'can_4_count'     => 'nullable|integer|min:0',
            'cans_in_return'     => 'nullable|boolean',
            'oil_return_divisor' => 'nullable|numeric|min:0.001',
            'can_return_divisor' => 'nullable|numeric|min:0.001',
            'driver_fee'         => 'nullable|numeric|min:0',
            'discount_amount'    => 'nullable|numeric|decimal:0,3|min:0|max:9999999.999',
            'status'          => 'required|in:pending,processing,completed,debt',
        ]);

        $validated['oil_return_kg']   = $validated['oil_return_kg']   ?? 0;
        $validated['oil_return_gram'] = $validated['oil_return_gram'] ?? 0;
        $validated['can_16_count']    = $validated['can_16_count']    ?? 0;
        $validated['can_8_count']     = $validated['can_8_count']     ?? 0;
        $validated['can_4_count']     = $validated['can_4_count']     ?? 0;
        $validated['cans_in_return']     = isset($validated['cans_in_return']) ? (bool)$validated['cans_in_return'] : false;
        $validated['oil_return_divisor'] = $validated['oil_return_divisor'] ?? 5.5;
        $validated['can_return_divisor'] = $validated['can_return_divisor'] ?? 5.5;
        $validated['driver_fee']         = $validated['driver_fee']         ?? 0;
        $validated['discount_amount']    = $validated['discount_amount']    ?? 0;
        $validated['discount_percent']   = 0;

        // تحذير: إذا حاول تغيير إيصال مسدَّد سابقاً إلى دين
        if (($validated['status'] ?? '') === 'debt' && $receipt->debt_paid_at) {
            return back()->with('warning',
                'تنبيه: الإيصال رقم ' . $receipt->receipt_number .
                ' كان مسدَّداً بالفعل بتاريخ ' . \Carbon\Carbon::parse($receipt->debt_paid_at)->format('Y/m/d') .
                ' — لم يتم تغيير الحالة. إذا أردت المتابعة، عدّل الحالة من صفحة تعديل الإيصال مباشرة.'
            );
        }

        // press_fee & total_fee & can_total تُحسب تلقائياً في model boot
        $receipt->update($validated);

        // Recalculate oil settlement from the saved, discounted amounts on the server.
        $returnKg = 0;
        if ($receipt->payment_method === 'oil_return') {
            $includesCans = $receipt->cans_in_return && $receipt->can_total > 0;
            $divisor = (float) ($includesCans ? $receipt->can_return_divisor : $receipt->oil_return_divisor);
            $amountCovered = $includesCans ? (float) $receipt->total_fee : $receipt->discounted_press_fee;
            $returnKg = $divisor > 0 ? round($amountCovered / $divisor, 3) : 0;
        }
        $receipt->update(['oil_return_kg' => $returnKg, 'oil_return_gram' => 0]);

        // إعادة القفل بعد كل حفظ
        session()->forget('cashier_unlocked_' . $receipt->id);

        return back()->with('success', 'تم تحديث إيصال رقم ' . $receipt->receipt_number . ' بنجاح');
    }

    /**
     * صفحة الدين — عرض إيصال معلّق واحد مع ديون المالك الأخرى
     */
    public function debt(Receipt $receipt)
    {
        $receipt->load('owner');
        $owner = $receipt->owner;
        if (!$owner || !$owner->id) {
            return redirect()->route('cashier.debts')->with('error', 'لم يتم العثور على بيانات صاحب الإيصال');
        }
        $owner->load(['receipts' => fn($q) => $q->where('status', 'debt')->latest()]);
        $pressName = \DB::table('settings')->where('key', 'press_name')->value('value') ?? 'معصرة الزيتون';
        return view('cashier.debt', compact('receipt', 'owner', 'pressName'));
    }

    /**
     * طباعة وصل الدين (standalone)
     */
    public function debtPrint(Receipt $receipt)
    {
        $receipt->load('owner');
        $owner = $receipt->owner;
        if (!$owner || !$owner->id) {
            return redirect()->route('cashier.debts')->with('error', 'لم يتم العثور على بيانات صاحب الإيصال');
        }
        $pressName      = \DB::table('settings')->where('key', 'press_name')->value('value')  ?? 'معصرة الزيتون';
        $pressPhone     = \DB::table('settings')->where('key', 'press_phone')->value('value') ?? '';
        $totalOwnerDebt = Receipt::where('owner_id', $owner->id)->where('status', 'debt')->sum('total_fee');
        $canPrices      = \App\Models\Receipt::getCanPrices();
        return view('cashier.debt-print', compact('receipt', 'owner', 'pressName', 'pressPhone', 'totalOwnerDebt', 'canPrices'));
    }

    /**
     * صفحة مبيعات الزيت — إيصالات رديات الزيت
     */
    public function oilSales(Request $request)
    {
        $activeYear = $this->getActiveYear();
        $query = $this->applyYearFilter(Receipt::with('owner')->where('payment_method', 'oil_return')->latest(), $activeYear);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('receipt_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('owner', fn($o) => $o->where('name', 'like', '%' . $request->search . '%'));
            });
        }

        $receipts      = $query->paginate(20)->withQueryString();
        $totalOilRetKg = $this->applyYearFilter(Receipt::where('payment_method', 'oil_return')->where('status', '!=', 'debt'), $activeYear)->sum('oil_return_kg');
        $totalCount    = $this->applyYearFilter(Receipt::where('payment_method', 'oil_return'), $activeYear)->count();
        $totalBuyKg    = $this->applyYearFilter(OilTrade::where('type', 'buy'), $activeYear)->sum('quantity_kg');
        $totalSoldKg   = $this->applyYearFilter(OilTrade::where('type', 'sell'), $activeYear)->sum('quantity_kg');
        $netOilKg      = $totalOilRetKg + $totalBuyKg - $totalSoldKg;
        return view('cashier.oil_sales', compact('receipts', 'totalOilRetKg', 'totalCount', 'totalBuyKg', 'totalSoldKg', 'netOilKg'));
    }

    /**
     * فتح حقول الكاشير للتعديل بكلمة مرور
     */
    public function unlockForm(Request $request, Receipt $receipt)
    {
        $savedPassword = \DB::table('settings')->where('key', 'cashier_delete_password')->value('value');
        if ($savedPassword && $request->input('unlock_password') === $savedPassword) {
            session(['cashier_unlocked_' . $receipt->id => true]);
            return back()->with('unlock_success', $receipt->id);
        }
        return back()->with('unlock_error', $receipt->id);
    }

    /**
     * قائمة جميع الإيصالات المعلّقة (الديون)
     */
    public function deleteWithPassword(Request $request, Receipt $receipt)
    {
        if ($receipt->status !== 'pending') {
            if ($request->expectsJson())
                return response()->json(['success' => false, 'message' => 'لا يمكن حذف هذا الإيصال — يُسمح بالحذف فقط للإيصالات في حالة "انتظار"'], 422);
            return back()->with('delete_error', 'لا يمكن حذف هذا الإيصال')->with('failed_receipt', $receipt->id);
        }
        $savedPassword = \DB::table('settings')->where('key', 'cashier_delete_password')->value('value');
        if (!$savedPassword || $request->input('delete_password') !== $savedPassword) {
            if ($request->expectsJson())
                return response()->json(['success' => false, 'message' => 'كلمة المرور غير صحيحة'], 422);
            return back()->with('delete_error', 'كلمة المرور غير صحيحة')->with('failed_receipt', $receipt->id);
        }
        $num = $receipt->receipt_number;
        $receipt->delete();
        if ($request->expectsJson())
            return response()->json(['success' => true, 'message' => 'تم حذف الإيصال رقم ' . $num]);
        return back()->with('success', 'تم حذف الإيصال رقم ' . $num);
    }

    /**
     * تسديد الدين — تحويل الإيصال من دين إلى مكتمل نقداً
     */
    public function markDebtPaid(Receipt $receipt)
    {
        if ($receipt->status !== 'debt') {
            return back()->with('error', 'هذا الإيصال ليس ديناً');
        }

        // إيصال كان مسدَّداً سابقاً وأُعيد كدين → غيّر الحالة فقط بدون سجل جديد
        if ($receipt->debt_paid_at) {
            $receipt->status = 'completed';
            $receipt->save();
            return back()->with('info',
                'الإيصال ' . $receipt->receipt_number .
                ' كان مسدَّداً بالفعل بتاريخ ' . \Carbon\Carbon::parse($receipt->debt_paid_at)->format('Y/m/d') .
                ' — تم تحديث الحالة إلى مكتمل تلقائياً'
            );
        }

        // سداد جديد
        $paidNote = 'تم السداد بتاريخ ' . now()->format('Y/m/d H:i');
        $receipt->status       = 'completed';
        $receipt->debt_paid_at = now();
        $receipt->notes        = $receipt->notes ? $receipt->notes . ' | ' . $paidNote : $paidNote;
        // ديون رديات الزيت: نبقي payment_method = oil_return حتى يُحتسب الزيت في المجاميع
        if ($receipt->payment_method !== 'oil_return') {
            $receipt->payment_method = 'cash';
        }
        $receipt->save();

        $suffix = $receipt->payment_method === 'oil_return'
            ? ' — سيُضاف ' . number_format($receipt->oil_return_kg, 2) . ' كيلو زيت لمجموع الرديات'
            : '';
        return back()->with('success', 'تم تسديد الدين وتحديث الإيصال رقم ' . $receipt->receipt_number . ' إلى مكتمل' . $suffix);
    }

    public function debts(Request $request)
    {
        $activeYear = $this->getActiveYear();

        // ── الديون النشطة ──
        $query = $this->applyYearFilter(Receipt::with('owner')->where('status', 'debt')->latest(), $activeYear);
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('receipt_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('owner', fn($o) => $o->where('name', 'like', '%' . $request->search . '%'));
            });
        }
        $receipts       = $query->paginate(20, ['*'], 'debt_page')->withQueryString();
        $debtBase       = $this->applyYearFilter(Receipt::where('status', 'debt'), $activeYear);
        $totalDebt      = (clone $debtBase)->where('payment_method', '!=', 'oil_return')->sum('total_fee');
        $totalDebtOilKg = (clone $debtBase)->where('payment_method', 'oil_return')->sum('oil_return_kg');
        $totalCount     = (clone $debtBase)->count();

        // ── الديون المسدّدة ──
        $paidSearch   = $request->input('paid_search', '');
        $paidFromDate = $request->input('paid_from');
        $paidToDate   = $request->input('paid_to');

        $paidQuery = $this->applyYearFilter(
            Receipt::with('owner')->whereNotNull('debt_paid_at')->latest('debt_paid_at'),
            $activeYear
        );
        if ($paidSearch) {
            $paidQuery->where(function ($q) use ($paidSearch) {
                $q->where('receipt_number', 'like', '%' . $paidSearch . '%')
                  ->orWhereHas('owner', fn($o) => $o->where('name', 'like', '%' . $paidSearch . '%'));
            });
        }
        if ($paidFromDate) $paidQuery->whereDate('debt_paid_at', '>=', $paidFromDate);
        if ($paidToDate)   $paidQuery->whereDate('debt_paid_at', '<=', $paidToDate);

        $paidTotal      = (clone $paidQuery)->where('payment_method', '!=', 'oil_return')->sum('total_fee');
        $paidOilKg      = (clone $paidQuery)->where('payment_method', 'oil_return')->sum('oil_return_kg');
        $paidCount      = (clone $paidQuery)->count();
        $paidReceipts   = $paidQuery->paginate(20, ['*'], 'paid_page')->withQueryString();

        return view('cashier.debts', compact(
            'receipts', 'totalDebt', 'totalDebtOilKg', 'totalCount',
            'paidReceipts', 'paidTotal', 'paidOilKg', 'paidCount',
            'paidSearch', 'paidFromDate', 'paidToDate'
        ));
    }

    /**
     * صفحة طباعة التفاصيل الكاملة للزبون (بدون أجرة الناقل)
     */
    public function printSummary(Owner $owner)
    {
        $owner->load(['receipts' => fn($q) => $q->latest()]);
        $pressName  = \DB::table('settings')->where('key', 'press_name')->value('value')  ?? 'معصرة الزيتون';
        $pressPhone = \DB::table('settings')->where('key', 'press_phone')->value('value') ?? '';
        $canPrices  = \App\Models\Receipt::getCanPrices();
        return view('cashier.print', compact('owner', 'pressName', 'pressPhone', 'canPrices'));
    }

    /**
     * صفحة أجور ناقلي الزيتون (سجل داخلي)
     */
    public function driverFees(Request $request)
    {
        $activeYear = $this->getActiveYear();
        $date    = $request->input('date');
        $carrier = $request->input('carrier');

        $query = $this->applyYearFilter(Receipt::with('owner')->latest(), $activeYear);

        if ($date) {
            $query->whereDate('created_at', $date);
        }

        if ($carrier) {
            $query->where('carrier_name', 'like', '%' . $carrier . '%');
        }

        $receipts     = $query->paginate(20)->withQueryString();
        $sumBase      = $this->applyYearFilter(Receipt::query(), $activeYear)
                             ->when($date,    fn($q) => $q->whereDate('created_at', $date))
                             ->when($carrier, fn($q) => $q->where('carrier_name', 'like', '%'.$carrier.'%'));
        $totalOliveKg = (clone $sumBase)->sum('olive_weight_kg');
        $totalCount   = (clone $sumBase)->count();

        return view('cashier.driver_fees', compact('receipts', 'totalOliveKg', 'totalCount'));
    }

    /**
     * تحديث أجرة الناقل فقط (معلومة داخلية للمعصرة)
     */
    public function updateDriverFee(Request $request, Receipt $receipt)
    {
        $request->validate([
            'olive_weight_kg' => 'required|numeric|min:0',
        ]);

        \DB::table('receipts')
            ->where('id', $receipt->id)
            ->update(['olive_weight_kg' => $request->olive_weight_kg]);

        return back()->with('success', 'تم تحديث كمية الزيتون');
    }
}
