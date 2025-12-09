@extends('layouts.app')

@section('title', 'Payment Methods')
@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <h1 class="mb-4">Payment Methods</h1>
            
            <!-- Add Payment Method Form -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Add New Payment Method</h5>
                </div>
                <div class="card-body">
                    <form id="addPaymentMethodForm">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Payment Type</label>
                                <select class="form-select" name="type" required>
                                    <option value="">Select Payment Type</option>
                                    <option value="credit_card">Credit/Debit Card</option>
                                    <option value="paypal">PayPal</option>
                                    <option value="bitcoin">Bitcoin</option>
                                    <option value="ethereum">Ethereum</option>
                                    <option value="mpesa">M-Pesa</option>
                                    <option value="mobile_money">Mobile Money</option>
                                    <option value="klarna">Klarna</option>
                                    <option value="afterpay">Afterpay</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Name</label>
                                <input type="text" class="form-control" name="name" placeholder="My Visa Card, etc." required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Provider</label>
                                <input type="text" class="form-control" name="provider" placeholder="e.g., Visa, PayPal, Bitcoin">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Identifier</label>
                                <input type="text" class="form-control" name="identifier" placeholder="Card number, wallet address, etc.">
                            </div>
                        </div>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="is_default" id="isDefault">
                            <label class="form-check-label" for="isDefault">
                                Set as default payment method
                            </label>
                        </div>
                        
                        <button type="submit" class="btn btn-success">Add Payment Method</button>
                    </form>
                </div>
            </div>
            
            <!-- Payment Methods List -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Your Payment Methods</h5>
                </div>
                <div class="card-body">
                    <div id="paymentMethodsContainer">
                        <!-- Payment methods will be loaded here -->
                        <div class="text-center">
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

<!-- Payment Method Modal -->
<div class="modal fade" id="paymentMethodModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Payment Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="paymentDetailsContent">
                    <!-- Payment details will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load payment methods
    loadPaymentMethods();
    
    // Form submission
    document.getElementById('addPaymentMethodForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const paymentData = Object.fromEntries(formData.entries());
        
        fetch('/advanced-payments/methods', {
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
                // Reset form
                this.reset();
                
                // Show success message
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-success mt-3';
                alertDiv.textContent = 'Payment method added successfully!';
                document.getElementById('addPaymentMethodForm').appendChild(alertDiv);
                
                // Reload payment methods
                loadPaymentMethods();
                
                // Remove alert after 5 seconds
                setTimeout(() => alertDiv.remove(), 5000);
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while adding payment method');
        });
    });
    
    function loadPaymentMethods() {
        fetch('/advanced-payments/methods', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('paymentMethodsContainer');
            
            if (data.success && data.payment_methods.length > 0) {
                let html = '<div class="row">';
                
                data.payment_methods.forEach(method => {
                    html += `
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title">${method.method_name}</h6>
                                <p class="card-text">
                                    <small class="text-muted">${method.provider}</small><br>
                                    <span class="badge bg-${method.is_default ? 'success' : 'secondary'}">${method.is_default ? 'Default' : 'Normal'}</span>
                                    <span class="badge bg-${method.is_active ? 'success' : 'danger'} ms-1">${method.is_active ? 'Active' : 'Inactive'}</span>
                                </p>
                                <div class="d-flex justify-content-between">
                                    <small class="text-muted">${method.method_type}</small>
                                    <button class="btn btn-sm btn-outline-primary" onclick="showPaymentDetails(${method.id})">
                                        View
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>`;
                });
                
                html += '</div>';
                container.innerHTML = html;
            } else {
                container.innerHTML = '<div class="alert alert-info">No payment methods found. Add one above.</div>';
            }
        })
        .catch(error => {
            console.error('Error loading payment methods:', error);
            document.getElementById('paymentMethodsContainer').innerHTML = 
                '<div class="alert alert-danger">Error loading payment methods</div>';
        });
    }
    
    window.showPaymentDetails = function(id) {
        fetch(`/advanced-payments/methods/${id}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('paymentDetailsContent').innerHTML = `
                    <p><strong>Type:</strong> ${data.payment_method.method_type}</p>
                    <p><strong>Name:</strong> ${data.payment_method.method_name}</p>
                    <p><strong>Provider:</strong> ${data.payment_method.provider}</p>
                    <p><strong>Default:</strong> ${data.payment_method.is_default ? 'Yes' : 'No'}</p>
                    <p><strong>Active:</strong> ${data.payment_method.is_active ? 'Yes' : 'No'}</p>
                    <p><strong>Created:</strong> ${new Date(data.payment_method.created_at).toLocaleDateString()}</p>
                `;
                
                // Show modal
                new bootstrap.Modal(document.getElementById('paymentMethodModal')).show();
            }
        });
    };

    // Make default button
    window.makeDefault = function(id) {
        fetch(`/advanced-payments/methods/${id}/default`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadPaymentMethods();
                alert('Default payment method updated successfully!');
            } else {
                alert('Error: ' + data.message);
            }
        });
    };
});
</script>
@endsection