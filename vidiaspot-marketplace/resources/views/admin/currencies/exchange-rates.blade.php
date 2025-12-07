@extends('admin.layout')

@section('title', 'Exchange Rates Management')

@section('content')
<div class="admin-card">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold">Exchange Rates Management</h2>
        <div>
            <a href="{{ route('admin.currencies.index') }}" class="admin-btn admin-btn-primary mr-2">← Back to Currencies</a>
            <button onclick="showCreateExchangeRateModal()" class="admin-btn admin-btn-primary">Add Exchange Rate</button>
        </div>
    </div>
    
    <!-- Exchange Rates Table -->
    <div class="overflow-x-auto">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>From Currency</th>
                    <th>To Currency</th>
                    <th>Rate</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($exchangeRates as $exchangeRate)
                <tr>
                    <td>{{ $exchangeRate->id }}</td>
                    <td>{{ $exchangeRate->fromCurrency->name }} ({{ $exchangeRate->fromCurrency->code }})</td>
                    <td>{{ $exchangeRate->toCurrency->name }} ({{ $exchangeRate->toCurrency->code }})</td>
                    <td>{{ $exchangeRate->rate }}</td>
                    <td>
                        <button onclick="editExchangeRate({{ $exchangeRate->id }})" class="admin-btn admin-btn-success admin-btn-sm">Edit</button>
                        <button onclick="deleteExchangeRate({{ $exchangeRate->id }})" class="admin-btn admin-btn-danger admin-btn-sm">Delete</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center">No exchange rates found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="mt-4">
        {{ $exchangeRates->links() }}
    </div>
</div>

<!-- Create/Edit Exchange Rate Modal -->
<div id="exchange-rate-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 id="exchange-rate-modal-title" class="text-lg font-medium">Create Exchange Rate</h3>
            <button onclick="closeExchangeRateModal()" class="text-gray-500 hover:text-gray-700">&times;</button>
        </div>
        
        <form id="exchange-rate-form">
            @csrf
            <input type="hidden" id="exchange-rate-id" name="id">
            
            <div class="admin-form-group">
                <label class="admin-form-label">From Currency *</label>
                <select id="from-currency-code" name="from_currency_code" required class="admin-form-select">
                    <option value="">Select currency</option>
                    @php
                        $availableCurrencies = \App\Models\Currency::all();
                    @endphp
                    @foreach($availableCurrencies as $currency)
                        <option value="{{ $currency->code }}">{{ $currency->name }} ({{ $currency->code }})</option>
                    @endforeach
                </select>
            </div>

            <div class="admin-form-group">
                <label class="admin-form-label">To Currency *</label>
                <select id="to-currency-code" name="to_currency_code" required class="admin-form-select">
                    <option value="">Select currency</option>
                    @foreach($availableCurrencies as $currency)
                        <option value="{{ $currency->code }}">{{ $currency->name }} ({{ $currency->code }})</option>
                    @endforeach
                </select>
            </div>
            
            <div class="admin-form-group">
                <label class="admin-form-label">Rate *</label>
                <input type="number" step="any" id="exchange-rate-value" name="rate" required class="admin-form-input" placeholder="1.00" min="0">
            </div>
            
            <div class="mt-4 flex justify-end space-x-2">
                <button type="button" onclick="closeExchangeRateModal()" class="admin-btn admin-btn-danger">Cancel</button>
                <button type="submit" class="admin-btn admin-btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
function showCreateExchangeRateModal() {
    document.getElementById('exchange-rate-form').reset();
    document.getElementById('exchange-rate-modal-title').textContent = 'Create Exchange Rate';
    document.getElementById('exchange-rate-id').value = '';
    document.getElementById('exchange-rate-modal').classList.remove('hidden');
}

function editExchangeRate(exchangeRateId) {
    fetch(`/admin/exchange-rates/${exchangeRateId}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('exchange-rate-modal-title').textContent = 'Edit Exchange Rate';
            document.getElementById('exchange-rate-id').value = data.id;
            document.getElementById('from-currency-code').value = data.from_currency_code;
            document.getElementById('to-currency-code').value = data.to_currency_code;
            document.getElementById('exchange-rate-value').value = data.rate;
            
            document.getElementById('exchange-rate-modal').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading exchange rate data');
        });
}

function closeExchangeRateModal() {
    document.getElementById('exchange-rate-modal').classList.add('hidden');
}

document.getElementById('exchange-rate-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const exchangeRateId = document.getElementById('exchange-rate-id').value;
    
    let url, method;
    if (exchangeRateId) {
        url = `/admin/exchange-rates/${exchangeRateId}`;
        method = 'PUT';
    } else {
        url = '/admin/exchange-rates';
        method = 'POST';
    }
    
    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(Object.fromEntries(formData))
    })
    .then(response => response.json())
    .then(data => {
        if (data.message) {
            alert(data.message);
            closeExchangeRateModal();
            location.reload();
        } else {
            alert(data.error || 'An error occurred');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while saving the exchange rate');
    });
});

function deleteExchangeRate(exchangeRateId) {
    if (!confirm('Are you sure you want to delete this exchange rate? This action cannot be undone.')) {
        return;
    }
    
    fetch(`/admin/exchange-rates/${exchangeRateId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.message) {
            alert(data.message);
            location.reload();
        } else {
            alert(data.error || 'An error occurred');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while deleting the exchange rate');
    });
}
</script>
@endsection