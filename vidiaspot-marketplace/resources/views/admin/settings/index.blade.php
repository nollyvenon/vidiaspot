@extends('admin.layout')

@section('title', 'Settings Management')

@section('content')
<div class="admin-card">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold">Settings Management</h2>
        <button onclick="showCreateSettingModal()" class="admin-btn admin-btn-primary">Add Setting</button>
    </div>
    
    <!-- Filters -->
    <form method="GET" class="mb-4 p-4 bg-gray-50 rounded-lg">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="admin-form-label">Section</label>
                <select name="section" class="admin-form-select">
                    <option value="">All Sections</option>
                    @foreach($sections as $section)
                        <option value="{{ $section }}" {{ request('section') === $section ? 'selected' : '' }}>{{ ucfirst($section) }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="admin-form-label">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Setting name or key" class="admin-form-input">
            </div>
            
            <div class="flex items-end space-x-2">
                <button type="submit" class="admin-btn admin-btn-primary">Filter</button>
                <a href="{{ route('admin.settings.index') }}" class="admin-btn admin-btn-danger">Reset</a>
            </div>
        </div>
    </form>
    
    <!-- Mobile App Configuration Section -->
    <div class="admin-card mb-6 bg-blue-50">
        <h3 class="text-lg font-semibold mb-4">Mobile App Configuration</h3>
        
        <form id="mobile-config-form">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="admin-form-group">
                    <label class="admin-form-label">App Name</label>
                    <input type="text" id="app-name" name="app_name" class="admin-form-input" placeholder="VidiaSpot">
                </div>
                
                <div class="admin-form-group">
                    <label class="admin-form-label">App Logo URL</label>
                    <input type="text" id="app-logo" name="app_logo" class="admin-form-input" placeholder="https://example.com/logo.png">
                </div>
                
                <div class="admin-form-group">
                    <label class="admin-form-label">App Icon URL</label>
                    <input type="text" id="app-icon" name="app_icon" class="admin-form-input" placeholder="https://example.com/icon.png">
                </div>
                
                <div class="admin-form-group">
                    <label class="admin-form-label">Primary Color</label>
                    <input type="color" id="primary-color" name="primary_color" class="admin-form-input" value="#3b82f6">
                </div>
                
                <div class="admin-form-group">
                    <label class="admin-form-label">Secondary Color</label>
                    <input type="color" id="secondary-color" name="secondary_color" class="admin-form-input" value="#10b981">
                </div>
                
                <div class="admin-form-group">
                    <label class="admin-form-label">Accent Color</label>
                    <input type="color" id="accent-color" name="accent_color" class="admin-form-input" value="#8b5cf6">
                </div>
            </div>
            
            <div class="mt-4">
                <button type="submit" class="admin-btn admin-btn-primary">Update Mobile Configuration</button>
            </div>
        </form>
    </div>
    
    <!-- Other Settings Table -->
    <div class="overflow-x-auto">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Key</th>
                    <th>Name</th>
                    <th>Value</th>
                    <th>Type</th>
                    <th>Section</th>
                    <th>Public</th>
                    <th>Active</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($settings as $setting)
                <tr>
                    <td>{{ $setting->id }}</td>
                    <td>{{ $setting->key }}</td>
                    <td>{{ $setting->name }}</td>
                    <td>{{ Str::limit($setting->value, 30) }}</td>
                    <td>{{ ucfirst($setting->type) }}</td>
                    <td>{{ $setting->section }}</td>
                    <td>
                        <span class="status-badge status-{{ $setting->is_public ? 'completed' : 'pending' }}">
                            {{ $setting->is_public ? 'Yes' : 'No' }}
                        </span>
                    </td>
                    <td>
                        <span class="status-badge status-{{ $setting->is_active ? 'completed' : 'pending' }}">
                            {{ $setting->is_active ? 'Yes' : 'No' }}
                        </span>
                    </td>
                    <td>
                        <button onclick="editSetting({{ $setting->id }})" class="admin-btn admin-btn-success admin-btn-sm">Edit</button>
                        <button onclick="deleteSetting({{ $setting->id }})" class="admin-btn admin-btn-danger admin-btn-sm">Delete</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center">No settings found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="mt-4">
        {{ $settings->appends(request()->query())->links() }}
    </div>
</div>

<!-- Create/Edit Setting Modal -->
<div id="setting-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 id="setting-modal-title" class="text-lg font-medium">Create Setting</h3>
            <button onclick="closeSettingModal()" class="text-gray-500 hover:text-gray-700">&times;</button>
        </div>
        
        <form id="setting-form">
            @csrf
            <input type="hidden" id="setting-id" name="id">
            
            <div class="admin-form-group">
                <label class="admin-form-label">Key *</label>
                <input type="text" id="setting-key" name="key" required class="admin-form-input" placeholder="app.name">
            </div>
            
            <div class="admin-form-group">
                <label class="admin-form-label">Name *</label>
                <input type="text" id="setting-name" name="name" required class="admin-form-input" placeholder="Application Name">
            </div>
            
            <div class="admin-form-group">
                <label class="admin-form-label">Value *</label>
                <textarea id="setting-value" name="value" required class="admin-form-input" placeholder="Setting value" rows="3"></textarea>
            </div>
            
            <div class="admin-form-group">
                <label class="admin-form-label">Type *</label>
                <select id="setting-type" name="type" class="admin-form-select">
                    <option value="string">String</option>
                    <option value="text">Text</option>
                    <option value="boolean">Boolean</option>
                    <option value="integer">Integer</option>
                    <option value="array">Array</option>
                    <option value="json">JSON</option>
                </select>
            </div>
            
            <div class="admin-form-group">
                <label class="admin-form-label">Section *</label>
                <input type="text" id="setting-section" name="section" required class="admin-form-input" placeholder="general">
            </div>
            
            <div class="admin-form-group">
                <label class="admin-form-label">Description</label>
                <textarea id="setting-description" name="description" class="admin-form-input" placeholder="Setting description" rows="2"></textarea>
            </div>
            
            <div class="admin-form-group">
                <label class="admin-form-label">
                    <input type="checkbox" id="setting-is-public" name="is_public" value="1" class="mr-2">
                    Public
                </label>
            </div>
            
            <div class="admin-form-group">
                <label class="admin-form-label">
                    <input type="checkbox" id="setting-is-active" name="is_active" value="1" checked class="mr-2">
                    Active
                </label>
            </div>
            
            <div class="mt-4 flex justify-end space-x-2">
                <button type="button" onclick="closeSettingModal()" class="admin-btn admin-btn-danger">Cancel</button>
                <button type="submit" class="admin-btn admin-btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
// Load mobile configuration when page loads
document.addEventListener('DOMContentLoaded', function() {
    loadMobileConfig();
});

function loadMobileConfig() {
    fetch('/admin/settings/mobile-config')
        .then(response => response.json())
        .then(data => {
            const config = data.mobile_config;
            config.forEach(setting => {
                if (setting.key === 'mobile.app_name') {
                    document.getElementById('app-name').value = setting.value;
                } else if (setting.key === 'mobile.app_logo') {
                    document.getElementById('app-logo').value = setting.value;
                } else if (setting.key === 'mobile.app_icon') {
                    document.getElementById('app-icon').value = setting.value;
                } else if (setting.key === 'mobile.primary_color') {
                    document.getElementById('primary-color').value = setting.value;
                } else if (setting.key === 'mobile.secondary_color') {
                    document.getElementById('secondary-color').value = setting.value;
                } else if (setting.key === 'mobile.accent_color') {
                    document.getElementById('accent-color').value = setting.value;
                }
            });
        })
        .catch(error => {
            console.error('Error loading mobile config:', error);
        });
}

document.getElementById('mobile-config-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('/admin/settings/mobile-config', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(Object.fromEntries(formData))
    })
    .then(response => response.json())
    .then(data => {
        if (data.message) {
            alert(data.message);
        } else {
            alert(data.error || 'An error occurred');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while saving the mobile configuration');
    });
});

function showCreateSettingModal() {
    document.getElementById('setting-form').reset();
    document.getElementById('setting-modal-title').textContent = 'Create Setting';
    document.getElementById('setting-id').value = '';
    document.getElementById('setting-is-active').checked = true;
    document.getElementById('setting-modal').classList.remove('hidden');
}

function editSetting(settingId) {
    fetch(`/admin/settings/${settingId}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('setting-modal-title').textContent = 'Edit Setting';
            document.getElementById('setting-id').value = data.id;
            document.getElementById('setting-key').value = data.key;
            document.getElementById('setting-name').value = data.name;
            document.getElementById('setting-value').value = data.value;
            document.getElementById('setting-type').value = data.type;
            document.getElementById('setting-section').value = data.section;
            document.getElementById('setting-description').value = data.description || '';
            document.getElementById('setting-is-public').checked = data.is_public;
            document.getElementById('setting-is-active').checked = data.is_active;
            
            document.getElementById('setting-modal').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading setting data');
        });
}

function closeSettingModal() {
    document.getElementById('setting-modal').classList.add('hidden');
}

document.getElementById('setting-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const settingId = document.getElementById('setting-id').value;
    
    let url, method;
    if (settingId) {
        url = `/admin/settings/${settingId}`;
        method = 'PUT';
    } else {
        url = '/admin/settings';
        method = 'POST';
    }
    
    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(Object.fromEntries(formData))
    })
    .then(response => response.json())
    .then(data => {
        if (data.message) {
            alert(data.message);
            closeSettingModal();
            location.reload();
        } else {
            alert(data.error || 'An error occurred');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while saving the setting');
    });
});

function deleteSetting(settingId) {
    if (!confirm('Are you sure you want to delete this setting? This action cannot be undone.')) {
        return;
    }
    
    fetch(`/admin/settings/${settingId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
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
        alert('An error occurred while deleting the setting');
    });
}
</script>
@endsection