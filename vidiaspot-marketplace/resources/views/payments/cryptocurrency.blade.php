@extends('layouts.app')

@section('title', 'Cryptocurrency Payments')
@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <h1 class="mb-4">Cryptocurrency Payments</h1>
            
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                Pay with popular cryptocurrencies like Bitcoin, Ethereum, and more. 
                All transactions are secured and verified on the blockchain.
            </div>
            
            <!-- Crypto Payment Form -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Make Cryptocurrency Payment</h5>
                </div>
                <div class="card-body">
                    <form id="cryptoPaymentForm">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Cryptocurrency</label>
                                <select class="form-select" name="currency" required>
                                    <option value="">Select Cryptocurrency</option>
                                    <option value="BTC">Bitcoin (BTC)</option>
                                    <option value="ETH">Ethereum (ETH)</option>
                                    <option value="USDT">Tether (USDT)</option>
                                    <option value="USDC">USD Coin (USDC)</option>
                                    <option value="BNB">Binance Coin (BNB)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Amount (NGN)</label>
                                <input type="number" class="form-control" name="amount_ngn" placeholder="Enter amount in Naira" step="0.01" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Wallet Address</label>
                            <input type="text" class="form-control" name="wallet_address" placeholder="Enter your wallet address" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Exchange Rate</label>
                            <div class="input-group">
                                <input type="number" class="form-control" name="exchange_rate" placeholder="1 BTC = ? NGN" step="0.0001" readonly>
                                <button class="btn btn-outline-primary" type="button" id="fetchRateBtn">Fetch Rate</button>
                            </div>
                            <div class="form-text">Current exchange rates will be fetched automatically</div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Amount (Crypto)</label>
                            <input type="number" class="form-control" name="amount_crypto" placeholder="Calculated amount in cryptocurrency" step="0.00000001" readonly>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Transaction ID</label>
                            <input type="text" class="form-control" name="transaction_id" placeholder="Enter existing transaction ID" required>
                        </div>
                        
                        <button type="submit" class="btn btn-success">Process Cryptocurrency Payment</button>
                    </form>
                </div>
            </div>
            
            <!-- Supported Cryptocurrencies -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Supported Cryptocurrencies</h5>
                </div>
                <div class="card-body">
                    <div id="supportedCryptosContainer">
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load supported cryptocurrencies
    loadSupportedCryptos();
    
    // Fetch current rates
    document.getElementById('fetchRateBtn').addEventListener('click', fetchCryptoRate);
    
    // Form submission
    document.getElementById('cryptoPaymentForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const paymentData = Object.fromEntries(formData.entries());
        
        fetch('/advanced-payments/cryptocurrency', {
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
                    Cryptocurrency payment processed successfully!<br>
                    Transaction Hash: ${data.payment.transaction_hash || 'N/A'}<br>
                    Status: ${data.payment.status}
                `;
                document.getElementById('cryptoPaymentForm').appendChild(alertDiv);
                
                // Reset form
                document.getElementById('cryptoPaymentForm').reset();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while processing payment');
        });
    });
    
    function loadSupportedCryptos() {
        fetch('/advanced-payments/cryptocurrency/supported', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('supportedCryptosContainer');
            
            if (data.success && data.currencies.length > 0) {
                let html = '<div class="row">';
                
                data.currencies.forEach(crypto => {
                    html += `
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <i class="fab fa-bitcoin fa-2x text-warning mb-2"></i>
                                <h6 class="card-title">${crypto}</h6>
                                <p class="card-text">
                                    <small class="text-muted">Secure blockchain transaction</small>
                                </p>
                            </div>
                        </div>
                    </div>`;
                });
                
                html += '</div>';
                container.innerHTML = html;
            } else {
                container.innerHTML = '<div class="alert alert-info">Unable to load supported cryptocurrencies</div>';
            }
        })
        .catch(error => {
            console.error('Error loading cryptos:', error);
            document.getElementById('supportedCryptosContainer').innerHTML = 
                '<div class="alert alert-danger">Error loading supported cryptocurrencies</div>';
        });
    }
    
    function fetchCryptoRate() {
        const currency = document.querySelector('[name="currency"]').value;
        const amountNgn = parseFloat(document.querySelector('[name="amount_ngn"]').value);
        
        if (!currency || !amountNgn) {
            alert('Please select currency and enter amount in NGN');
            return;
        }
        
        // In a real application, this would fetch actual rates from an API
        // For demo, we'll use mock rates
        const mockRates = {
            'BTC': 25000000, // 1 BTC = 25,000,000 NGN
            'ETH': 1500000,  // 1 ETH = 1,500,000 NGN
            'USDT': 1560,    // 1 USDT = 1,560 NGN
            'USDC': 1560,    // 1 USDC = 1,560 NGN
            'BNB': 450000    // 1 BNB = 450,000 NGN
        };
        
        const rate = mockRates[currency] || 0;
        const cryptoAmount = amountNgn / rate;
        
        document.querySelector('[name="exchange_rate"]').value = rate.toFixed(2);
        document.querySelector('[name="amount_crypto"]').value = cryptoAmount.toFixed(8);
    }
});
</script>
@endsection