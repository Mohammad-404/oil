<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class OilTrade extends Model
{
    protected $fillable = ['type', 'quantity_kg', 'price_per_kg', 'total_amount', 'notes', 'payment_status', 'counterparty_name'];

    protected $attributes = ['payment_status' => 'paid'];

    public const CASH_DATE = 'COALESCE(debt_paid_at, created_at)';

    protected $casts = [
        'quantity_kg'  => 'float',
        'price_per_kg' => 'float',
        'total_amount' => 'float',
        'debt_paid_at' => 'datetime',
    ];

    public function scopePaid(Builder $query): Builder
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopeOutstandingDebt(Builder $query): Builder
    {
        return $query->where('payment_status', 'debt');
    }

    // Cash trades use their creation date; settled debts use the actual payment date.
    public function scopeCashBetween(Builder $query, ?string $from, ?string $to, ?int $year = null): Builder
    {
        return $query->paid()
            ->when($from, fn($q) => $q->whereDate(DB::raw(self::CASH_DATE), '>=', $from))
            ->when($to, fn($q) => $q->whereDate(DB::raw(self::CASH_DATE), '<=', $to))
            ->when($year, fn($q) => $q->whereYear(DB::raw(self::CASH_DATE), $year));
    }

    public static function totalOilStock(?int $year = null): float
    {
        // رديات الزيت: فقط المسددة (status != debt) — الديون لا تُضاف للمخزون حتى تُسدَّد
        $receipts   = \App\Models\Receipt::where('payment_method', 'oil_return')->where('status', '!=', 'debt');
        $buys       = static::where('type', 'buy');
        $sells      = static::where('type', 'sell');

        if ($year) {
            $receipts->whereYear('created_at', $year);
            $buys->whereYear('created_at', $year);
            $sells->whereYear('created_at', $year);
        }

        return (float)$receipts->sum('oil_return_kg')
             + (float)$buys->sum('quantity_kg')
             - (float)$sells->sum('quantity_kg');
    }
}
