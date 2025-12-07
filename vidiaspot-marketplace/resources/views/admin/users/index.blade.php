@extends('admin.layout')

@section('title', 'Users Management')

@section('content')
<div class="admin-card">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold">Users Management</h2>
    </div>
    
    <!-- Filters -->
    <form method="GET" class="mb-4 p-4 bg-gray-50 rounded-lg">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="admin-form-label">Role</label>
                <select name="role" class="admin-form-select">
                    <option value="">All Roles</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ request('role') === $role->name ? 'selected' : '' }}>{{ $role->display_name ?? $role->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="admin-form-label">Verification Status</label>
                <select name="verified" class="admin-form-select">
                    <option value="">All Statuses</option>
                    <option value="yes" {{ request('verified') === 'yes' ? 'selected' : '' }}>Verified</option>
                    <option value="no" {{ request('verified') === 'no' ? 'selected' : '' }}>Not Verified</option>
                </select>
            </div>
            
            <div>
                <label class="admin-form-label">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, Email, Phone" class="admin-form-input">
            </div>
            
            <div class="flex items-end space-x-2">
                <button type="submit" class="admin-btn admin-btn-primary">Filter</button>
                <a href="{{ route('admin.users.index') }}" class="admin-btn admin-btn-danger">Reset</a>
            </div>
        </div>
    </form>
    
    <!-- Users Table -->
    <div class="overflow-x-auto">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Verified</th>
                    <th>Vendor?</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @foreach($user->roles as $role)
                            <span class="status-badge status-pending">{{ $role->display_name ?? $role->name }}</span>
                        @endforeach
                    </td>
                    <td>
                        <span class="status-badge status-{{ $user->is_verified ? 'completed' : 'pending' }}">
                            {{ $user->is_verified ? 'Yes' : 'No' }}
                        </span>
                    </td>
                    <td>
                        @if($user->vendor)
                            <span class="status-badge status-completed">Yes</span>
                        @else
                            <span class="status-badge status-pending">No</span>
                        @endif
                    </td>
                    <td>{{ $user->created_at->format('Y-m-d') }}</td>
                    <td>
                        <a href="{{ route('admin.users.show', $user) }}" class="admin-btn admin-btn-primary admin-btn-sm">View</a>
                        <button onclick="toggleUserVerification({{ $user->id }}, {{ $user->is_verified ? 'false' : 'true' }})" 
                                class="admin-btn admin-btn-{{ $user->is_verified ? 'danger' : 'success' }} admin-btn-sm">
                            {{ $user->is_verified ? 'Unverify' : 'Verify' }}
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center">No users found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="mt-4">
        {{ $users->appends(request()->query())->links() }}
    </div>
</div>

<script>
function toggleUserVerification(userId, verify) {
    if (!confirm(`Are you sure you want to ${verify ? 'verify' : 'unverify'} this user?`)) {
        return;
    }
    
    fetch(`/admin/users/${userId}/verification`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ is_verified: verify })
    })
    .then(response => response.json())
    .then(data => {
        if (data.message) {
            alert(data.message);
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating user verification status');
    });
}
</script>
@endsection