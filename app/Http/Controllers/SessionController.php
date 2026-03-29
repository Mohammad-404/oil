<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use app\Model\PressSession;
use app\Model\Owner;

class SessionController extends Controller
{
        public function index(Request $request)
    {
        $sessions = PressSession::with('owner')
            ->filter($request->only(['search','from','to','status','owner_id']))
            ->orderByDesc('session_date')
            ->orderByDesc('queue_number')
            ->paginate(25);

        return view('sessions.index', compact('sessions'));
    }

    public function create()
    {
        $nextQueue = (PressSession::whereDate('session_date', today())->max('queue_number') ?? 0) + 1;
        $settings  = (object)['fee_per_kg' => Setting::get('fee_per_kg', 0.100)];
        return view('sessions.create', compact('nextQueue', 'settings'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'queue_number'       => 'required|integer|min:1',
            'session_date'       => 'required|date',
            'owner_id'           => 'nullable|exists:owners,id',
            'owner_name_search'  => 'required_without:owner_id|string',
            'carrier_name'       => 'nullable|string|max:255',
            'shares'             => 'nullable|string|max:50',
            'phone'              => 'nullable|string|max:20',
            'city'               => 'nullable|string|max:100',
            'olives_weight_kg'   => 'required|numeric|min:0',
            'olives_weight_gram' => 'nullable|integer|min:0|max:999',
            'extracted_oil_kg'   => 'nullable|numeric|min:0',
            'filtered_oil_kg'    => 'nullable|numeric|min:0',
            'fee_per_kg'         => 'required|numeric|min:0',
            'extra_fee'          => 'nullable|numeric|min:0',
            'payment_status'     => 'required|in:pending,paid,partial',
            'status'             => 'required|in:received,processing,done',
            'notes'              => 'nullable|string',
        ]);

        // Create or get owner
        if (empty($data['owner_id']) && !empty($data['owner_name_search'])) {
            $owner = Owner::firstOrCreate(
                ['name' => $data['owner_name_search']],
                ['city' => $data['city'] ?? null, 'phone' => $data['phone'] ?? null]
            );
            $data['owner_id'] = $owner->id;
        }

        $data['extra_fee'] = $data['extra_fee'] ?? 0;
        unset($data['owner_name_search'], $data['phone'], $data['city']);

        $session = PressSession::create($data);

        if ($request->action === 'save_print') {
            return redirect()->route('sessions.receipt', $session)
                ->with('success', 'تم حفظ الجلسة');
        }

        return redirect()->route('sessions.show', $session)
            ->with('success', '✅ تم تسجيل الجلسة رقم ' . $session->queue_number . ' بنجاح');
    }

    public function show(PressSession $session)
    {
        $session->load('owner');
        return view('sessions.show', compact('session'));
    }

    public function edit(PressSession $session)
    {
        $settings = (object)['fee_per_kg' => Setting::get('fee_per_kg', 0.100)];
        return view('sessions.edit', compact('session', 'settings'));
    }

    public function update(Request $request, PressSession $session)
    {
        $data = $request->validate([
            'queue_number'       => 'required|integer|min:1',
            'session_date'       => 'required|date',
            'carrier_name'       => 'nullable|string|max:255',
            'shares'             => 'nullable|string|max:50',
            'olives_weight_kg'   => 'required|numeric|min:0',
            'olives_weight_gram' => 'nullable|integer',
            'extracted_oil_kg'   => 'nullable|numeric|min:0',
            'filtered_oil_kg'    => 'nullable|numeric|min:0',
            'fee_per_kg'         => 'required|numeric|min:0',
            'extra_fee'          => 'nullable|numeric|min:0',
            'payment_status'     => 'required|in:pending,paid,partial',
            'status'             => 'required|in:received,processing,done',
            'notes'              => 'nullable|string',
        ]);

        $data['extra_fee'] = $data['extra_fee'] ?? 0;
        $session->update($data);

        return redirect()->route('sessions.show', $session)->with('success', 'تم تحديث الجلسة');
    }

    public function destroy(PressSession $session)
    {
        $session->delete();
        return redirect()->route('sessions.index')->with('success', 'تم حذف الجلسة');
    }

    public function receipt(PressSession $session)
    {
        $session->load('owner');
        return view('sessions.receipt', compact('session'));
    }
}
