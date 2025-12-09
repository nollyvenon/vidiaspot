@extends('layouts.app')

@section('title', 'Split Payment')
@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <h1 class="mb-4">Split Payment</h1>
            
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                Split the cost of an item with friends, family, or colleagues. Everyone pays their share individually.
            </div>
            
            <!-- Split Payment Form -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Set Up Split Payment</h5>
                </div>
                <div class="card-body">
                    <form id="splitPaymentForm">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Ad ID</label>
                                <input type="number" class="form-control" name="ad_id" placeholder="Enter Ad ID" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Group Title</label>
                                <input type="text" class="form-control" name="title" placeholder="e.g., Group Purchase, Office Gift" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" placeholder="Describe the purpose of this group purchase"></textarea>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Total Amount (NGN)</label>
                                <input type="number" class="form-control" name="total_amount" placeholder="Enter total amount" step="0.01" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Participants Count</label>
                                <input type="number" class="form-control" name="participant_count" min="2" value="2" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Expires In (Days)</label>
                                <input type="number" class="form-control" name="expires_in_days" min="1" max="365" value="30" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Split Link</label>
                            <input type="text" class="form-control" id="splitLink" readonly>
                            <div class="form-text">Copy and share this link with participants to join the split payment</div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Transaction ID</label>
                            <input type="text" class="form-control" name="transaction_id" placeholder="Enter existing transaction ID" required>
                        </div>
                        
                        <button type="submit" class="btn btn-success">Create Split Payment</button>
                    </form>
                </div>
            </div>
            
            <!-- Recent Split Payments -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Your Recent Split Payments</h5>
                </div>
                <div class="card-body">
                    <div id="recentSplitPayments" class="row">
                        <div class="col-12 text-center">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">Loading...</span>
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
    // Calculate per person amount
    document.querySelector('[name="total_amount"]').addEventListener('input', calculatePerPerson);
    document.querySelector('[name="participant_count"]').addEventListener('input', calculatePerPerson);
    
    // Form submission
    document.getElementById('splitPaymentForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const paymentData = Object.fromEntries(formData.entries());
        
        fetch('/advanced-payments/split', {
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
                    Split payment created successfully!<br>
                    Title: ${data.split_payment.title}<br>
                    Status: ${data.split_payment.status}<br>
                    Each participant pays: ₦${(data.split_payment.total_amount / data.split_payment.participant_count).toFixed(2)}
                `;
                document.getElementById('splitPaymentForm').appendChild(alertDiv);
                
                // Update split link
                const splitLinkId = data.split_payment.id;
                const splitLink = `${window.location.origin}/advanced-payments/split/${splitLinkId}/join`;
                document.getElementById('splitLink').value = splitLink;
                
                // Reset form
                document.getElementById('splitPaymentForm').reset();
                document.getElementById('splitLink').value = '';
                
                // Load recent split payments
                loadRecentSplitPayments();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while creating split payment');
        });
    });
    
    function calculatePerPerson() {
        const totalAmount = parseFloat(document.querySelector('[name="total_amount"]').value) || 0;
        const participantCount = parseInt(document.querySelector('[name="participant_count"]').value) || 1;
        
        if (participantCount > 0) {
            const perPerson = totalAmount / participantCount;
            document.querySelector('[name="per_person"]').value = perPerson.toFixed(2);
        }
    }
    
    function loadRecentSplitPayments() {
        fetch('/advanced-payments/split/recent', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('recentSplitPayments');
            
            if (data.success && data.split_payments.length > 0) {
                let html = '';
                
                data.split_payments.forEach(payment => {
                    html += `
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title">${payment.title}</h6>
                                <p class="card-text">
                                    <small class="text-muted">ID: ${payment.id}</small><br>
                                    Amount: ₦${payment.total_amount}<br>
                                    Participants: ${payment.participant_count}<br>
                                    Status: <span class="badge bg-${payment.status === 'completed' ? 'success' : 'warning'}">${payment.status}</span><br>
                                    Expires: ${new Date(payment.expires_at).toLocaleDateString()}<br>
                                </p>
                                <div class="d-flex justify-content-between">
                                    <small class="text-muted">${payment.created_at}</small>
                                    <button class="btn btn-sm btn-outline-primary" onclick="viewSplitPayment(${payment.id})">
                                        View
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>`;
                });
                
                container.innerHTML = html;
            } else {
                container.innerHTML = '<div class="col-12"><p class="text-muted">No recent split payments found</p></div>';
            }
        })
        .catch(error => {
            console.error('Error loading recent payments:', error);
            document.getElementById('recentSplitPayments').innerHTML = 
                '<div class="col-12"><div class="alert alert-danger">Error loading recent payments</div></div>';
        });
    }
    
    function viewSplitPayment(id) {
        alert(`Viewing split payment details for ID: ${id}`);
    }
});
</script>
@endsection