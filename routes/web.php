<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\CashierController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/reports', [DashboardController::class, 'reports'])->name('reports');
Route::get('/settings', [DashboardController::class, 'settings'])->name('settings');
Route::post('/settings', [DashboardController::class, 'updateSettings'])->name('settings.update');

// أصحاب الزيتون
Route::resource('owners', OwnerController::class);

// الإيصالات
Route::resource('receipts', ReceiptController::class);
Route::get('receipts/{receipt}/print', [ReceiptController::class, 'printReceipt'])->name('receipts.print');
Route::get('receipts/{receipt}/pdf', [ReceiptController::class, 'exportPdf'])->name('receipts.pdf');
Route::get('receipts/{receipt}/cashier', [ReceiptController::class, 'cashier'])->name('receipts.cashier');
Route::patch('receipts/{receipt}/oil', [ReceiptController::class, 'updateOil'])->name('receipts.update-oil');

// الكاشير
Route::get('/cashier', [CashierController::class, 'index'])->name('cashier.index');
Route::patch('/cashier/{receipt}/oil', [CashierController::class, 'updateOil'])->name('cashier.update-oil');
