@extends('admin.layout')

@section('title', 'Blogs Management')

@section('content')
<div class="admin-card">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold">Blogs Management</h2>
        <button onclick="showCreateBlogModal()" class="admin-btn admin-btn-primary">Add Blog</button>
    </div>
    
    <!-- Filters -->
    <form method="GET" class="mb-4 p-4 bg-gray-50 rounded-lg">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="admin-form-label">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Title or content" class="admin-form-input">
            </div>
            
            <div>
                <label class="admin-form-label">Status</label>
                <select name="status" class="admin-form-select">
                    <option value="">All Statuses</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                </select>
            </div>
            
            <div>
                <label class="admin-form-label">Author</label>
                <select name="user_id" class="admin-form-select">
                    <option value="">All Authors</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="flex items-end space-x-2">
                <button type="submit" class="admin-btn admin-btn-primary">Filter</button>
                <a href="{{ route('admin.blogs.index') }}" class="admin-btn admin-btn-danger">Reset</a>
            </div>
        </div>
    </form>
    
    <!-- Blogs Table -->
    <div class="overflow-x-auto">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($blogs as $blog)
                <tr>
                    <td>{{ $blog->id }}</td>
                    <td>{{ Str::limit($blog->title, 30) }}</td>
                    <td>{{ $blog->user->name ?? 'N/A' }}</td>
                    <td>
                        <span class="status-badge status-{{ $blog->status }}">
                            {{ ucfirst($blog->status) }}
                        </span>
                    </td>
                    <td>{{ $blog->created_at->format('Y-m-d') }}</td>
                    <td>
                        <button onclick="editBlog({{ $blog->id }})" class="admin-btn admin-btn-success admin-btn-sm">Edit</button>
                        <button onclick="deleteBlog({{ $blog->id }})" class="admin-btn admin-btn-danger admin-btn-sm">Delete</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center">No blogs found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="mt-4">
        {{ $blogs->appends(request()->query())->links() }}
    </div>
</div>

<!-- Create/Edit Blog Modal -->
<div id="blog-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-2xl">
        <div class="flex justify-between items-center mb-4">
            <h3 id="blog-modal-title" class="text-lg font-medium">Create Blog</h3>
            <button onclick="closeBlogModal()" class="text-gray-500 hover:text-gray-700">&times;</button>
        </div>
        
        <form id="blog-form">
            @csrf
            <input type="hidden" id="blog-id" name="id">
            
            <div class="admin-form-group">
                <label class="admin-form-label">Title *</label>
                <input type="text" id="blog-title" name="title" required class="admin-form-input" placeholder="Blog title">
            </div>
            
            <div class="admin-form-group">
                <label class="admin-form-label">Excerpt</label>
                <textarea id="blog-excerpt" name="excerpt" class="admin-form-input" placeholder="Brief description" rows="3"></textarea>
            </div>
            
            <div class="admin-form-group">
                <label class="admin-form-label">Content *</label>
                <textarea id="blog-content" name="content" required class="admin-form-input" placeholder="Blog content" rows="8"></textarea>
            </div>
            
            <div class="admin-form-group">
                <label class="admin-form-label">Featured Image URL</label>
                <input type="text" id="blog-featured-image" name="featured_image" class="admin-form-input" placeholder="https://example.com/image.jpg">
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="admin-form-group">
                    <label class="admin-form-label">Meta Title</label>
                    <input type="text" id="blog-meta-title" name="meta_title" class="admin-form-input" placeholder="SEO title">
                </div>
                
                <div class="admin-form-group">
                    <label class="admin-form-label">Meta Description</label>
                    <input type="text" id="blog-meta-description" name="meta_description" class="admin-form-input" placeholder="SEO description">
                </div>
            </div>
            
            <div class="admin-form-group">
                <label class="admin-form-label">Status *</label>
                <select id="blog-status" name="status" class="admin-form-select">
                    <option value="draft">Draft</option>
                    <option value="pending">Pending</option>
                    <option value="published">Published</option>
                </select>
            </div>
            
            <div class="mt-4 flex justify-end space-x-2">
                <button type="button" onclick="closeBlogModal()" class="admin-btn admin-btn-danger">Cancel</button>
                <button type="submit" class="admin-btn admin-btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
function showCreateBlogModal() {
    document.getElementById('blog-form').reset();
    document.getElementById('blog-modal-title').textContent = 'Create Blog';
    document.getElementById('blog-id').value = '';
    document.getElementById('blog-status').value = 'draft';
    document.getElementById('blog-modal').classList.remove('hidden');
}

function editBlog(blogId) {
    fetch(`/admin/blogs/${blogId}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('blog-modal-title').textContent = 'Edit Blog';
            document.getElementById('blog-id').value = data.id;
            document.getElementById('blog-title').value = data.title;
            document.getElementById('blog-excerpt').value = data.excerpt || '';
            document.getElementById('blog-content').value = data.content;
            document.getElementById('blog-featured-image').value = data.featured_image || '';
            document.getElementById('blog-meta-title').value = data.meta_title || '';
            document.getElementById('blog-meta-description').value = data.meta_description || '';
            document.getElementById('blog-status').value = data.status;
            
            document.getElementById('blog-modal').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading blog data');
        });
}

function closeBlogModal() {
    document.getElementById('blog-modal').classList.add('hidden');
}

document.getElementById('blog-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const blogId = document.getElementById('blog-id').value;
    
    let url, method;
    if (blogId) {
        url = `/admin/blogs/${blogId}`;
        method = 'PUT';
    } else {
        url = '/admin/blogs';
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
            closeBlogModal();
            location.reload();
        } else {
            alert(data.error || 'An error occurred');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while saving the blog');
    });
});

function deleteBlog(blogId) {
    if (!confirm('Are you sure you want to delete this blog? This action cannot be undone.')) {
        return;
    }
    
    fetch(`/admin/blogs/${blogId}`, {
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
        alert('An error occurred while deleting the blog');
    });
}
</script>
@endsection