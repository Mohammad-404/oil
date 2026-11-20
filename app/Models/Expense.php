<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = ['type', 'amount', 'description', 'notes'];

    protected $casts = [
        'amount' => 'float',
    ];

    public static function totalIncome(?int $year = null): float
    {
        $q = static::where('type', 'income');
        if ($year) $q->whereYear('created_at', $year);
        return (float) $q->sum('amount');
    }

    public static function totalExpense(?int $year = null): float
    {
        $q = static::where('type', 'expense');
        if ($year) $q->whereYear('created_at', $year);
        return (float) $q->sum('amount');
    }
}
