@extends('admin.layout')

@section('title', 'Vendors Report')

@section('content')
<div class="admin-card">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold">Vendors Report</h2>
        <a href="{{ route('admin.reports.export', ['type' => 'vendors']) }}" class="admin-btn admin-btn-primary">Export CSV</a>
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
                <label class="admin-form-label">Date From</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="admin-form-input">
            </div>
            
            <div>
                <label class="admin-form-label">Date To</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="admin-form-input">
            </div>
            
            <div class="flex items-end space-x-2">
                <button type="submit" class="admin-btn admin-btn-primary">Filter</button>
                <a href="{{ route('admin.reports.vendors') }}" class="admin-btn admin-btn-danger">Reset</a>
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
                    <th>Country</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                @forelse($vendors as $vendor)
                <tr>
                    <td>{{ $vendor->id }}</td>
                    <td>{{ $vendor->business_name }}</td>
                    <td>{{ $vendor->user->name ?? 'N/A' }}</td>
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
                    <td>{{ $vendor->country->name ?? 'N/A' }}</td>
                    <td>{{ $vendor->created_at->format('Y-m-d') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center">No vendors found</td>
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
@endsection