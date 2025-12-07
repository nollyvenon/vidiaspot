@extends('admin.layout')

@section('title', 'FAQ Management')

@section('content')
<div class="admin-card">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold">FAQ Management</h2>
        <button onclick="showCreateFaqModal()" class="admin-btn admin-btn-primary">Add FAQ</button>
    </div>
    
    <!-- Filters -->
    <form method="GET" class="mb-4 p-4 bg-gray-50 rounded-lg">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="admin-form-label">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Question or answer" class="admin-form-input">
            </div>
            
            <div>
                <label class="admin-form-label">Category</label>
                <select name="category_id" class="admin-form-select">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="admin-form-label">Active?</label>
                <select name="active" class="admin-form-select">
                    <option value="">All</option>
                    <option value="yes" {{ request('active') === 'yes' ? 'selected' : '' }}>Active</option>
                    <option value="no" {{ request('active') === 'no' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            
            <div>
                <label class="admin-form-label">Featured?</label>
                <select name="featured" class="admin-form-select">
                    <option value="">All</option>
                    <option value="yes" {{ request('featured') === 'yes' ? 'selected' : '' }}>Featured</option>
                    <option value="no" {{ request('featured') === 'no' ? 'selected' : '' }}>Not Featured</option>
                </select>
            </div>
            
            <div class="flex items-end space-x-2">
                <button type="submit" class="admin-btn admin-btn-primary">Filter</button>
                <a href="{{ route('admin.faqs.index') }}" class="admin-btn admin-btn-danger">Reset</a>
            </div>
        </div>
    </form>
    
    <!-- FAQs Table -->
    <div class="overflow-x-auto">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Question</th>
                    <th>Category</th>
                    <th>Active</th>
                    <th>Featured</th>
                    <th>Order</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($faqs as $faq)
                <tr>
                    <td>{{ $faq->id }}</td>
                    <td>{{ Str::limit($faq->question, 50) }}</td>
                    <td>{{ $faq->category->name ?? 'Uncategorized' }}</td>
                    <td>
                        <span class="status-badge status-{{ $faq->is_active ? 'completed' : 'pending' }}">
                            {{ $faq->is_active ? 'Yes' : 'No' }}
                        </span>
                    </td>
                    <td>
                        <span class="status-badge status-{{ $faq->is_featured ? 'completed' : 'pending' }}">
                            {{ $faq->is_featured ? 'Yes' : 'No' }}
                        </span>
                    </td>
                    <td>{{ $faq->order }}</td>
                    <td>
                        <button onclick="editFaq({{ $faq->id }})" class="admin-btn admin-btn-success admin-btn-sm">Edit</button>
                        <button onclick="deleteFaq({{ $faq->id }})" class="admin-btn admin-btn-danger admin-btn-sm">Delete</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center">No FAQs found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="mt-4">
        {{ $faqs->appends(request()->query())->links() }}
    </div>
</div>

<!-- Create/Edit FAQ Modal -->
<div id="faq-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-2xl">
        <div class="flex justify-between items-center mb-4">
            <h3 id="faq-modal-title" class="text-lg font-medium">Create FAQ</h3>
            <button onclick="closeFaqModal()" class="text-gray-500 hover:text-gray-700">&times;</button>
        </div>
        
        <form id="faq-form">
            @csrf
            <input type="hidden" id="faq-id" name="id">
            
            <div class="admin-form-group">
                <label class="admin-form-label">Question *</label>
                <input type="text" id="faq-question" name="question" required class="admin-form-input" placeholder="Enter the question">
            </div>
            
            <div class="admin-form-group">
                <label class="admin-form-label">Answer *</label>
                <textarea id="faq-answer" name="answer" required class="admin-form-input" placeholder="Enter the answer" rows="5"></textarea>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="admin-form-group">
                    <label class="admin-form-label">Category</label>
                    <select id="faq-category-id" name="category_id" class="admin-form-select">
                        <option value="">Uncategorized</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="admin-form-group">
                    <label class="admin-form-label">Parent FAQ</label>
                    <select id="faq-parent-id" name="parent_id" class="admin-form-select">
                        <option value="">No Parent</option>
                        @foreach($faqs as $faq)
                            <option value="{{ $faq->id }}">{{ Str::limit($faq->question, 50) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="admin-form-group">
                    <label class="admin-form-label">Order</label>
                    <input type="number" id="faq-order" name="order" class="admin-form-input" value="0">
                </div>
                
                <div class="admin-form-group">
                    <label class="admin-form-label">
                        <input type="checkbox" id="faq-is-active" name="is_active" value="1" checked class="mr-2">
                        Active
                    </label>
                </div>
            </div>
            
            <div class="admin-form-group">
                <label class="admin-form-label">
                    <input type="checkbox" id="faq-is-featured" name="is_featured" value="1" class="mr-2">
                    Featured
                </label>
            </div>
            
            <div class="mt-4 flex justify-end space-x-2">
                <button type="button" onclick="closeFaqModal()" class="admin-btn admin-btn-danger">Cancel</button>
                <button type="submit" class="admin-btn admin-btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
function showCreateFaqModal() {
    document.getElementById('faq-form').reset();
    document.getElementById('faq-modal-title').textContent = 'Create FAQ';
    document.getElementById('faq-id').value = '';
    document.getElementById('faq-is-active').checked = true;
    document.getElementById('faq-is-featured').checked = false;
    document.getElementById('faq-order').value = '0';
    document.getElementById('faq-modal').classList.remove('hidden');
}

function editFaq(faqId) {
    fetch(`/admin/faqs/${faqId}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('faq-modal-title').textContent = 'Edit FAQ';
            document.getElementById('faq-id').value = data.id;
            document.getElementById('faq-question').value = data.question;
            document.getElementById('faq-answer').value = data.answer;
            document.getElementById('faq-category-id').value = data.category_id || '';
            document.getElementById('faq-parent-id').value = data.parent_id || '';
            document.getElementById('faq-order').value = data.order || 0;
            document.getElementById('faq-is-active').checked = data.is_active;
            document.getElementById('faq-is-featured').checked = data.is_featured;
            
            document.getElementById('faq-modal').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading FAQ data');
        });
}

function closeFaqModal() {
    document.getElementById('faq-modal').classList.add('hidden');
}

document.getElementById('faq-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const faqId = document.getElementById('faq-id').value;
    
    let url, method;
    if (faqId) {
        url = `/admin/faqs/${faqId}`;
        method = 'PUT';
    } else {
        url = '/admin/faqs';
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
            closeFaqModal();
            location.reload();
        } else {
            alert(data.error || 'An error occurred');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while saving the FAQ');
    });
});

function deleteFaq(faqId) {
    if (!confirm('Are you sure you want to delete this FAQ? This action cannot be undone.')) {
        return;
    }
    
    fetch(`/admin/faqs/${faqId}`, {
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
        alert('An error occurred while deleting the FAQ');
    });
}
</script>
@endsection