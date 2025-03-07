<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\AreaOfKnowledge;
use App\Models\TAAreaOfKnowledge;
use App\Models\Availability;

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
            'teaching_assistants.contracted_hours'
        )
        ->join('teaching_assistants', 'teaching_assistants.user_id', '=', 'users.id')
        ->where('users.id', $user->id)
        ->first();

        $availability = Availability::where('ta_id', $user->id)->get();

        $all_areas = AreaOfKnowledge::all();

        $ta_areas_of_knowledge = TAAreaOfKnowledge::where('ta_id', $user->id)->pluck('area_id')->toArray();

        return view('edit_account', compact('ta_details', 'availability', 'all_areas', 'ta_areas_of_knowledge'));
    }
}