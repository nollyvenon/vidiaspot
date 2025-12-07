@extends('admin.layout')

@section('title', 'Subscriptions Report')

@section('content')
<div class="admin-card">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold">Subscriptions Report</h2>
        <a href="{{ route('admin.reports.export', ['type' => 'subscriptions']) }}" class="admin-btn admin-btn-primary">Export CSV</a>
    </div>
    
    <!-- Filters -->
    <form method="GET" class="mb-4 p-4 bg-gray-50 rounded-lg">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="admin-form-label">Active Status</label>
                <select name="active" class="admin-form-select">
                    <option value="">All</option>
                    <option value="yes" {{ request('active') === 'yes' ? 'selected' : '' }}>Active</option>
                    <option value="no" {{ request('active') === 'no' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            
            <div class="flex items-end space-x-2">
                <button type="submit" class="admin-btn admin-btn-primary">Filter</button>
                <a href="{{ route('admin.reports.subscriptions') }}" class="admin-btn admin-btn-danger">Reset</a>
            </div>
        </div>
    </form>
    
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
                    <th>Featured Ads Limit</th>
                    <th>Active</th>
                    <th>Payments Count</th>
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
                    <td>{{ $subscription->featured_ads_limit }}</td>
                    <td>
                        <span class="status-badge status-{{ $subscription->is_active ? 'completed' : 'pending' }}">
                            {{ $subscription->is_active ? 'Yes' : 'No' }}
                        </span>
                    </td>
                    <td>{{ $subscription->payments->count() }}</td>
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