@extends('layouts.app')

@section('title', 'Tax Calculator')
@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <h1 class="mb-4">Tax Calculation</h1>
            
            <div class="alert alert-info">
                <i class="fas fa-calculator me-2"></i>
                Automatically calculate taxes based on your location. Our system applies the correct tax rates for your region.
            </div>
            
            <!-- Tax Calculator -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Calculate Tax</h5>
                </div>
                <div class="card-body">
                    <form id="taxCalculatorForm">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Amount (NGN)</label>
                                <input type="number" class="form-control" id="amountInput" name="amount" placeholder="Enter amount" step="0.01" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Location</label>
                                <select class="form-select" id="locationSelect" name="location" required>
                                    <option value="">Select Location</option>
                                    <option value="Lagos">Lagos State - 7.5%</option>
                                    <option value="Abuja">Abuja (FCT) - 5%</option>
                                    <option value="Kano">Kano State - 5%</option>
                                    <option value="Rivers">Rivers State - 7.5%</option>
                                    <option value="Ogun">Ogun State - 5%</option>
                                    <option value="Oyo">Oyo State - 5%</option>
                                    <option value="Enugu">Enugu State - 7.5%</option>
                                    <option value="Anambra">Anambra State - 7.5%</option>
                                    <option value="Delta">Delta State - 7.5%</option>
                                    <option value="Edo">Edo State - 7.5%</option>
                                    <option value="Cross River">Cross River State - 7.5%</option>
                                    <option value="Akwa Ibom">Akwa Ibom State - 7.5%</option>
                                    <option value="Imo">Imo State - 5%</option>
                                    <option value="others">Other States - 7.5%</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">Calculate Tax</button>
                            <button type="button" class="btn btn-secondary" id="resetBtn">Reset</button>
                        </div>
                    </form>
                    
                    <!-- Results -->
                    <div id="taxResults" class="mt-4" style="display: none;">
                        <h6>Tax Calculation Results</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tr>
                                    <td><strong>Original Amount:</strong></td>
                                    <td id="originalAmount">₦0.00</td>
                                </tr>
                                <tr>
                                    <td><strong>Tax Rate:</strong></td>
                                    <td id="taxRate">0%</td>
                                </tr>
                                <tr>
                                    <td><strong>Tax Amount:</strong></td>
                                    <td id="taxAmount">₦0.00</td>
                                </tr>
                                <tr>
                                    <td><strong>Total Amount (with tax):</strong></td>
                                    <td id="totalWithTax">₦0.00</td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="mt-3">
                            <h6>Tax Breakdown</h6>
                            <div id="taxBreakdown" class="border p-3 bg-light rounded">
                                <!-- Tax breakdown will be displayed here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tax Rates by State -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Tax Rates by Location</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>State</th>
                                    <th>Tax Rate</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Lagos</td>
                                    <td>7.5%</td>
                                    <td>VAT + State tax</td>
                                </tr>
                                <tr>
                                    <td>Abuja (FCT)</td>
                                    <td>5%</td>
                                    <td>Federal Capital Territory tax</td>
                                </tr>
                                <tr>
                                    <td>Kano</td>
                                    <td>5%</td>
                                    <td>State tax</td>
                                </tr>
                                <tr>
                                    <td>Rivers</td>
                                    <td>7.5%</td>
                                    <td>VAT + State tax</td>
                                </tr>
                                <tr>
                                    <td>Ogun</td>
                                    <td>5%</td>
                                    <td>State tax</td>
                                </tr>
                                <tr>
                                    <td>Oyo</td>
                                    <td>5%</td>
                                    <td>State tax</td>
                                </tr>
                                <tr>
                                    <td>Others</td>
                                    <td>7.5%</td>
                                    <td>Standard VAT rate</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        <h6>About Tax Calculation</h6>
                        <p class="mb-2">Our tax calculator automatically applies the appropriate tax rates based on the delivery location of your purchase. This ensures compliance with local tax regulations.</p>
                        <p class="mb-0"><strong>Note:</strong> The calculated tax is for estimation purposes. Actual tax charged during checkout may vary slightly due to rounding or additional local taxes.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const taxCalculatorForm = document.getElementById('taxCalculatorForm');
    const resetBtn = document.getElementById('resetBtn');
    
    // Set default location to Lagos
    document.getElementById('locationSelect').value = 'Lagos';
    
    // Calculate tax on form submission
    taxCalculatorForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const amount = parseFloat(document.getElementById('amountInput').value);
        const location = document.getElementById('locationSelect').value;
        
        if (!amount || isNaN(amount)) {
            alert('Please enter a valid amount');
            return;
        }
        
        // In a real application, we would call the API endpoint
        // For now, let's simulate the calculation
        const taxCalculation = {
            tax_rate: location === 'Abuja' || location === 'Kano' || location === 'Ogun' || location === 'Oyo' || location === 'Imo' ? 0.05 : 0.075,
            tax_amount: amount * (location === 'Abuja' || location === 'Kano' || location === 'Ogun' || location === 'Oyo' || location === 'Imo' ? 0.05 : 0.075),
            total_with_tax: amount + (amount * (location === 'Abuja' || location === 'Kano' || location === 'Ogun' || location === 'Oyo' || location === 'Imo' ? 0.05 : 0.075)),
            breakdown: {
                original_amount: amount,
                tax_rate: (location === 'Abuja' || location === 'Kano' || location === 'Ogun' || location === 'Oyo' || location === 'Imo' ? 0.05 : 0.075) * 100 + '%',
                tax_amount: amount * (location === 'Abuja' || location === 'Kano' || location === 'Ogun' || location === 'Oyo' || location === 'Imo' ? 0.05 : 0.075),
                total_with_tax: amount + (amount * (location === 'Abuja' || location === 'Kano' || location === 'Ogun' || location === 'Oyo' || location === 'Imo' ? 0.05 : 0.075))
            }
        };
        
        // Update the UI with results
        document.getElementById('originalAmount').textContent = '₦' + amount.toFixed(2);
        document.getElementById('taxRate').textContent = taxCalculation.breakdown.tax_rate;
        document.getElementById('taxAmount').textContent = '₦' + taxCalculation.tax_amount.toFixed(2);
        document.getElementById('totalWithTax').textContent = '₦' + taxCalculation.total_with_tax.toFixed(2);
        
        // Update breakdown
        document.getElementById('taxBreakdown').innerHTML = `
            <p class="mb-1"><strong>Original Amount:</strong> ₦${taxCalculation.breakdown.original_amount.toFixed(2)}</p>
            <p class="mb-1"><strong>Tax Rate:</strong> ${taxCalculation.breakdown.tax_rate}</p>
            <p class="mb-1"><strong>Tax Amount:</strong> ₦${taxCalculation.breakdown.tax_amount.toFixed(2)}</p>
            <p class="mb-0"><strong>Total with Tax:</strong> ₦${taxCalculation.total_with_tax.toFixed(2)}</p>
        `;
        
        // Show results
        document.getElementById('taxResults').style.display = 'block';
    });
    
    // Reset button
    resetBtn.addEventListener('click', function() {
        document.getElementById('taxCalculatorForm').reset();
        document.getElementById('locationSelect').value = 'Lagos';
        document.getElementById('taxResults').style.display = 'none';
    });
});
</script>
@endsection