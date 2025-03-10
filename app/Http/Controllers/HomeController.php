<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\TAAreaOfKnowledge;
use App\Models\TAEditAreasOfKnowledgeRequest;

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
        $ta_edit_account_requests = TAEditAreasOfKnowledgeRequest::where('request_status', 'Pending')
            ->with('teaching_assistant.user')
            ->get();

        $count = $ta_edit_account_requests->count();
        // dd($ta_edit_account_requests);

        return view('home', compact('user', 'ta_edit_account_requests', 'count'));
    }

    public function approve(Request $request)
    {
        $ta_edit_account_request = TAEditAreasOfKnowledgeRequest::find($request->input('request_id'));
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

        return redirect()->route('home')->with('success', 'Request approved successfully');
    }
}
