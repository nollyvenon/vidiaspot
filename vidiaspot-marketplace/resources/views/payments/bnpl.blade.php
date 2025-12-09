@extends('layouts.app')

@section('title', 'Buy Now Pay Later')
@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <h1 class="mb-4">Buy Now Pay Later</h1>
            
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                Shop now and pay later in flexible installments. Get approved instantly without affecting your credit score.
            </div>
            
            <!-- BNPL Form -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Set Up Buy Now Pay Later</h5>
                </div>
                <div class="card-body">
                    <form id="bnplForm">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Provider</label>
                                <select class="form-select" name="provider" required>
                                    <option value="">Select Provider</option>
                                    <option value="klarna">Klarna</option>
                                    <option value="afterpay">Afterpay</option>
                                    <option value="paypal_credit">PayPal Credit</option>
                                    <option value="affirm">Affirm</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Ad ID</label>
                                <input type="number" class="form-control" name="ad_id" placeholder="Enter Ad ID" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Total Amount (NGN)</label>
                                <input type="number" class="form-control" name="total_amount" placeholder="Enter total amount" step="0.01" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Down Payment (NGN)</label>
                                <input type="number" class="form-control" name="down_payment" value="0" placeholder="Down payment amount" step="0.01">
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Installment Count</label>
                                <select class="form-select" name="installment_count" required>
                                    <option value="4">4 installments</option>
                                    <option value="6">6 installments</option>
                                    <option value="12">12 installments</option>
                                    <option value="24">24 installments</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Installment Amount (NGN)</label>
                                <input type="number" class="form-control" name="installment_amount" placeholder="Calculated installment amount" step="0.01" readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Payment Frequency</label>
                                <select class="form-select" name="frequency" required>
                                    <option value="month">Monthly</option>
                                    <option value="week">Weekly</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">APR Rate (%)</label>
                            <input type="number" class="form-control" name="apr_rate" value="0" placeholder="Annual Percentage Rate" step="0.01">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">First Payment Date</label>
                            <input type="date" class="form-control" name="first_payment_date" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Transaction ID</label>
                            <input type="text" class="form-control" name="transaction_id" placeholder="Enter existing transaction ID" required>
                        </div>
                        
                        <button type="submit" class="btn btn-success">Process Buy Now Pay Later</button>
                    </form>
                </div>
            </div>
            
            <!-- BNPL Providers Info -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Available BNPL Providers</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 col-lg-3 mb-3">
                            <div class="card h-100 text-center">
                                <div class="card-body">
                                    <i class="fab fa-kickstarter fa-2x text-primary mb-2"></i>
                                    <h6 class="card-title">Klarna</h6>
                                    <p class="card-text small">
                                        4 equal installments, interest-free
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3 mb-3">
                            <div class="card h-100 text-center">
                                <div class="card-body">
                                    <i class="fas fa-shopping-cart fa-2x text-success mb-2"></i>
                                    <h6 class="card-title">Afterpay</h6>
                                    <p class="card-text small">
                                        4 payments, due every 2 weeks
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3 mb-3">
                            <div class="card h-100 text-center">
                                <div class="card-body">
                                    <i class="fab fa-paypal fa-2x text-info mb-2"></i>
                                    <h6 class="card-title">PayPal Credit</h6>
                                    <p class="card-text small">
                                        Revolving credit line
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3 mb-3">
                            <div class="card h-100 text-center">
                                <div class="card-body">
                                    <i class="fas fa-percentage fa-2x text-warning mb-2"></i>
                                    <h6 class="card-title">Affirm</h6>
                                    <p class="card-text small">
                                        Fixed monthly payments
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
    // Set default first payment date
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    document.querySelector('[name="first_payment_date"]').value = tomorrow.toISOString().split('T')[0];
    
    // Calculate installment amount when total amount changes
    document.querySelector('[name="total_amount"]').addEventListener('input', calculateInstallments);
    document.querySelector('[name="down_payment"]').addEventListener('input', calculateInstallments);
    document.querySelector('[name="installment_count"]').addEventListener('change', calculateInstallments);
    
    // Form submission
    document.getElementById('bnplForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const paymentData = Object.fromEntries(formData.entries());
        
        fetch('/advanced-payments/bnpl', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(paymentData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-success mt-3';
                alertDiv.innerHTML = `
                    <i class="fas fa-check-circle me-2"></i>
                    Buy now pay later setup completed successfully!<br>
                    Provider: ${data.bnpl.provider}<br>
                    Status: ${data.bnpl.status}<br>
                    Schedule: ${data.bnpl.installment_count} payments of ₦${data.bnpl.installment_amount} each
                `;
                document.getElementById('bnplForm').appendChild(alertDiv);
                
                // Reset form
                document.getElementById('bnplForm').reset();
                
                // Set default date again
                const tomorrow = new Date();
                tomorrow.setDate(tomorrow.getDate() + 1);
                document.querySelector('[name="first_payment_date"]').value = tomorrow.toISOString().split('T')[0];
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while setting up BNPL');
        });
    });
    
    function calculateInstallments() {
        const totalAmount = parseFloat(document.querySelector('[name="total_amount"]').value) || 0;
        const downPayment = parseFloat(document.querySelector('[name="down_payment"]').value) || 0;
        const installmentCount = parseInt(document.querySelector('[name="installment_count"]').value) || 1;
        
        const remainingAmount = totalAmount - downPayment;
        const installmentAmount = remainingAmount / installmentCount;
        
        document.querySelector('[name="installment_amount"]').value = installmentAmount.toFixed(2);
    }
});
</script>
@endsection