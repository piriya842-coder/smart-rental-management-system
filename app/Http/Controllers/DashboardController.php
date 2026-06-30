<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // TEMP: everyone goes to rooms after login
        return redirect()->route('rooms.index');
    }
}
