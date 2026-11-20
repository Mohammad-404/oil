<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Receipt;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $activeYear  = $this->getActiveYear();
        $search      = trim($request->input('search', ''));
        $fromDate    = $request->input('from_date');
        $toDate      = $request->input('to_date');

        $base = function(string $type) use ($activeYear, $search, $fromDate, $toDate) {
            $q = $this->applyYearFilter(Expense::where('type', $type)->latest(), $activeYear);
            if ($search)   $q->where('description', 'like', '%' . $search . '%');
            if ($fromDate) $q->whereDate('created_at', '>=', $fromDate);
            if ($toDate)   $q->whereDate('created_at', '<=', $toDate);
            return $q;
        };

        $incomes  = $base('income')->paginate(15, ['*'], 'income_page')->withQueryString();
        $expenses = $base('expense')->paginate(15, ['*'], 'expense_page')->withQueryString();

        // الإجماليات تعكس الفلتر المطبّق
        $totalIncome  = (clone $base('income'))->sum('amount');
        $totalExpense = (clone $base('expense'))->sum('amount');
        $netBalance   = $totalIncome - $totalExpense;
        $isFiltered   = $search || $fromDate || $toDate;

        // أجرة نقدية من الإيصالات — تعكس فلتر التاريخ (created_at للأجرة، debt_paid_at للديون المسددة)
        $rq1 = $this->applyYearFilter(
            Receipt::where('status', '!=', 'debt')->where('payment_method', '!=', 'oil_return'), $activeYear);
        $rq2 = $this->applyYearFilter(
            Receipt::where('status', '!=', 'debt')->where('payment_method', 'oil_return')->where('cans_in_return', false), $activeYear);
        $rq3 = $this->applyYearFilter(
            Receipt::whereNotNull('debt_paid_at')->where('payment_method', '!=', 'oil_return'), $activeYear);

        if ($fromDate) {
            $rq1->whereDate('created_at', '>=', $fromDate);
            $rq2->whereDate('created_at', '>=', $fromDate);
            $rq3->whereDate('debt_paid_at', '>=', $fromDate);
        }
        if ($toDate) {
            $rq1->whereDate('created_at', '<=', $toDate);
            $rq2->whereDate('created_at', '<=', $toDate);
            $rq3->whereDate('debt_paid_at', '<=', $toDate);
        }

        $receiptCashFees  = $rq1->sum('total_fee') + $rq2->sum('discounted_can_total');
        $receiptDebtFees  = $rq3->sum('total_fee');
        // صافي الأجور الشامل = أجرة + ديون مسددة + واردات − صادرات (مثل لوحة التحكم)
        $netReceiptFees   = $receiptCashFees + $receiptDebtFees + $totalIncome - $totalExpense;

        return view('cashier.expenses', compact(
            'incomes', 'expenses',
            'totalIncome', 'totalExpense', 'netBalance',
            'receiptCashFees', 'receiptDebtFees', 'netReceiptFees',
            'search', 'fromDate', 'toDate', 'isFiltered'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type'        => 'required|in:income,expense',
            'amount'      => 'required|numeric|min:0.001',
            'description' => 'required|string|max:255',
            'notes'       => 'nullable|string|max:500',
        ]);

        Expense::create($request->only('type', 'amount', 'description', 'notes'));

        $msg = $request->type === 'income' ? 'تم تسجيل الوارد بنجاح' : 'تم تسجيل الصادر بنجاح';
        return back()->with('success', $msg);
    }

    public function destroy(Request $request, Expense $expense)
    {
        $savedPassword = \DB::table('settings')->where('key', 'cashier_delete_password')->value('value');
        if (!$savedPassword || $request->input('delete_password') !== $savedPassword) {
            return back()
                ->with('expense_delete_error', 'كلمة المرور غير صحيحة')
                ->with('failed_expense', $expense->id);
        }
        $expense->delete();
        return back()->with('success', 'تم حذف السجل بنجاح');
    }
}
