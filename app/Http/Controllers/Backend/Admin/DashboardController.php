<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use App\Models\Donor;
use App\Models\Recipient;
use App\Models\BloodInventory;
use App\Models\BloodUnit;
use App\Models\BloodCollectionCamp;
use App\Models\BloodTest;
use App\Models\DonationHistory;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
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

        // Blood Group Distribution
        $bloodGroupStats = $this->getBloodGroupDistribution();

        // Monthly Donation Trends
        $monthlyDonations = $this->getMonthlyDonationTrends();

        // Camp Performance
        $campPerformance = $this->getCampPerformance();

        // Test Results Summary
        $testResults = $this->getTestResultsSummary();

        // Recent Activities
        $recentActivities = $this->getRecentActivities();

        // Low Stock Alerts
        $lowStockAlerts = $this->getLowStockAlerts();

        // Expiry Alerts
        $expiryAlerts = $this->getExpiryAlerts();

        return view('backend.admin.dashboard.index', compact(
            'stats',
            'bloodGroupStats',
            'monthlyDonations',
            'campPerformance',
            'testResults',
            'recentActivities',
            'lowStockAlerts',
            'expiryAlerts'
        ));
    }

    private function getBloodGroupDistribution()
    {
        $bloodGroups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
        $distribution = [];

        foreach ($bloodGroups as $group) {
            $distribution[$group] = [
                'available' => BloodUnit::where('blood_group', $group)
                    ->where('status', 'available')
                    ->count(),
                'total' => BloodUnit::where('blood_group', $group)->count(),
                'donors' => Donor::whereHas('userBloodGroup', function($query) use ($group) {
                    $query->where('blood_group', $group);
                })->count(),
            ];
        }

        return $distribution;
    }

    private function getMonthlyDonationTrends()
    {
        $months = [];
        $donations = [];

        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $months[] = $month->format('M Y');

            $donations[] = DonationHistory::whereYear('donation_date', $month->year)
                ->whereMonth('donation_date', $month->month)
                ->count();
        }

        return [
            'months' => $months,
            'donations' => $donations
        ];
    }

    private function getCampPerformance()
    {
        return BloodCollectionCamp::with(['createBy'])
            ->orderBy('start_date', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($camp) {
                return [
                    'name' => $camp->name,
                    'location' => $camp->location,
                    'start_date' => $camp->start_date->format('M d, Y'),
                    'target_donors' => $camp->target_donors,
                    'actual_donors' => $camp->actual_donors,
                    'success_rate' => $camp->getSuccessRate(),
                    'status' => $camp->status,
                ];
            });
    }

    private function getTestResultsSummary()
    {
        return [
            'total_tests' => BloodTest::count(),
            'pending' => BloodTest::pending()->count(),
            'approved' => BloodTest::approved()->count(),
            'quarantined' => BloodTest::quarantined()->count(),
            'rejected' => BloodTest::rejected()->count(),
            'recent_tests' => BloodTest::with(['bloodUnit', 'technician'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
        ];
    }

    private function getRecentActivities()
    {
        $activities = collect();

        // Recent donations
        $recentDonations = DonationHistory::with(['donor'])
            ->orderBy('donation_date', 'desc')
            ->limit(3)
            ->get()
            ->map(function ($donation) {
                return [
                    'type' => 'donation',
                    'message' => "New donation from {$donation->donor->first_name} {$donation->donor->last_name}",
                    'date' => $donation->donation_date->format('M d, Y H:i'),
                    'icon' => 'fas fa-heart text-danger'
                ];
            });

        // Recent blood units
        $recentUnits = BloodUnit::with(['donor'])
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get()
            ->map(function ($unit) {
                return [
                    'type' => 'blood_unit',
                    'message' => "New blood unit {$unit->unit_id} added",
                    'date' => $unit->created_at->format('M d, Y H:i'),
                    'icon' => 'fas fa-tint text-primary'
                ];
            });

        // Recent tests
        $recentTests = BloodTest::with(['bloodUnit'])
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get()
            ->map(function ($test) {
                return [
                    'type' => 'test',
                    'message' => "Test completed for unit {$test->bloodUnit->unit_id}",
                    'date' => $test->created_at->format('M d, Y H:i'),
                    'icon' => 'fas fa-flask text-info'
                ];
            });

        return $activities->merge($recentDonations)
            ->merge($recentUnits)
            ->merge($recentTests)
            ->sortByDesc('date')
            ->take(10);
    }

    private function getLowStockAlerts()
    {
        $bloodGroups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
        $alerts = [];

        foreach ($bloodGroups as $group) {
            $available = BloodUnit::where('blood_group', $group)
                ->where('status', 'available')
                ->count();

            if ($available < 5) { // Threshold for low stock
                $alerts[] = [
                    'blood_group' => $group,
                    'available_units' => $available,
                    'status' => $available == 0 ? 'critical' : 'low'
                ];
            }
        }

        return $alerts;
    }

    private function getExpiryAlerts()
    {
        $today = Carbon::today();
        $sevenDaysFromNow = $today->copy()->addDays(7);

        return BloodUnit::where('status', 'available')
            ->whereBetween('expiry_date', [$today, $sevenDaysFromNow])
            ->with(['donor'])
            ->orderBy('expiry_date', 'asc')
            ->limit(10)
            ->get()
            ->map(function ($unit) {
                $daysUntilExpiry = $unit->expiry_date->diffInDays(Carbon::today());
                return [
                    'unit_id' => $unit->unit_id,
                    'blood_group' => $unit->blood_group,
                    'expiry_date' => $unit->expiry_date->format('M d, Y'),
                    'days_until_expiry' => $daysUntilExpiry,
                    'status' => $daysUntilExpiry <= 3 ? 'critical' : 'warning'
                ];
            });
    }

    public function reports()
    {
        return view('backend.admin.dashboard.reports');
    }

    public function generateReport(Request $request)
    {
        $request->validate([
            'report_type' => 'required|in:donors,blood_units,donations,tests,camps',
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'format' => 'required|in:pdf,excel'
        ]);

        $reportType = $request->report_type;
        $dateFrom = Carbon::parse($request->date_from);
        $dateTo = Carbon::parse($request->date_to);

        switch ($reportType) {
            case 'donors':
                return $this->generateDonorReport($dateFrom, $dateTo, $request->format);
            case 'blood_units':
                return $this->generateBloodUnitReport($dateFrom, $dateTo, $request->format);
            case 'donations':
                return $this->generateDonationReport($dateFrom, $dateTo, $request->format);
            case 'tests':
                return $this->generateTestReport($dateFrom, $dateTo, $request->format);
            case 'camps':
                return $this->generateCampReport($dateFrom, $dateTo, $request->format);
        }
    }

    private function generateDonorReport($dateFrom, $dateTo, $format)
    {
        $donors = Donor::with(['userBloodGroup', 'userGender', 'userTitle'])
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->get();

        // For now, return JSON data (in real implementation, you'd generate PDF/Excel)
        return response()->json([
            'report_type' => 'Donor Report',
            'period' => $dateFrom->format('M d, Y') . ' - ' . $dateTo->format('M d, Y'),
            'total_donors' => $donors->count(),
            'data' => $donors
        ]);
    }

    private function generateBloodUnitReport($dateFrom, $dateTo, $format)
    {
        $units = BloodUnit::with(['donor'])
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->get();

        return response()->json([
            'report_type' => 'Blood Unit Report',
            'period' => $dateFrom->format('M d, Y') . ' - ' . $dateTo->format('M d, Y'),
            'total_units' => $units->count(),
            'data' => $units
        ]);
    }

    private function generateDonationReport($dateFrom, $dateTo, $format)
    {
        $donations = DonationHistory::with(['donor'])
            ->whereBetween('donation_date', [$dateFrom, $dateTo])
            ->get();

        return response()->json([
            'report_type' => 'Donation Report',
            'period' => $dateFrom->format('M d, Y') . ' - ' . $dateTo->format('M d, Y'),
            'total_donations' => $donations->count(),
            'data' => $donations
        ]);
    }

    private function generateTestReport($dateFrom, $dateTo, $format)
    {
        $tests = BloodTest::with(['bloodUnit', 'technician'])
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->get();

        return response()->json([
            'report_type' => 'Test Report',
            'period' => $dateFrom->format('M d, Y') . ' - ' . $dateTo->format('M d, Y'),
            'total_tests' => $tests->count(),
            'data' => $tests
        ]);
    }

    private function generateCampReport($dateFrom, $dateTo, $format)
    {
        $camps = BloodCollectionCamp::with(['createdBy'])
            ->whereBetween('start_date', [$dateFrom, $dateTo])
            ->get();

        return response()->json([
            'report_type' => 'Camp Report',
            'period' => $dateFrom->format('M d, Y') . ' - ' . $dateTo->format('M d, Y'),
            'total_camps' => $camps->count(),
            'data' => $camps
        ]);
    }
}
