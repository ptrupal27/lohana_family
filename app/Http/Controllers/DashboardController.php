<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $totalMembers = Member::where('is_main', true)->count();
        $totalFamilyMembers = Member::where('is_main', false)->count();

        $totalMales = Member::where('gender', 'Male')->count();
        $totalFemales = Member::where('gender', 'Female')->count();

        return view('dashboard', compact('totalMembers', 'totalFamilyMembers', 'totalMales', 'totalFemales'));
    }
}
