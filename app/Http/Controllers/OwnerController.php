<?php

namespace App\Http\Controllers;

use App\Models\Owner;
use App\Models\Receipt;
use Illuminate\Http\Request;

class OwnerController extends Controller
{
    public function index(Request $request)
    {
        $query = Owner::withCount('receipts')->with('receipts');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%')
                  ->orWhere('city', 'like', '%' . $request->search . '%');
            });
        }

        $owners = $query->latest()->paginate(15);
        return view('owners.index', compact('owners'));
    }

    public function create()
    {
        return view('owners.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'   => 'required|string|max:255',
            'city'   => 'required|string|max:255',
            'street' => 'required|string|max:255',
            'phone'  => 'required|string|max:20',
        ], [
            'name.required'   => 'اسم صاحب الزيتون مطلوب',
            'city.required'   => 'المدينة مطلوبة',
            'street.required' => 'الشارع مطلوب',
            'phone.required'  => 'رقم الهاتف مطلوب',
        ]);

        $owner = Owner::create($validated);

        return redirect()->route('owners.show', $owner)
            ->with('success', 'تم إضافة صاحب الزيتون بنجاح');
    }

    public function show(Owner $owner)
    {
        $receipts = $owner->receipts()->latest()->paginate(10);
        return view('owners.show', compact('owner', 'receipts'));
    }

    public function edit(Owner $owner)
    {
        return view('owners.edit', compact('owner'));
    }

    public function update(Request $request, Owner $owner)
    {
        $validated = $request->validate([
            'name'   => 'required|string|max:255',
            'city'   => 'required|string|max:255',
            'street' => 'required|string|max:255',
            'phone'  => 'required|string|max:20',
        ]);

        $owner->update($validated);

        return redirect()->route('owners.show', $owner)
            ->with('success', 'تم تحديث البيانات بنجاح');
    }

    public function destroy(Owner $owner)
    {
        $owner->delete();
        return redirect()->route('owners.index')
            ->with('success', 'تم حذف صاحب الزيتون بنجاح');
    }
}
