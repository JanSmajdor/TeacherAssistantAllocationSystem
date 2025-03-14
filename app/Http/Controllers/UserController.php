<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\AreaOfKnowledge;
use App\Models\TAAreaOfKnowledge;
use App\Models\Availability;
use App\Models\TAEditAreasOfKnowledgeRequest;
use App\Models\TeachingAssistant;

class UserController extends Controller 
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the edit account page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function show()
    {
        $user = Auth::user();
        $ta_details = User::select(
            'users.first_name',
            'users.last_name',
            'users.email',
            'users.password',
            'teaching_assistants.id AS ta_id',
            'teaching_assistants.contracted_hours'
        )
        ->join('teaching_assistants', 'teaching_assistants.user_id', '=', 'users.id')
        ->where('users.id', $user->id)
        ->first();

        $availability = Availability::where('ta_id', $ta_details->ta_id)->get();

        $all_areas = AreaOfKnowledge::all();

        $ta_areas_of_knowledge = TAAreaOfKnowledge::where('ta_id', $ta_details->ta_id)->pluck('area_id')->toArray();

        return view('edit_account', compact('ta_details', 'availability', 'all_areas', 'ta_areas_of_knowledge'));
    }

    public function edit(Request $request) {

        $loggedIn_userid = Auth::user()->id;
        $ta_id = TeachingAssistant::where('user_id', $loggedIn_userid)->first()->id;
        $available_from = $request->input('start_times');
        $available_to = $request->input('end_times');
        $ta_areas_of_knowledge = $request->input('areas_of_knowledge');
        $ta_contracted_hours = $request->input('contracted_hours');

        if (empty($available_from) && empty($available_to)) {
            Availability::where('ta_id', $ta_id)->delete();
            return redirect()->back()->with('success', 'All Availability has been cleared.');
        }

        if (empty($ta_areas_of_knowledge)) {
            TAAreaOfKnowledge::where('ta_id', $ta_id)->delete();
            return redirect()->back()->with('success', 'All Areas of Knowledge have been cleared.');
        }

        if (count($available_from) != count($available_to)) {
            return redirect()->back()->with('error', 'Please ensure that all start times have an end time.');
        }
        
        if (empty($ta_contracted_hours)) {
            return redirect()->back()->with('error', 'Please enter a value for Contracted Hours.');
        }
        
        // Update teaching_assistants table with contracted hours
        TeachingAssistant::where('id', $ta_id)->update(['contracted_hours' => $ta_contracted_hours]);

        // Populate ta_availability table
        foreach ($available_from as $key => $start_time) {
            $end_time = $available_to[$key];
            Availability::updateOrCreate(
                [
                    'ta_id' => $ta_id,
                    'available_from' => $start_time,
                    'available_to' => $end_time
                ],
                [
                    'available_from' => $start_time,
                    'available_to' => $end_time
                ]
            );
        }

        // Add Areas of knowledge to request table
        if(!empty($ta_areas_of_knowledge)) {
            foreach ($ta_areas_of_knowledge as $area_id) {
                TAEditAreasOfKnowledgeRequest::updateOrCreate(
                    [
                        'ta_id' => $ta_id,
                        'area_id' => $area_id
                    ],
                    [
                        'ta_id' => $ta_id,
                        'area_id' => $area_id,
                        'request_status' => 'Pending'
                    ]
                );
            }        
        }
        return redirect()->back()->with('success', 'Account details form submitted successfully.');
    }
}