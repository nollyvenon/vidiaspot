@extends('admin.layout')

@section('title', 'Cities Management')

@section('content')
<div class="admin-card">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold">Cities in {{ $state->name }}, {{ $country->name }}</h2>
        <div>
            <a href="{{ route('admin.locations.states', $country) }}" class="admin-btn admin-btn-primary mr-2">← Back to States</a>
            <button onclick="showCreateCityModal()" class="admin-btn admin-btn-primary">Add City</button>
        </div>
    </div>
    
    <!-- Filters -->
    <form method="GET" class="mb-4 p-4 bg-gray-50 rounded-lg">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="admin-form-label">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="City name" class="admin-form-input">
            </div>
            
            <div class="flex items-end space-x-2">
                <button type="submit" class="admin-btn admin-btn-primary">Filter</button>
                <a href="{{ route('admin.locations.cities', $state) }}" class="admin-btn admin-btn-danger">Reset</a>
            </div>
        </div>
    </form>
    
    <!-- Cities Table -->
    <div class="overflow-x-auto">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($cities as $city)
                <tr>
                    <td>{{ $city->id }}</td>
                    <td>{{ $city->name }}</td>
                    <td>
                        <button onclick="editCity({{ $city->id }})" class="admin-btn admin-btn-success admin-btn-sm">Edit</button>
                        <button onclick="deleteCity({{ $city->id }})" class="admin-btn admin-btn-danger admin-btn-sm">Delete</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="text-center">No cities found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="mt-4">
        {{ $cities->appends(request()->query())->links() }}
    </div>
</div>

<!-- Create/Edit City Modal -->
<div id="city-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 id="city-modal-title" class="text-lg font-medium">Create City</h3>
            <button onclick="closeCityModal()" class="text-gray-500 hover:text-gray-700">&times;</button>
        </div>
        
        <form id="city-form">
            @csrf
            <input type="hidden" id="city-id" name="id">
            <input type="hidden" name="state_id" value="{{ $state->id }}">
            
            <div class="admin-form-group">
                <label class="admin-form-label">Name *</label>
                <input type="text" id="city-name" name="name" required class="admin-form-input" placeholder="City name">
            </div>
            
            <div class="mt-4 flex justify-end space-x-2">
                <button type="button" onclick="closeCityModal()" class="admin-btn admin-btn-danger">Cancel</button>
                <button type="submit" class="admin-btn admin-btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
function showCreateCityModal() {
    document.getElementById('city-form').reset();
    document.getElementById('city-modal-title').textContent = 'Create City';
    document.getElementById('city-id').value = '';
    document.getElementById('city-modal').classList.remove('hidden');
}

function editCity(cityId) {
    fetch(`/admin/locations/cities/${cityId}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('city-modal-title').textContent = 'Edit City';
            document.getElementById('city-id').value = data.id;
            document.getElementById('city-name').value = data.name;
            
            document.getElementById('city-modal').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading city data');
        });
}

function closeCityModal() {
    document.getElementById('city-modal').classList.add('hidden');
}

document.getElementById('city-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const cityId = document.getElementById('city-id').value;
    
    let url, method;
    if (cityId) {
        url = `/admin/locations/cities/${cityId}`;
        method = 'PUT';
    } else {
        url = '/admin/locations/cities';
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
            closeCityModal();
            location.reload();
        } else {
            alert(data.error || 'An error occurred');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while saving the city');
    });
});

function deleteCity(cityId) {
    if (!confirm('Are you sure you want to delete this city? This action cannot be undone.')) {
        return;
    }
    
    fetch(`/admin/locations/cities/${cityId}`, {
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
        alert('An error occurred while deleting the city');
    });
}
</script>
@endsection