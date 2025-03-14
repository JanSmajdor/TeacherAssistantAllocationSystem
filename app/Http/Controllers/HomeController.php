<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\TAAreaOfKnowledge;
use App\Models\TAEditAreasOfKnowledgeRequest;
use App\Models\TeachingAssistant;

class HomeController extends Controller
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
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
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

        return view('home', compact('user', 'admin_edit_account_requests', 'admin_count'));
    }

    public function approve(Request $request)
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

        return redirect()->route('home')->with('success', 'Request approved successfully');
    }

    public function deny(Request $request)
    {
        // dd($request->all());

        $ta_edit_account_request = TAEditAreasOfKnowledgeRequest::find($request->input('ta_id'));
        $ta_edit_account_request->request_status = 'Rejected';
        $ta_edit_account_request->save();

        return redirect()->route('home')->with('success', 'Request denied successfully');
    }
}
