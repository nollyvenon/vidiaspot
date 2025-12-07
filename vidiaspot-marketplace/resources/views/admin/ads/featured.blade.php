@extends('admin.layout')

@section('title', 'Featured Ads Management')

@section('content')
<div class="admin-card">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold">Featured Ads Management</h2>
        <a href="{{ route('admin.ads.index') }}" class="admin-btn admin-btn-primary">← Back to Ads</a>
    </div>
    
    <!-- Filters -->
    <form method="GET" class="mb-4 p-4 bg-gray-50 rounded-lg">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="admin-form-label">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Ad title" class="admin-form-input">
            </div>
            
            <div>
                <label class="admin-form-label">Status</label>
                <select name="status" class="admin-form-select">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                </select>
            </div>
            
            <div class="flex items-end space-x-2">
                <button type="submit" class="admin-btn admin-btn-primary">Filter</button>
                <a href="{{ route('admin.ads.featured') }}" class="admin-btn admin-btn-danger">Reset</a>
            </div>
        </div>
    </form>
    
    <!-- Featured Ads Table -->
    <div class="overflow-x-auto">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Ad Title</th>
                    <th>Category</th>
                    <th>User</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($featuredAds as $featuredAd)
                <tr>
                    <td>{{ $featuredAd->id }}</td>
                    <td>{{ Str::limit($featuredAd->ad->title, 30) }}</td>
                    <td>{{ $featuredAd->ad->category->name ?? 'N/A' }}</td>
                    <td>{{ $featuredAd->ad->user->name ?? 'N/A' }}</td>
                    <td>{{ $featuredAd->start_date->format('Y-m-d') }}</td>
                    <td>{{ $featuredAd->end_date->format('Y-m-d') }}</td>
                    <td>
                        <span class="status-badge status-{{ $featuredAd->is_active ? 'completed' : 'pending' }}">
                            {{ $featuredAd->is_active ? 'Active' : 'Expired' }}
                        </span>
                    </td>
                    <td>
                        <button onclick="removeFeatured({{ $featuredAd->id }})" class="admin-btn admin-btn-danger admin-btn-sm">Remove</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center">No featured ads found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="mt-4">
        {{ $featuredAds->appends(request()->query())->links() }}
    </div>
</div>

<script>
function removeFeatured(featuredAdId) {
    if (!confirm('Are you sure you want to remove this ad from featured status?')) {
        return;
    }
    
    fetch(`/admin/featured-ads/${featuredAdId}`, {
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
        alert('An error occurred while removing featured status');
    });
}
</script>
@endsection