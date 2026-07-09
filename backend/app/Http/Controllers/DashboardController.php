<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Redirect user to appropriate dashboard based on role
     */
    public function index(Request $request)
    {
        // If user has admin role, show admin dashboard
        if ($request->user()->hasRole('admin')) {
            return view('admin.dashboard');
        }

        // Otherwise redirect to beranda (user area)
        return redirect()->route('beranda');
    }
}
