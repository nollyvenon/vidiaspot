@extends('admin.layout')

@section('title', 'Categories Report')

@section('content')
<div class="admin-card">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold">Categories Report</h2>
        <a href="{{ route('admin.reports.export', ['type' => 'categories']) }}" class="admin-btn admin-btn-primary">Export CSV</a>
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
                <a href="{{ route('admin.reports.categories') }}" class="admin-btn admin-btn-danger">Reset</a>
            </div>
        </div>
    </form>
    
    <!-- Categories Table -->
    <div class="overflow-x-auto">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Parent</th>
                    <th>Active</th>
                    <th>Ads Count</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                <tr>
                    <td>{{ $category->id }}</td>
                    <td>{{ $category->name }}</td>
                    <td>{{ $category->slug }}</td>
                    <td>{{ $category->parent ? $category->parent->name : 'None' }}</td>
                    <td>
                        <span class="status-badge status-{{ $category->is_active ? 'completed' : 'pending' }}">
                            {{ $category->is_active ? 'Yes' : 'No' }}
                        </span>
                    </td>
                    <td>{{ $category->ads->count() }}</td>
                    <td>{{ $category->created_at->format('Y-m-d') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center">No categories found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="mt-4">
        {{ $categories->appends(request()->query())->links() }}
    </div>
</div>
@endsection