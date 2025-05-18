<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Constructor method for the the class.
     * Create a new instance.
     * Initializes necessary dependencies.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // xdebug_break();
        return view('backend.index');
    }

    /**
     * Show the application dashboard for Admin.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function adminDashboard()
    {
        // xdebug_break();
        return view('backend.dashboard.admin');
    }

    /**
     * Show the application dashboard for Donor.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function donorDashboard()
    {
        // xdebug_break();
        return view('backend.dashboard.donor');
    }
}
