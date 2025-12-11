@extends('layouts.app')

@section('title', 'Trending Farm Articles - VidiaSpot')
@section('meta_description', 'Discover our most popular and trending articles about farming, sustainability, and agriculture.')
@section('meta_keywords', 'trending farm articles, popular farm content, trending farming tips, agriculture trends')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8">
            <div class="mb-4">
                <h2 class="fw-bold mb-3">Trending Farm Content</h2>
                <p class="text-muted">Most viewed and commented farm articles in the last 7 days</p>
            </div>
            
            @if($trendingPosts && $trendingPosts->count() > 0)
            <div class="row g-4">
                @foreach($trendingPosts as $post)
                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        @if($post->featured_image)
                            <img src="{{ $post->featured_image }}" 
                                 class="card-img-top" 
                                 style="height: 200px; object-fit: cover;" 
                                 alt="{{ $post->title }}">
                        @else
                            <div class="card-img-top bg-success bg-opacity-10 d-flex align-items-center justify-content-center" style="height: 200px;">
                                <i class="fas fa-leaf fa-3x text-success opacity-50"></i>
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
                                    @if($post->like_count > 0)
                                    <i class="fas fa-heart ms-3 me-1 text-danger"></i>
                                    <span class="text-muted">{{ $post->like_count }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent">
                            <a href="{{ route('blog.show', $post->slug) }}" class="btn btn-success">Read Full Article</a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-5">
                <i class="fas fa-chart-line fa-5x text-muted mb-4"></i>
                <h4>No trending articles available</h4>
                <p class="text-muted">Check back later for popular farm content</p>
                <a href="{{ route('blog.index') }}" class="btn btn-outline-success">View All Articles</a>
            </div>
            @endif
        </div>
        
        <div class="col-lg-4">
            <!-- Categories Widget -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5>Categories</h5>
                    <div class="list-group">
                        @foreach($categories as $cat)
                        <a href="{{ route('blog.category', $cat) }}" 
                           class="list-group-item list-group-item-action">
                            {{ ucfirst($cat) }}
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
            
            <!-- Most Commented Articles -->
            @php
                $mostCommentedPosts = App\Models\Blog::published()
                    ->orderBy('comment_count', 'desc')
                    ->limit(5)
                    ->get();
            @endphp
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5>Most Commented</h5>
                    <div class="list-group">
                        @forelse($mostCommentedPosts as $post)
                        <a href="{{ route('blog.show', $post->slug) }}" 
                           class="list-group-item list-group-item-action">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="mb-0">{{ Str::limit($post->title, 40) }}</h6>
                                    <small class="text-muted"><i class="fas fa-comments me-1"></i> {{ $post->comment_count }} comments</small>
                                </div>
                                <span class="badge bg-info">{{ $post->published_at->diffForHumans() }}</span>
                            </div>
                        </a>
                        @empty
                        <div class="list-group-item">
                            <p class="text-muted mb-0">No commented articles available</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
            
            <!-- Newsletter Widget -->
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="mb-3">Top Trending Topics</h5>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge bg-light text-dark">Organic Farming</span>
                        <span class="badge bg-light text-dark">Sustainable Agriculture</span>
                        <span class="badge bg-light text-dark">Local Produce</span>
                        <span class="badge bg-light text-dark">Farm-to-Table</span>
                        <span class="badge bg-light text-dark">Seasonal Crops</span>
                        <span class="badge bg-light text-dark">Climate-Smart Farming</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
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