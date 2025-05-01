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
use App\Models\SuggestedTA;

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

        $bookingRequestsCount = $bookingRequests->count();

        // Fetch confirmed bookings for the module leader
        $moduleLeaderConfirmedBookings = Bookings::where('module_leader_id', $user->id)
            ->where('status', 'Approved')
            ->with(['module', 'taBookings.ta.user'])
            ->orderBy('date_from', 'asc')
            ->get();
        
        $moduleLeaderConfirmedBookingsCount = $moduleLeaderConfirmedBookings->count();

        return view('moduleLeaderDashboard', compact('user', 'moduleLeaderConfirmedBookings', 'bookingRequests', 'bookingRequestsCount', 'moduleLeaderConfirmedBookingsCount'));
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
                SuggestedTA::create([
                    'ta_id' => $matchedTa->id,
                    'booking_id' => $booking->id,
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
            // ->where('id', '!=', 1)
            ->get();

        foreach ($tas as $ta) {
            // Check if TA is available for the full booking range
            $isAvailable = $ta->availability->where('available_from', '<=', $booking->date_from)
                ->where('available_to', '>=', $booking->date_to)
                ->isNotEmpty();

            // Skip this TA if not available
            if (!$isAvailable) {
                continue;
            }

            // Check if TA has confirmed bookings that overlap with the requested booking
            $hasOverlap = $ta->bookings->where(function ($prevBooking) use ($booking) {
                return $prevBooking->date_from <= $booking->date_to &&
                       $prevBooking->date_to >= $booking->date_from;
            })->isNotEmpty();

            // Skip this TA if there is an overlap
            if ($hasOverlap) {
                continue; 
            }

            // Check if the booking exceeds 80% of contracted hours
            $weeklyHours = $ta->bookings->whereBetween('date_from', [
                $booking->date_from->startOfWeek(),
                $booking->date_from->endOfWeek(),
            ])->sum('hours');

            $newBookingHours = $booking->date_from->diffInHours($booking->date_to);
            $totalHours = $weeklyHours + $newBookingHours;
                        
            // Check for schedule clashes
            $hasScheduleClash = false;
            foreach ($ta->bookings as $prevBooking) {
                if ($prevBooking->date_from->isSameDay($booking->date_from)) {
                    $prevSite = $prevBooking->site;
                    $prevEndTime = $prevBooking->date_to;

                    $travelTime = $this->getTravelTime($prevSite, $booking->site);

                    if ($prevEndTime->addMinutes($travelTime)->greaterThan($booking->date_from)) {
                        $hasScheduleClash = true;
                        break;
                    }
                }
            }

            if ($totalHours <= ($ta->contracted_hours * 0.8) && !$hasScheduleClash) {
                return $ta; // Return the first perfect match
            }
        }

        return null; // No perfect match found
    }

    private function getTravelTime($fromSite, $toSite)
    {
        // travel times between sites (in minutes)
        $travelTimes = [
            'Site 1' => ['Site 1' => 0, 'Site 2' => 20, 'Site 3' => 30],
            'Site 2' => ['Site 1' => 20, 'Site 2' => 0, 'Site 3' => 10],
            'Site 3' => ['Site 1' => 30, 'Site 2' => 10, 'Site 3' => 0],
        ];

        return $travelTimes[$fromSite][$toSite] ?? 0;
    }
}
