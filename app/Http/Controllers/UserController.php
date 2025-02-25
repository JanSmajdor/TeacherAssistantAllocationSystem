<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

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

        return view('edit_account', compact('user'));
    }
}