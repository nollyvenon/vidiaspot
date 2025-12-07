@extends('admin.layout')

@section('title', 'User Details')

@section('content')
<div class="admin-card">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold">User: {{ $user->name }}</h2>
        <a href="{{ route('admin.users.index') }}" class="admin-btn admin-btn-primary">← Back to Users</a>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="admin-card bg-gray-50">
            <h3 class="font-medium mb-3">User Information</h3>
            <table class="w-full">
                <tr>
                    <td class="py-2"><strong>ID:</strong></td>
                    <td class="py-2">{{ $user->id }}</td>
                </tr>
                <tr>
                    <td class="py-2"><strong>Name:</strong></td>
                    <td class="py-2">{{ $user->name }}</td>
                </tr>
                <tr>
                    <td class="py-2"><strong>Email:</strong></td>
                    <td class="py-2">{{ $user->email }}</td>
                </tr>
                <tr>
                    <td class="py-2"><strong>Phone:</strong></td>
                    <td class="py-2">{{ $user->phone ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="py-2"><strong>Address:</strong></td>
                    <td class="py-2">{{ $user->address ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="py-2"><strong>City:</strong></td>
                    <td class="py-2">{{ $user->city ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="py-2"><strong>State:</strong></td>
                    <td class="py-2">{{ $user->state ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="py-2"><strong>Country:</strong></td>
                    <td class="py-2">{{ $user->country ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="py-2"><strong>Verified:</strong></td>
                    <td class="py-2">
                        <span class="status-badge status-{{ $user->is_verified ? 'completed' : 'pending' }}">
                            {{ $user->is_verified ? 'Yes' : 'No' }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <td class="py-2"><strong>Joined:</strong></td>
                    <td class="py-2">{{ $user->created_at->format('Y-m-d H:i:s') }}</td>
                </tr>
            </table>
        </div>
        
        <div class="admin-card bg-blue-50">
            <h3 class="font-medium mb-3">Roles</h3>
            <div class="mb-3">
                <form method="POST" action="{{ route('admin.users.update-role', $user) }}" class="flex items-end gap-2">
                    @csrf
                    @method('PUT')
                    <div class="flex-1">
                        <label class="admin-form-label">Update Role</label>
                        <select name="role" class="admin-form-select w-full" required>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                                    {{ $role->display_name ?? $role->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="admin-btn admin-btn-success">Update</button>
                </form>
            </div>
            
            <div class="mt-4">
                <h4 class="font-medium mb-2">Current Roles:</h4>
                @forelse($user->roles as $role)
                    <span class="status-badge status-pending mr-2">{{ $role->display_name ?? $role->name }}</span>
                @empty
                    <p class="text-gray-500">No roles assigned</p>
                @endforelse
            </div>
        </div>
    </div>
    
    @if($user->vendor)
    <div class="admin-card mt-6 bg-green-50">
        <h3 class="font-medium mb-3">Vendor Information</h3>
        <table class="w-full">
            <tr>
                <td class="py-2"><strong>Business Name:</strong></td>
                <td class="py-2">{{ $user->vendor->business_name }}</td>
            </tr>
            <tr>
                <td class="py-2"><strong>Business Email:</strong></td>
                <td class="py-2">{{ $user->vendor->business_email ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="py-2"><strong>Business Phone:</strong></td>
                <td class="py-2">{{ $user->vendor->business_phone ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="py-2"><strong>Business Type:</strong></td>
                <td class="py-2">{{ $user->vendor->business_type ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="py-2"><strong>Status:</strong></td>
                <td class="py-2">
                    <span class="status-badge status-{{ $user->vendor->status }}">
                        {{ ucfirst($user->vendor->status) }}
                    </span>
                </td>
            </tr>
            <tr>
                <td class="py-2"><strong>Verified:</strong></td>
                <td class="py-2">
                    <span class="status-badge status-{{ $user->vendor->is_verified ? 'completed' : 'pending' }}">
                        {{ $user->vendor->is_verified ? 'Yes' : 'No' }}
                    </span>
                </td>
            </tr>
            <tr>
                <td class="py-2"><strong>Rating:</strong></td>
                <td class="py-2">{{ $user->vendor->rating }}</td>
            </tr>
            <tr>
                <td class="py-2"><strong>Total Sales:</strong></td>
                <td class="py-2">{{ $user->vendor->total_sales }}</td>
            </tr>
        </table>
    </div>
    @endif
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
        <div class="admin-card">
            <h3 class="font-medium mb-3">User Statistics</h3>
            <ul class="space-y-2">
                <li><strong>Ads:</strong> {{ $user->ads->count() }}</li>
                <li><strong>Payments:</strong> {{ $user->payments->count() }}</li>
                <li><strong>Subscriptions:</strong> {{ $user->subscriptions->count() }}</li>
                <li><strong>Blogs:</strong> {{ $user->blogs->count() }}</li>
            </ul>
        </div>
        
        <div class="admin-card">
            <h3 class="font-medium mb-3">Latest Payments</h3>
            @forelse($user->payments->take(5) as $payment)
                <div class="border-b pb-2 mb-2 last:border-0 last:mb-0 last:pb-0">
                    <p><strong>₦{{ number_format($payment->amount, 2) }}</strong></p>
                    <p class="text-sm text-gray-600">{{ $payment->payment_gateway }} - {{ ucfirst($payment->status) }}</p>
                </div>
            @empty
                <p class="text-gray-500">No payments</p>
            @endforelse
        </div>
        
        <div class="admin-card">
            <h3 class="font-medium mb-3">Latest Ads</h3>
            @forelse($user->ads->take(5) as $ad)
                <div class="border-b pb-2 mb-2 last:border-0 last:mb-0 last:pb-0">
                    <p><strong>{{ $ad->title }}</strong></p>
                    <p class="text-sm text-gray-600">₦{{ number_format($ad->price, 2) }} - {{ ucfirst($ad->status) }}</p>
                </div>
            @empty
                <p class="text-gray-500">No ads</p>
            @endforelse
        </div>
    </div>
    
    <div class="mt-6 flex space-x-3">
        <button onclick="toggleUserVerification({{ $user->id }}, {{ $user->is_verified ? 'false' : 'true' }})" 
                class="admin-btn admin-btn-{{ $user->is_verified ? 'danger' : 'success' }}">
            {{ $user->is_verified ? 'Unverify User' : 'Verify User' }}
        </button>
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