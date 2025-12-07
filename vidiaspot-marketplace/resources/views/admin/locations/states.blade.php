@extends('admin.layout')

@section('title', 'States Management')

@section('content')
<div class="admin-card">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold">States in {{ $country->name }}</h2>
        <div>
            <a href="{{ route('admin.locations.countries') }}" class="admin-btn admin-btn-primary mr-2">← Back to Countries</a>
            <button onclick="showCreateStateModal()" class="admin-btn admin-btn-primary">Add State</button>
        </div>
    </div>
    
    <!-- Filters -->
    <form method="GET" class="mb-4 p-4 bg-gray-50 rounded-lg">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="admin-form-label">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="State name" class="admin-form-input">
            </div>
            
            <div class="flex items-end space-x-2">
                <button type="submit" class="admin-btn admin-btn-primary">Filter</button>
                <a href="{{ route('admin.locations.states', $country) }}" class="admin-btn admin-btn-danger">Reset</a>
            </div>
        </div>
    </form>
    
    <!-- States Table -->
    <div class="overflow-x-auto">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Cities</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($states as $state)
                <tr>
                    <td>{{ $state->id }}</td>
                    <td>{{ $state->name }}</td>
                    <td>{{ $state->cities->count() }}</td>
                    <td>
                        <a href="{{ route('admin.locations.cities', $state) }}" class="admin-btn admin-btn-primary admin-btn-sm">Cities</a>
                        <button onclick="editState({{ $state->id }})" class="admin-btn admin-btn-success admin-btn-sm">Edit</button>
                        <button onclick="deleteState({{ $state->id }})" class="admin-btn admin-btn-danger admin-btn-sm">Delete</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center">No states found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="mt-4">
        {{ $states->appends(request()->query())->links() }}
    </div>
</div>

<!-- Create/Edit State Modal -->
<div id="state-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 id="state-modal-title" class="text-lg font-medium">Create State</h3>
            <button onclick="closeStateModal()" class="text-gray-500 hover:text-gray-700">&times;</button>
        </div>
        
        <form id="state-form">
            @csrf
            <input type="hidden" id="state-id" name="id">
            <input type="hidden" name="country_id" value="{{ $country->id }}">
            
            <div class="admin-form-group">
                <label class="admin-form-label">Name *</label>
                <input type="text" id="state-name" name="name" required class="admin-form-input" placeholder="State name">
            </div>
            
            <div class="mt-4 flex justify-end space-x-2">
                <button type="button" onclick="closeStateModal()" class="admin-btn admin-btn-danger">Cancel</button>
                <button type="submit" class="admin-btn admin-btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
function showCreateStateModal() {
    document.getElementById('state-form').reset();
    document.getElementById('state-modal-title').textContent = 'Create State';
    document.getElementById('state-id').value = '';
    document.getElementById('state-modal').classList.remove('hidden');
}

function editState(stateId) {
    fetch(`/admin/locations/states/${stateId}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('state-modal-title').textContent = 'Edit State';
            document.getElementById('state-id').value = data.id;
            document.getElementById('state-name').value = data.name;
            
            document.getElementById('state-modal').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading state data');
        });
}

function closeStateModal() {
    document.getElementById('state-modal').classList.add('hidden');
}

document.getElementById('state-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const stateId = document.getElementById('state-id').value;
    
    let url, method;
    if (stateId) {
        url = `/admin/locations/states/${stateId}`;
        method = 'PUT';
    } else {
        url = '/admin/locations/states';
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
            closeStateModal();
            location.reload();
        } else {
            alert(data.error || 'An error occurred');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while saving the state');
    });
});

function deleteState(stateId) {
    if (!confirm('Are you sure you want to delete this state? This action cannot be undone.')) {
        return;
    }
    
    fetch(`/admin/locations/states/${stateId}`, {
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
        alert('An error occurred while deleting the state');
    });
}
</script>
@endsection