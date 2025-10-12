<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\BloodCollectionCamp;
use App\Models\BloodTest;
use App\Models\BloodUnit;
use App\Models\Donor;
use App\Models\Recipient;
use Carbon\Carbon;
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
        // Get current date and date ranges
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();
        $thisYear = Carbon::now()->startOfYear();

        // Basic Statistics
        $stats = [
            'total_donors' => Donor::count(),
            'active_donors' => Donor::active()->count(),
            'eligible_donors' => Donor::eligible()->count(),
            'total_recipients' => Recipient::count(),
            'total_blood_units' => BloodUnit::count(),
            'available_units' => BloodUnit::where('status', 'available')->count(),
            'expired_units' => BloodUnit::where('expiry_date', '<', $today)->count(),
            'total_camps' => BloodCollectionCamp::count(),
            'completed_camps' => BloodCollectionCamp::completed()->count(),
            'pending_tests' => BloodTest::pending()->count(),
            'approved_tests' => BloodTest::approved()->count(),
            'quarantined_tests' => BloodTest::quarantined()->count(),
        ];

        return view('backend.dashboard.admin', compact('stats'));
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
