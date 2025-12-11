@extends('layouts.app')

@section('title', $blog->title . ' - Farm Insights')
@section('meta_description', $blog->excerpt ?? Str::limit(strip_tags($blog->content), 160))
@section('meta_keywords', implode(',', $blog->tags ?? []))

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8">
            <!-- Blog Post Header -->
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="badge bg-success">{{ ucfirst($blog->category) }}</span>
                    <small class="text-muted">{{ $blog->published_at->format('F j, Y') }}</small>
                </div>
                
                <h1 class="fw-bold mb-3">{{ $blog->title }}</h1>
                
                <div class="d-flex align-items-center mb-4">
                    <div class="me-3">
                        @if($blog->user)
                            <img src="{{ $blog->user->profile_image ?: 'https://placehold.co/40x40?text=AU' }}" 
                                 class="rounded-circle" 
                                 width="40" 
                                 height="40" 
                                 alt="{{ $blog->user->name }}">
                        @else
                            <div class="bg-success rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <span>{{ substr($blog->author ?? 'Anonymous Author', 0, 1) }}</span>
                            </div>
                        @endif
                    </div>
                    <div>
                        <h6 class="mb-0">{{ $blog->author ?: ($blog->user->name ?? 'Anonymous') }}</h6>
                        <small class="text-muted">Author</small>
                    </div>
                </div>
                
                @if($blog->featured_image)
                <img src="{{ $blog->featured_image }}" 
                     class="img-fluid rounded" 
                     style="width: 100%; height: 400px; object-fit: cover;" 
                     alt="{{ $blog->title }}">
                @endif
            </div>
            
            <!-- Blog Content -->
            <div class="blog-content">
                @if($blog->excerpt)
                    <div class="lead mb-4 text-muted">
                        {{ $blog->excerpt }}
                    </div>
                @endif
                
                <div class="content">
                    {!! nl2br(e($blog->content)) !!}
                </div>
            </div>
            
            <!-- Tag Section -->
            @if($blog->tags && count($blog->tags) > 0)
            <div class="mt-4">
                <h5>Tags:</h5>
                <div class="d-flex flex-wrap gap-2">
                    @foreach($blog->tags as $tag)
                    <span class="badge bg-light text-success border border-success">{{ $tag }}</span>
                    @endforeach
                </div>
            </div>
            @endif
            
            <!-- Reading Stats -->
            @if($blog->reading_stats)
            <div class="mt-4">
                <div class="d-flex gap-4">
                    @if($blog->reading_stats['reading_time'])
                        <div class="text-muted">
                            <i class="fas fa-clock me-1"></i>
                            {{ $blog->reading_stats['reading_time'] ?? 0 }} min read
                        </div>
                    @endif
                    @if($blog->reading_stats['word_count'])
                        <div class="text-muted">
                            <i class="fas fa-font me-1"></i>
                            {{ $blog->reading_stats['word_count'] ?? 0 }} words
                        </div>
                    @endif
                    <div class="text-muted">
                        <i class="fas fa-eye me-1"></i>
                        {{ $blog->view_count }} views
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Related Posts -->
            @if(isset($relatedPosts) && $relatedPosts->count() > 0)
            <div class="mt-5">
                <h4>Related Articles</h4>
                <div class="row g-3">
                    @foreach($relatedPosts as $post)
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <h6 class="card-title">
                                    <a href="{{ route('blog.show', $post->slug) }}" class="text-decoration-none text-dark">
                                        {{ $post->title }}
                                    </a>
                                </h6>
                                <p class="card-text text-muted small">
                                    {{ Str::limit($post->excerpt, 100) }}
                                </p>
                                <small class="text-muted">{{ $post->published_at->format('M d, Y') }}</small>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="sticky-top">
                <!-- Share Button -->
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-body">
                        <h5>Share Article</h5>
                        <div class="d-flex gap-2">
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" 
                               class="btn btn-outline-primary btn-sm">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($blog->title) }}" 
                               class="btn btn-outline-info btn-sm">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(url()->current()) }}&title={{ urlencode($blog->title) }}" 
                               class="btn btn-outline-primary btn-sm">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                            <button class="btn btn-outline-secondary btn-sm" onclick="copyToClipboard()">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Posts -->
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-body">
                        <h5>Recent Articles</h5>
                        <div class="list-group list-group-flush">
                            @php
                                $recentPosts = App\Models\Blog::where('id', '!=', $blog->id)
                                    ->published()
                                    ->latest()
                                    ->limit(5)
                                    ->get();
                            @endphp
                            
                            @forelse($recentPosts as $post)
                            <a href="{{ route('blog.show', $post->slug) }}" class="list-group-item list-group-item-action text-decoration-none">
                                <h6 class="mb-1">{{ $post->title }}</h6>
                                <small class="text-muted">{{ $post->published_at->format('M d') }}</small>
                            </a>
                            @empty
                            <div class="list-group-item text-muted">
                                No recent articles
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
                
                <!-- Categories -->
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-body">
                        <h5>Categories</h5>
                        <div class="d-flex flex-wrap gap-2">
                            @php
                                $categories = App\Models\Blog::select('category')
                                    ->where('status', 'published')
                                    ->distinct()
                                    ->pluck('category');
                            @endphp
                            
                            @foreach($categories as $category)
                            <a href="{{ route('blog.category', $category) }}" class="badge bg-light text-success border border-success text-decoration-none">
                                {{ ucfirst($category) }}
                            </a>
                            @endforeach
                        </div>
                    </div>
                </div>
                
                <!-- Newsletter Signup -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5>Subscribe to Updates</h5>
                        <p class="text-muted">Get notified when new farm insights are published</p>
                        <form>
                            <div class="mb-3">
                                <input type="email" class="form-control" placeholder="Your email address">
                            </div>
                            <button type="submit" class="btn btn-success w-100">Subscribe</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function copyToClipboard() {
        navigator.clipboard.writeText(window.location.href).then(function() {
            alert('Link copied to clipboard!');
        }, function(err) {
            console.error('Could not copy text: ', err);
        });
    }
</script>
@endsection