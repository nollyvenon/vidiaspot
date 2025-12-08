@extends('admin.layout')

@section('title', 'Payment Settings')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Payment Settings</h4>
                    <a href="{{ route('admin.payment-settings.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Add New Setting
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Feature Name</th>
                                    <th>Feature Type</th>
                                    <th>Status</th>
                                    <th>Available Countries</th>
                                    <th>Configuration</th>
                                    <th style="width: 150px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($settings as $setting)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <strong>{{ $setting->feature_name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $setting->feature_key }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $setting->feature_type)) }}</span>
                                    </td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input status-toggle" type="checkbox" 
                                                id="status-{{ $setting->id }}"
                                                data-id="{{ $setting->id }}"
                                                {{ $setting->is_enabled ? 'checked' : '' }}>
                                            <label class="form-check-label" for="status-{{ $setting->id }}">
                                                {{ $setting->is_enabled ? 'Active' : 'Inactive' }}
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        @if($setting->available_countries && count($setting->available_countries) > 0)
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach($setting->available_countries as $country)
                                                    <span class="badge bg-info">{{ $country }}</span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="badge bg-success">Global</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($setting->configuration)
                                            <button class="btn btn-sm btn-outline-primary" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#configModal{{ $setting->id }}">
                                                View Config
                                            </button>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('admin.payment-settings.edit', $setting->id) }}" 
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                
                                <!-- Configuration Modal -->
                                @if($setting->configuration)
                                <div class="modal fade" id="configModal{{ $setting->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">{{ $setting->feature_name }} Configuration</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <pre class="bg-light p-3 rounded">{{ json_encode($setting->configuration, JSON_PRETTY_PRINT) }}</pre>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">No payment settings found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle status toggle switches
        const statusToggles = document.querySelectorAll('.status-toggle');
        statusToggles.forEach(toggle => {
            toggle.addEventListener('change', function() {
                const settingId = this.dataset.id;
                const isChecked = this.checked;
                
                fetch(`/admin/payment-settings/${settingId}/toggle-status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({})
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update the label text
                        const label = this.parentNode.querySelector('label');
                        label.innerHTML = isChecked ? 'Active' : 'Inactive';
                        
                        // Show success message
                        const toast = document.createElement('div');
                        toast.className = 'toast align-items-center text-white bg-success border-0 position-fixed bottom-0 end-0 m-3';
                        toast.setAttribute('role', 'alert');
                        toast.innerHTML = `
                            <div class="d-flex">
                                <div class="toast-body">
                                    ${data.message}
                                </div>
                                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                            </div>
                        `;
                        
                        document.body.appendChild(toast);
                        const bsToast = new bootstrap.Toast(toast);
                        bsToast.show();
                        
                        toast.addEventListener('hidden.bs.toast', function() {
                            document.body.removeChild(toast);
                        });
                    } else {
                        // Revert the toggle on error
                        this.checked = !isChecked;
                        alert(data.message || 'Failed to update status');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Revert the toggle on error
                    this.checked = !isChecked;
                    alert('Network error occurred');
                });
            });
        });
    });
</script>
@endsection