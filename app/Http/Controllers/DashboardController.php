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

        $query = Member::where('is_main', true)->withCount('children');

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('member_no', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('middle_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%");
            });
        }

        $recentMembers = $query->latest()->take(10)->get();

        return view('dashboard', compact('totalMembers', 'totalFamilyMembers', 'recentMembers'));
    }
}
