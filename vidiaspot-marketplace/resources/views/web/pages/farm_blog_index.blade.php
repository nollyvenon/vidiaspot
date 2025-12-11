@extends('layouts.app')

@section('title', 'Farm Blog - Tips, News and Insights')
@section('meta_description', 'Read our farm blog for tips, news, and insights about farming practices, sustainability, and direct farm sales.')
@section('meta_keywords', 'farm blog, farming tips, agriculture news, organic farming, sustainability, farming insights')

@section('content')
<div class="container-fluid">
    <!-- Blog Hero Section -->
    <section class="hero-section bg-success text-white py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 text-center">
                    <h1 class="display-4 fw-bold mb-3">Farm Insights & Updates</h1>
                    <p class="lead mb-4">Discover expert tips, industry news, and stories from the farm community</p>
                </div>
            </div>
        </div>
    </section>

    <div class="container py-5">
        <div class="row">
            <!-- Blog List -->
            <div class="col-lg-8">
                <!-- Featured Posts -->
                @if($featuredPosts && $featuredPosts->count() > 0)
                <div class="mb-5">
                    <h2 class="fw-bold mb-4">Featured Articles</h2>
                    <div class="row g-4">
                        @foreach($featuredPosts as $post)
                        <div class="col-md-6">
                            <div class="card h-100 featured-post-card border-0 shadow-sm">
                                @if($post->featured_image)
                                    <img src="{{ $post->featured_image }}" class="card-img-top" style="height: 200px; object-fit: cover;" alt="{{ $post->title }}">
                                @else
                                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                        <i class="fas fa-leaf fa-5x text-success opacity-25"></i>
                                    </div>
                                @endif
                                
                                <div class="card-body p-4">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="badge bg-success">{{ ucfirst($post->category) }}</span>
                                        <small class="text-muted">{{ $post->published_at->format('M d, Y') }}</small>
                                    </div>
                                    <h5 class="card-title">{{ $post->title }}</h5>
                                    <p class="card-text text-muted">{{ Str::limit($post->excerpt, 100) }}</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <i class="fas fa-user me-1"></i> {{ $post->author ?: ($post->user->name ?? 'Anonymous') }}
                                        </small>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-eye me-1 text-muted"></i>
                                            <span class="text-muted">{{ $post->view_count }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer bg-transparent border-0">
                                    <a href="{{ route('blog.show', $post->slug) }}" class="btn btn-outline-success">Read More</a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <div class="mb-4">
                    <h2 class="fw-bold mb-4">Latest Articles</h2>
                </div>

                <div class="row g-4">
                    @forelse($blogs as $post)
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm">
                            @if($post->featured_image)
                                <img src="{{ $post->featured_image }}" class="card-img-top" style="height: 180px; object-fit: cover;" alt="{{ $post->title }}">
                            @else
                                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 180px;">
                                    <i class="fas fa-leaf fa-3x text-success opacity-25"></i>
                                </div>
                            @endif
                            
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge bg-success">{{ ucfirst($post->category) }}</span>
                                    <small class="text-muted">{{ $post->published_at->format('M d, Y') }}</small>
                                </div>
                                <h5 class="card-title">{{ $post->title }}</h5>
                                <p class="card-text text-muted">{{ Str::limit($post->excerpt, 120) }}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <i class="fas fa-user me-1"></i> {{ $post->author ?: ($post->user->name ?? 'Anonymous') }}
                                    </small>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-eye me-1 text-muted"></i>
                                        <span class="text-muted">{{ $post->view_count }}</span>
                                        @if($post->quality_rating)
                                        <i class="fas fa-star text-warning ms-3 me-1"></i>
                                        <span class="text-muted">{{ number_format($post->quality_rating, 1) }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent border-0">
                                <a href="{{ route('blog.show', $post->slug) }}" class="btn btn-success">Read Article</a>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12 text-center py-5">
                        <i class="fas fa-blog fa-3x text-muted mb-3"></i>
                        <h4>No articles found</h4>
                        <p class="text-muted">Please check back later for new content.</p>
                    </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                @if($blogs->hasPages())
                <div class="d-flex justify-content-center mt-5">
                    {{ $blogs->links() }}
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Search Widget -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h5>Search Articles</h5>
                        <form action="{{ route('blog.index') }}" method="GET">
                            <div class="input-group">
                                <input type="text" class="form-control" name="search" placeholder="Search blog posts..." value="{{ request('search') }}">
                                <button class="btn btn-success" type="submit"><i class="fas fa-search"></i></button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Categories Widget -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h5>Categories</h5>
                        <div class="list-group">
                            @foreach($categories as $cat)
                            <a href="{{ route('blog.category', $cat) }}" 
                               class="list-group-item list-group-item-action {{ request('category') == $cat ? 'active' : '' }}">
                                {{ ucfirst($cat) }}
                            </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Popular Articles -->
                @php
                    $popularPosts = App\Models\Blog::published()
                        ->orderBy('view_count', 'desc')
                        ->limit(5)
                        ->get();
                @endphp
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h5>Popular Articles</h5>
                        <div class="list-group">
                            @forelse($popularPosts as $post)
                            <a href="{{ route('blog.show', $post->slug) }}" 
                               class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">{{ Str::limit($post->title, 40) }}</h6>
                                    <small class="text-muted">{{ $post->view_count }} views</small>
                                </div>
                                <span class="badge bg-success rounded-pill">{{ $post->published_at->diffInDays() }}d</span>
                            </a>
                            @empty
                            <div class="list-group-item text-muted">
                                No popular articles available
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Newsletter Widget -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h5>Subscribe to Updates</h5>
                        <p>Get notified when new articles are published</p>
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

<style>
    .featured-post-card {
        border-left: 4px solid #28a745 !important;
    }
    
    .card {
        border-radius: 8px;
    }
    
    .shadow-sm {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
    }
    
    .object-fit-cover {
        object-fit: cover;
    }
</style>
@endsection