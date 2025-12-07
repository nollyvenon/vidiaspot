@extends('admin.layout')

@section('title', 'Analytics Dashboard')

@section('content')
<div class="admin-card">
    <h2 class="text-lg font-semibold mb-4">Analytics Dashboard</h2>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
            <h3 class="text-lg font-medium text-blue-800">Total Users</h3>
            <p class="text-2xl font-bold text-blue-600">{{ $totalUsers }}</p>
        </div>
        
        <div class="bg-green-50 p-4 rounded-lg border border-green-100">
            <h3 class="text-lg font-medium text-green-800">Total Vendors</h3>
            <p class="text-2xl font-bold text-green-600">{{ $totalVendors }}</p>
        </div>
        
        <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-100">
            <h3 class="text-lg font-medium text-yellow-800">Total Ads</h3>
            <p class="text-2xl font-bold text-yellow-600">{{ $totalAds }}</p>
        </div>
        
        <div class="bg-purple-50 p-4 rounded-lg border border-purple-100">
            <h3 class="text-lg font-medium text-purple-800">Total Revenue</h3>
            <p class="text-2xl font-bold text-purple-600">₦{{ number_format($revenueStats['total_revenue'], 2) }}</p>
        </div>
    </div>

    <!-- Revenue Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-indigo-50 p-4 rounded-lg border border-indigo-100">
            <h3 class="text-sm font-medium text-indigo-800">Today's Revenue</h3>
            <p class="text-xl font-bold text-indigo-600">₦{{ number_format($revenueStats['today_revenue'], 2) }}</p>
        </div>
        
        <div class="bg-pink-50 p-4 rounded-lg border border-pink-100">
            <h3 class="text-sm font-medium text-pink-800">Monthly Revenue</h3>
            <p class="text-xl font-bold text-pink-600">₦{{ number_format($revenueStats['monthly_revenue'], 2) }}</p>
        </div>
        
        <div class="bg-teal-50 p-4 rounded-lg border border-teal-100">
            <h3 class="text-sm font-medium text-teal-800">Yearly Revenue</h3>
            <p class="text-xl font-bold text-teal-600">₦{{ number_format($revenueStats['yearly_revenue'], 2) }}</p>
        </div>
        
        <div class="bg-orange-50 p-4 rounded-lg border border-orange-100">
            <h3 class="text-sm font-medium text-orange-800">Total Payments</h3>
            <p class="text-xl font-bold text-orange-600">{{ $totalPayments }}</p>
        </div>
    </div>

    <!-- Charts and Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Monthly Stats Chart -->
        <div class="admin-card">
            <h3 class="text-md font-semibold mb-3">Monthly Activity</h3>
            <div class="h-64">
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>
        
        <!-- Recent Activity -->
        <div class="admin-card">
            <h3 class="text-md font-semibold mb-3">Recent Activity</h3>
            <div class="space-y-3">
                <div>
                    <h4 class="font-medium">Recent Users</h4>
                    <ul class="mt-2">
                        @forelse($recentUsers as $user)
                            <li class="text-sm">{{ $user->name }} - {{ $user->email }} ({{ $user->created_at->diffForHumans() }})</li>
                        @empty
                            <li class="text-sm text-gray-500">No recent users</li>
                        @endforelse
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-medium">Recent Ads</h4>
                    <ul class="mt-2">
                        @forelse($recentAds as $ad)
                            <li class="text-sm">{{ $ad->title }} by {{ $ad->user->name ?? 'N/A' }} ({{ $ad->created_at->diffForHumans() }})</li>
                        @empty
                            <li class="text-sm text-gray-500">No recent ads</li>
                        @endforelse
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-medium">Recent Payments</h4>
                    <ul class="mt-2">
                        @forelse($recentPayments as $payment)
                            <li class="text-sm">₦{{ number_format($payment->amount, 2) }} by {{ $payment->user->name ?? 'N/A' }} via {{ $payment->payment_gateway }} ({{ $payment->created_at->diffForHumans() }})</li>
                        @empty
                            <li class="text-sm text-gray-500">No recent payments</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Prepare data for chart
    const monthlyData = @json($monthlyStats);
    
    const months = monthlyData.map(item => item.month);
    const newUserCounts = monthlyData.map(item => item.new_users);
    const newAdCounts = monthlyData.map(item => item.new_ads);
    const newPaymentCounts = monthlyData.map(item => item.new_payments);
    
    // Create chart
    const ctx = document.getElementById('monthlyChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: months,
            datasets: [{
                label: 'New Users',
                data: newUserCounts,
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.1
            }, {
                label: 'New Ads',
                data: newAdCounts,
                borderColor: 'rgb(16, 185, 129)',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                tension: 0.1
            }, {
                label: 'New Payments',
                data: newPaymentCounts,
                borderColor: 'rgb(139, 92, 246)',
                backgroundColor: 'rgba(139, 92, 246, 0.1)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});
</script>
@endsection