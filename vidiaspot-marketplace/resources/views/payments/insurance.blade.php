@extends('layouts.app')

@section('title', 'Purchase Insurance')
@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <h1 class="mb-4">Purchase Insurance</h1>
            
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                Protect your purchase with our comprehensive insurance options. Get peace of mind knowing your items are covered.
            </div>
            
            <!-- Insurance Form -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Purchase Insurance</h5>
                </div>
                <div class="card-body">
                    <form id="insuranceForm">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Item Type</label>
                                <select class="form-select" name="type" required>
                                    <option value="">Select Item Type</option>
                                    <option value="device_protection">Device Protection</option>
                                    <option value="product_insurance">Product Insurance</option>
                                    <option value="delivery_insurance">Delivery Insurance</option>
                                    <option value="high_value_item">High Value Item Coverage</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Ad ID</label>
                                <input type="number" class="form-control" name="ad_id" placeholder="Enter Ad ID" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Insurance Provider</label>
                                <select class="form-select" name="provider" required>
                                    <option value="">Select Provider</option>
                                    <option value="allianz">Allianz</option>
                                    <option value="africover">Africover</option>
                                    <option value="insurance_company_ng">Insurance Company of Nigeria</option>
                                    <option value="starcomms">Starcomms</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Risk Level</label>
                                <select class="form-select" name="risk_level" required>
                                    <option value="low">Low Risk</option>
                                    <option value="medium">Medium Risk</option>
                                    <option value="high">High Risk</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Premium Amount (NGN)</label>
                                <input type="number" class="form-control" name="premium_amount" placeholder="Enter premium amount" step="0.01" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Coverage Amount (NGN)</label>
                                <input type="number" class="form-control" name="coverage_amount" placeholder="Enter coverage amount" step="0.01" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Effective From</label>
                                <input type="date" class="form-control" name="effective_from" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Effective Until</label>
                                <input type="date" class="form-control" name="effective_until" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Terms and Conditions</label>
                            <textarea class="form-control" name="terms" rows="3" placeholder="Insurance terms and conditions"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Transaction ID</label>
                            <input type="text" class="form-control" name="transaction_id" placeholder="Enter existing transaction ID" required>
                        </div>
                        
                        <button type="submit" class="btn btn-success">Purchase Insurance</button>
                    </form>
                </div>
            </div>
            
            <!-- Insurance Providers -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Insurance Providers</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 col-lg-3 mb-3">
                            <div class="card h-100 text-center">
                                <div class="card-body">
                                    <i class="fas fa-shield-alt fa-2x text-primary mb-2"></i>
                                    <h6 class="card-title">Allianz</h6>
                                    <p class="card-text small">
                                        Comprehensive protection for all items
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3 mb-3">
                            <div class="card h-100 text-center">
                                <div class="card-body">
                                    <i class="fas fa-shield-alt fa-2x text-success mb-2"></i>
                                    <h6 class="card-title">Africover</h6>
                                    <p class="card-text small">
                                        Digital-first insurance solutions
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3 mb-3">
                            <div class="card h-100 text-center">
                                <div class="card-body">
                                    <i class="fas fa-shield-alt fa-2x text-warning mb-2"></i>
                                    <h6 class="card-title">Insurance Company of Nigeria</h6>
                                    <p class="card-text small">
                                        Trusted local insurance provider
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3 mb-3">
                            <div class="card h-100 text-center">
                                <div class="card-body">
                                    <i class="fas fa-shield-alt fa-2x text-info mb-2"></i>
                                    <h6 class="card-title">Starcomms</h6>
                                    <p class="card-text small">
                                        Technology-focused insurance
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set today as default effective date
    const today = new Date();
    document.querySelector('[name="effective_from"]').value = today.toISOString().split('T')[0];
    
    // Set effective until to 365 days from today
    const oneYearFromToday = new Date();
    oneYearFromToday.setDate(oneYearFromToday.getDate() + 365);
    document.querySelector('[name="effective_until"]').value = oneYearFromToday.toISOString().split('T')[0];
    
    // Form submission
    document.getElementById('insuranceForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const insuranceData = Object.fromEntries(formData.entries());
        
        fetch('/advanced-payments/insurance', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(insuranceData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-success mt-3';
                alertDiv.innerHTML = `
                    <i class="fas fa-check-circle me-2"></i>
                    Insurance purchased successfully!<br>
                    Policy Number: ${data.insurance.policy_number}<br>
                    Provider: ${data.insurance.provider}<br>
                    Coverage: ₦${data.insurance.coverage_amount}<br>
                    Premium Paid: ₦${data.insurance.premium_amount}<br>
                    Status: ${data.insurance.status}
                `;
                document.getElementById('insuranceForm').appendChild(alertDiv);
                
                // Reset form
                document.getElementById('insuranceForm').reset();
                
                // Set default dates again
                const today = new Date();
                document.querySelector('[name="effective_from"]').value = today.toISOString().split('T')[0];
                
                const oneYearFromToday = new Date();
                oneYearFromToday.setDate(oneYearFromToday.getDate() + 365);
                document.querySelector('[name="effective_until"]').value = oneYearFromToday.toISOString().split('T')[0];
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while purchasing insurance');
        });
    });
});
</script>
@endsection