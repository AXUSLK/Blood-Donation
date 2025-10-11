<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use App\Models\Donor;
use App\Models\Lov;
use Illuminate\Http\Request;
use App\Services\Backend\Admin\DonorStoreService;
use App\Services\Backend\Admin\DonorUpdateService;
use Illuminate\Support\Facades\Auth;

class DonorController extends Controller
{
    protected $storeService, $updateService;

    /**
     * Constructor method for the the class.
     * Create a new instance.
     * Initializes necessary dependencies.
     *
     * @return void
     */
    public function __construct(DonorStoreService $storeService, DonorUpdateService $updateService)
    {
        $this->storeService = $storeService;
        $this->updateService = $updateService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $bloodGroup = $request->input('blood_group');
        $gender = $request->input('gender');
        $eligibility = $request->input('eligibility');

        $donors = Donor::with(['userBloodGroup', 'userGender', 'userTitle'])
            ->when($search, fn($q) => $q->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('donor_id', 'like', "%{$search}%");
            }))
            ->when($bloodGroup, fn($q) => $q->where('blood_group', $bloodGroup))
            ->when($gender, fn($q) => $q->where('gender', $gender))
            ->when($eligibility !== null, fn($q) => $q->where('is_eligible', $eligibility))
            ->paginate(10);

        $genders = Lov::where('lov_category_id', 1)->get();
        $bloodGroups = Lov::where('lov_category_id', 3)->get();

        return view('backend.admin.donors.index', compact('donors', 'bloodGroups', 'genders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $genders = Lov::where('lov_category_id', 1)->get();
        $titles = Lov::where('lov_category_id', 2)->get();
        $bloodGroups = Lov::where('lov_category_id', 3)->get();

        return view('backend.admin.donors.create', compact('genders', 'titles', 'bloodGroups'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->storeService->store($request->all());
        return redirect()->route('backend.admin.donors.index')->with('success', 'Donor registered successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Donor $donor)
    {
        $donor->load(['userBloodGroup', 'userGender', 'userTitle', 'donationHistories.technician']);

        return view('backend.admin.donors.show', compact('donor'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Donor $donor)
    {
        $genders = Lov::where('lov_category_id', 1)->get();
        $titles = Lov::where('lov_category_id', 2)->get();
        $bloodGroups = Lov::where('lov_category_id', 3)->get();

        return view('backend.admin.donors.edit', compact('genders', 'titles', 'bloodGroups', 'donor'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Donor $donor)
    {
        $this->updateService->update($donor, $request->all());
        return redirect()->route('backend.admin.donors.index')->with('success', 'Donor updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Donor $donor)
    {
        $donor->delete();
        return redirect()->route('backend.admin.donors.index')->with('success', 'Donor deleted successfully.');
    }

    /**
     * Toggle donor eligibility status.
     */
    public function toggleEligibility(Donor $donor)
    {
        $donor->update([
            'is_eligible' => !$donor->is_eligible,
            'updated_by' => Auth::user()->id,
        ]);

        $status = $donor->is_eligible ? 'eligible' : 'ineligible';
        return redirect()->back()->with('success', "Donor marked as {$status}.");
    }

    /**
     * Get donor statistics for dashboard.
     */
    public function getStats()
    {
        $totalDonors = Donor::count();
        $eligibleDonors = Donor::where('is_eligible', true)->count();
        $newDonorsThisMonth = Donor::whereMonth('created_at', now()->month)->count();
        $totalDonations = Donor::sum('total_donations');

        return response()->json([
            'total_donors' => $totalDonors,
            'eligible_donors' => $eligibleDonors,
            'new_donors_this_month' => $newDonorsThisMonth,
            'total_donations' => $totalDonations,
        ]);
    }
}
