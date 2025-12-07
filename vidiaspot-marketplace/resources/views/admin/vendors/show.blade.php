@extends('admin.layout')

@section('title', 'Vendor Details')

@section('content')
<div class="admin-card">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold">Vendor: {{ $vendor->business_name }}</h2>
        <a href="{{ route('admin.vendors.index') }}" class="admin-btn admin-btn-primary">← Back to Vendors</a>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="admin-card bg-gray-50">
            <h3 class="font-medium mb-3">Vendor Information</h3>
            <table class="w-full">
                <tr>
                    <td class="py-2"><strong>Business Name:</strong></td>
                    <td class="py-2">{{ $vendor->business_name }}</td>
                </tr>
                <tr>
                    <td class="py-2"><strong>Business Email:</strong></td>
                    <td class="py-2">{{ $vendor->business_email ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="py-2"><strong>Business Phone:</strong></td>
                    <td class="py-2">{{ $vendor->business_phone ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="py-2"><strong>Business Type:</strong></td>
                    <td class="py-2">{{ $vendor->business_type ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="py-2"><strong>Description:</strong></td>
                    <td class="py-2">{{ $vendor->business_description ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="py-2"><strong>Registration Number:</strong></td>
                    <td class="py-2">{{ $vendor->business_registration_number ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="py-2"><strong>Status:</strong></td>
                    <td class="py-2">
                        <span class="status-badge status-{{ $vendor->status }}">
                            {{ ucfirst($vendor->status) }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <td class="py-2"><strong>Verified:</strong></td>
                    <td class="py-2">
                        <span class="status-badge status-{{ $vendor->is_verified ? 'completed' : 'pending' }}">
                            {{ $vendor->is_verified ? 'Yes' : 'No' }}
                        </span>
                    </td>
                </tr>
            </table>
        </div>
        
        <div class="admin-card bg-blue-50">
            <h3 class="font-medium mb-3">User Information</h3>
            @if($vendor->user)
            <table class="w-full">
                <tr>
                    <td class="py-2"><strong>Name:</strong></td>
                    <td class="py-2">{{ $vendor->user->name }}</td>
                </tr>
                <tr>
                    <td class="py-2"><strong>Email:</strong></td>
                    <td class="py-2">{{ $vendor->user->email }}</td>
                </tr>
                <tr>
                    <td class="py-2"><strong>Phone:</strong></td>
                    <td class="py-2">{{ $vendor->user->phone ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="py-2"><strong>Joined:</strong></td>
                    <td class="py-2">{{ $vendor->user->created_at->format('Y-m-d') }}</td>
                </tr>
            </table>
            @else
            <p>No user associated with this vendor</p>
            @endif
        </div>
        
        <div class="admin-card bg-green-50">
            <h3 class="font-medium mb-3">Location Information</h3>
            <table class="w-full">
                <tr>
                    <td class="py-2"><strong>Country:</strong></td>
                    <td class="py-2">{{ $vendor->country?->name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="py-2"><strong>State:</strong></td>
                    <td class="py-2">{{ $vendor->state?->name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="py-2"><strong>City:</strong></td>
                    <td class="py-2">{{ $vendor->city?->name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="py-2"><strong>Address:</strong></td>
                    <td class="py-2">{{ $vendor->address ?? 'N/A' }}</td>
                </tr>
            </table>
        </div>
        
        <div class="admin-card bg-purple-50">
            <h3 class="font-medium mb-3">Business Stats</h3>
            <table class="w-full">
                <tr>
                    <td class="py-2"><strong>Rating:</strong></td>
                    <td class="py-2">{{ number_format($vendor->rating, 2) }}</td>
                </tr>
                <tr>
                    <td class="py-2"><strong>Total Sales:</strong></td>
                    <td class="py-2">{{ $vendor->total_sales }}</td>
                </tr>
                <tr>
                    <td class="py-2"><strong>Featured:</strong></td>
                    <td class="py-2">
                        <span class="status-badge status-{{ $vendor->is_featured ? 'completed' : 'pending' }}">
                            {{ $vendor->is_featured ? 'Yes' : 'No' }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <td class="py-2"><strong>Logo:</strong></td>
                    <td class="py-2">
                        @if($vendor->logo_url)
                            <img src="{{ $vendor->logo_url }}" alt="Logo" class="h-12 w-12 object-contain" />
                        @else
                            N/A
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    </div>
    
    @if($vendor->documents)
    <div class="admin-card mt-6">
        <h3 class="font-medium mb-3">Documents</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($vendor->documents as $document)
                @if(is_string($document))
                    <div class="border rounded p-3">
                        <a href="{{ $document }}" target="_blank" class="text-blue-600 hover:underline">View Document</a>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
    @endif
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
        <div class="admin-card">
            <h3 class="font-medium mb-3">Vendor Statistics</h3>
            <ul class="space-y-2">
                <li><strong>Ads:</strong> {{ $vendor->ads->count() }}</li>
                <li><strong>Featured Ads:</strong> {{ $vendor->featuredAds->count() }}</li>
            </ul>
        </div>
        
        <div class="admin-card">
            <h3 class="font-medium mb-3">Latest Ads</h3>
            @forelse($vendor->ads->take(5) as $ad)
                <div class="border-b pb-2 mb-2 last:border-0 last:mb-0 last:pb-0">
                    <p><strong>{{ $ad->title }}</strong></p>
                    <p class="text-sm text-gray-600">₦{{ number_format($ad->price, 2) }} - {{ ucfirst($ad->status) }}</p>
                </div>
            @empty
                <p class="text-gray-500">No ads</p>
            @endforelse
        </div>
        
        <div class="admin-card">
            <h3 class="font-medium mb-3">Actions</h3>
            <div class="space-y-2">
                @if($vendor->status === 'pending')
                    <button onclick="approveVendor({{ $vendor->id }})" class="admin-btn admin-btn-success w-full">Approve Vendor</button>
                    <button onclick="rejectVendor({{ $vendor->id }})" class="admin-btn admin-btn-danger w-full">Reject Vendor</button>
                @elseif($vendor->status === 'approved')
                    <button onclick="suspendVendor({{ $vendor->id }})" class="admin-btn admin-btn-danger w-full">Suspend Vendor</button>
                    @if(!$vendor->is_featured)
                        <button onclick="toggleFeatured({{ $vendor->id }}, true)" class="admin-btn admin-btn-primary w-full">Feature Vendor</button>
                    @else
                        <button onclick="toggleFeatured({{ $vendor->id }}, false)" class="admin-btn admin-btn-warning w-full">Unfeature Vendor</button>
                    @endif
                @elseif($vendor->status === 'suspended')
                    <button onclick="approveVendor({{ $vendor->id }})" class="admin-btn admin-btn-success w-full">Approve Vendor</button>
                @endif
            </div>
        </div>
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

function toggleFeatured(vendorId, featured) {
    if (!confirm(`Are you sure you want to ${featured ? 'feature' : 'unfeature'} this vendor?`)) {
        return;
    }
    
    fetch(`/admin/vendors/${vendorId}/toggle-featured`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ featured: featured })
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
        alert('An error occurred while updating vendor featured status');
    });
}
</script>
@endsection