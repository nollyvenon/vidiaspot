@extends('admin.layout')

@section('title', 'Subscriptions Management')

@section('content')
<div class="admin-card">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold">Subscriptions Management</h2>
        <a href="{{ route('admin.subscriptions.create') }}" class="admin-btn admin-btn-primary">Create Subscription</a>
    </div>
    
    <!-- Subscriptions Table -->
    <div class="overflow-x-auto">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Billing Cycle</th>
                    <th>Ad Limit</th>
                    <th>Featured</th>
                    <th>Active</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($subscriptions as $subscription)
                <tr>
                    <td>{{ $subscription->id }}</td>
                    <td>{{ $subscription->name }}</td>
                    <td>₦{{ number_format($subscription->price, 2) }}</td>
                    <td>{{ ucfirst($subscription->billing_cycle) }}</td>
                    <td>{{ $subscription->ad_limit }}</td>
                    <td>
                        <span class="status-badge status-{{ $subscription->is_featured ? 'completed' : 'pending' }}">
                            {{ $subscription->is_featured ? 'Yes' : 'No' }}
                        </span>
                    </td>
                    <td>
                        <span class="status-badge status-{{ $subscription->is_active ? 'completed' : 'pending' }}">
                            {{ $subscription->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('admin.subscriptions.show', $subscription) }}" class="admin-btn admin-btn-primary admin-btn-sm">View</a>
                        <a href="{{ route('admin.subscriptions.edit', $subscription) }}" class="admin-btn admin-btn-success admin-btn-sm">Edit</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center">No subscriptions found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="mt-4">
        {{ $subscriptions->appends(request()->query())->links() }}
    </div>
</div>
@endsection