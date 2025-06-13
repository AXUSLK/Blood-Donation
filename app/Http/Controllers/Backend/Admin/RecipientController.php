<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lov;
use App\Models\Recipient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecipientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search      = $request->input('search');
        $blood_group = $request->input('blood_group');
        $gender      = $request->input('gender');
        $status      = $request->input('status');

        $recipients = Recipient::with(['userBloodGroup', 'userGender'])
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('patient_code', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('contact_number', 'like', "%{$search}%");
                });
            })
            ->when($blood_group, function ($q) use ($blood_group) {
                $q->where('blood_group', $blood_group);
            })
            ->when($gender, function ($q) use ($gender) {
                $q->where('gender', $gender);
            })
            ->when($status, function ($q) use ($status) {
                $q->where('request_status', $status);
            })
            ->orderByDesc('created_at')
            ->paginate(10);

        // Assuming category 1 = gender, category 3 = blood group in LOV
        $genders = Lov::where('lov_category_id', 1)->get();
        $bloodGroups = Lov::where('lov_category_id', 3)->get();
        $statuses = ['pending', 'accepted', 'fulfilled', 'rejected'];

        return view('backend.admin.recipients.index', compact('recipients', 'bloodGroups', 'genders', 'statuses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $genders = Lov::where('lov_category_id', 1)->get();
        $titles = Lov::where('lov_category_id', 2)->get();
        $bloodGroups = Lov::where('lov_category_id', 3)->get();
        return view('backend.admin.recipients.create', compact('genders', 'titles', 'bloodGroups'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_code'             => 'required|string|unique:recipients',
            'name'                     => 'required|string|max:255',
            'dob'                      => 'required|date',
            'gender'                   => 'required|string',
            'blood_group'              => 'required|string',
            'contact_number'           => 'required|string|max:20',
            'email'                    => 'nullable|email',
            'address'                  => 'nullable|string',
            'hospital_name'            => 'nullable|string',
            'doctor_name'              => 'required|string|max:255',
            'admission_date'           => 'nullable|date',
            'blood_required_date'      => 'nullable|date',
            'blood_quantity_required'  => 'nullable|integer|min:1',
            'request_status'           => 'required|in:pending,accepted,fulfilled,rejected',
            'diagnosis'                => 'required|string',
            'notes'                    => 'nullable|string',
        ]);

        $validated['created_by'] = Auth::id();

        Recipient::create($validated);

        return redirect()->route('backend.admin.recipients.index')->with('success', 'Recipient added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Recipient $recipient)
    {
        return view('backend.admin.recipients.show', compact('recipient'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Recipient $recipient)
    {
        $genders = Lov::where('lov_category_id', 1)->get();
        $titles = Lov::where('lov_category_id', 2)->get();
        $bloodGroups = Lov::where('lov_category_id', 3)->get();
        return view('backend.admin.recipients.edit', compact('recipient', 'genders', 'titles', 'bloodGroups'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Recipient $recipient)
    {
        $validated = $request->validate([
            'patient_code'             => 'required|string|unique:recipients,patient_code,' . $recipient->id,
            'name'                     => 'required|string|max:255',
            'dob'                      => 'required|date',
            'gender'                   => 'required|string',
            'blood_group'              => 'required|string',
            'contact_number'           => 'required|string|max:20',
            'email'                    => 'nullable|email',
            'address'                  => 'nullable|string',
            'hospital_name'            => 'nullable|string',
            'doctor_name'              => 'required|string|max:255',
            'admission_date'           => 'nullable|date',
            'blood_required_date'      => 'nullable|date',
            'blood_quantity_required'  => 'nullable|integer|min:1',
            'request_status'           => 'required|in:pending,accepted,fulfilled,rejected',
            'diagnosis'                => 'required|string',
            'notes'                    => 'nullable|string',
        ]);

        $recipient->update($validated);

        return redirect()->route('backend.admin.recipients.index')->with('success', 'Recipient updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Recipient $recipient)
    {
        $recipient->delete();
        return redirect()->route('backend.admin.recipients.index')->with('success', 'Recipient deleted successfully.');
    }
}
