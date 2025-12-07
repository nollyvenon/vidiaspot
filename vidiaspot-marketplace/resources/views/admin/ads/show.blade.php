@extends('admin.layout')

@section('title', 'Ad Details')

@section('content')
<div class="admin-card">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold">Ad Details: {{ $ad->title }}</h2>
        <a href="{{ route('admin.ads.index') }}" class="admin-btn admin-btn-primary">← Back to Ads</a>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="admin-card bg-gray-50">
            <h3 class="font-medium mb-3">Ad Information</h3>
            <table class="w-full">
                <tr>
                    <td class="py-2"><strong>ID:</strong></td>
                    <td class="py-2">{{ $ad->id }}</td>
                </tr>
                <tr>
                    <td class="py-2"><strong>Title:</strong></td>
                    <td class="py-2">{{ $ad->title }}</td>
                </tr>
                <tr>
                    <td class="py-2"><strong>Category:</strong></td>
                    <td class="py-2">{{ $ad->category->name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="py-2"><strong>User:</strong></td>
                    <td class="py-2">
                        @if($ad->user)
                            <a href="{{ route('admin.users.show', $ad->user) }}" class="text-blue-600 hover:underline">
                                {{ $ad->user->name }}
                            </a>
                        @else
                            N/A
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="py-2"><strong>Price:</strong></td>
                    <td class="py-2">₦{{ number_format($ad->price, 2) }}</td>
                </tr>
                <tr>
                    <td class="py-2"><strong>Condition:</strong></td>
                    <td class="py-2">{{ ucfirst($ad->condition) }}</td>
                </tr>
                <tr>
                    <td class="py-2"><strong>Status:</strong></td>
                    <td class="py-2">
                        <span class="status-badge status-{{ $ad->status }}">
                            {{ ucfirst($ad->status) }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <td class="py-2"><strong>Location:</strong></td>
                    <td class="py-2">{{ $ad->location }}</td>
                </tr>
                <tr>
                    <td class="py-2"><strong>Contact Phone:</strong></td>
                    <td class="py-2">{{ $ad->contact_phone ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="py-2"><strong>Negotiable:</strong></td>
                    <td class="py-2">{{ $ad->negotiable ? 'Yes' : 'No' }}</td>
                </tr>
                <tr>
                    <td class="py-2"><strong>Created:</strong></td>
                    <td class="py-2">{{ $ad->created_at->format('Y-m-d H:i:s') }}</td>
                </tr>
            </table>
        </div>
        
        <div class="admin-card bg-blue-50">
            <h3 class="font-medium mb-3">Description</h3>
            <p>{{ $ad->description }}</p>
        </div>
    </div>
    
    @if($ad->images->count() > 0)
    <div class="admin-card mt-6">
        <h3 class="font-medium mb-3">Ad Images</h3>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($ad->images as $image)
                <div class="border rounded overflow-hidden">
                    <img src="{{ $image->image_url }}" alt="Ad image" class="w-full h-32 object-cover">
                </div>
            @endforeach
        </div>
    </div>
    @endif
    
    <div class="mt-6">
        <h3 class="font-medium mb-3">Update Ad Status</h3>
        <div class="flex space-x-2">
            <select id="ad-status-select" class="admin-form-select">
                <option value="active" {{ $ad->status === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ $ad->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                <option value="sold" {{ $ad->status === 'sold' ? 'selected' : '' }}>Sold</option>
                <option value="pending" {{ $ad->status === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="rejected" {{ $ad->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
            <button onclick="updateAdStatus({{ $ad->id }})" class="admin-btn admin-btn-success">Update Status</button>
        </div>
    </div>
</div>

<script>
function updateAdStatus(adId) {
    const newStatus = document.getElementById('ad-status-select').value;
    
    fetch(`/admin/ads/${adId}/status`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ status: newStatus })
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