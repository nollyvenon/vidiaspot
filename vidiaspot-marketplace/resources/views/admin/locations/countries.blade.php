@extends('admin.layout')

@section('title', 'Countries Management')

@section('content')
<div class="admin-card">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold">Countries Management</h2>
        <button onclick="showCreateCountryModal()" class="admin-btn admin-btn-primary">Add Country</button>
    </div>
    
    <!-- Filters -->
    <form method="GET" class="mb-4 p-4 bg-gray-50 rounded-lg">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="admin-form-label">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Country name or code" class="admin-form-input">
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
                <a href="{{ route('admin.locations.countries') }}" class="admin-btn admin-btn-danger">Reset</a>
            </div>
        </div>
    </form>
    
    <!-- Countries Table -->
    <div class="overflow-x-auto">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Code</th>
                    <th>Phone Code</th>
                    <th>Currency</th>
                    <th>Active</th>
                    <th>States</th>
                    <th>Cities</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($countries as $country)
                <tr>
                    <td>{{ $country->id }}</td>
                    <td>{{ $country->name }}</td>
                    <td>{{ $country->code }}</td>
                    <td>{{ $country->phone_code ?? 'N/A' }}</td>
                    <td>{{ $country->currency_code }}</td>
                    <td>
                        <span class="status-badge status-{{ $country->is_active ? 'completed' : 'pending' }}">
                            {{ $country->is_active ? 'Yes' : 'No' }}
                        </span>
                    </td>
                    <td>{{ $country->states->count() }}</td>
                    <td>{{ $country->cities->count() }}</td>
                    <td>
                        <a href="{{ route('admin.locations.states', $country) }}" class="admin-btn admin-btn-primary admin-btn-sm">States</a>
                        <button onclick="editCountry({{ $country->id }})" class="admin-btn admin-btn-success admin-btn-sm">Edit</button>
                        <button onclick="deleteCountry({{ $country->id }})" class="admin-btn admin-btn-danger admin-btn-sm">Delete</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center">No countries found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="mt-4">
        {{ $countries->appends(request()->query())->links() }}
    </div>
</div>

<!-- Create/Edit Country Modal -->
<div id="country-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 id="country-modal-title" class="text-lg font-medium">Create Country</h3>
            <button onclick="closeCountryModal()" class="text-gray-500 hover:text-gray-700">&times;</button>
        </div>
        
        <form id="country-form">
            @csrf
            <input type="hidden" id="country-id" name="id">
            
            <div class="admin-form-group">
                <label class="admin-form-label">Name *</label>
                <input type="text" id="country-name" name="name" required class="admin-form-input" placeholder="Country name">
            </div>
            
            <div class="admin-form-group">
                <label class="admin-form-label">Code *</label>
                <input type="text" id="country-code" name="code" required class="admin-form-input" placeholder="3-letter code (e.g. NGN)">
            </div>
            
            <div class="admin-form-group">
                <label class="admin-form-label">Phone Code</label>
                <input type="text" id="country-phone-code" name="phone_code" class="admin-form-input" placeholder="+234">
            </div>
            
            <div class="admin-form-group">
                <label class="admin-form-label">Currency Code *</label>
                <input type="text" id="country-currency-code" name="currency_code" required class="admin-form-input" placeholder="NGN">
            </div>
            
            <div class="admin-form-group">
                <label class="admin-form-label">
                    <input type="checkbox" id="country-is-active" name="is_active" value="1" checked class="mr-2">
                    Active
                </label>
            </div>
            
            <div class="admin-form-group">
                <label class="admin-form-label">Flag Icon</label>
                <input type="text" id="country-flag-icon" name="flag_icon" class="admin-form-input" placeholder="e.g. 🇳🇬">
            </div>
            
            <div class="mt-4 flex justify-end space-x-2">
                <button type="button" onclick="closeCountryModal()" class="admin-btn admin-btn-danger">Cancel</button>
                <button type="submit" class="admin-btn admin-btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
function showCreateCountryModal() {
    document.getElementById('country-form').reset();
    document.getElementById('country-modal-title').textContent = 'Create Country';
    document.getElementById('country-id').value = '';
    document.getElementById('country-is-active').checked = true;
    document.getElementById('country-modal').classList.remove('hidden');
}

function editCountry(countryId) {
    fetch(`/admin/locations/countries/${countryId}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('country-modal-title').textContent = 'Edit Country';
            document.getElementById('country-id').value = data.id;
            document.getElementById('country-name').value = data.name;
            document.getElementById('country-code').value = data.code;
            document.getElementById('country-phone-code').value = data.phone_code || '';
            document.getElementById('country-currency-code').value = data.currency_code;
            document.getElementById('country-is-active').checked = data.is_active;
            document.getElementById('country-flag-icon').value = data.flag_icon || '';
            
            document.getElementById('country-modal').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading country data');
        });
}

function closeCountryModal() {
    document.getElementById('country-modal').classList.add('hidden');
}

document.getElementById('country-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const countryId = document.getElementById('country-id').value;
    
    let url, method;
    if (countryId) {
        url = `/admin/locations/countries/${countryId}`;
        method = 'PUT';
    } else {
        url = '/admin/locations/countries';
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
            closeCountryModal();
            location.reload();
        } else {
            alert(data.error || 'An error occurred');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while saving the country');
    });
});

function deleteCountry(countryId) {
    if (!confirm('Are you sure you want to delete this country? This action cannot be undone.')) {
        return;
    }
    
    fetch(`/admin/locations/countries/${countryId}`, {
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
        alert('An error occurred while deleting the country');
    });
}
</script>
@endsection