@extends('admin.layout')

@section('title', 'Status Tracking')

@section('content')
<div class="admin-card">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold">Status Tracking</h2>
    </div>
    
    <!-- Filters -->
    <form method="GET" class="mb-4 p-4 bg-gray-50 rounded-lg">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="admin-form-label">Entity Type</label>
                <select name="entity_type" class="admin-form-select">
                    <option value="">All Types</option>
                    <option value="App\Models\Ad" {{ request('entity_type') === 'App\Models\Ad' ? 'selected' : '' }}>Ad</option>
                    <option value="App\Models\Vendor" {{ request('entity_type') === 'App\Models\Vendor' ? 'selected' : '' }}>Vendor</option>
                    <option value="App\Models\Payment" {{ request('entity_type') === 'App\Models\Payment' ? 'selected' : '' }}>Payment</option>
                    <option value="App\Models\Subscription" {{ request('entity_type') === 'App\Models\Subscription' ? 'selected' : '' }}>Subscription</option>
                </select>
            </div>
            
            <div>
                <label class="admin-form-label">Status</label>
                <select name="status" class="admin-form-select">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                    <option value="refunded" {{ request('status') === 'refunded' ? 'selected' : '' }}>Refunded</option>
                    <option value="sold" {{ request('status') === 'sold' ? 'selected' : '' }}>Sold</option>
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
                <a href="{{ route('admin.status-tracking.index') }}" class="admin-btn admin-btn-danger">Reset</a>
            </div>
        </div>
    </form>
    
    <!-- Status Logs Table -->
    <div class="overflow-x-auto">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Entity</th>
                    <th>Type</th>
                    <th>Previous Status</th>
                    <th>New Status</th>
                    <th>Changed By</th>
                    <th>Reason</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($statusLogs as $log)
                <tr>
                    <td>{{ $log->id }}</td>
                    <td>
                        {{ $log->statusable_type }}
                        @if($log->statusable)
                            #{{ $log->statusable_id }}
                        @else
                            (deleted)
                        @endif
                    </td>
                    <td>{{ class_basename($log->statusable_type) }}</td>
                    <td>
                        @if($log->previous_status)
                            <span class="status-badge status-pending">{{ $log->previous_status }}</span>
                        @else
                            <em>N/A</em>
                        @endif
                    </td>
                    <td>
                        <span class="status-badge status-{{ $log->status }}">{{ $log->status }}</span>
                    </td>
                    <td>{{ $log->user->name ?? 'System' }}</td>
                    <td>{{ Str::limit($log->reason, 50) }}</td>
                    <td>{{ $log->created_at->format('Y-m-d H:i') }}</td>
                    <td>
                        <button onclick="viewStatusDetails({{ $log->id }})" class="admin-btn admin-btn-primary admin-btn-sm">View</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center">No status logs found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="mt-4">
        {{ $statusLogs->appends(request()->query())->links() }}
    </div>
</div>

<!-- Status Details Modal -->
<div id="status-details-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-2xl">
        <div class="flex justify-between items-center mb-4">
            <h3 id="status-details-title" class="text-lg font-medium">Status Details</h3>
            <button onclick="closeStatusDetailsModal()" class="text-gray-500 hover:text-gray-700">&times;</button>
        </div>
        
        <div id="status-details-content">
            <!-- Status details will be loaded here -->
        </div>
        
        <div class="mt-6">
            <button onclick="closeStatusDetailsModal()" class="admin-btn admin-btn-danger">Close</button>
        </div>
    </div>
</div>

<script>
function viewStatusDetails(logId) {
    fetch(`/admin/status-logs/${logId}`)
        .then(response => response.json())
        .then(data => {
            let content = `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="admin-form-group">
                        <label class="admin-form-label"><strong>ID:</strong></label>
                        <p>${data.id}</p>
                    </div>
                    
                    <div class="admin-form-group">
                        <label class="admin-form-label"><strong>Entity Type:</strong></label>
                        <p>${data.statusable_type}</p>
                    </div>
                    
                    <div class="admin-form-group">
                        <label class="admin-form-label"><strong>Entity ID:</strong></label>
                        <p>${data.statusable_id}</p>
                    </div>
                    
                    <div class="admin-form-group">
                        <label class="admin-form-label"><strong>Previous Status:</strong></label>
                        <p>${data.previous_status || 'N/A'}</p>
                    </div>
                    
                    <div class="admin-form-group">
                        <label class="admin-form-label"><strong>New Status:</strong></label>
                        <p><span class="status-badge status-${data.status}">${data.status}</span></p>
                    </div>
                    
                    <div class="admin-form-group">
                        <label class="admin-form-label"><strong>Changed By:</strong></label>
                        <p>${data.user ? data.user.name : 'System'}</p>
                    </div>
                    
                    <div class="admin-form-group md:col-span-2">
                        <label class="admin-form-label"><strong>Reason:</strong></label>
                        <p>${data.reason || 'N/A'}</p>
                    </div>
                    
                    <div class="admin-form-group md:col-span-2">
                        <label class="admin-form-label"><strong>Metadata:</strong></label>
                        <pre class="bg-gray-800 text-white p-3 rounded text-sm overflow-x-auto">${JSON.stringify(data.metadata, null, 2)}</pre>
                    </div>
                    
                    <div class="admin-form-group md:col-span-2">
                        <label class="admin-form-label"><strong>Created At:</strong></label>
                        <p>${new Date(data.created_at).toLocaleString()}</p>
                    </div>
                </div>
            `;
            
            document.getElementById('status-details-content').innerHTML = content;
            document.getElementById('status-details-modal').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading status details');
        });
}

function closeStatusDetailsModal() {
    document.getElementById('status-details-modal').classList.add('hidden');
}
</script>
@endsection