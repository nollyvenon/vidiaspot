@extends('layouts.app')

@section('title', ucfirst($type) . ' Reports')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Reports</a></li>
                        <li class="breadcrumb-item active">{{ ucfirst($type) . ' Reports' }}</li>
                    </ol>
                </div>
                <h4 class="page-title">{{ ucfirst($type) . ' Reports' }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Report List</h5>
                        <div>
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#generateReportModal">
                                <i class="mdi mdi-plus"></i> Generate New Report
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Report ID</th>
                                    <th>Period</th>
                                    <th>Generated At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reports as $report)
                                <tr>
                                    <td>{{ $report->id }}</td>
                                    <td>
                                        @if(isset($report->period_start) && isset($report->period_end))
                                            {{ $report->period_start->format('M d, Y') }} - {{ $report->period_end->format('M d, Y') }}
                                        @elseif(isset($report->date))
                                            {{ $report->date->format('M d, Y') }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>{{ $report->created_at->format('M d, Y H:i:s') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('reports.show', ['type' => $type, 'id' => $report->id]) }}" 
                                               class="btn btn-outline-primary btn-sm">
                                                <i class="mdi mdi-eye"></i> View
                                            </a>
                                            <button type="button" class="btn btn-outline-secondary btn-sm dropdown-toggle dropdown-toggle-split" 
                                                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="mdi mdi-chevron-down"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="#">
                                                    <i class="mdi mdi-download me-1"></i> Export PDF
                                                </a>
                                                <a class="dropdown-item" href="#">
                                                    <i class="mdi mdi-file-excel me-1"></i> Export Excel
                                                </a>
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item text-danger" href="#">
                                                    <i class="mdi mdi-delete me-1"></i> Delete
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">
                                        <i class="mdi mdi-clipboard-text mdi-48px text-muted"></i>
                                        <p class="mt-2">No reports found</p>
                                        <p class="text-muted">Generate your first report to get started</p>
                                        <button type="button" class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#generateReportModal">
                                            <i class="mdi mdi-plus"></i> Generate Report
                                        </button>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-3 d-flex justify-content-center">
                        {{ $reports->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Generate Report Modal -->
<div class="modal fade" id="generateReportModal" tabindex="-1" role="dialog" aria-labelledby="generateReportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="generateReportModalLabel">Generate {{ ucfirst($type) }} Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('reports.generate', ['type' => $type]) }}" method="GET">
                <div class="modal-body">
                    @if(!in_array($type, ['daily-trading', 'live-dashboard', 'automated-alerts']))
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" required>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    @if($type === 'user-trade-history')
                    <div class="mb-3">
                        <label for="user_id" class="form-label">User ID</label>
                        <input type="number" class="form-control" id="user_id" name="user_id" value="{{ auth()->id() }}" placeholder="Enter user ID">
                        <small class="form-text text-muted">Leave blank to use your own user ID</small>
                    </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Generate Report</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set default dates to last 30 days
    const endDate = new Date();
    const startDate = new Date();
    startDate.setDate(startDate.getDate() - 30);
    
    const startInput = document.getElementById('start_date');
    const endInput = document.getElementById('end_date');
    
    if (startInput) {
        startInput.valueAsDate = startDate;
    }
    if (endInput) {
        endInput.valueAsDate = endDate;
    }
});
</script>
@endsection