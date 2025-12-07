@extends('admin.layout')

@section('title', 'Payments Report')

@section('content')
<div class="admin-card">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold">Payments Report</h2>
        <a href="{{ route('admin.reports.export', ['type' => 'payments']) }}" class="admin-btn admin-btn-primary">Export CSV</a>
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
                <label class="admin-form-label">Gateway</label>
                <select name="gateway" class="admin-form-select">
                    <option value="">All Gateways</option>
                    <option value="paystack" {{ request('gateway') === 'paystack' ? 'selected' : '' }}>Paystack</option>
                    <option value="flutterwave" {{ request('gateway') === 'flutterwave' ? 'selected' : '' }}>Flutterwave</option>
                    <option value="stripe" {{ request('gateway') === 'stripe' ? 'selected' : '' }}>Stripe</option>
                    <option value="paypal" {{ request('gateway') === 'paypal' ? 'selected' : '' }}>PayPal</option>
                    <option value="mpesa" {{ request('gateway') === 'mpesa' ? 'selected' : '' }}>M-Pesa</option>
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
                <a href="{{ route('admin.reports.payments') }}" class="admin-btn admin-btn-danger">Reset</a>
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
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                <tr>
                    <td>{{ $payment->id }}</td>
                    <td>{{ $payment->transaction_id }}</td>
                    <td>{{ $payment->user->name ?? 'N/A' }}</td>
                    <td>₦{{ number_format($payment->amount, 2) }}</td>
                    <td>{{ ucfirst($payment->payment_gateway) }}</td>
                    <td>
                        <span class="status-badge status-{{ $payment->status }}">
                            {{ ucfirst($payment->status) }}
                        </span>
                    </td>
                    <td>{{ $payment->created_at->format('Y-m-d H:i') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center">No payments found</td>
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
@endsection