@extends('admin.layout')

@section('title', 'Payments Management')

@section('content')
<div class="admin-card">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold">Payments Management</h2>
    </div>
    
    <!-- Filters -->
    <form method="GET" class="mb-4 p-4 bg-gray-50 rounded-lg">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="admin-form-label">Status</label>
                <select name="status" class="admin-form-select">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                    <option value="refunded" {{ request('status') === 'refunded' ? 'selected' : '' }}>Refunded</option>
                </select>
            </div>
            
            <div>
                <label class="admin-form-label">Payment Gateway</label>
                <select name="gateway" class="admin-form-select">
                    <option value="">All Gateways</option>
                    @foreach(['paystack', 'flutterwave', 'stripe', 'paypal', 'mpesa', 'sofort', 'bank_transfer'] as $gateway)
                        <option value="{{ $gateway }}" {{ request('gateway') === $gateway ? 'selected' : '' }}>{{ ucfirst($gateway) }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="admin-form-label">User ID</label>
                <input type="text" name="user_id" value="{{ request('user_id') }}" placeholder="User ID" class="admin-form-input">
            </div>
            
            <div class="flex items-end space-x-2">
                <button type="submit" class="admin-btn admin-btn-primary">Filter</button>
                <a href="{{ route('admin.payments.index') }}" class="admin-btn admin-btn-danger">Reset</a>
            </div>
        </div>
    </form>
    
    <!-- Payments Table -->
    <div class="overflow-x-auto">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Transaction ID</th>
                    <th>User</th>
                    <th>Amount</th>
                    <th>Gateway</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                <tr>
                    <td>{{ $payment->id }}</td>
                    <td>{{ $payment->transaction_id }}</td>
                    <td>
                        @if($payment->user)
                            <a href="{{ route('admin.users.show', $payment->user) }}" class="text-blue-600 hover:underline">
                                {{ $payment->user->name }}
                            </a>
                        @else
                            N/A
                        @endif
                    </td>
                    <td>₦{{ number_format($payment->amount, 2) }}</td>
                    <td>{{ ucfirst($payment->payment_gateway) }}</td>
                    <td>
                        <span class="status-badge status-{{ $payment->status }}">
                            {{ ucfirst($payment->status) }}
                        </span>
                    </td>
                    <td>{{ $payment->created_at->format('Y-m-d H:i') }}</td>
                    <td>
                        <a href="{{ route('admin.payments.show', $payment) }}" class="admin-btn admin-btn-primary admin-btn-sm">View</a>
                        @if($payment->status === 'completed')
                            <button onclick="refundPayment({{ $payment->id }})" class="admin-btn admin-btn-danger admin-btn-sm">Refund</button>
                        @elseif(in_array($payment->status, ['pending', 'failed']))
                            <button onclick="updatePaymentStatus({{ $payment->id }}, 'completed')" class="admin-btn admin-btn-success admin-btn-sm">Complete</button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center">No payments found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="mt-4">
        {{ $payments->appends(request()->query())->links() }}
    </div>
</div>

<script>
function updatePaymentStatus(paymentId, status) {
    if (!confirm(`Are you sure you want to update payment status to ${status}?`)) {
        return;
    }
    
    fetch(`/admin/payments/${paymentId}/status`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ status: status })
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
        alert('An error occurred while updating the payment status');
    });
}

function refundPayment(paymentId) {
    const reason = prompt('Enter refund reason:');
    if (!reason) return;
    
    fetch(`/admin/payments/${paymentId}/refund`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ refund_reason: reason })
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
        alert('An error occurred while processing the refund');
    });
}
</script>
@endsection