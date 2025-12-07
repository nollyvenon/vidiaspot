@extends('admin.layout')

@section('title', 'Currencies Management')

@section('content')
<div class="admin-card">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold">Currencies Management</h2>
        <button onclick="showCreateCurrencyModal()" class="admin-btn admin-btn-primary">Add Currency</button>
    </div>
    
    <!-- Filters -->
    <form method="GET" class="mb-4 p-4 bg-gray-50 rounded-lg">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="admin-form-label">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Currency name or code" class="admin-form-input">
            </div>
            
            <div>
                <label class="admin-form-label">Active Status</label>
                <select name="active" class="admin-form-select">
                    <option value="">All</option>
                    <option value="yes" {{ request('active') === 'yes' ? 'selected' : '' }}>Active</option>
                    <option value="no" {{ request('active') === 'no' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            
            <div class="flex items-end space-x-2">
                <button type="submit" class="admin-btn admin-btn-primary">Filter</button>
                <a href="{{ route('admin.currencies.index') }}" class="admin-btn admin-btn-danger">Reset</a>
            </div>
        </div>
    </form>
    
    <!-- Currencies Table -->
    <div class="overflow-x-auto">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Code</th>
                    <th>Symbol</th>
                    <th>Format</th>
                    <th>Active</th>
                    <th>Decimal Places</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($currencies as $currency)
                <tr>
                    <td>{{ $currency->id }}</td>
                    <td>{{ $currency->name }}</td>
                    <td>{{ $currency->code }}</td>
                    <td>{{ $currency->symbol }}</td>
                    <td>{{ $currency->format }}</td>
                    <td>
                        <span class="status-badge status-{{ $currency->is_active ? 'completed' : 'pending' }}">
                            {{ $currency->is_active ? 'Yes' : 'No' }}
                        </span>
                    </td>
                    <td>{{ $currency->decimal_places }}</td>
                    <td>
                        <button onclick="editCurrency({{ $currency->id }})" class="admin-btn admin-btn-success admin-btn-sm">Edit</button>
                        @if($currency->code !== 'NGN')
                            <button onclick="deleteCurrency({{ $currency->id }})" class="admin-btn admin-btn-danger admin-btn-sm">Delete</button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center">No currencies found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="mt-4">
        {{ $currencies->appends(request()->query())->links() }}
    </div>
    
    <!-- Navigation to Exchange Rates -->
    <div class="mt-6">
        <a href="{{ route('admin.currencies.exchange-rates') }}" class="admin-btn admin-btn-primary">Manage Exchange Rates</a>
    </div>
</div>

<!-- Create/Edit Currency Modal -->
<div id="currency-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 id="currency-modal-title" class="text-lg font-medium">Create Currency</h3>
            <button onclick="closeCurrencyModal()" class="text-gray-500 hover:text-gray-700">&times;</button>
        </div>
        
        <form id="currency-form">
            @csrf
            <input type="hidden" id="currency-id" name="id">
            
            <div class="admin-form-group">
                <label class="admin-form-label">Name *</label>
                <input type="text" id="currency-name" name="name" required class="admin-form-input" placeholder="Currency name">
            </div>
            
            <div class="admin-form-group">
                <label class="admin-form-label">Code *</label>
                <input type="text" id="currency-code" name="code" required class="admin-form-input" placeholder="3-letter code (e.g. USD)">
            </div>
            
            <div class="admin-form-group">
                <label class="admin-form-label">Symbol *</label>
                <input type="text" id="currency-symbol" name="symbol" required class="admin-form-input" placeholder="e.g. $, €, ₦">
            </div>
            
            <div class="admin-form-group">
                <label class="admin-form-label">Format</label>
                <input type="text" id="currency-format" name="format" class="admin-form-input" placeholder="{{'{{symbol}}{{amount}}'}}">
                <p class="text-sm text-gray-500 mt-1">Use {{'{{symbol}}'}} and {{'{{amount}}'}} as placeholders</p>
            </div>
            
            <div class="admin-form-group">
                <label class="admin-form-label">Thousand Separator</label>
                <input type="text" id="currency-thousand-separator" name="thousand_separator" class="admin-form-input" value=",">
            </div>
            
            <div class="admin-form-group">
                <label class="admin-form-label">Decimal Separator</label>
                <input type="text" id="currency-decimal-separator" name="decimal_separator" class="admin-form-input" value=".">
            </div>
            
            <div class="admin-form-group">
                <label class="admin-form-label">Decimal Places</label>
                <input type="number" id="currency-decimal-places" name="decimal_places" min="0" max="6" class="admin-form-input" value="2">
            </div>
            
            <div class="admin-form-group">
                <label class="admin-form-label">
                    <input type="checkbox" id="currency-is-active" name="is_active" value="1" checked class="mr-2">
                    Active
                </label>
            </div>
            
            <div class="mt-4 flex justify-end space-x-2">
                <button type="button" onclick="closeCurrencyModal()" class="admin-btn admin-btn-danger">Cancel</button>
                <button type="submit" class="admin-btn admin-btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
function showCreateCurrencyModal() {
    document.getElementById('currency-form').reset();
    document.getElementById('currency-modal-title').textContent = 'Create Currency';
    document.getElementById('currency-id').value = '';
    document.getElementById('currency-is-active').checked = true;
    document.getElementById('currency-thousand-separator').value = ',';
    document.getElementById('currency-decimal-separator').value = '.';
    document.getElementById('currency-decimal-places').value = '2';
    document.getElementById('currency-format').value = '{{symbol}}{{amount}}';
    document.getElementById('currency-modal').classList.remove('hidden');
}

function editCurrency(currencyId) {
    fetch(`/admin/currencies/${currencyId}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('currency-modal-title').textContent = 'Edit Currency';
            document.getElementById('currency-id').value = data.id;
            document.getElementById('currency-name').value = data.name;
            document.getElementById('currency-code').value = data.code;
            document.getElementById('currency-symbol').value = data.symbol;
            document.getElementById('currency-format').value = data.format;
            document.getElementById('currency-thousand-separator').value = data.thousand_separator;
            document.getElementById('currency-decimal-separator').value = data.decimal_separator;
            document.getElementById('currency-decimal-places').value = data.decimal_places;
            document.getElementById('currency-is-active').checked = data.is_active;
            
            document.getElementById('currency-modal').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading currency data');
        });
}

function closeCurrencyModal() {
    document.getElementById('currency-modal').classList.add('hidden');
}

document.getElementById('currency-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const currencyId = document.getElementById('currency-id').value;
    
    let url, method;
    if (currencyId) {
        url = `/admin/currencies/${currencyId}`;
        method = 'PUT';
    } else {
        url = '/admin/currencies';
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
            closeCurrencyModal();
            location.reload();
        } else {
            alert(data.error || 'An error occurred');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while saving the currency');
    });
});

function deleteCurrency(currencyId) {
    if (!confirm('Are you sure you want to delete this currency? This action cannot be undone.')) {
        return;
    }
    
    fetch(`/admin/currencies/${currencyId}`, {
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
        alert('An error occurred while deleting the currency');
    });
}
</script>
@endsection