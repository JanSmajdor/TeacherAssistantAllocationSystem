<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Module;
use Carbon\Carbon;
use App\Models\Bookings; // Updated to use the Bookings model
use App\Models\TeachingAssistant;
use App\Models\TAAreasOfKnowledge;
use App\Models\Availability;
use App\Models\TABookings;

class ModuleLeaderController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Group bookings by module_leader_id and include their IDs
        $bookingRequests = Bookings::where('module_leader_id', $user->id)
            ->select('id', 'module_id', 'module_leader_id', 'request_batch_id', 'num_tas_requested', 'date_from', 'date_to', 'booking_type', 'site', 'status', 'created_at', 'updated_at')
            ->orderBy('date_from', 'asc')
            ->get()
            ->groupBy('request_batch_id');

        return view('moduleLeaderDashboard', compact('user', 'bookingRequests'));
    }

    public function show()
    {
        $user = Auth::user();
        $modules = Module::where('module_leader_id', $user->id)->get();

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

        $repeatWeeks = $validated['repeat_weeks'] ?? 0;
        $dateFrom = Carbon::parse($validated['date_from']);
        $dateTo = Carbon::parse($validated['date_to']);

        $requestBatchId = Bookings::max('request_batch_id') + 1;

        // Create the initial booking
        $bookings = [];
        $bookings[] = Bookings::create([
            'module_id' => $validated['module_id'],
            'module_leader_id' => auth()->id(),
            'request_batch_id' => $requestBatchId,
            'num_tas_requested' => $validated['num_tas_requested'],
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'booking_type' => $validated['booking_type'],
            'site' => $validated['site'],
            'status' => 'Pending',
        ]);

        // If repeat weeks are specified, create additional bookings
        for ($i = 1; $i <= $repeatWeeks - 1; $i++) {
            $newDateFrom = $dateFrom->copy()->addWeeks($i);
            $newDateTo = $dateTo->copy()->addWeeks($i);

            $bookings[] = Bookings::create([
                'module_id' => $validated['module_id'],
                'module_leader_id' => auth()->id(),
                'request_batch_id' => $requestBatchId,
                'num_tas_requested' => $validated['num_tas_requested'],
                'date_from' => $newDateFrom,
                'date_to' => $newDateTo,
                'booking_type' => $validated['booking_type'],
                'site' => $validated['site'],
                'status' => 'Pending',
            ]);
        }

        // Run the matching algorithm for all bookings
        foreach ($bookings as $booking) {
            $matchedTa = $this->findPerfectMatch($booking);

            if ($matchedTa) {
                // Store the match suggestion for Admin approval
                TABookings::create([
                    'ta_id' => $matchedTa->id,
                    'booking_id' => $booking->id,
                    'status' => 'Suggested',
                ]);

                // Update booking status to auto_matched
                $booking->update(['status' => 'Auto Matched']);
            } else {
                // Update booking status to pending_manual_assignment
                $booking->update(['status' => 'Pending Manual Assignment']);
            }
        }

        return redirect()->back()->with('success', 'Booking(s) submitted successfully.');
    }

    private function findPerfectMatch($booking)
    {
        $moduleAreas = $booking->module->areasOfKnowledge;
        
        $tas = TeachingAssistant::with(['areasOfKnowledge', 'availability', 'bookings'])
            ->whereHas('areasOfKnowledge', function ($query) use ($moduleAreas) {
                $query->whereIn('area_id', $moduleAreas->pluck('id'));
            })
            ->where('id', '!=', 1)
            ->get();

        foreach ($tas as $ta) {
            // Check if TA is available for the full booking range
            $isAvailable = $ta->availability->where('available_from', '<=', $booking->date_from)
                ->where('available_to', '>=', $booking->date_to)
                ->isNotEmpty();

            // Check if the booking exceeds 80% of contracted hours
            $weeklyHours = $ta->bookings->whereBetween('date_from', [
                $booking->date_from->startOfWeek(),
                $booking->date_from->endOfWeek(),
            ])->sum('hours');

            $newBookingHours = $booking->date_from->diffInHours($booking->date_to);
            $totalHours = $weeklyHours + $newBookingHours;

            if ($isAvailable && $totalHours <= ($ta->contracted_hours * 0.8)) {
                return $ta; // Return the first perfect match
            }
        }

        return null; // No perfect match found
    }
}
