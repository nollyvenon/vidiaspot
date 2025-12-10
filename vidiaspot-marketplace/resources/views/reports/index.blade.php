@extends('layouts.app')

@section('title', 'Reports Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Reports Dashboard</h4>
                <p class="text-muted font-14">Comprehensive reporting for your crypto P2P marketplace</p>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Quick Stats -->
        <div class="col-xl-3 col-md-6">
            <div class="card box-shadow">
                <div class="card-body">
                    <div class="dropdown float-end position-relative">
                        <a href="#" class="dropdown-toggle arrow-none text-muted" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="mdi mdi-dots-vertical"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="javascript:void(0);" class="dropdown-item">Generate Report</a>
                            <a href="javascript:void(0);" class="dropdown-item">Export Data</a>
                            <div class="dropdown-divider"></div>
                            <a href="javascript:void(0);" class="dropdown-item text-danger">Delete Cache</a>
                        </div>
                    </div>
                    <h6 class="text-uppercase mt-0" title="Balance">Balance Sheet</h6>
                    <h2 class="my-2">12</h2>
                    <p class="mb-0 text-muted">
                        <span class="text-success me-1"><i class="mdi mdi-arrow-up-bold"></i> 3.27%</span>
                        <small>Since last month</small>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card box-shadow">
                <div class="card-body">
                    <div class="dropdown float-end position-relative">
                        <a href="#" class="dropdown-toggle arrow-none text-muted" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="mdi mdi-dots-vertical"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="javascript:void(0);" class="dropdown-item">Generate Report</a>
                            <a href="javascript:void(0);" class="dropdown-item">Export Data</a>
                            <div class="dropdown-divider"></div>
                            <a href="javascript:void(0);" class="dropdown-item text-danger">Delete Cache</a>
                        </div>
                    </div>
                    <h6 class="text-uppercase mt-0" title="Income">Income Statement</h6>
                    <h2 class="my-2">8</h2>
                    <p class="mb-0 text-muted">
                        <span class="text-success me-1"><i class="mdi mdi-arrow-up-bold"></i> 1.87%</span>
                        <small>Since last month</small>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card box-shadow">
                <div class="card-body">
                    <div class="dropdown float-end position-relative">
                        <a href="#" class="dropdown-toggle arrow-none text-muted" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="mdi mdi-dots-vertical"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="javascript:void(0);" class="dropdown-item">Generate Report</a>
                            <a href="javascript:void(0);" class="dropdown-item">Export Data</a>
                            <div class="dropdown-divider"></div>
                            <a href="javascript:void(0);" class="dropdown-item text-danger">Delete Cache</a>
                        </div>
                    </div>
                    <h6 class="text-uppercase mt-0" title="Trading">Trading Reports</h6>
                    <h2 class="my-2">24</h2>
                    <p class="mb-0 text-muted">
                        <span class="text-success me-1"><i class="mdi mdi-arrow-up-bold"></i> 5.43%</span>
                        <small>Since last month</small>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card box-shadow">
                <div class="card-body">
                    <div class="dropdown float-end position-relative">
                        <a href="#" class="dropdown-toggle arrow-none text-muted" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="mdi mdi-dots-vertical"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="javascript:void(0);" class="dropdown-item">Generate Report</a>
                            <a href="javascript:void(0);" class="dropdown-item">Export Data</a>
                            <div class="dropdown-divider"></div>
                            <a href="javascript:void(0);" class="dropdown-item text-danger">Delete Cache</a>
                        </div>
                    </div>
                    <h6 class="text-uppercase mt-0" title="Security">Security Reports</h6>
                    <h2 class="my-2">15</h2>
                    <p class="mb-0 text-muted">
                        <span class="text-danger me-1"><i class="mdi mdi-arrow-down-bold"></i> 1.12%</span>
                        <small>Since last month</small>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Categories -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="header-title mb-3">Report Categories</h5>
                    
                    <!-- Financial Reports -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h6 class="text-primary mb-3">Financial Reports</h6>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="card card-hover border shadow-none">
                                <div class="card-body text-center">
                                    <i class="mdi mdi-chart-line h2 text-success"></i>
                                    <h6 class="mt-2">Balance Sheet</h6>
                                    <p class="text-muted font-13">Assets, liabilities & equity</p>
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('reports.generate', ['type' => 'balance-sheet']) }}" class="btn btn-outline-primary btn-sm">Generate</a>
                                        <a href="{{ route('reports.list', ['type' => 'balance-sheet']) }}" class="btn btn-outline-info btn-sm">View Reports</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="card card-hover border shadow-none">
                                <div class="card-body text-center">
                                    <i class="mdi mdi-currency-usd h2 text-info"></i>
                                    <h6 class="mt-2">Income Statement</h6>
                                    <p class="text-muted font-13">Revenue & expenses</p>
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('reports.generate', ['type' => 'income-statement']) }}" class="btn btn-outline-primary btn-sm">Generate</a>
                                        <a href="{{ route('reports.list', ['type' => 'income-statement']) }}" class="btn btn-outline-info btn-sm">View Reports</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="card card-hover border shadow-none">
                                <div class="card-body text-center">
                                    <i class="mdi mdi-water h2 text-warning"></i>
                                    <h6 class="mt-2">Cash Flow</h6>
                                    <p class="text-muted font-13">Operating, investing & financing</p>
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('reports.generate', ['type' => 'cash-flow']) }}" class="btn btn-outline-primary btn-sm">Generate</a>
                                        <a href="{{ route('reports.list', ['type' => 'cash-flow']) }}" class="btn btn-outline-info btn-sm">View Reports</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="card card-hover border shadow-none">
                                <div class="card-body text-center">
                                    <i class="mdi mdi-account-cash h2 text-purple"></i>
                                    <h6 class="mt-2">General Ledger</h6>
                                    <p class="text-muted font-13">Chart of accounts & trial balance</p>
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('reports.generate', ['type' => 'general-ledger']) }}" class="btn btn-outline-primary btn-sm">Generate</a>
                                        <a href="{{ route('reports.list', ['type' => 'general-ledger']) }}" class="btn btn-outline-info btn-sm">View Reports</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Trading Activity Reports -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h6 class="text-primary mb-3">Trading Activity Reports</h6>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="card card-hover border shadow-none">
                                <div class="card-body text-center">
                                    <i class="mdi mdi-chart-bubble h2 text-success"></i>
                                    <h6 class="mt-2">Daily Trading</h6>
                                    <p class="text-muted font-13">Volume & transaction analysis</p>
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('reports.generate', ['type' => 'daily-trading']) }}" class="btn btn-outline-primary btn-sm">Generate</a>
                                        <a href="{{ route('reports.list', ['type' => 'daily-trading']) }}" class="btn btn-outline-info btn-sm">View Reports</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="card card-hover border shadow-none">
                                <div class="card-body text-center">
                                    <i class="mdi mdi-account-group h2 text-info"></i>
                                    <h6 class="mt-2">User Activity</h6>
                                    <p class="text-muted font-13">DAU, MAU & retention metrics</p>
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('reports.generate', ['type' => 'user-activity']) }}" class="btn btn-outline-primary btn-sm">Generate</a>
                                        <a href="{{ route('reports.list', ['type' => 'user-activity']) }}" class="btn btn-outline-info btn-sm">View Reports</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="card card-hover border shadow-none">
                                <div class="card-body text-center">
                                    <i class="mdi mdi-account-card-details h2 text-warning"></i>
                                    <h6 class="mt-2">User Trade History</h6>
                                    <p class="text-muted font-13">Individual user transactions</p>
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('reports.generate', ['type' => 'user-trade-history']) }}" class="btn btn-outline-primary btn-sm">Generate</a>
                                        <a href="{{ route('reports.list', ['type' => 'user-trade-history']) }}" class="btn btn-outline-info btn-sm">View Reports</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="card card-hover border shadow-none">
                                <div class="card-body text-center">
                                    <i class="mdi mdi-account-multiple h2 text-purple"></i>
                                    <h6 class="mt-2">User Segmentation</h6>
                                    <p class="text-muted font-13">VIP, tier-based & regional analysis</p>
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('reports.generate', ['type' => 'user-segmentation']) }}" class="btn btn-outline-primary btn-sm">Generate</a>
                                        <a href="{{ route('reports.list', ['type' => 'user-segmentation']) }}" class="btn btn-outline-info btn-sm">View Reports</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Risk Management Reports -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h6 class="text-primary mb-3">Risk Management Reports</h6>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="card card-hover border shadow-none">
                                <div class="card-body text-center">
                                    <i class="mdi mdi-shield-alert h2 text-success"></i>
                                    <h6 class="mt-2">Security</h6>
                                    <p class="text-muted font-13">Suspicious activities & fraud</p>
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('reports.generate', ['type' => 'security']) }}" class="btn btn-outline-primary btn-sm">Generate</a>
                                        <a href="{{ route('reports.list', ['type' => 'security']) }}" class="btn btn-outline-info btn-sm">View Reports</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="card card-hover border shadow-none">
                                <div class="card-body text-center">
                                    <i class="mdi mdi-trending-up h2 text-info"></i>
                                    <h6 class="mt-2">Market Risk</h6>
                                    <p class="text-muted font-13">Volatility & liquidity analysis</p>
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('reports.generate', ['type' => 'market-risk']) }}" class="btn btn-outline-primary btn-sm">Generate</a>
                                        <a href="{{ route('reports.list', ['type' => 'market-risk']) }}" class="btn btn-outline-info btn-sm">View Reports</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="card card-hover border shadow-none">
                                <div class="card-body text-center">
                                    <i class="mdi mdi-account-card-details-outline h2 text-warning"></i>
                                    <h6 class="mt-2">AML/KYC</h6>
                                    <p class="text-muted font-13">Compliance & verification</p>
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('reports.generate', ['type' => 'aml-kyc']) }}" class="btn btn-outline-primary btn-sm">Generate</a>
                                        <a href="{{ route('reports.list', ['type' => 'aml-kyc']) }}" class="btn btn-outline-info btn-sm">View Reports</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="card card-hover border shadow-none">
                                <div class="card-body text-center">
                                    <i class="mdi mdi-cash h2 text-purple"></i>
                                    <h6 class="mt-2">Tax Reports</h6>
                                    <p class="text-muted font-13">Tax obligations & forms</p>
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('reports.generate', ['type' => 'tax']) }}" class="btn btn-outline-primary btn-sm">Generate</a>
                                        <a href="{{ route('reports.list', ['type' => 'tax']) }}" class="btn btn-outline-info btn-sm">View Reports</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Operational Reports -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h6 class="text-primary mb-3">Operational Reports</h6>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="card card-hover border shadow-none">
                                <div class="card-body text-center">
                                    <i class="mdi mdi-monitor-dashboard h2 text-success"></i>
                                    <h6 class="mt-2">System Performance</h6>
                                    <p class="text-muted font-13">Uptime & response metrics</p>
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('reports.generate', ['type' => 'system-performance']) }}" class="btn btn-outline-primary btn-sm">Generate</a>
                                        <a href="{{ route('reports.list', ['type' => 'system-performance']) }}" class="btn btn-outline-info btn-sm">View Reports</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="card card-hover border shadow-none">
                                <div class="card-body text-center">
                                    <i class="mdi mdi-headset h2 text-info"></i>
                                    <h6 class="mt-2">Customer Service</h6>
                                    <p class="text-muted font-13">Support tickets & satisfaction</p>
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('reports.generate', ['type' => 'customer-service']) }}" class="btn btn-outline-primary btn-sm">Generate</a>
                                        <a href="{{ route('reports.list', ['type' => 'customer-service']) }}" class="btn btn-outline-info btn-sm">View Reports</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="card card-hover border shadow-none">
                                <div class="card-body text-center">
                                    <i class="mdi mdi-scale-balance h2 text-warning"></i>
                                    <h6 class="mt-2">Revenue Recognition</h6>
                                    <p class="text-muted font-13">Revenue by geography & type</p>
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('reports.generate', ['type' => 'revenue-recognition']) }}" class="btn btn-outline-primary btn-sm">Generate</a>
                                        <a href="{{ route('reports.list', ['type' => 'revenue-recognition']) }}" class="btn btn-outline-info btn-sm">View Reports</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="card card-hover border shadow-none">
                                <div class="card-body text-center">
                                    <i class="mdi mdi-speedometer h2 text-purple"></i>
                                    <h6 class="mt-2">Performance Metrics</h6>
                                    <p class="text-muted font-13">KPIs & efficiency ratios</p>
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('reports.generate', ['type' => 'performance-metrics']) }}" class="btn btn-outline-primary btn-sm">Generate</a>
                                        <a href="{{ route('reports.list', ['type' => 'performance-metrics']) }}" class="btn btn-outline-info btn-sm">View Reports</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Advanced Analytics Reports -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h6 class="text-primary mb-3">Advanced Analytics Reports</h6>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="card card-hover border shadow-none">
                                <div class="card-body text-center">
                                    <i class="mdi mdi-chart-areaspline h2 text-success"></i>
                                    <h6 class="mt-2">Predictive Analytics</h6>
                                    <p class="text-muted font-13">Forecasts & trend analysis</p>
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('reports.generate', ['type' => 'predictive-analytics']) }}" class="btn btn-outline-primary btn-sm">Generate</a>
                                        <a href="{{ route('reports.list', ['type' => 'predictive-analytics']) }}" class="btn btn-outline-info btn-sm">View Reports</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="card card-hover border shadow-none">
                                <div class="card-body text-center">
                                    <i class="mdi mdi-monitor-dashboard h2 text-info"></i>
                                    <h6 class="mt-2">Live Dashboard</h6>
                                    <p class="text-muted font-13">Real-time monitoring</p>
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('reports.live-dashboard') }}" class="btn btn-outline-info btn-sm">View Live Dashboard</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="card card-hover border shadow-none">
                                <div class="card-body text-center">
                                    <i class="mdi mdi-bell h2 text-warning"></i>
                                    <h6 class="mt-2">Automated Alerts</h6>
                                    <p class="text-muted font-13">Threshold breaches & incidents</p>
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('reports.list', ['type' => 'automated-alerts']) }}" class="btn btn-outline-info btn-sm">View Alerts</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection