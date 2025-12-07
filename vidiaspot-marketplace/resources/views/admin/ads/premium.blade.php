@extends('admin.layout')

@section('title', 'Premium Ads Management')

@section('content')
<div class="admin-card">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold">Premium Ads Management</h2>
        <a href="{{ route('admin.ads.index') }}" class="admin-btn admin-btn-primary">← Back to Ads</a>
    </div>
    
    <!-- Filters -->
    <form method="GET" class="mb-4 p-4 bg-gray-50 rounded-lg">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="admin-form-label">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Ad title" class="admin-form-input">
            </div>
            
            <div class="flex items-end space-x-2">
                <button type="submit" class="admin-btn admin-btn-primary">Filter</button>
                <a href="{{ route('admin.ads.premium') }}" class="admin-btn admin-btn-danger">Reset</a>
            </div>
        </div>
    </form>
    
    <!-- Premium Ads Table -->
    <div class="overflow-x-auto">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Ad Title</th>
                    <th>Category</th>
                    <th>User</th>
                    <th>Price</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($premiumAds as $premiumAd)
                <tr>
                    <td>{{ $premiumAd->id }}</td>
                    <td>{{ Str::limit($premiumAd->ad->title, 30) }}</td>
                    <td>{{ $premiumAd->ad->category->name ?? 'N/A' }}</td>
                    <td>{{ $premiumAd->ad->user->name ?? 'N/A' }}</td>
                    <td>₦{{ number_format($premiumAd->price, 2) }}</td>
                    <td>{{ $premiumAd->start_date->format('Y-m-d') }}</td>
                    <td>{{ $premiumAd->end_date->format('Y-m-d') }}</td>
                    <td>
                        <button onclick="removePremium({{ $premiumAd->id }})" class="admin-btn admin-btn-danger admin-btn-sm">Remove</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center">No premium ads found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="mt-4">
        {{ $premiumAds->appends(request()->query())->links() }}
    </div>
</div>

<script>
function removePremium(premiumAdId) {
    if (!confirm('Are you sure you want to remove this ad from premium status?')) {
        return;
    }
    
    fetch(`/admin/premium-ads/${premiumAdId}`, {
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
        alert('An error occurred while removing premium status');
    });
}
</script>
@endsection