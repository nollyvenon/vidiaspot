@extends('admin.layout')

@section('title', 'Vendors Management')

@section('content')
<div class="admin-card">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold">Vendors Management</h2>
    </div>
    
    <!-- Filters -->
    <form method="GET" class="mb-4 p-4 bg-gray-50 rounded-lg">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="admin-form-label">Status</label>
                <select name="status" class="admin-form-select">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>
            </div>
            
            <div>
                <label class="admin-form-label">Verification</label>
                <select name="verified" class="admin-form-select">
                    <option value="">All</option>
                    <option value="yes" {{ request('verified') === 'yes' ? 'selected' : '' }}>Verified</option>
                    <option value="no" {{ request('verified') === 'no' ? 'selected' : '' }}>Not Verified</option>
                </select>
            </div>
            
            <div>
                <label class="admin-form-label">Featured</label>
                <select name="featured" class="admin-form-select">
                    <option value="">All</option>
                    <option value="yes" {{ request('featured') === 'yes' ? 'selected' : '' }}>Featured</option>
                    <option value="no" {{ request('featured') === 'no' ? 'selected' : '' }}>Not Featured</option>
                </select>
            </div>
            
            <div>
                <label class="admin-form-label">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Business Name or User" class="admin-form-input">
            </div>
            
            <div class="flex items-end space-x-2">
                <button type="submit" class="admin-btn admin-btn-primary">Filter</button>
                <a href="{{ route('admin.vendors.index') }}" class="admin-btn admin-btn-danger">Reset</a>
            </div>
        </div>
    </form>
    
    <!-- Vendors Table -->
    <div class="overflow-x-auto">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Business Name</th>
                    <th>Owner</th>
                    <th>Status</th>
                    <th>Verified</th>
                    <th>Featured</th>
                    <th>Rating</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($vendors as $vendor)
                <tr>
                    <td>{{ $vendor->id }}</td>
                    <td>{{ $vendor->business_name }}</td>
                    <td>
                        @if($vendor->user)
                            <a href="{{ route('admin.users.show', $vendor->user) }}" class="text-blue-600 hover:underline">
                                {{ $vendor->user->name }}
                            </a>
                        @else
                            N/A
                        @endif
                    </td>
                    <td>
                        <span class="status-badge status-{{ $vendor->status }}">
                            {{ ucfirst($vendor->status) }}
                        </span>
                    </td>
                    <td>
                        <span class="status-badge status-{{ $vendor->is_verified ? 'completed' : 'pending' }}">
                            {{ $vendor->is_verified ? 'Yes' : 'No' }}
                        </span>
                    </td>
                    <td>
                        <span class="status-badge status-{{ $vendor->is_featured ? 'completed' : 'pending' }}">
                            {{ $vendor->is_featured ? 'Yes' : 'No' }}
                        </span>
                    </td>
                    <td>{{ number_format($vendor->rating, 1) }}</td>
                    <td>{{ $vendor->created_at->format('Y-m-d') }}</td>
                    <td>
                        <a href="{{ route('admin.vendors.show', $vendor) }}" class="admin-btn admin-btn-primary admin-btn-sm">View</a>
                        @if($vendor->status === 'pending')
                            <button onclick="approveVendor({{ $vendor->id }})" class="admin-btn admin-btn-success admin-btn-sm">Approve</button>
                            <button onclick="rejectVendor({{ $vendor->id }})" class="admin-btn admin-btn-danger admin-btn-sm">Reject</button>
                        @elseif($vendor->status === 'approved')
                            <button onclick="suspendVendor({{ $vendor->id }})" class="admin-btn admin-btn-danger admin-btn-sm">Suspend</button>
                        @elseif($vendor->status === 'suspended')
                            <button onclick="approveVendor({{ $vendor->id }})" class="admin-btn admin-btn-success admin-btn-sm">Approve</button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center">No vendors found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="mt-4">
        {{ $vendors->appends(request()->query())->links() }}
    </div>
</div>

<script>
function approveVendor(vendorId) {
    if (!confirm('Are you sure you want to approve this vendor?')) {
        return;
    }
    
    fetch(`/admin/vendors/${vendorId}/approve`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
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
        alert('An error occurred while approving the vendor');
    });
}

function rejectVendor(vendorId) {
    const reason = prompt('Enter rejection reason:');
    if (!reason) return;
    
    fetch(`/admin/vendors/${vendorId}/reject`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ rejection_reason: reason })
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
        alert('An error occurred while rejecting the vendor');
    });
}

function suspendVendor(vendorId) {
    const reason = prompt('Enter suspension reason:');
    if (!reason) return;
    
    fetch(`/admin/vendors/${vendorId}/suspend`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ suspension_reason: reason })
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
        alert('An error occurred while suspending the vendor');
    });
}
</script>
@endsection