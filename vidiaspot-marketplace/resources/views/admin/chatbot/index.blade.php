@extends('admin.layout')

@section('title', 'Chatbot Management')

@section('content')
<div class="admin-card">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold">Chatbot Management</h2>
    </div>
    
    <!-- Bot Stats Summary -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
            <h3 class="text-sm font-medium text-blue-800">Total Conversations</h3>
            <p class="text-2xl font-bold text-blue-600">{{ $analytics['total_conversations'] ?? 0 }}</p>
        </div>
        
        <div class="bg-green-50 p-4 rounded-lg border border-green-100">
            <h3 class="text-sm font-medium text-green-800">Resolution Rate</h3>
            <p class="text-2xl font-bold text-green-600">{{ number_format(($analytics['resolution_rate'] ?? 0) * 100, 1) }}%</p>
        </div>
        
        <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-100">
            <h3 class="text-sm font-medium text-yellow-800">Human Handoff</h3>
            <p class="text-2xl font-bold text-yellow-600">{{ number_format(($analytics['human_handoff_rate'] ?? 0) * 100, 1) }}%</p>
        </div>
        
        <div class="bg-purple-50 p-4 rounded-lg border border-purple-100">
            <h3 class="text-sm font-medium text-purple-800">Avg Response Time</h3>
            <p class="text-2xl font-bold text-purple-600">{{ $analytics['avg_response_time'] ?? 0 }}s</p>
        </div>
    </div>
    
    <!-- Tabs -->
    <div class="border-b border-gray-200 mb-4">
        <nav class="flex space-x-8">
            <button class="tab-button active" data-tab="dashboard">Dashboard</button>
            <button class="tab-button" data-tab="conversations">Conversations</button>
            <button class="tab-button" data-tab="intents">Intents</button>
            <button class="tab-button" data-tab="faq">FAQ</button>
            <button class="tab-button" data-tab="analytics">Analytics</button>
        </nav>
    </div>
    
    <!-- Dashboard Tab -->
    <div id="dashboard-tab" class="tab-content active">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Top Intents Chart -->
            <div class="admin-card">
                <h3 class="text-md font-semibold mb-3">Top Intents</h3>
                <div class="h-64">
                    <canvas id="intents-chart"></canvas>
                </div>
            </div>
            
            <!-- Satisfaction Trends -->
            <div class="admin-card">
                <h3 class="text-md font-semibold mb-3">Satisfaction Trends</h3>
                <div class="h-64">
                    <canvas id="satisfaction-chart"></canvas>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="admin-card lg:col-span-2">
                <h3 class="text-md font-semibold mb-3">Quick Actions</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <button class="admin-btn admin-btn-primary" onclick="trainBot()">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Train Bot
                    </button>
                    <button class="admin-btn admin-btn-success" onclick="testBot()">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        Test Bot
                    </button>
                    <button class="admin-btn admin-btn-warning" onclick="exportConversations()">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Export Data
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Conversations Tab -->
    <div id="conversations-tab" class="tab-content">
        <div class="overflow-x-auto">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Session ID</th>
                        <th>User</th>
                        <th>Last Message</th>
                        <th>Resolved</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- This would be populated dynamically -->
                    <tr>
                        <td colspan="6" class="text-center">Conversation data would appear here</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Intents Tab -->
    <div id="intents-tab" class="tab-content">
        <div class="mb-4">
            <button class="admin-btn admin-btn-primary" onclick="showAddIntentModal()">Add Intent</button>
        </div>
        
        <div class="overflow-x-auto">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Intent</th>
                        <th>Patterns</th>
                        <th>Responses</th>
                        <th>Confidence</th>
                        <th>Uses</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- This would be populated dynamically -->
                    <tr>
                        <td colspan="6" class="text-center">Intent data would appear here</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- FAQ Tab -->
    <div id="faq-tab" class="tab-content">
        <div class="mb-4">
            <button class="admin-btn admin-btn-primary" onclick="showAddFaqModal()">Add FAQ</button>
        </div>
        
        <div class="overflow-x-auto">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Question</th>
                        <th>Answer</th>
                        <th>Category</th>
                        <th>Active</th>
                        <th>Featured</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- This would be populated dynamically -->
                    <tr>
                        <td colspan="6" class="text-center">FAQ data would appear here</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Analytics Tab -->
    <div id="analytics-tab" class="tab-content">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="admin-card">
                <h3 class="text-md font-semibold mb-3">Daily Conversations</h3>
                <div class="h-64">
                    <canvas id="daily-conversations-chart"></canvas>
                </div>
            </div>
            
            <div class="admin-card">
                <h3 class="text-md font-semibold mb-3">Resolution by Intent</h3>
                <div class="h-64">
                    <canvas id="resolution-by-intent-chart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Intent Modal -->
<div id="add-intent-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium">Add New Intent</h3>
            <button onclick="closeAddIntentModal()" class="text-gray-500 hover:text-gray-700">&times;</button>
        </div>
        
        <form id="intent-form">
            <div class="admin-form-group">
                <label class="admin-form-label">Intent Name *</label>
                <input type="text" name="intent_name" required class="admin-form-input" placeholder="e.g. product_info">
            </div>
            
            <div class="admin-form-group">
                <label class="admin-form-label">Pattern Keywords *</label>
                <textarea name="patterns" required class="admin-form-input" rows="3" placeholder="Enter keywords separated by commas"></textarea>
            </div>
            
            <div class="admin-form-group">
                <label class="admin-form-label">Responses *</label>
                <textarea name="responses" required class="admin-form-input" rows="3" placeholder="Enter possible responses separated by new lines"></textarea>
            </div>
            
            <div class="mt-4 flex justify-end space-x-2">
                <button type="button" onclick="closeAddIntentModal()" class="admin-btn admin-btn-danger">Cancel</button>
                <button type="submit" class="admin-btn admin-btn-primary">Save Intent</button>
            </div>
        </form>
    </div>
</div>

<!-- Add FAQ Modal -->
<div id="add-faq-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium">Add New FAQ</h3>
            <button onclick="closeAddFaqModal()" class="text-gray-500 hover:text-gray-700">&times;</button>
        </div>
        
        <form id="faq-form">
            <div class="admin-form-group">
                <label class="admin-form-label">Question *</label>
                <input type="text" name="question" required class="admin-form-input" placeholder="Enter question">
            </div>
            
            <div class="admin-form-group">
                <label class="admin-form-label">Answer *</label>
                <textarea name="answer" required class="admin-form-input" rows="4" placeholder="Enter answer"></textarea>
            </div>
            
            <div class="admin-form-group">
                <label class="admin-form-label">Category</label>
                <select name="category_id" class="admin-form-select">
                    <option value="">General</option>
                    <option value="1">Account</option>
                    <option value="2">Orders</option>
                    <option value="3">Products</option>
                    <option value="4">Payments</option>
                    <option value="5">Shipping</option>
                </select>
            </div>
            
            <div class="admin-form-group">
                <label class="admin-form-label">
                    <input type="checkbox" name="is_active" value="1" checked class="mr-2">
                    Active
                </label>
            </div>
            
            <div class="admin-form-group">
                <label class="admin-form-label">
                    <input type="checkbox" name="is_featured" value="1" class="mr-2">
                    Featured
                </label>
            </div>
            
            <div class="mt-4 flex justify-end space-x-2">
                <button type="button" onclick="closeAddFaqModal()" class="admin-btn admin-btn-danger">Cancel</button>
                <button type="submit" class="admin-btn admin-btn-primary">Save FAQ</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Tab functionality
document.querySelectorAll('.tab-button').forEach(button => {
    button.addEventListener('click', () => {
        // Remove active class from all buttons and tabs
        document.querySelectorAll('.tab-button').forEach(btn => {
            btn.classList.remove('active');
        });
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.remove('active');
        });
        
        // Add active class to clicked button
        button.classList.add('active');
        
        // Show corresponding tab content
        const tabId = button.getAttribute('data-tab');
        document.getElementById(`${tabId}-tab`).classList.add('active');
    });
});

// Mock data for charts (in real implementation, this would come from API)
const analytics = @json($analytics ?? []);

// Intents chart
if (analytics.top_intents) {
    const intentCtx = document.getElementById('intents-chart').getContext('2d');
    new Chart(intentCtx, {
        type: 'bar',
        data: {
            labels: analytics.top_intents.map(i => i.intent),
            datasets: [{
                label: 'Number of Occurrences',
                data: analytics.top_intents.map(i => i.count),
                backgroundColor: 'rgba(59, 130, 246, 0.6)',
                borderColor: 'rgba(59, 130, 246, 1)',
                borderWidth: 1
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
}

// Satisfaction chart
const satisfactionCtx = document.getElementById('satisfaction-chart').getContext('2d');
new Chart(satisfactionCtx, {
    type: 'line',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        datasets: [{
            label: 'Satisfaction Rate',
            data: [65, 70, 75, 78, 82, 85],
            borderColor: 'rgb(16, 185, 129)',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                max: 100
            }
        }
    }
});

// Intent modal functions
function showAddIntentModal() {
    document.getElementById('add-intent-modal').classList.remove('hidden');
}

function closeAddIntentModal() {
    document.getElementById('add-intent-modal').classList.add('hidden');
}

// FAQ modal functions
function showAddFaqModal() {
    document.getElementById('add-faq-modal').classList.remove('hidden');
}

function closeAddFaqModal() {
    document.getElementById('add-faq-modal').classList.add('hidden');
}

// Form submissions
document.getElementById('intent-form').addEventListener('submit', function(e) {
    e.preventDefault();
    // In real implementation, submit to API
    alert('Intent saved successfully');
    closeAddIntentModal();
});

document.getElementById('faq-form').addEventListener('submit', function(e) {
    e.preventDefault();
    // In real implementation, submit to API
    alert('FAQ saved successfully');
    closeAddFaqModal();
});

// Quick action functions
function trainBot() {
    alert('Starting bot training...');
    // In real implementation, call training API
}

function testBot() {
    alert('Opening bot tester...');
    // In real implementation, open a test modal
}

function exportConversations() {
    alert('Exporting conversations...');
    // In real implementation, initiate download
}
</script>
@endsection