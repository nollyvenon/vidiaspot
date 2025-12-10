@extends('layouts.app')

@section('title', 'Multi-Platform Reports Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Multi-Platform Reports Dashboard</h4>
                <p class="text-muted font-14">Comprehensive reporting across food vending, classified ads, e-commerce, and crypto P2P marketplace</p>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Quick Stats - Platform Summary -->
        <div class="col-xl-3 col-md-6">
            <div class="card box-shadow">
                <div class="card-body">
                    <div class="dropdown float-end position-relative">
                        <a href="#" class="dropdown-toggle arrow-none text-muted" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="mdi mdi-dots-vertical"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="javascript:void(0);" class="dropdown-item">View Consolidated Reports</a>
                            <a href="javascript:void(0);" class="dropdown-item">Export Summary</a>
                            <div class="dropdown-divider"></div>
                            <a href="javascript:void(0);" class="dropdown-item text-danger">Refresh Cache</a>
                        </div>
                    </div>
                    <h6 class="text-uppercase mt-0" title="Total Revenue">Total Revenue</h6>
                    <h2 class="my-2">$2.4M</h2>
                    <p class="mb-0 text-muted">
                        <span class="text-success me-1"><i class="mdi mdi-arrow-up-bold"></i> 12.3%</span>
                        <small>Across all platforms</small>
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
                            <a href="javascript:void(0);" class="dropdown-item">View Traffic Comparison</a>
                            <a href="javascript:void(0);" class="dropdown-item">Export Data</a>
                            <div class="dropdown-divider"></div>
                            <a href="javascript:void(0);" class="dropdown-item text-danger">Update Sources</a>
                        </div>
                    </div>
                    <h6 class="text-uppercase mt-0" title="Active Users">Active Users</h6>
                    <h2 class="my-2">18.5K</h2>
                    <p class="mb-0 text-muted">
                        <span class="text-success me-1"><i class="mdi mdi-arrow-up-bold"></i> 8.7%</span>
                        <small>Across all platforms</small>
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
                            <a href="javascript:void(0);" class="dropdown-item">View Order Stats</a>
                            <a href="javascript:void(0);" class="dropdown-item">Export Orders</a>
                            <div class="dropdown-divider"></div>
                            <a href="javascript:void(0);" class="dropdown-item text-danger">Clear Stats</a>
                        </div>
                    </div>
                    <h6 class="text-uppercase mt-0" title="Total Orders">Total Orders</h6>
                    <h2 class="my-2">42.8K</h2>
                    <p class="mb-0 text-muted">
                        <span class="text-success me-1"><i class="mdi mdi-arrow-up-bold"></i> 15.2%</span>
                        <small>Across all platforms</small>
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
                            <a href="javascript:void(0);" class="dropdown-item">View Cross-Platform</a>
                            <a href="javascript:void(0);" class="dropdown-item">Export Analysis</a>
                            <div class="dropdown-divider"></div>
                            <a href="javascript:void(0);" class="dropdown-item text-danger">Reset Metrics</a>
                        </div>
                    </div>
                    <h6 class="text-uppercase mt-0" title="Cross-Platform">Multi-Platform Users</h6>
                    <h2 class="my-2">3.2K</h2>
                    <p class="mb-0 text-muted">
                        <span class="text-success me-1"><i class="mdi mdi-arrow-up-bold"></i> 22.1%</span>
                        <small>Users active on 2+ platforms</small>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Platform-Specific Reports -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="header-title mb-3">Platform-Specific Reports</h5>

                    <!-- Food Vending & Delivery Reports -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h6 class="text-primary mb-3">🍔 Food Vending & Delivery Reports</h6>
                        </div>

                        <div class="col-md-3 mb-3">
                            <div class="card card-hover border shadow-none">
                                <div class="card-body text-center">
                                    <i class="mdi mdi-cart h2 text-success"></i>
                                    <h6 class="mt-2">Sales & Revenue</h6>
                                    <p class="text-muted font-13">Daily sales & performance</p>
                                    <div class="d-grid gap-2">
                                        <button onclick="generateReport('food-sales-revenue')" class="btn btn-outline-primary btn-sm">Generate</button>
                                        <a href="{{ route('reports.list', ['type' => 'food-sales-revenue']) }}" class="btn btn-outline-info btn-sm">View Reports</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 mb-3">
                            <div class="card card-hover border shadow-none">
                                <div class="card-body text-center">
                                    <i class="mdi mdi-timer-sand h2 text-info"></i>
                                    <h6 class="mt-2">Operational Efficiency</h6>
                                    <p class="text-muted font-13">Kitchen & delivery performance</p>
                                    <div class="d-grid gap-2">
                                        <button onclick="generateReport('food-operational-efficiency')" class="btn btn-outline-primary btn-sm">Generate</button>
                                        <a href="{{ route('reports.list', ['type' => 'food-operational-efficiency']) }}" class="btn btn-outline-info btn-sm">View Reports</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 mb-3">
                            <div class="card card-hover border shadow-none">
                                <div class="card-body text-center">
                                    <i class="mdi mdi-emoticon-happy h2 text-warning"></i>
                                    <h6 class="mt-2">Customer Experience</h6>
                                    <p class="text-muted font-13">Order fulfillment & feedback</p>
                                    <div class="d-grid gap-2">
                                        <button onclick="generateReport('food-customer-experience')" class="btn btn-outline-primary btn-sm">Generate</button>
                                        <a href="{{ route('reports.list', ['type' => 'food-customer-experience']) }}" class="btn btn-outline-info btn-sm">View Reports</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 mb-3">
                            <div class="card card-hover border shadow-none">
                                <div class="card-body text-center">
                                    <i class="mdi mdi-cash-multiple h2 text-purple"></i>
                                    <h6 class="mt-2">Financial Reports</h6>
                                    <p class="text-muted font-13">COGS & profit margins</p>
                                    <div class="d-grid gap-2">
                                        <button onclick="generateReport('food-financial')" class="btn btn-outline-primary btn-sm">Generate</button>
                                        <a href="{{ route('reports.list', ['type' => 'food-financial']) }}" class="btn btn-outline-info btn-sm">View Reports</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Classified Ads Reports -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h6 class="text-primary mb-3">📋 Classified Ads Reports</h6>
                        </div>

                        <div class="col-md-3 mb-3">
                            <div class="card card-hover border shadow-none">
                                <div class="card-body text-center">
                                    <i class="mdi mdi-account-group h2 text-success"></i>
                                    <h6 class="mt-2">User Activity</h6>
                                    <p class="text-muted font-13">Registrations & engagement</p>
                                    <div class="d-grid gap-2">
                                        <button onclick="generateReport('classified-user-activity')" class="btn btn-outline-primary btn-sm">Generate</button>
                                        <a href="{{ route('reports.list', ['type' => 'classified-user-activity']) }}" class="btn btn-outline-info btn-sm">View Reports</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 mb-3">
                            <div class="card card-hover border shadow-none">
                                <div class="card-body text-center">
                                    <i class="mdi mdi-currency-usd h2 text-info"></i>
                                    <h6 class="mt-2">Revenue & Financial</h6>
                                    <p class="text-muted font-13">Subscriptions & commissions</p>
                                    <div class="d-grid gap-2">
                                        <button onclick="generateReport('classified-revenue')" class="btn btn-outline-primary btn-sm">Generate</button>
                                        <a href="{{ route('reports.list', ['type' => 'classified-revenue']) }}" class="btn btn-outline-info btn-sm">View Reports</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 mb-3">
                            <div class="card card-hover border shadow-none">
                                <div class="card-body text-center">
                                    <i class="mdi mdi-file-check h2 text-warning"></i>
                                    <h6 class="mt-2">Content Quality</h6>
                                    <p class="text-muted font-13">Moderation & compliance</p>
                                    <div class="d-grid gap-2">
                                        <button onclick="generateReport('classified-content-quality')" class="btn btn-outline-primary btn-sm">Generate</button>
                                        <a href="{{ route('reports.list', ['type' => 'classified-content-quality']) }}" class="btn btn-outline-info btn-sm">View Reports</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 mb-3">
                            <div class="card card-hover border shadow-none">
                                <div class="card-body text-center">
                                    <i class="mdi mdi-chart-bar h2 text-purple"></i>
                                    <h6 class="mt-2">Market Intelligence</h6>
                                    <p class="text-muted font-13">Pricing & demand trends</p>
                                    <div class="d-grid gap-2">
                                        <button onclick="generateReport('classified-market-intelligence')" class="btn btn-outline-primary btn-sm">Generate</button>
                                        <a href="{{ route('reports.list', ['type' => 'classified-market-intelligence']) }}" class="btn btn-outline-info btn-sm">View Reports</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- E-commerce Reports -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h6 class="text-primary mb-3">📦 E-commerce Reports</h6>
                        </div>

                        <div class="col-md-3 mb-3">
                            <div class="card card-hover border shadow-none">
                                <div class="card-body text-center">
                                    <i class="mdi mdi-trending-up h2 text-success"></i>
                                    <h6 class="mt-2">Sales Performance</h6>
                                    <p class="text-muted font-13">Revenue & customer metrics</p>
                                    <div class="d-grid gap-2">
                                        <button onclick="generateReport('ecommerce-sales-performance')" class="btn btn-outline-primary btn-sm">Generate</button>
                                        <a href="{{ route('reports.list', ['type' => 'ecommerce-sales-performance']) }}" class="btn btn-outline-info btn-sm">View Reports</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 mb-3">
                            <div class="card card-hover border shadow-none">
                                <div class="card-body text-center">
                                    <i class="mdi mdi-package-variant h2 text-info"></i>
                                    <h6 class="mt-2">Inventory Management</h6>
                                    <p class="text-muted font-13">Stock & turnover analysis</p>
                                    <div class="d-grid gap-2">
                                        <button onclick="generateReport('ecommerce-inventory')" class="btn btn-outline-primary btn-sm">Generate</button>
                                        <a href="{{ route('reports.list', ['type' => 'ecommerce-inventory']) }}" class="btn btn-outline-info btn-sm">View Reports</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 mb-3">
                            <div class="card card-hover border shadow-none">
                                <div class="card-body text-center">
                                    <i class="mdi mdi-bullhorn h2 text-warning"></i>
                                    <h6 class="mt-2">Marketing & Customer</h6>
                                    <p class="text-muted font-13">Campaigns & segmentation</p>
                                    <div class="d-grid gap-2">
                                        <button onclick="generateReport('ecommerce-marketing-customer')" class="btn btn-outline-primary btn-sm">Generate</button>
                                        <a href="{{ route('reports.list', ['type' => 'ecommerce-marketing-customer']) }}" class="btn btn-outline-info btn-sm">View Reports</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 mb-3">
                            <div class="card card-hover border shadow-none">
                                <div class="card-body text-center">
                                    <i class="mdi mdi-finance h2 text-purple"></i>
                                    <h6 class="mt-2">Financial & Operational</h6>
                                    <p class="text-muted font-13">Margins & processing</p>
                                    <div class="d-grid gap-2">
                                        <button onclick="generateReport('ecommerce-financial-operational')" class="btn btn-outline-primary btn-sm">Generate</button>
                                        <a href="{{ route('reports.list', ['type' => 'ecommerce-financial-operational']) }}" class="btn btn-outline-info btn-sm">View Reports</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Crypto P2P Marketplace Reports -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h6 class="text-primary mb-3">💸 Crypto P2P Marketplace Reports</h6>
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
                                    <h6 class="mt-2">Trading Activity</h6>
                                    <p class="text-muted font-13">Volume & performance metrics</p>
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('reports.generate', ['type' => 'daily-trading']) }}" class="btn btn-outline-primary btn-sm">Generate</a>
                                        <a href="{{ route('reports.list', ['type' => 'daily-trading']) }}" class="btn btn-outline-info btn-sm">View Reports</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cross-Platform Integration Reports -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h6 class="text-primary mb-3">🔗 Cross-Platform Integration Reports</h6>
                        </div>

                        <div class="col-md-3 mb-3">
                            <div class="card card-hover border shadow-none">
                                <div class="card-body text-center">
                                    <i class="mdi mdi-google-analytics h2 text-success"></i>
                                    <h6 class="mt-2">Unified Financial Dashboard</h6>
                                    <p class="text-muted font-13">Consolidated revenue & metrics</p>
                                    <div class="d-grid gap-2">
                                        <button onclick="generateReport('unified-financial-dashboard')" class="btn btn-outline-primary btn-sm">Generate</button>
                                        <a href="{{ route('reports.list', ['type' => 'unified-financial-dashboard']) }}" class="btn btn-outline-info btn-sm">View Reports</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 mb-3">
                            <div class="card card-hover border shadow-none">
                                <div class="card-body text-center">
                                    <i class="mdi mdi-account-multiple h2 text-info"></i>
                                    <h6 class="mt-2">Customer Journey</h6>
                                    <p class="text-muted font-13">Cross-platform behavior</p>
                                    <div class="d-grid gap-2">
                                        <button onclick="generateReport('cross-platform-customer-journey')" class="btn btn-outline-primary btn-sm">Generate</button>
                                        <a href="{{ route('reports.list', ['type' => 'cross-platform-customer-journey']) }}" class="btn btn-outline-info btn-sm">View Reports</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 mb-3">
                            <div class="card card-hover border shadow-none">
                                <div class="card-body text-center">
                                    <i class="mdi mdi-wrench h2 text-warning"></i>
                                    <h6 class="mt-2">Operational Efficiency</h6>
                                    <p class="text-muted font-13">Shared resources & processes</p>
                                    <div class="d-grid gap-2">
                                        <button onclick="generateReport('cross-platform-operational-efficiency')" class="btn btn-outline-primary btn-sm">Generate</button>
                                        <a href="{{ route('reports.list', ['type' => 'cross-platform-operational-efficiency']) }}" class="btn btn-outline-info btn-sm">View Reports</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 mb-3">
                            <div class="card card-hover border shadow-none">
                                <div class="card-body text-center">
                                    <i class="mdi mdi-security h2 text-purple"></i>
                                    <h6 class="mt-2">Risk Management</h6>
                                    <p class="text-muted font-13">Cross-platform risks</p>
                                    <div class="d-grid gap-2">
                                        <button onclick="generateReport('cross-platform-risk-management')" class="btn btn-outline-primary btn-sm">Generate</button>
                                        <a href="{{ route('reports.list', ['type' => 'cross-platform-risk-management']) }}" class="btn btn-outline-info btn-sm">View Reports</a>
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

<script>
function generateReport(type) {
    // Generic function to generate reports
    let params = new URLSearchParams({
        start_date: moment().subtract(30, 'days').format('YYYY-MM-DD'),
        end_date: moment().format('YYYY-MM-DD')
    });

    fetch(`/reports/generate/${type}?${params}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Authorization': 'Bearer ' + localStorage.getItem('api_token') // if using API tokens
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Report generated successfully!');
            // Optionally redirect to the new report
            // window.location.href = `/reports/view/${type}/${data.data.id}`;
        } else {
            alert('Error generating report: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while generating the report.');
    });
}

// Include Moment.js for date handling
document.write('<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"><\/script>');
</script>
@endsection