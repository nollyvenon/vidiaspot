@extends('admin.layout')

@section('title', 'Users Report')

@section('content')
<div class="admin-card">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold">Users Report</h2>
        <a href="{{ route('admin.reports.export', ['type' => 'users']) }}" class="admin-btn admin-btn-primary">Export CSV</a>
    </div>
    
    <!-- Filters -->
    <form method="GET" class="mb-4 p-4 bg-gray-50 rounded-lg">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="admin-form-label">Role</label>
                <select name="role" class="admin-form-select">
                    <option value="">All Roles</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ request('role') === $role->name ? 'selected' : '' }}>{{ $role->display_name ?? $role->name }}</option>
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
                <a href="{{ route('admin.reports.users') }}" class="admin-btn admin-btn-danger">Reset</a>
            </div>
        </div>
    </form>
    
    <!-- Users Table -->
    <div class="overflow-x-auto">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Verified</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @foreach($user->roles as $role)
                            <span class="status-badge status-pending mr-1">{{ $role->display_name ?? $role->name }}</span>
                        @endforeach
                    </td>
                    <td>
                        <span class="status-badge status-{{ $user->is_verified ? 'completed' : 'pending' }}">
                            {{ $user->is_verified ? 'Yes' : 'No' }}
                        </span>
                    </td>
                    <td>{{ $user->created_at->format('Y-m-d') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center">No users found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="mt-4">
        {{ $users->appends(request()->query())->links() }}
    </div>
</div>
@endsection