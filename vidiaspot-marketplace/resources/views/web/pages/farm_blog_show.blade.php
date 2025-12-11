@extends('layouts.app')

@section('title', $blog->title . ' - Farm Blog')
@section('meta_description', $blog->excerpt ?? Str::limit(strip_tags($blog->content), 160))
@section('meta_keywords', implode(',', $blog->tags ?: []))

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8">
            <!-- Blog Post Header -->
            <article class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="badge bg-success text-uppercase fw-normal">{{ ucfirst($blog->category) }}</span>
                    <small class="text-muted">{{ $blog->published_at->format('F j, Y') }}</small>
                </div>
                
                <h1 class="fw-bold mb-4">{{ $blog->title }}</h1>
                
                <div class="d-flex align-items-center mb-4">
                    <div class="me-3">
                        @if($blog->user)
                            <img src="{{ $blog->user->profile_image ?: 'https://placehold.co/40x40?text=AU' }}" 
                                 class="rounded-circle" 
                                 width="40" 
                                 height="40" 
                                 alt="{{ $blog->author ?: ($blog->user->name ?? 'Author') }}">
                        @else
                            <div class="bg-success rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <span class="text-white">{{ substr($blog->author ?: 'A', 0, 1) }}</span>
                            </div>
                        @endif
                    </div>
                    <div>
                        <h6 class="mb-0">{{ $blog->author ?: ($blog->user->name ?? 'Anonymous Author') }}</h6>
                        <small class="text-muted">Farm Expert & Sustainable Agriculture Advocate</small>
                    </div>
                </div>
                
                @if($blog->featured_image)
                <img src="{{ $blog->featured_image }}" 
                     class="img-fluid rounded mb-4" 
                     style="width: 100%; height: 400px; object-fit: cover;" 
                     alt="{{ $blog->title }}">
                @endif
                
                <!-- Blog Content -->
                <div class="blog-content fs-5 mb-4">
                    @if($blog->excerpt)
                        <p class="lead text-muted">{{ $blog->excerpt }}</p>
                    @endif
                    
                    <div class="content">
                        {!! nl2br(e($blog->content)) !!}
                    </div>
                </div>
                
                <!-- Reading Stats -->
                @if($blog->reading_stats)
                <div class="d-flex gap-4 text-muted mb-4 pb-2 border-bottom">
                    @if($blog->reading_stats['reading_time'])
                        <div>
                            <i class="fas fa-clock me-2"></i>{{ $blog->reading_stats['reading_time'] ?? 0 }} min read
                        </div>
                    @endif
                    @if($blog->reading_stats['word_count'])
                        <div>
                            <i class="fas fa-font me-2"></i>{{ $blog->reading_stats['word_count'] ?? 0 }} words
                        </div>
                    @endif
                    <div>
                        <i class="fas fa-eye me-2"></i>{{ $blog->view_count }} views
                    </div>
                    <div>
                        <i class="fas fa-heart me-2"></i>{{ $blog->like_count }} likes
                    </div>
                </div>
                @endif
                
                <!-- Tags -->
                @if($blog->tags && count($blog->tags) > 0)
                <div class="mb-4">
                    <h6>Tags:</h6>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($blog->tags as $tag)
                        <span class="badge bg-light text-success border border-success">{{ $tag }}</span>
                        @endforeach
                    </div>
                </div>
                @endif
                
                <!-- Social Sharing -->
                <div class="d-flex justify-content-between align-items-center border-top pt-4 mt-4">
                    <div>
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" 
                           class="btn btn-outline-primary me-2" 
                           target="_blank">
                            <i class="fab fa-facebook-f"></i> Share
                        </a>
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($blog->title) }}" 
                           class="btn btn-outline-info me-2" 
                           target="_blank">
                            <i class="fab fa-twitter"></i> Tweet
                        </a>
                        <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(request()->url()) }}&title={{ urlencode($blog->title) }}" 
                           class="btn btn-outline-primary" 
                           target="_blank">
                            <i class="fab fa-linkedin-in"></i> Share
                        </a>
                    </div>
                    <button class="btn btn-outline-success">
                        <i class="fas fa-bookmark me-2"></i> Save Article
                    </button>
                </div>
            </article>
            
            <!-- Related Posts -->
            @if($relatedPosts && $relatedPosts->count() > 0)
            <section class="mt-5">
                <h3 class="fw-bold mb-4">Related Articles</h3>
                <div class="row g-3">
                    @foreach($relatedPosts as $post)
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm">
                            @if($post->featured_image)
                                <img src="{{ $post->featured_image }}" 
                                     class="card-img-top" 
                                     style="height: 150px; object-fit: cover;" 
                                     alt="{{ $post->title }}">
                            @else
                                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 150px;">
                                    <i class="fas fa-leaf fa-2x text-success opacity-25"></i>
                                </div>
                            @endif
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge bg-success text-uppercase fw-normal">{{ ucfirst($post->category) }}</span>
                                    <small class="text-muted">{{ $post->published_at->diffForHumans() }}</small>
                                </div>
                                <h6 class="card-title">{{ $post->title }}</h6>
                                <p class="card-text text-muted small">{{ Str::limit(strip_tags($post->excerpt), 80) }}</p>
                            </div>
                            <div class="card-footer bg-transparent p-3">
                                <a href="{{ route('blog.show', $post->slug) }}" class="btn btn-sm btn-outline-success">Read More</a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </section>
            @endif
            
            <!-- Comments Section -->
            <section class="mt-5">
                <h3 class="fw-bold mb-4">Comments</h3>
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <p class="text-muted">Comments are disabled for this article. Join our community forums to discuss farming topics.</p>
                        <a href="/community" class="btn btn-outline-success">Join Community</a>
                    </div>
                </div>
            </section>
        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- About the Author -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="mb-3">About the Author</h5>
                    <div class="d-flex align-items-center mb-3">
                        <div class="me-3">
                            @if($blog->user)
                                <img src="{{ $blog->user->profile_image ?: 'https://placehold.co/60x60?text=AU' }}" 
                                     class="rounded-circle" 
                                     width="60" 
                                     height="60" 
                                     alt="{{ $blog->author ?: ($blog->user->name ?? 'Author') }}">
                            @else
                                <div class="bg-success rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                    <span class="text-white">{{ substr($blog->author ?: 'A', 0, 1) }}</span>
                                </div>
                            @endif
                        </div>
                        <div>
                            <h6 class="mb-0">{{ $blog->author ?: ($blog->user->name ?? 'Anonymous') }}</h6>
                            <small class="text-muted">Farm Expert</small>
                        </div>
                    </div>
                    <p class="text-muted">
                        {{ $blog->author_bio ?: $blog->user?->bio ?: 'Agriculture expert with deep knowledge of farming practices and sustainable agriculture.' }}
                    </p>
                    <button class="btn btn-outline-success btn-sm">Follow Author</button>
                </div>
            </div>
            
            <!-- Newsletter Widget -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="mb-3">Stay Updated</h5>
                    <p>Get the latest farming insights and sustainable agriculture tips</p>
                    <form>
                        <div class="mb-3">
                            <input type="email" class="form-control" placeholder="Your email address">
                        </div>
                        <button type="submit" class="btn btn-success w-100">Subscribe</button>
                    </form>
                </div>
            </div>
            
            <!-- Popular Articles -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="mb-3">Popular Articles</h5>
                    @php
                        $popularPosts = App\Models\Blog::published()
                            ->orderBy('view_count', 'desc')
                            ->limit(5)
                            ->get();
                    @endphp
                    <div class="list-group list-group-flush">
                        @forelse($popularPosts as $post)
                        <a href="{{ route('blog.show', $post->slug) }}" class="list-group-item list-group-item-action">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="mb-0">{{ Str::limit($post->title, 40) }}</h6>
                                    <small class="text-muted">{{ $post->view_count }} views</small>
                                </div>
                                <small class="text-muted">{{ $post->published_at->diffForHumans() }}</small>
                            </div>
                        </a>
                        @empty
                        <div class="list-group-item">
                            <p class="text-muted mb-0">No popular articles available</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
            
            <!-- Related by Category -->
            @if($blog->category)
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="mb-3">More from {{ ucfirst($blog->category) }}</h5>
                    @php
                        $relatedByCategory = App\Models\Blog::published()
                            ->where('category', $blog->category)
                            ->where('id', '!=', $blog->id)
                            ->limit(3)
                            ->get();
                    @endphp
                    <div class="list-group list-group-flush">
                        @forelse($relatedByCategory as $post)
                        <a href="{{ route('blog.show', $post->slug) }}" class="list-group-item list-group-item-action">
                            {{ Str::limit($post->title, 45) }}
                        </a>
                        @empty
                        <div class="list-group-item">
                            <p class="text-muted mb-0">No other articles in this category</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
    .blog-content {
        line-height: 1.8;
    }
    
    .blog-content p {
        margin-bottom: 1.2rem;
    }
    
    .card {
        border: none;
    }
    
    .shadow-sm {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
    }
</style>

@endsection