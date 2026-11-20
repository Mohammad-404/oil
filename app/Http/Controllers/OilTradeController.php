<?php

namespace App\Http\Controllers;

use App\Models\OilTrade;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class OilTradeController extends Controller
{
    public function index(Request $request)
    {
        $activeYear = $this->getActiveYear();
        $search    = trim($request->input('search', ''));
        $fromDate  = $request->input('from_date');
        $toDate    = $request->input('to_date');
        $paymentStatus = $request->input('payment_status', '');
        $request->validate([
            'from_date' => 'nullable|date_format:Y-m-d',
            'to_date' => 'nullable|date_format:Y-m-d',
            'payment_status' => 'nullable|in:paid,debt',
        ]);
        $isFiltered = $search || $fromDate || $toDate || $paymentStatus;

        $base = function (string $type) use ($activeYear, $search, $fromDate, $toDate, $paymentStatus) {
            $q = $this->applyYearFilter(OilTrade::where('type', $type)->latest(), $activeYear);
            if ($search)   $q->where(fn($q) => $q->where('notes', 'like', '%' . $search . '%')
                                               ->orWhere('counterparty_name', 'like', '%' . $search . '%'));
            if ($fromDate) $q->whereDate('created_at', '>=', $fromDate);
            if ($toDate)   $q->whereDate('created_at', '<=', $toDate);
            if ($paymentStatus) $q->where('payment_status', $paymentStatus);
            return $q;
        };

        $buys  = $base('buy')->paginate(15, ['*'], 'buy_page')->withQueryString();
        $sells = $base('sell')->paginate(15, ['*'], 'sell_page')->withQueryString();

        $totalBuyKg   = (clone $base('buy'))->sum('quantity_kg');
        $totalSellKg  = (clone $base('sell'))->sum('quantity_kg');
        $totalBuyVal  = (clone $base('buy'))->sum('total_amount');
        $totalSellVal = (clone $base('sell'))->sum('total_amount');
        $totalBuyDebt = $base('buy')->outstandingDebt()->sum('total_amount');
        $totalSellDebt = $base('sell')->outstandingDebt()->sum('total_amount');
        $totalBuyPaid = $base('buy')->paid()->sum('total_amount');
        $totalSellPaid = $base('sell')->paid()->sum('total_amount');
        $oilStock     = OilTrade::totalOilStock($activeYear);

        return view('cashier.oil_trade', compact(
            'buys', 'sells',
            'totalBuyKg', 'totalSellKg',
            'totalBuyVal', 'totalSellVal',
            'totalBuyDebt', 'totalSellDebt', 'totalBuyPaid', 'totalSellPaid', 'paymentStatus',
            'oilStock',
            'search', 'fromDate', 'toDate', 'isFiltered'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type'         => 'required|in:buy,sell',
            'quantity_kg'  => 'required|numeric|decimal:0,3|min:0.001|max:9999999.999',
            'total_amount' => 'required|numeric|decimal:0,3|min:0|max:9999999.999',
            'notes'        => 'nullable|string|max:500',
            'payment_status' => 'sometimes|required|in:paid,debt',
            'counterparty_name' => 'nullable|required_if:payment_status,debt|string|max:150',
        ]);

        $validated['payment_status'] = $validated['payment_status'] ?? 'paid';
        $validated['total_amount'] = round((float) $validated['total_amount'], 3);
        // العمود القديم مطلوب في الجدول؛ نحفظ متوسط السعر للكيلو للعرض فقط.
        $validated['price_per_kg'] = round($validated['total_amount'] / (float) $validated['quantity_kg'], 3);
        if ($validated['price_per_kg'] > 9999999.999) {
            throw ValidationException::withMessages(['total_amount' => 'المبلغ كبير جداً مقارنة بالكمية المدخلة.']);
        }
        OilTrade::create($validated);

        $msg = $validated['type'] === 'buy' ? 'تم تسجيل شراء الزيت بنجاح' : 'تم تسجيل بيع الزيت بنجاح';
        return back()->with('success', $msg);
    }

    public function settle(OilTrade $oilTrade)
    {
        // A conditional update makes repeated/double-clicked payments idempotent.
        $updated = OilTrade::whereKey($oilTrade->id)->outstandingDebt()->update([
            'payment_status' => 'paid',
            'debt_paid_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', $updated
            ? 'تم تسديد الدين بالكامل وتسجيل المبلغ النقدي بتاريخ اليوم'
            : 'هذه العملية مسددة بالفعل');
    }

    public function destroy(Request $request, OilTrade $oilTrade)
    {
        $savedPassword = \DB::table('settings')->where('key', 'cashier_delete_password')->value('value');
        if (!$savedPassword || $request->input('delete_password') !== $savedPassword) {
            return back()->with('trade_delete_error', 'كلمة المرور غير صحيحة')->with('failed_trade', $oilTrade->id);
        }
        $oilTrade->delete();
        return back()->with('success', 'تم حذف السجل');
    }
}
