@extends('admin.layout')

@section('title', 'Dashboard')

@section('content')
<div class="admin-card">
    <h2 class="text-lg font-semibold mb-4">Dashboard Overview</h2>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
            <h3 class="text-lg font-medium text-blue-800">Total Users</h3>
            <p class="text-2xl font-bold text-blue-600" id="total-users">0</p>
        </div>
        
        <div class="bg-green-50 p-4 rounded-lg border border-green-100">
            <h3 class="text-lg font-medium text-green-800">Total Payments</h3>
            <p class="text-2xl font-bold text-green-600" id="total-payments">0</p>
        </div>
        
        <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-100">
            <h3 class="text-lg font-medium text-yellow-800">Total Revenue</h3>
            <p class="text-2xl font-bold text-yellow-600" id="total-revenue">₦0</p>
        </div>
        
        <div class="bg-purple-50 p-4 rounded-lg border border-purple-100">
            <h3 class="text-lg font-medium text-purple-800">Active Subscriptions</h3>
            <p class="text-2xl font-bold text-purple-600" id="active-subscriptions">0</p>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="admin-card">
            <h3 class="text-md font-semibold mb-3">Recent Payments</h3>
            <div class="overflow-x-auto">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody id="recent-payments">
                        <tr>
                            <td colspan="5" class="text-center">Loading...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="admin-card">
            <h3 class="text-md font-semibold mb-3">Recent Users</h3>
            <div class="overflow-x-auto">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Joined</th>
                        </tr>
                    </thead>
                    <tbody id="recent-users">
                        <tr>
                            <td colspan="5" class="text-center">Loading...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Fetch dashboard statistics
    fetch('/api/admin/stats')
        .then(response => response.json())
        .then(data => {
            document.getElementById('total-users').textContent = data.total_users || 0;
            document.getElementById('total-payments').textContent = data.total_payments || 0;
            document.getElementById('total-revenue').textContent = '₦' + (data.total_revenue || 0);
            document.getElementById('active-subscriptions').textContent = data.active_subscriptions || 0;
            
            // Update recent payments
            const paymentsBody = document.getElementById('recent-payments');
            paymentsBody.innerHTML = '';
            (data.recent_payments || []).forEach(payment => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${payment.id}</td>
                    <td>${payment.user?.name || 'N/A'}</td>
                    <td>₦${payment.amount}</td>
                    <td><span class="status-badge status-${payment.status}">${payment.status}</span></td>
                    <td>${new Date(payment.created_at).toLocaleDateString()}</td>
                `;
                paymentsBody.appendChild(row);
            });
            
            // Update recent users
            const usersBody = document.getElementById('recent-users');
            usersBody.innerHTML = '';
            (data.recent_users || []).forEach(user => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${user.id}</td>
                    <td>${user.name}</td>
                    <td>${user.email}</td>
                    <td><span class="status-badge status-${user.is_verified ? 'completed' : 'pending'}">${user.is_verified ? 'Verified' : 'Pending'}</span></td>
                    <td>${new Date(user.created_at).toLocaleDateString()}</td>
                `;
                usersBody.appendChild(row);
            });
        })
        .catch(error => console.error('Error loading dashboard data:', error));
});
</script>
@endsection