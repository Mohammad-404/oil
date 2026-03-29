<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use app\Model\Owner;
use app\Model\PressSession;

class ReportController extends Controller
{
      public function index()
    {
        $monthly = PressSession::selectRaw("DATE_FORMAT(session_date,'%Y-%m') as month, SUM(olives_weight_kg) as olives, SUM(total_fee) as fees, COUNT(*) as sessions")
            ->groupBy('month')
            ->orderByDesc('month')
            ->limit(12)
            ->get();

        return view('reports.index', compact('monthly'));
    }

    public function daily(Request $request)
    {
        $date = $request->date ?? today()->toDateString();
        $sessions = PressSession::with('owner')
            ->whereDate('session_date', $date)
            ->orderBy('queue_number')
            ->get();

        return view('reports.daily', compact('sessions', 'date'));
    }

    public function calculations(Request $request)
    {
        $owners = Owner::orderBy('name')->get();

        $sessions = PressSession::with('owner')
            ->when($request->from, fn($q) => $q->whereDate('session_date', '>=', $request->from))
            ->when($request->to,   fn($q) => $q->whereDate('session_date', '<=', $request->to))
            ->when($request->owner_id, fn($q) => $q->where('owner_id', $request->owner_id))
            ->orderBy('session_date')
            ->orderBy('queue_number')
            ->get();

        if (!$request->from && !$request->to) {
            $sessions = PressSession::with('owner')
                ->whereDate('session_date', today())
                ->orderBy('queue_number')
                ->get();
        }

        return view('reports.calculations', compact('sessions', 'owners'));
    }
}
