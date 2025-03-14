<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $profiles = \App\Models\User::all(); // Fetch all users
        return view('home', compact('profiles'));
    }
}
