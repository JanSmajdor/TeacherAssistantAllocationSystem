<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\TAEditAreasOfKnowledgeRequest;
use App\Models\TAAreaOfKnowledge;
use App\Models\AreaOfKnowledge;
use App\Models\ModuleAreasOfKnowledge;
use App\Models\Module;
use Illuminate\Support\Facades\DB;
use App\Models\Bookings;
use App\Models\TABookings;

class AdminController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // get any ta edit account details requests as well as their user details
        $admin_edit_account_requests = TAEditAreasOfKnowledgeRequest::where('request_status', 'Pending')
            ->with(['teaching_assistant.user', 'area_of_knowledge'])
            ->get()
            ->groupBy('ta_id')
            ->map(function ($requests) {
                $firstRequest = $requests->first();
                $firstRequest->areas_of_knowledge = $requests->pluck('area_of_knowledge.name')->toArray();
                return $firstRequest;
            });
        

        $admin_count = $admin_edit_account_requests->count();

        // Fetch booking requests with status 'Pending', 'Auto Matched', or 'Pending Manual Assignment'
        $booking_requests = Bookings::whereIn('status', ['Pending', 'Auto Matched', 'Pending Manual Assignment'])
            ->with(['module', 'moduleLeader', 'taBookings.ta'])
            ->get();
        
        $booking_count = $booking_requests->count();

        // Dynamically determine a suggested TA for each booking
        foreach ($booking_requests as $booking) {
            if ($booking->status === 'Auto Matched' || $booking->status === 'Approved') {
                // Fetch the auto-matched or manually assigned TA details
                $booking->suggested_ta = $booking->taBookings->first()?->ta; // Ensure it's a single TA or null
            } else {
                // Use the matching algorithm for suggestions
                $booking->suggested_ta = $this->getSuggestedTA($booking)->first(); // Get the first TA or null
            }
        }

        return view('adminDashboard', compact('user', 'admin_edit_account_requests', 'admin_count', 'booking_requests', 'booking_count'));
    }

    /**
     * Determine a suggested TA for a booking.
     */
    private function getSuggestedTA($booking)
    {
        return User::where('role', 'Teaching Assistant')
            ->whereHas('teachingAssistant.availability', function ($query) use ($booking) {
                $query->where('available_from', '<=', $booking->date_from)
                      ->where('available_to', '>=', $booking->date_to);
            })
            ->with(['teachingAssistant' => function ($query) {
                $query->select('id', 'user_id', 'contracted_hours'); // Ensure contracted_hours is included
            }])
            ->get();
    }

    public function showAreaOfKnowledgeForm()
    {
        return view('new_area_of_knowledge');
    }

    public function createAreaOfKnowledge(Request $request)
    {
        //still need to add proper request data verification

        //check if this AoK exists already
        try {
            $new_area_of_knowledge = AreaOfKnowledge::firstOrCreate([
                'name' => $request->input('aok-name')
            ]);

            if (!$new_area_of_knowledge->wasRecentlyCreated) {
                return redirect()->back()->with('error', 'Error Adding Area of Knowledge to the Database: Already Exists');
            }

            return redirect()->back()->with('success', 'Area of Knowledge has been Successfully Added to the Database!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error Adding Area of Knowledge to the Database: ' . $e->getMessage());
        }
    }

    public function showModuleForm()
    {
        //still need to add proper request data verification

        // all module leaders, used for ML dropdown field. all aok used for aok dropdown field
        $module_leaders = User::select('id', 'first_name', 'last_name')->where('role', 'Module Leader')->get(); //potentially modify this to stop showing ML if they are already assigned to a module
        $aok = AreaOfKnowledge::select('id', 'name')->get();

        return view('new_module')->with('module_leaders', $module_leaders)->with('aok', $aok);
    }

    public function createModule(Request $request)
    {   
        
        try{
            DB::beginTransaction();

            $new_module = Module::firstOrCreate([
                'module_leader_id' => $request->input('module-leader'),
                'module_name' => $request->input('module-name'),
                'module_code' => $request->input('module-code'),
                'num_of_students' => $request->input('number-of-students')
            ]);

            $module_aok = ModuleAreasOfKnowledge::firstOrCreate([
                'area_id' => $request->input('module-area-of-knowledge'),
                'module_id' => $new_module->id
            ]);
            
            DB::commit();
            return redirect()->back()->with('success', 'Module has been Succesfully Added to the Database!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error Adding Module to the Database:' . $e->getMessage());
        }
    }

    public function approveEditAccountRequest(Request $request)
    {
        $ta_edit_account_requests = TAEditAreasOfKnowledgeRequest::where('ta_id', $request->input('ta_id'))
            ->where('request_status', 'Pending')
            ->get();

        foreach ($ta_edit_account_requests as $ta_edit_account_request) {
            $ta_edit_account_request->request_status = 'Approved';
            $ta_edit_account_request->save();

            $exists = TAAreaOfKnowledge::where('ta_id', $ta_edit_account_request->ta_id)
                ->where('area_id', $ta_edit_account_request->area_id)
                ->exists();

            if (!$exists) {
                TAAreaOfKnowledge::create([
                    'ta_id' => $ta_edit_account_request->ta_id,
                    'area_id' => $ta_edit_account_request->area_id
                ]);
            }
        }

        return redirect()->route('admin.dashboard')->with('success', 'Requests approved successfully');
    }

    public function denyEditAccountReuest(Request $request)
    {
        $ta_edit_account_requests = TAEditAreasOfKnowledgeRequest::where('ta_id', $request->input('ta_id'))
            ->where('request_status', 'Pending')
            ->get();

        foreach ($ta_edit_account_requests as $ta_edit_account_request) {
            $ta_edit_account_request->request_status = 'Denied';
            $ta_edit_account_request->save();
        }

        return redirect()->route('admin.dashboard')->with('success', 'Requests denied successfully');
    }

    public function approveBooking(Request $request)
    {
        try {
            // Find the booking request
            $booking = Bookings::findOrFail($request->input('booking_id'));
            $suggested_ta = $this->getSuggestedTA($booking);

            // Update the status to 'Approved'
            $booking->status = 'Approved';
            $booking->save();

            // Populate the ta_bookings table with the booking ID and corresponding TA ID
            if ($suggested_ta) {
                TABookings::create([
                    'booking_id' => $booking->id,
                    'ta_id' => $suggested_ta->id,
                ]);
            }

            return redirect()->route('admin.dashboard')->with('success', 'Booking request approved successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.dashboard')->with('error', 'Error approving booking request: ' . $e->getMessage());
        }
    }

    public function denyBooking(Request $request)
    {
        try {
            // Find the booking request
            $booking = Bookings::findOrFail($request->input('booking_id'));

            // Update the status to 'Denied'
            $booking->status = 'Denied';
            $booking->save();

            return redirect()->route('admin.dashboard')->with('success', 'Booking request denied successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.dashboard')->with('error', 'Error denying booking request: ' . $e->getMessage());
        }
    }

    public function manuallyAssignTA(Request $request)
    {
        try {
            // Find the booking request
            $booking = Bookings::findOrFail($request->input('booking_id'));

            // Update the status to 'Manually Assigned'
            $booking->status = 'Approved';
            $booking->save();

            // Populate the ta_bookings table with the booking ID and corresponding TA ID
            TABookings::create([
                'booking_id' => $booking->id,
                'ta_id' => $request->input('ta_id'),
            ]);

            return redirect()->route('admin.dashboard')->with('success', 'TA manually assigned successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.dashboard')->with('error', 'Error manually assigning TA: ' . $e->getMessage());
        }
    }
}
