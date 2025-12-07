@extends('admin.layout')

@section('title', 'Categories Management')

@section('content')
<div class="admin-card">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold">Categories Management</h2>
        <button onclick="showCreateCategoryModal()" class="admin-btn admin-btn-primary">Add Category</button>
    </div>
    
    <!-- Filters -->
    <form method="GET" class="mb-4 p-4 bg-gray-50 rounded-lg">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="admin-form-label">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Category name" class="admin-form-input">
            </div>
            
            <div>
                <label class="admin-form-label">Active Status</label>
                <select name="active" class="admin-form-select">
                    <option value="">All</option>
                    <option value="yes" {{ request('active') === 'yes' ? 'selected' : '' }}>Active</option>
                    <option value="no" {{ request('active') === 'no' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            
            <div class="flex items-end space-x-2">
                <button type="submit" class="admin-btn admin-btn-primary">Filter</button>
                <a href="{{ route('admin.categories.index') }}" class="admin-btn admin-btn-danger">Reset</a>
            </div>
        </div>
    </form>
    
    <!-- Categories Table -->
    <div class="overflow-x-auto">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Parent</th>
                    <th>Active</th>
                    <th>Ads Count</th>
                    <th>Order</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                <tr>
                    <td>{{ $category->id }}</td>
                    <td>{{ $category->name }}</td>
                    <td>{{ $category->slug }}</td>
                    <td>{{ $category->parent ? $category->parent->name : 'None' }}</td>
                    <td>
                        <span class="status-badge status-{{ $category->is_active ? 'completed' : 'pending' }}">
                            {{ $category->is_active ? 'Yes' : 'No' }}
                        </span>
                    </td>
                    <td>{{ $category->ads->count() }}</td>
                    <td>{{ $category->order }}</td>
                    <td>
                        <button onclick="editCategory({{ $category->id }})" class="admin-btn admin-btn-success admin-btn-sm">Edit</button>
                        <button onclick="deleteCategory({{ $category->id }})" class="admin-btn admin-btn-danger admin-btn-sm">Delete</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center">No categories found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Create/Edit Category Modal -->
<div id="category-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 id="category-modal-title" class="text-lg font-medium">Create Category</h3>
            <button onclick="closeCategoryModal()" class="text-gray-500 hover:text-gray-700">&times;</button>
        </div>
        
        <form id="category-form">
            @csrf
            <input type="hidden" id="category-id" name="id">
            
            <div class="admin-form-group">
                <label class="admin-form-label">Name *</label>
                <input type="text" id="category-name" name="name" required class="admin-form-input" placeholder="Category name">
            </div>
            
            <div class="admin-form-group">
                <label class="admin-form-label">Slug *</label>
                <input type="text" id="category-slug" name="slug" required class="admin-form-input" placeholder="category-slug">
            </div>
            
            <div class="admin-form-group">
                <label class="admin-form-label">Parent Category</label>
                <select id="category-parent-id" name="parent_id" class="admin-form-select">
                    <option value="">No Parent</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="admin-form-group">
                <label class="admin-form-label">Description</label>
                <textarea id="category-description" name="description" class="admin-form-input" placeholder="Description" rows="3"></textarea>
            </div>
            
            <div class="admin-form-group">
                <label class="admin-form-label">Icon</label>
                <input type="text" id="category-icon" name="icon" class="admin-form-input" placeholder="e.g. fas fa-home">
            </div>
            
            <div class="admin-form-group">
                <label class="admin-form-label">Order</label>
                <input type="number" id="category-order" name="order" class="admin-form-input" value="0">
            </div>
            
            <div class="admin-form-group">
                <label class="admin-form-label">
                    <input type="checkbox" id="category-is-active" name="is_active" value="1" checked class="mr-2">
                    Active
                </label>
            </div>
            
            <div class="mt-4 flex justify-end space-x-2">
                <button type="button" onclick="closeCategoryModal()" class="admin-btn admin-btn-danger">Cancel</button>
                <button type="submit" class="admin-btn admin-btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
function showCreateCategoryModal() {
    document.getElementById('category-form').reset();
    document.getElementById('category-modal-title').textContent = 'Create Category';
    document.getElementById('category-id').value = '';
    document.getElementById('category-is-active').checked = true;
    document.getElementById('category-order').value = '0';
    document.getElementById('category-parent-id').value = '';
    document.getElementById('category-modal').classList.remove('hidden');
}

function editCategory(categoryId) {
    fetch(`/admin/categories/${categoryId}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('category-modal-title').textContent = 'Edit Category';
            document.getElementById('category-id').value = data.id;
            document.getElementById('category-name').value = data.name;
            document.getElementById('category-slug').value = data.slug;
            document.getElementById('category-parent-id').value = data.parent_id || '';
            document.getElementById('category-description').value = data.description || '';
            document.getElementById('category-icon').value = data.icon || '';
            document.getElementById('category-order').value = data.order || 0;
            document.getElementById('category-is-active').checked = data.is_active;
            
            document.getElementById('category-modal').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading category data');
        });
}

function closeCategoryModal() {
    document.getElementById('category-modal').classList.add('hidden');
}

document.getElementById('category-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const categoryId = document.getElementById('category-id').value;
    
    let url, method;
    if (categoryId) {
        url = `/admin/categories/${categoryId}`;
        method = 'PUT';
    } else {
        url = '/admin/categories';
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
            closeCategoryModal();
            location.reload();
        } else {
            alert(data.error || 'An error occurred');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while saving the category');
    });
});

function deleteCategory(categoryId) {
    if (!confirm('Are you sure you want to delete this category? This action cannot be undone.')) {
        return;
    }
    
    fetch(`/admin/categories/${categoryId}`, {
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
        alert('An error occurred while deleting the category');
    });
}
</script>
@endsection