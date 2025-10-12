@extends('backend.layouts.master')

@section('content')
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">AI Donor Eligibility Predictor</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('backend.admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">AI Eligibility</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-robot mr-1"></i>
                                AI Donor Eligibility Predictor
                            </h3>
                        </div>
                        <div class="card-body">
                            <form id="eligibilityForm">
                                @csrf
                                <div class="form-group">
                                    <label for="donor_id">Select Donor</label>
                                    <select class="form-control" id="donor_id" name="donor_id" required>
                                        <option value="">Select a Donor</option>
                                        @foreach(\App\Models\Donor::all() as $donor)
                                        <option value="{{ $donor->id }}">
                                            {{ $donor->first_name }} {{ $donor->last_name }}
                                            ({{ $donor->userBloodGroup->value ?? 'N/A' }})
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-brain mr-1"></i>
                                        AI Eligibility Prediction
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-info-circle mr-1"></i>
                                AI Analysis Factors
                            </h3>
                        </div>
                        <div class="card-body">
                            <p>Our AI analyzes donor eligibility using:</p>
                            <ul>
                                <li>Age and weight requirements</li>
                                <li>Medical history patterns</li>
                                <li>Donation frequency analysis</li>
                                <li>Hemoglobin levels</li>
                                <li>Blood pressure readings</li>
                                <li>Previous donation intervals</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- AI Results -->
            <div class="row" id="aiResults" style="display: none;">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-chart-line mr-1"></i>
                                AI Eligibility Analysis
                            </h3>
                        </div>
                        <div class="card-body">
                            <div id="aiResultsContent">
                                <!-- AI results will be loaded here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Debug: Check if page loaded
    console.log('AI Eligibility page loaded');

    document.getElementById('eligibilityForm').addEventListener('submit', function(e) {
        e.preventDefault();

        console.log('Form submitted!');
        const formData = new FormData(this);
        console.log('Form data:', Object.fromEntries(formData));

        // Add loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Analyzing...';
        submitBtn.disabled = true;

        fetch('{{ route("backend.admin.ai.eligibility.predict") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log('AI Response:', data);
            if (data.error) {
                alert('Error: ' + data.error);
            } else {
                displayAIResults(data);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error predicting eligibility: ' + error.message);
        })
        .finally(() => {
            // Reset button state
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    });

    function displayAIResults(data) {
        document.getElementById('aiResults').style.display = 'block';

        const statusClass = data.eligible ? 'success' : 'danger';
        const statusIcon = data.eligible ? 'check-circle' : 'times-circle';

        document.getElementById('aiResultsContent').innerHTML = `
            <div class="alert alert-${statusClass}">
                <h4><i class="fas fa-${statusIcon}"></i> ${data.eligible ? 'ELIGIBLE' : 'NOT ELIGIBLE'}</h4>
                <p><strong>AI Confidence:</strong> ${data.confidence}%</p>
                ${data.next_donation_date ? `<p><strong>Next Donation Date:</strong> ${data.next_donation_date}</p>` : ''}
            </div>
            <div class="row">
                <div class="col-md-6">
                    <h5>AI Analysis:</h5>
                    <ul class="list-group">
                        ${data.reasons.map(reason => `<li class="list-group-item">${reason}</li>`).join('')}
                    </ul>
                </div>
                <div class="col-md-6">
                    <h5>AI Recommendations:</h5>
                    <ul class="list-group">
                        ${data.recommendations.map(rec => `<li class="list-group-item">${rec}</li>`).join('')}
                    </ul>
                </div>
            </div>
        `;
    }
</script>
@endpush
