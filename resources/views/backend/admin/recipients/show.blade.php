@extends('backend.layouts.master')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2 align-items-center">
                <div class="col-sm-6">
                    <h1 class="mb-0">Recipient Details</h1>
                </div>
                <div class="col-sm-6 d-flex justify-content-end align-items-center">
                    <a href="{{ route('backend.admin.recipients.index') }}" class="btn btn-sm btn-secondary mr-2">Back to List</a>
                    <a href="{{ route('backend.admin.recipients.edit', $recipient->id) }}" class="btn btn-sm btn-info">Edit</a>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title mb-0">Recipient Information</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered mb-0">
                        <tr>
                            <th width="220">Patient Code</th>
                            <td>{{ $recipient->patient_code }}</td>
                        </tr>
                        <tr>
                            <th>Name</th>
                            <td>{{ $recipient->name }}</td>
                        </tr>
                        <tr>
                            <th>Date of Birth</th>
                            <td>{{ $recipient->dob }}</td>
                        </tr>
                        <tr>
                            <th>Gender</th>
                            <td>{{ $recipient->userGender?->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Blood Group</th>
                            <td>{{ $recipient->userBloodGroup?->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Contact Number</th>
                            <td>{{ $recipient->contact_number }}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>{{ $recipient->email ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Address</th>
                            <td>{{ $recipient->address ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Hospital Name</th>
                            <td>{{ $recipient->hospital_name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Doctor Name</th>
                            <td>{{ $recipient->doctor_name }}</td>
                        </tr>
                        <tr>
                            <th>Admission Date</th>
                            <td>{{ $recipient->admission_date ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Blood Required Date</th>
                            <td>{{ $recipient->blood_required_date ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Blood Quantity Required</th>
                            <td>{{ $recipient->blood_quantity_required ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Request Status</th>
                            <td>
                                <span class="badge
                                    @if($recipient->request_status=='pending') badge-warning
                                    @elseif($recipient->request_status=='accepted') badge-info
                                    @elseif($recipient->request_status=='fulfilled') badge-success
                                    @elseif($recipient->request_status=='rejected') badge-danger
                                    @endif">
                                    {{ ucfirst($recipient->request_status) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Diagnosis</th>
                            <td>{{ $recipient->diagnosis }}</td>
                        </tr>
                        <tr>
                            <th>Notes</th>
                            <td>{{ $recipient->notes ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Created By</th>
                            <td>{{ $recipient->createdBy?->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Created At</th>
                            <td>{{ $recipient->created_at }}</td>
                        </tr>
                        <tr>
                            <th>Updated At</th>
                            <td>{{ $recipient->updated_at }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection
