<?php

namespace App\Http\Controllers;

use App\Models\Owner;
use App\Models\Receipt;
use App\Models\Setting;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReceiptController extends Controller
{
    public function index(Request $request)
    {
        $query = Receipt::with('owner')->latest();

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('receipt_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('owner', fn($o) => $o->where('name', 'like', '%' . $request->search . '%'));
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->date) {
            $query->whereDate('received_at', $request->date);
        }

        $receipts = $query->paginate(15);
        return view('receipts.index', compact('receipts'));
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
            'oil_weight_kg'   => 'nullable|numeric|min:0',
            'oil_weight_gram' => 'nullable|numeric|min:0|max:999',
            'floor_number'    => 'nullable|integer',
            'fee_per_kg'      => 'required|numeric|min:0',
            'driver_fee'      => 'nullable|numeric|min:0',
            'notes'           => 'nullable|string',
        ], [
            'owner_id.required'        => 'يرجى اختيار صاحب الزيتون',
            'carrier_name.required'    => 'اسم حامل الزيتون مطلوب',
            'olive_weight_kg.required' => 'كمية الزيتون مطلوبة',
            'fee_per_kg.required'      => 'سعر العصر مطلوب',
        ]);

        $validated['driver_fee'] = $validated['driver_fee'] ?? 0;
        $validated['status']     = !empty($validated['oil_weight_kg']) ? 'completed' : 'pending';

        $receipt = Receipt::create($validated);

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
            'status'          => 'required|in:pending,processing,completed',
            'notes'           => 'nullable|string',
        ]);

        $validated['driver_fee'] = $validated['driver_fee'] ?? 0;
        $receipt->update($validated);

        return redirect()->route('receipts.show', $receipt)
            ->with('success', 'تم تحديث الإيصال بنجاح');
    }

    public function destroy(Receipt $receipt)
    {
        $receipt->delete();
        return redirect()->route('receipts.index')
            ->with('success', 'تم حذف الإيصال');
    }

    public function printReceipt(Receipt $receipt)
    {
        $receipt->load('owner');
        $pressName = \DB::table('settings')->where('key', 'press_name')->value('value') ?? 'معصرة الزيتون';
        return view('receipts.print', compact('receipt', 'pressName'));
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
            'status'          => 'required|in:pending,processing,completed',
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
