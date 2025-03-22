<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Module;
use Carbon\Carbon;
use App\Models\BookingRequest;

class ModuleLeaderController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        return view('moduleLeaderDashboard', compact('user'));
    }

    public function show()
    {
        $user = Auth::user();
        $modules = Module::where('module_leader_id', $user->id)->get();
        // dd($modules);

        return view('create_booking', compact('modules'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'module_id' => 'required|exists:modules,id',
            'booking_type' => 'required|in:Lecture,Lab,Seminar,Marking,Other',
            'num_tas_requested' => 'required|integer|min:1',
            'date_from' => 'required|date|before:date_to',
            'date_to' => 'required|date|after:date_from',
            'site' => 'required|in:Site 1,Site 2,Site 3',
            'repeat_weeks' => 'nullable|integer|min:1',
        ]);

        $repeatWeeks = $validated['repeat_weeks'] ?? 0; // Default to 0 if not provided
        $dateFrom = Carbon::parse($validated['date_from']);
        $dateTo = Carbon::parse($validated['date_to']);

        // Create the initial booking request
        BookingRequest::create([
            'module_id' => $validated['module_id'],
            'module_leader_id' => auth()->id(),
            'num_tas_requested' => $validated['num_tas_requested'],
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'booking_type' => $validated['booking_type'],
            'site' => $validated['site'],
            'status' => 'Pending',
        ]);

        // If repeat weeks are specified, create additional booking requests
        for ($i = 1; $i <= $repeatWeeks - 1; $i++) {
            $newDateFrom = $dateFrom->copy()->addWeeks($i);
            $newDateTo = $dateTo->copy()->addWeeks($i);

            BookingRequest::create([
                'module_id' => $validated['module_id'],
                'module_leader_id' => auth()->id(),
                'num_tas_requested' => $validated['num_tas_requested'],
                'date_from' => $newDateFrom,
                'date_to' => $newDateTo,
                'booking_type' => $validated['booking_type'],
                'site' => $validated['site'],
                'status' => 'Pending',
            ]);
        }

        return redirect()->back()->with('success', 'Booking request(s) submitted successfully.');
    }
}
