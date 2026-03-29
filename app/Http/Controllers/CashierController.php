<?php

namespace App\Http\Controllers;

use App\Models\Owner;
use App\Models\Receipt;
use Illuminate\Http\Request;

class CashierController extends Controller
{
    public function index(Request $request)
    {
        $owner    = null;
        $receipts = collect();

        if ($request->filled('floor_number')) {
            $owner = Owner::where('floor_number', $request->floor_number)
                          ->with(['receipts' => fn($q) => $q->latest()])
                          ->first();

            if ($owner) {
                $receipts = $owner->receipts;
            }
        }

        return view('cashier.index', compact('owner', 'receipts'));
    }

    public function updateOil(Request $request, Receipt $receipt)
    {
        $validated = $request->validate([
            'oil_weight_kg'   => 'required|numeric|min:0',
            'oil_weight_gram' => 'nullable|numeric|min:0|max:999',
            'fee_per_kg'      => 'required|numeric|min:0',
            'driver_fee'      => 'nullable|numeric|min:0',
            'status'          => 'required|in:pending,processing,completed',
        ]);

        $validated['driver_fee'] = $validated['driver_fee'] ?? 0;

        // press_fee & total_fee recalculated automatically by model boot
        $receipt->update($validated);

        return back()->with('success', 'تم تحديث إيصال رقم ' . $receipt->receipt_number . ' بنجاح');
    }
}
