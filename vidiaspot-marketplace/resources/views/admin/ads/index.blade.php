@extends('admin.layout')

@section('title', 'Ads Management')

@section('content')
<div class="admin-card">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold">Ads Management</h2>
        <a href="{{ route('admin.ads.featured') }}" class="admin-btn admin-btn-primary">Manage Featured Ads</a>
    </div>
    
    <!-- Filters -->
    <form method="GET" class="mb-4 p-4 bg-gray-50 rounded-lg">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="admin-form-label">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Title or description" class="admin-form-input">
            </div>
            
            <div>
                <label class="admin-form-label">Status</label>
                <select name="status" class="admin-form-select">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="sold" {{ request('status') === 'sold' ? 'selected' : '' }}>Sold</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            
            <div>
                <label class="admin-form-label">Category</label>
                <select name="category_id" class="admin-form-select">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="admin-form-label">User</label>
                <select name="user_id" class="admin-form-select">
                    <option value="">All Users</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="flex items-end space-x-2">
                <button type="submit" class="admin-btn admin-btn-primary">Filter</button>
                <a href="{{ route('admin.ads.index') }}" class="admin-btn admin-btn-danger">Reset</a>
            </div>
        </div>
    </form>
    
    <!-- Ads Table -->
    <div class="overflow-x-auto">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>User</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ads as $ad)
                <tr>
                    <td>{{ $ad->id }}</td>
                    <td>{{ Str::limit($ad->title, 30) }}</td>
                    <td>{{ $ad->category->name ?? 'N/A' }}</td>
                    <td>{{ $ad->user->name ?? 'N/A' }}</td>
                    <td>₦{{ number_format($ad->price, 2) }}</td>
                    <td>
                        <span class="status-badge status-{{ $ad->status }}">
                            {{ ucfirst($ad->status) }}
                        </span>
                    </td>
                    <td>{{ $ad->created_at->format('Y-m-d') }}</td>
                    <td>
                        <a href="{{ route('admin.ads.show', $ad) }}" class="admin-btn admin-btn-primary admin-btn-sm">View</a>
                        <button onclick="updateAdStatus({{ $ad->id }})" class="admin-btn admin-btn-success admin-btn-sm">Status</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center">No ads found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="mt-4">
        {{ $ads->appends(request()->query())->links() }}
    </div>
</div>

<script>
function updateAdStatus(adId) {
    const status = prompt('Enter new status (active, inactive, sold, pending, rejected):');
    if (!status) return;
    
    if (!['active', 'inactive', 'sold', 'pending', 'rejected'].includes(status)) {
        alert('Invalid status. Please enter one of: active, inactive, sold, pending, rejected');
        return;
    }
    
    fetch(`/admin/ads/${adId}/status`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ status: status })
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
        alert('An error occurred while updating ad status');
    });
}
</script>
@endsection