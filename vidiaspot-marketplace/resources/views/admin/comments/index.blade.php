@extends('admin.layout')

@section('title', 'Comments Management')

@section('content')
<div class="admin-card">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold">Comments Management</h2>
    </div>
    
    <!-- Filters -->
    <form method="GET" class="mb-4 p-4 bg-gray-50 rounded-lg">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="admin-form-label">Entity Type</label>
                <select name="commentable_type" class="admin-form-select">
                    <option value="">All Types</option>
                    @foreach($commentableTypes as $type => $name)
                        <option value="{{ $type }}" {{ request('commentable_type') === $type ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="admin-form-label">Private?</label>
                <select name="private" class="admin-form-select">
                    <option value="">All</option>
                    <option value="yes" {{ request('private') === 'yes' ? 'selected' : '' }}>Private</option>
                    <option value="no" {{ request('private') === 'no' ? 'selected' : '' }}>Public</option>
                </select>
            </div>
            
            <div>
                <label class="admin-form-label">Approved?</label>
                <select name="approved" class="admin-form-select">
                    <option value="">All</option>
                    <option value="yes" {{ request('approved') === 'yes' ? 'selected' : '' }}>Approved</option>
                    <option value="no" {{ request('approved') === 'no' ? 'selected' : '' }}>Not Approved</option>
                </select>
            </div>
            
            <div>
                <label class="admin-form-label">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Comment content" class="admin-form-input">
            </div>
            
            <div class="flex items-end space-x-2">
                <button type="submit" class="admin-btn admin-btn-primary">Filter</button>
                <a href="{{ route('admin.comments.index') }}" class="admin-btn admin-btn-danger">Reset</a>
            </div>
        </div>
    </form>
    
    <!-- Comments Table -->
    <div class="overflow-x-auto">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Content</th>
                    <th>User</th>
                    <th>Entity</th>
                    <th>Type</th>
                    <th>Private</th>
                    <th>Approved</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($comments as $comment)
                <tr>
                    <td>{{ $comment->id }}</td>
                    <td>{{ Str::limit($comment->content, 50) }}</td>
                    <td>{{ $comment->user->name ?? 'N/A' }}</td>
                    <td>
                        {{ class_basename($comment->commentable_type) }}
                        @if($comment->commentable)
                            #{{ $comment->commentable_id }}
                        @else
                            (deleted)
                        @endif
                    </td>
                    <td>{{ $comment->commentable ? get_class($comment->commentable) : 'N/A' }}</td>
                    <td>
                        <span class="status-badge status-{{ $comment->is_private ? 'completed' : 'pending' }}">
                            {{ $comment->is_private ? 'Yes' : 'No' }}
                        </span>
                    </td>
                    <td>
                        <span class="status-badge status-{{ $comment->is_approved ? 'completed' : 'pending' }}">
                            {{ $comment->is_approved ? 'Yes' : 'No' }}
                        </span>
                    </td>
                    <td>{{ $comment->created_at->format('Y-m-d H:i') }}</td>
                    <td>
                        <button onclick="editComment({{ $comment->id }})" class="admin-btn admin-btn-success admin-btn-sm">Edit</button>
                        <button onclick="deleteComment({{ $comment->id }})" class="admin-btn admin-btn-danger admin-btn-sm">Delete</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center">No comments found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="mt-4">
        {{ $comments->appends(request()->query())->links() }}
    </div>
</div>

<!-- Edit Comment Modal -->
<div id="comment-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 id="comment-modal-title" class="text-lg font-medium">Edit Comment</h3>
            <button onclick="closeCommentModal()" class="text-gray-500 hover:text-gray-700">&times;</button>
        </div>
        
        <form id="comment-form">
            @csrf
            <input type="hidden" id="comment-id" name="id">
            
            <div class="admin-form-group">
                <label class="admin-form-label">Content *</label>
                <textarea id="comment-content" name="content" required class="admin-form-input" rows="4" placeholder="Comment content"></textarea>
            </div>
            
            <div class="admin-form-group">
                <label class="admin-form-label">
                    <input type="checkbox" id="comment-is-private" name="is_private" value="1" class="mr-2">
                    Private (Admin Only)
                </label>
            </div>
            
            <div class="admin-form-group">
                <label class="admin-form-label">
                    <input type="checkbox" id="comment-is-approved" name="is_approved" value="1" class="mr-2">
                    Approved
                </label>
            </div>
            
            <div class="mt-4 flex justify-end space-x-2">
                <button type="button" onclick="closeCommentModal()" class="admin-btn admin-btn-danger">Cancel</button>
                <button type="submit" class="admin-btn admin-btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
function editComment(commentId) {
    fetch(`/admin/comments/${commentId}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('comment-modal-title').textContent = 'Edit Comment';
            document.getElementById('comment-id').value = data.id;
            document.getElementById('comment-content').value = data.content;
            document.getElementById('comment-is-private').checked = data.is_private;
            document.getElementById('comment-is-approved').checked = data.is_approved;
            
            document.getElementById('comment-modal').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading comment data');
        });
}

function closeCommentModal() {
    document.getElementById('comment-modal').classList.add('hidden');
}

document.getElementById('comment-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const commentId = document.getElementById('comment-id').value;
    
    fetch(`/admin/comments/${commentId}`, {
        method: 'PUT',
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
            closeCommentModal();
            location.reload();
        } else {
            alert(data.error || 'An error occurred');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while saving the comment');
    });
});

function deleteComment(commentId) {
    if (!confirm('Are you sure you want to delete this comment? This action cannot be undone.')) {
        return;
    }
    
    fetch(`/admin/comments/${commentId}`, {
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
        alert('An error occurred while deleting the comment');
    });
}
</script>
@endsection