@extends('admin.layout')

@section('title', 'Payment Details')

@section('content')
<div class="admin-card">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold">Payment #{{ $payment->id }} Details</h2>
        <a href="{{ route('admin.payments.index') }}" class="admin-btn admin-btn-primary">← Back to Payments</a>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="admin-card bg-gray-50">
            <h3 class="font-medium mb-3">Payment Information</h3>
            <table class="w-full">
                <tr>
                    <td class="py-2"><strong>ID:</strong></td>
                    <td class="py-2">{{ $payment->id }}</td>
                </tr>
                <tr>
                    <td class="py-2"><strong>Transaction ID:</strong></td>
                    <td class="py-2">{{ $payment->transaction_id }}</td>
                </tr>
                <tr>
                    <td class="py-2"><strong>Amount:</strong></td>
                    <td class="py-2">₦{{ number_format($payment->amount, 2) }}</td>
                </tr>
                <tr>
                    <td class="py-2"><strong>Currency:</strong></td>
                    <td class="py-2">{{ $payment->currency_code }}</td>
                </tr>
                <tr>
                    <td class="py-2"><strong>Gateway:</strong></td>
                    <td class="py-2">{{ ucfirst($payment->payment_gateway) }}</td>
                </tr>
                <tr>
                    <td class="py-2"><strong>Method:</strong></td>
                    <td class="py-2">{{ $payment->payment_method ? ucfirst($payment->payment_method) : 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="py-2"><strong>Status:</strong></td>
                    <td class="py-2">
                        <span class="status-badge status-{{ $payment->status }}">
                            {{ ucfirst($payment->status) }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <td class="py-2"><strong>Date:</strong></td>
                    <td class="py-2">{{ $payment->created_at->format('Y-m-d H:i:s') }}</td>
                </tr>
                <tr>
                    <td class="py-2"><strong>Completed:</strong></td>
                    <td class="py-2">{{ $payment->completed_at ? $payment->completed_at->format('Y-m-d H:i:s') : 'N/A' }}</td>
                </tr>
            </table>
        </div>
        
        <div class="admin-card bg-gray-50">
            <h3 class="font-medium mb-3">User Information</h3>
            @if($payment->user)
            <table class="w-full">
                <tr>
                    <td class="py-2"><strong>Name:</strong></td>
                    <td class="py-2">{{ $payment->user->name }}</td>
                </tr>
                <tr>
                    <td class="py-2"><strong>Email:</strong></td>
                    <td class="py-2">{{ $payment->user->email }}</td>
                </tr>
                <tr>
                    <td class="py-2"><strong>Phone:</strong></td>
                    <td class="py-2">{{ $payment->user->phone ?? 'N/A' }}</td>
                </tr>
            </table>
            @else
            <p>No user associated with this payment</p>
            @endif
        </div>
        
        @if($payment->subscription)
        <div class="admin-card bg-blue-50">
            <h3 class="font-medium mb-3">Subscription Information</h3>
            <table class="w-full">
                <tr>
                    <td class="py-2"><strong>Subscription Name:</strong></td>
                    <td class="py-2">{{ $payment->subscription->name }}</td>
                </tr>
                <tr>
                    <td class="py-2"><strong>Price:</strong></td>
                    <td class="py-2">₦{{ number_format($payment->subscription->price, 2) }}</td>
                </tr>
                <tr>
                    <td class="py-2"><strong>Billing Cycle:</strong></td>
                    <td class="py-2">{{ ucfirst($payment->subscription->billing_cycle) }}</td>
                </tr>
            </table>
        </div>
        @endif
        
        @if($payment->ad)
        <div class="admin-card bg-green-50">
            <h3 class="font-medium mb-3">Ad Information</h3>
            <table class="w-full">
                <tr>
                    <td class="py-2"><strong>Ad Title:</strong></td>
                    <td class="py-2">{{ $payment->ad->title }}</td>
                </tr>
                <tr>
                    <td class="py-2"><strong>Ad Price:</strong></td>
                    <td class="py-2">₦{{ number_format($payment->ad->price, 2) }}</td>
                </tr>
            </table>
        </div>
        @endif
    </div>
    
    <div class="admin-card mt-6">
        <h3 class="font-medium mb-3">Payment Metadata</h3>
        <pre class="bg-gray-800 text-white p-4 rounded text-sm overflow-x-auto">{{ json_encode($payment->metadata, JSON_PRETTY_PRINT) }}</pre>
    </div>
    
    <div class="mt-6 flex space-x-3">
        @if($payment->status === 'completed')
            <button onclick="refundPayment({{ $payment->id }})" class="admin-btn admin-btn-danger">Process Refund</button>
        @elseif(in_array($payment->status, ['pending', 'failed']))
            <button onclick="updatePaymentStatus({{ $payment->id }}, 'completed')" class="admin-btn admin-btn-success">Mark as Completed</button>
        @endif
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