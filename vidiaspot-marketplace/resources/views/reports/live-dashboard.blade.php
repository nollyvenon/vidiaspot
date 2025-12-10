@extends('layouts.app')

@section('title', 'Live Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <div class="d-flex">
                        <div class="dropdown me-1">
                            <button class="btn btn-light dropdown-toggle" type="button" id="dateRangeDropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Today
                            </button>
                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dateRangeDropdown">
                                <a class="dropdown-item" href="#">Today</a>
                                <a class="dropdown-item" href="#">Yesterday</a>
                                <a class="dropdown-item" href="#">Last 7 Days</a>
                                <a class="dropdown-item" href="#">Last 30 Days</a>
                            </div>
                        </div>
                        <button type="button" class="btn btn-primary" id="refreshData">
                            <i class="mdi mdi-refresh"></i> Refresh
                        </button>
                    </div>
                </div>
                <h4 class="page-title">Live Dashboard</h4>
            </div>
        </div>
    </div>

    <!-- Live Metrics -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="dropdown float-end position-relative">
                        <a href="#" class="dropdown-toggle arrow-none text-muted" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="mdi mdi-dots-vertical"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="javascript:void(0);" class="dropdown-item">View Details</a>
                            <a href="javascript:void(0);" class="dropdown-item">Export</a>
                        </div>
                    </div>
                    <h6 class="text-uppercase mt-0" title="BTC Price">BTC Price</h6>
                    <h2 class="my-2">${{ number_format($report->market_data['btc_price'] ?? 0, 2) }}</h2>
                    <p class="mb-0 text-muted">
                        <span class="text-success me-1"><i class="mdi mdi-arrow-up-bold"></i> 2.3%</span>
                        <small>Last 24h</small>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="dropdown float-end position-relative">
                        <a href="#" class="dropdown-toggle arrow-none text-muted" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="mdi mdi-dots-vertical"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="javascript:void(0);" class="dropdown-item">View Details</a>
                            <a href="javascript:void(0);" class="dropdown-item">Export</a>
                        </div>
                    </div>
                    <h6 class="text-uppercase mt-0" title="Active Users">Active Users</h6>
                    <h2 class="my-2">{{ number_format($report->active_sessions['total_active_users'] ?? 0) }}</h2>
                    <p class="mb-0 text-muted">
                        <span class="text-success me-1"><i class="mdi mdi-arrow-up-bold"></i> 5.2%</span>
                        <small>Since last hour</small>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="dropdown float-end position-relative">
                        <a href="#" class="dropdown-toggle arrow-none text-muted" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="mdi mdi-dots-vertical"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="javascript:void(0);" class="dropdown-item">View Details</a>
                            <a href="javascript:void(0);" class="dropdown-item">Export</a>
                        </div>
                    </div>
                    <h6 class="text-uppercase mt-0" title="24h Volume">24h Volume</h6>
                    <h2 class="my-2">${{ number_format($report->market_data['total_volume_24h'] ?? 0, 2) }}</h2>
                    <p class="mb-0 text-muted">
                        <span class="text-danger me-1"><i class="mdi mdi-arrow-down-bold"></i> 1.8%</span>
                        <small>Since last hour</small>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="dropdown float-end position-relative">
                        <a href="#" class="dropdown-toggle arrow-none text-muted" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="mdi mdi-dots-vertical"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="javascript:void(0);" class="dropdown-item">View Details</a>
                            <a href="javascript:void(0);" class="dropdown-item">Export</a>
                        </div>
                    </div>
                    <h6 class="text-uppercase mt-0" title="System Uptime">System Uptime</h6>
                    <h2 class="my-2">{{ $report->system_health['server_uptime'] ?? 0 }}%</h2>
                    <p class="mb-0 text-muted">
                        <span class="text-success me-1"><i class="mdi mdi-arrow-up-bold"></i> 0.02%</span>
                        <small>Since last hour</small>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Dashboard Content -->
    <div class="row">
        <!-- Market Data -->
        <div class="col-xl-8">
            <div class="card">
                <div class="card-body">
                    <div class="dropdown float-end position-relative">
                        <a href="#" class="dropdown-toggle arrow-none text-muted" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="mdi mdi-dots-vertical"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="javascript:void(0);" class="dropdown-item">View Chart</a>
                            <a href="javascript:void(0);" class="dropdown-item">Export Data</a>
                            <div class="dropdown-divider"></div>
                            <a href="javascript:void(0);" class="dropdown-item text-danger">Alert Settings</a>
                        </div>
                    </div>
                    <h5 class="card-title header-title mb-0">Market Data</h5>
                    
                    <div class="row mt-3">
                        <div class="col-md-4">
                            <div class="text-center p-2 border rounded">
                                <h6 class="text-uppercase text-muted">ETH Price</h6>
                                <h4>${{ number_format($report->market_data['eth_price'] ?? 0, 2) }}</h4>
                                <p class="text-success mb-0"><i class="mdi mdi-arrow-up-bold"></i> 1.5%</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-2 border rounded">
                                <h6 class="text-uppercase text-muted">Market Trend</h6>
                                <h4 class="text-{{ $report->market_data['market_trend'] === 'bullish' ? 'success' : 'danger' }}">
                                    {{ ucfirst($report->market_data['market_trend'] ?? 'neutral') }}
                                </h4>
                                <p class="mb-0">Current Trend</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-2 border rounded">
                                <h6 class="text-uppercase text-muted">Top Gainer</h6>
                                <h4>{{ array_key_first($report->market_data['top_gainers'] ?? []) ?? 'N/A' }}</h4>
                                <p class="text-success mb-0"><i class="mdi mdi-arrow-up-bold"></i> {{ array_values($report->market_data['top_gainers'] ?? [0])[0] ?? '0' }}%</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Top Movers -->
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <h6 class="mb-3">Top Gainers</h6>
                            <ul class="list-group">
                                @foreach($report->market_data['top_gainers'] ?? [] as $currency => $change)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        {{ $currency }}
                                        <span class="badge bg-success rounded-pill"><i class="mdi mdi-arrow-up-bold"></i> {{ $change }}%</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-3">Top Losers</h6>
                            <ul class="list-group">
                                @foreach($report->market_data['top_losers'] ?? [] as $currency => $change)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        {{ $currency }}
                                        <span class="badge bg-danger rounded-pill"><i class="mdi mdi-arrow-down-bold"></i> {{ abs($change) }}%</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Active Sessions -->
            <div class="card mt-4">
                <div class="card-body">
                    <div class="dropdown float-end position-relative">
                        <a href="#" class="dropdown-toggle arrow-none text-muted" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="mdi mdi-dots-vertical"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="javascript:void(0);" class="dropdown-item">View Details</a>
                            <a href="javascript:void(0);" class="dropdown-item">Export Data</a>
                        </div>
                    </div>
                    <h5 class="card-title header-title mb-0">Active Sessions</h5>
                    
                    <div class="row mt-3">
                        <div class="col-md-4">
                            <div class="text-center p-2 border rounded">
                                <h6 class="text-uppercase text-muted">Web Users</h6>
                                <h4>{{ $report->active_sessions['web_users'] ?? 0 }}</h4>
                                <p class="text-muted mb-0">On Web Platform</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-2 border rounded">
                                <h6 class="text-uppercase text-muted">Mobile Users</h6>
                                <h4>{{ $report->active_sessions['mobile_users'] ?? 0 }}</h4>
                                <p class="text-muted mb-0">On Mobile App</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-2 border rounded">
                                <h6 class="text-uppercase text-muted">Peak Times</h6>
                                <h4>{{ count($report->active_sessions['peak_usage_times'] ?? []) }}</h4>
                                <p class="text-muted mb-0">Active Periods</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <h6 class="mb-3">Peak Usage Times</h6>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($report->active_sessions['peak_usage_times'] ?? [] as $time)
                                <span class="badge bg-primary">{{ $time }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- System Status -->
        <div class="col-xl-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title header-title mb-0">System Health</h5>
                    
                    <div class="mt-3">
                        <div class="row">
                            <div class="col-6">
                                <p class="text-muted mb-1">Response Time</p>
                                <h5>{{ $report->system_health['api_response_time_avg'] ?? 0 }}ms</h5>
                                <div class="progress progress-sm">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ min(100, $report->system_health['api_response_time_avg'] ?? 0 / 500 * 100) }}%"></div>
                                </div>
                            </div>
                            <div class="col-6">
                                <p class="text-muted mb-1">DB Connections</p>
                                <h5>{{ $report->system_health['database_connections'] ?? 0 }}</h5>
                                <p class="text-success mb-0">Normal</p>
                            </div>
                        </div>
                        
                        <div class="row mt-2">
                            <div class="col-6">
                                <p class="text-muted mb-1">Error Rate</p>
                                <h5>{{ $report->system_health['error_rate'] ?? 0 }}%</h5>
                                <div class="progress progress-sm">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ 100 - ($report->system_health['error_rate'] ?? 0) }}%"></div>
                                </div>
                            </div>
                            <div class="col-6">
                                <p class="text-muted mb-1">System Load</p>
                                <h5>{{ $report->system_health['system_load'] ?? 0 }}%</h5>
                                <p class="text-{{ ($report->system_health['system_load'] ?? 0) > 80 ? 'danger' : 'success' }} mb-0">
                                    {{ ($report->system_health['system_load'] ?? 0) > 80 ? 'High' : 'Normal' }}
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <h6 class="mb-3">Health Status</h6>
                        <div class="d-grid">
                            <div class="alert alert-{{ ($report->system_health['error_rate'] ?? 0) < 1 ? 'success' : 'warning' }} mb-0">
                                <i class="mdi mdi-check-circle"></i> System Operational
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Pending Transactions -->
            <div class="card mt-4">
                <div class="card-body">
                    <h5 class="card-title header-title mb-0">Pending Transactions</h5>
                    
                    <div class="mt-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>P2P Orders</span>
                            <span class="badge bg-warning rounded-pill">{{ $report->pending_transactions['p2p_orders_pending'] ?? 0 }}</span>
                        </div>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-warning" role="progressbar" style="width: {{ min(100, ($report->pending_transactions['p2p_orders_pending'] ?? 0) / 200 * 100) }}%"></div>
                        </div>
                        
                        <div class="d-flex justify-content-between mb-2 mt-2">
                            <span>Trading Orders</span>
                            <span class="badge bg-info rounded-pill">{{ $report->pending_transactions['trading_orders_pending'] ?? 0 }}</span>
                        </div>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-info" role="progressbar" style="width: {{ min(100, ($report->pending_transactions['trading_orders_pending'] ?? 0) / 150 * 100) }}%"></div>
                        </div>
                        
                        <div class="d-flex justify-content-between mb-2 mt-2">
                            <span>Disputes</span>
                            <span class="badge bg-danger rounded-pill">{{ $report->pending_transactions['disputes_open'] ?? 0 }}</span>
                        </div>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-danger" role="progressbar" style="width: {{ min(100, ($report->pending_transactions['disputes_open'] ?? 0) / 20 * 100) }}%"></div>
                        </div>
                        
                        <div class="d-flex justify-content-between mb-2 mt-2">
                            <span>Withdrawals</span>
                            <span class="badge bg-secondary rounded-pill">{{ $report->pending_transactions['withdrawals_pending'] ?? 0 }}</span>
                        </div>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-secondary" role="progressbar" style="width: {{ min(100, ($report->pending_transactions['withdrawals_pending'] ?? 0) / 100 * 100) }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Active Alerts -->
            <div class="card mt-4">
                <div class="card-body">
                    <div class="dropdown float-end position-relative">
                        <a href="#" class="dropdown-toggle arrow-none text-muted" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="mdi mdi-dots-vertical"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="javascript:void(0);" class="dropdown-item">View All Alerts</a>
                            <a href="javascript:void(0);" class="dropdown-item">Alert Settings</a>
                        </div>
                    </div>
                    <h5 class="card-title header-title mb-0">Active Alerts</h5>
                    
                    <div class="mt-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span><i class="mdi mdi-alert-box text-warning"></i> Threshold Breaches</span>
                            <span class="badge bg-warning rounded-pill">{{ $report->alerts['threshold_breaches'] ?? 0 }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span><i class="mdi mdi-shield-alert text-danger"></i> Security Incidents</span>
                            <span class="badge bg-danger rounded-pill">{{ $report->alerts['security_incidents'] ?? 0 }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span><i class="mdi mdi-gavel text-info"></i> Compliance Violations</span>
                            <span class="badge bg-info rounded-pill">{{ $report->alerts['compliance_violations'] ?? 0 }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span><i class="mdi mdi-chart-line text-success"></i> Financial Anomalies</span>
                            <span class="badge bg-success rounded-pill">{{ $report->alerts['financial_anomalies'] ?? 0 }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span><i class="mdi mdi-server text-primary"></i> System Issues</span>
                            <span class="badge bg-primary rounded-pill">{{ $report->alerts['system_performance_issues'] ?? 0 }}</span>
                        </div>
                    </div>
                    
                    @if(array_sum($report->alerts) > 0)
                        <div class="mt-3">
                            <div class="alert alert-warning mb-0">
                                <i class="mdi mdi-alert-circle"></i> You have {{ array_sum($report->alerts) }} active alerts requiring attention.
                            </div>
                        </div>
                    @else
                        <div class="mt-3">
                            <div class="alert alert-success mb-0">
                                <i class="mdi mdi-check-circle"></i> No active alerts at this time.
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-refresh data every 30 seconds
    setInterval(function() {
        if (document.visibilityState === 'visible') {
            location.reload();
        }
    }, 30000); // 30 seconds
    
    // Manual refresh button
    document.getElementById('refreshData').addEventListener('click', function() {
        location.reload();
    });
    
    // Simulate real-time updates for demo purposes
    // In a real application, this would be handled by WebSocket or Server-Sent Events
    function simulateRealTimeUpdates() {
        // This would be replaced with actual WebSocket/SSE connection
        console.log('Connected to real-time data stream...');
    }
    
    simulateRealTimeUpdates();
});
</script>
@endsection