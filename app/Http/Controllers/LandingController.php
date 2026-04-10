<?php

namespace App\Http\Controllers;

use App\Models\Plan;

class LandingController extends Controller
{
    public function index()
    {
        $plans = Plan::actifs();
        return view('landing.index', compact('plans'));
    }
}
