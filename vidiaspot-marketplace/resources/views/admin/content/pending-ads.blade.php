@extends('admin.layout')

@section('title', 'Pending Ads Management')

@section('content')
<div class="admin-card">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold">Pending Ads for Approval</h2>
    </div>
    
    <!-- Filters -->
    <form method="GET" class="mb-4 p-4 bg-gray-50 rounded-lg">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="admin-form-label">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Title or description" class="admin-form-input">
            </div>
            
            <div class="flex items-end space-x-2">
                <button type="submit" class="admin-btn admin-btn-primary">Filter</button>
                <a href="{{ route('admin.pending-ads.index') }}" class="admin-btn admin-btn-danger">Reset</a>
            </div>
        </div>
    </form>
    
    <!-- Pending Ads Table -->
    <div class="overflow-x-auto">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>User</th>
                    <th>Price</th>
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
                    <td>{{ $ad->created_at->format('Y-m-d') }}</td>
                    <td>
                        <button onclick="approveAd({{ $ad->id }})" class="admin-btn admin-btn-success admin-btn-sm">Approve</button>
                        <button onclick="rejectAd({{ $ad->id }})" class="admin-btn admin-btn-danger admin-btn-sm">Reject</button>
                        <button onclick="viewAd({{ $ad->id }})" class="admin-btn admin-btn-primary admin-btn-sm">View</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center">No pending ads found</td>
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

<!-- View Ad Modal -->
<div id="ad-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-2xl">
        <div class="flex justify-between items-center mb-4">
            <h3 id="ad-modal-title" class="text-lg font-medium">Ad Details</h3>
            <button onclick="closeAdModal()" class="text-gray-500 hover:text-gray-700">&times;</button>
        </div>
        
        <div id="ad-details">
            <!-- Ad details will be loaded here -->
        </div>
        
        <div class="mt-6 flex justify-end space-x-2">
            <button type="button" onclick="closeAdModal()" class="admin-btn admin-btn-danger">Close</button>
            <button type="button" onclick="approveAd(currentAdId)" class="admin-btn admin-btn-success">Approve</button>
            <button type="button" onclick="rejectAd(currentAdId)" class="admin-btn admin-btn-danger">Reject</button>
        </div>
    </div>
</div>

<script>
let currentAdId = null;

function viewAd(adId) {
    currentAdId = adId;
    
    fetch(`/admin/ads/${adId}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('ad-modal-title').textContent = data.title;
            
            let adDetails = `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="admin-form-group">
                        <label class="admin-form-label"><strong>Title:</strong></label>
                        <p>${data.title}</p>
                    </div>
                    
                    <div class="admin-form-group">
                        <label class="admin-form-label"><strong>Category:</strong></label>
                        <p>${data.category ? data.category.name : 'N/A'}</p>
                    </div>
                    
                    <div class="admin-form-group">
                        <label class="admin-form-label"><strong>User:</strong></label>
                        <p>${data.user ? data.user.name : 'N/A'}</p>
                    </div>
                    
                    <div class="admin-form-group">
                        <label class="admin-form-label"><strong>Price:</strong></label>
                        <p>₦${Number(data.price).toFixed(2)}</p>
                    </div>
                    
                    <div class="admin-form-group">
                        <label class="admin-form-label"><strong>Condition:</strong></label>
                        <p>${data.condition}</p>
                    </div>
                    
                    <div class="admin-form-group">
                        <label class="admin-form-label"><strong>Status:</strong></label>
                        <p><span class="status-badge status-${data.status}">${data.status}</span></p>
                    </div>
                    
                    <div class="admin-form-group md:col-span-2">
                        <label class="admin-form-label"><strong>Description:</strong></label>
                        <p>${data.description}</p>
                    </div>
                    
                    <div class="admin-form-group">
                        <label class="admin-form-label"><strong>Location:</strong></label>
                        <p>${data.location}</p>
                    </div>
                    
                    <div class="admin-form-group">
                        <label class="admin-form-label"><strong>Contact Phone:</strong></label>
                        <p>${data.contact_phone || 'N/A'}</p>
                    </div>
                </div>
            `;
            
            document.getElementById('ad-details').innerHTML = adDetails;
            document.getElementById('ad-modal').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading ad details');
        });
}

function closeAdModal() {
    document.getElementById('ad-modal').classList.add('hidden');
    currentAdId = null;
}

function approveAd(adId) {
    if (!confirm('Are you sure you want to approve this ad?')) {
        return;
    }
    
    fetch(`/admin/ads/${adId}/approve`, {
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
            closeAdModal();
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while approving the ad');
    });
}

function rejectAd(adId) {
    const reason = prompt('Enter rejection reason:');
    if (!reason) return;
    
    fetch(`/admin/ads/${adId}/reject`, {
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
            closeAdModal();
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while rejecting the ad');
    });
}
</script>
@endsection