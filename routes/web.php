<?php

use Illuminate\Support\Facades\Route;

Route::get('/health', fn () => response()->json(['status' => 'ok']));
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\OilTradeController;
use App\Http\Controllers\ExpenseController;

// ── مسارات المصادقة ──
Auth::routes(['verify' => false, 'confirm' => false]);

// ── جميع المسارات محمية بتسجيل الدخول ──
Route::middleware('auth')->group(function () {

    // ── الكاشير فقط ──
    Route::middleware('role:cashier')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/reports', [DashboardController::class, 'reports'])->name('reports');
        Route::get('/financial-report', [DashboardController::class, 'financialReport'])->name('reports.financial');
        Route::post('/financial-report/unlock', [DashboardController::class, 'unlockFinancial'])->name('reports.financial.unlock');

        Route::get('/cashier', [CashierController::class, 'index'])->name('cashier.index');
        Route::get('/cashier/debts', [CashierController::class, 'debts'])->name('cashier.debts');
        Route::get('/cashier/oil-sales', [CashierController::class, 'oilSales'])->name('cashier.oil-sales');
        Route::get('/cashier/debt/{receipt}', [CashierController::class, 'debt'])->name('cashier.debt');
        Route::get('/cashier/debt/{receipt}/print', [CashierController::class, 'debtPrint'])->name('cashier.debt-print');
        Route::get('/cashier/driver-fees', [CashierController::class, 'driverFees'])->name('cashier.driver-fees');
        Route::get('/cashier/print/{owner}', [CashierController::class, 'printSummary'])->name('cashier.print');
        Route::patch('/cashier/{receipt}/oil', [CashierController::class, 'updateOil'])->name('cashier.update-oil');
        Route::patch('/cashier/{receipt}/driver-fee', [CashierController::class, 'updateDriverFee'])->name('cashier.driver-fee');
        Route::post('/cashier/{receipt}/delete', [CashierController::class, 'deleteWithPassword'])->name('cashier.delete');
        Route::post('/cashier/{receipt}/unlock-form', [CashierController::class, 'unlockForm'])->name('cashier.unlock-form');
        Route::post('/cashier/{receipt}/mark-paid', [CashierController::class, 'markDebtPaid'])->name('cashier.mark-paid');
        Route::post('/receipts/{receipt}/request-edit', [ReceiptController::class, 'requestEdit'])->name('receipts.request-edit');

        // بيع وشراء الزيت
        Route::get('/cashier/oil-trade', [OilTradeController::class, 'index'])->name('cashier.oil-trade');
        Route::post('/cashier/oil-trade', [OilTradeController::class, 'store'])->name('cashier.oil-trade.store');
        Route::post('/cashier/oil-trade/{oilTrade}/settle', [OilTradeController::class, 'settle'])->name('cashier.oil-trade.settle');
        Route::delete('/cashier/oil-trade/{oilTrade}', [OilTradeController::class, 'destroy'])->name('cashier.oil-trade.destroy');

        // المصروفات
        Route::get('/cashier/expenses', [ExpenseController::class, 'index'])->name('cashier.expenses');
        Route::post('/cashier/expenses', [ExpenseController::class, 'store'])->name('cashier.expenses.store');
        Route::delete('/cashier/expenses/{expense}', [ExpenseController::class, 'destroy'])->name('cashier.expenses.destroy');

        // حذف الإيصالات — كاشير فقط
        Route::delete('receipts/{receipt}', [ReceiptController::class, 'destroy'])->name('receipts.destroy');
        Route::post('/receipts/unlock', [ReceiptController::class, 'unlockReports'])->name('receipts.unlock');
        Route::get('receipts/{receipt}/pdf', [ReceiptController::class, 'exportPdf'])->name('receipts.pdf');
        Route::patch('receipts/{receipt}/oil', [ReceiptController::class, 'updateOil'])->name('receipts.update-oil');
    });

    // ── اليوزر فقط (عرض + إضافة) ──
    Route::middleware('role:user')->group(function () {
        Route::get('/user/home', [OwnerController::class, 'create'])->name('user.home');
        Route::get('/user/receipts', [ReceiptController::class, 'userReceipts'])->name('user.receipts');

        // إيصالات: إضافة فقط (عرض مشترك)
        Route::resource('receipts', ReceiptController::class)->only(['create', 'store']);
        // أصحاب الزيتون: إضافة فقط (عرض مشترك)
        Route::resource('owners', OwnerController::class)->only(['create', 'store']);
    });

    // ── مسارات ثابتة قبل الـ resource (مهم: لازم تكون قبل receipts/{receipt}) ──
    Route::get('receipts/print-table', [ReceiptController::class, 'printTable'])->name('receipts.print-table');
    Route::get('receipts/{receipt}/print', [ReceiptController::class, 'printReceipt'])->name('receipts.print');
    Route::get('receipts/{receipt}/print-row', [ReceiptController::class, 'printRow'])->name('receipts.print-row');

    // ── عرض + تعديل + حذف — مشترك بين الدورين ──
    Route::resource('receipts', ReceiptController::class)->only(['index', 'show', 'edit', 'update']);
    Route::resource('owners',   OwnerController::class)->only(['index', 'show', 'edit', 'update', 'destroy']);

    // ── الإعدادات — مشتركة بين الدورين (محمية بكلمة مرور) ──
    Route::get('/settings', [DashboardController::class, 'settings'])->name('settings');
    Route::post('/settings/unlock', [DashboardController::class, 'unlockSettings'])->name('settings.unlock');
    Route::post('/settings', [DashboardController::class, 'updateSettings'])->name('settings.update');
    Route::post('/settings/bulk-delete', [DashboardController::class, 'bulkDelete'])->name('settings.bulk-delete');

});
