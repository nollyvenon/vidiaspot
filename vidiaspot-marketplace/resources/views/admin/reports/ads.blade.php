@extends('admin.layout')

@section('title', 'Ads Report')

@section('content')
<div class="admin-card">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold">Ads Report</h2>
        <a href="{{ route('admin.reports.export', ['type' => 'ads']) }}" class="admin-btn admin-btn-primary">Export CSV</a>
    </div>
    
    <!-- Filters -->
    <form method="GET" class="mb-4 p-4 bg-gray-50 rounded-lg">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
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
                <label class="admin-form-label">Date From</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="admin-form-input">
            </div>
            
            <div>
                <label class="admin-form-label">Date To</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="admin-form-input">
            </div>
            
            <div class="flex items-end space-x-2">
                <button type="submit" class="admin-btn admin-btn-primary">Filter</button>
                <a href="{{ route('admin.reports.ads') }}" class="admin-btn admin-btn-danger">Reset</a>
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
                    <th>Created At</th>
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
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center">No ads found</td>
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
@endsection