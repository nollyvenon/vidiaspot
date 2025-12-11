@extends('layouts.app')

@section('title', 'Farm Blog - Tips, News and Insights for Farmers and Buyers')
@section('meta_description', 'Read our farm blog for tips, news, and insights about farming practices, sustainability, and direct farm sales.')
@section('meta_keywords', 'farm blog, farming tips, agriculture news, organic farming, sustainability, farming insights, farm products')

@section('content')
<div class="container-fluid">
    <!-- Blog Hero Section -->
    <section class="hero-section bg-success text-white py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 text-center">
                    <h1 class="display-4 fw-bold mb-3">Farm Insights & News</h1>
                    <p class="lead">Discover tips, trends, and stories from the farm community</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Blog Posts -->
    @if(isset($featuredPosts) && $featuredPosts->count() > 0)
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="mb-4">Featured Articles</h2>
            <div class="row g-4">
                @foreach($featuredPosts as $post)
                <div class="col-lg-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <img src="{{ $post->featured_image ?: 'https://placehold.co/400x250?text=Farm+Blog' }}" class="card-img-top" style="height: 200px; object-fit: cover;" alt="{{ $post->title }}">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-success">{{ ucfirst($post->category) }}</span>
                                <small class="text-muted">{{ $post->published_at->format('M d, Y') }}</small>
                            </div>
                            <h5 class="card-title">{{ $post->title }}</h5>
                            <p class="card-text text-muted">{{ Str::limit($post->excerpt, 100) }}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="{{ route('blog.show', $post->slug) }}" class="text-success text-decoration-none">Read More</a>
                                <small class="text-muted">
                                    <i class="fas fa-eye me-1"></i>{{ $post->view_count }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Main Blog Content -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <!-- Blog Posts -->
                <div class="col-lg-8">
                    <div class="row mb-4">
                        <div class="col-12">
                            <h2>Latest Articles</h2>
                        </div>
                    </div>

                    <div class="row g-4">
                        @forelse($blogs as $post)
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100">
                                <img src="{{ $post->featured_image ?: 'https://placehold.co/400x200?text=Farm+Blog' }}" class="card-img-top" style="height: 180px; object-fit: cover;" alt="{{ $post->title }}">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="badge bg-success">{{ ucfirst($post->category) }}</span>
                                        <small class="text-muted">{{ $post->published_at->format('M d, Y') }}</small>
                                    </div>
                                    <h5 class="card-title">{{ $post->title }}</h5>
                                    <p class="card-text text-muted">{{ Str::limit($post->excerpt, 120) }}</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <a href="{{ route('blog.show', $post->slug) }}" class="btn btn-success btn-sm">Read Article</a>
                                        <div class="d-flex align-items-center text-muted">
                                            <i class="fas fa-eye me-1"></i>{{ $post->view_count }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12 text-center">
                            <i class="fas fa-blog fa-3x text-muted mb-3"></i>
                            <h4>No blog posts found</h4>
                            <p class="text-muted">Check back later for new articles</p>
                        </div>
                        @endforelse
                    </div>

                    <!-- Pagination -->
                    @if(isset($blogs))
                    <div class="d-flex justify-content-center mt-5">
                        {{ $blogs->links() }}
                    </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Search -->
                    <div class="card mb-4 border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Search Articles</h5>
                            <form action="{{ route('blog.index') }}" method="GET">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" placeholder="Search articles..." value="{{ request('search') }}">
                                    <button class="btn btn-success" type="submit"><i class="fas fa-search"></i></button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Categories -->
                    <div class="card mb-4 border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Categories</h5>
                            <div class="row g-2">
                                @foreach($categories as $category)
                                <div class="col-6">
                                    <a href="{{ route('blog.category', $category) }}" class="btn btn-outline-success btn-sm w-100">
                                        {{ ucfirst($category) }}
                                    </a>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Popular Tags -->
                    @php
                        // Extract all tags and their counts across all posts
                        $allTags = collect();
                        $postsForTags = App\Models\Blog::published()->get();
                        foreach($postsForTags as $p) {
                            if($p->tags) {
                                foreach($p->tags as $tag) {
                                    $allTags->push($tag);
                                }
                            }
                        }
                        $tagCounts = $allTags->countBy();
                    @endphp
                    
                    @if($tagCounts->count() > 0)
                    <div class="card mb-4 border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Popular Tags</h5>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($tagCounts->take(10) as $tag => $count)
                                <span class="badge bg-light text-success border border-success">{{ $tag }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Newsletter Signup -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Subscribe to Updates</h5>
                            <p>Get notified when new farm insights are published</p>
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
    </section>

    <!-- Mobile App Section -->
    <section class="py-5 bg-success text-white">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-7 mb-4 mb-md-0">
                    <h2 class="fw-bold mb-3">Download Our Mobile Apps</h2>
                    <p class="mb-4">Get the full experience on your mobile device with dedicated apps for buyers and sellers.</p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="#" class="btn btn-light text-success">
                            <i class="fab fa-google-play me-2"></i> Google Play
                        </a>
                        <a href="#" class="btn btn-light text-success">
                            <i class="fab fa-app-store-ios me-2"></i> App Store
                        </a>
                    </div>
                </div>
                <div class="col-md-5 text-center">
                    <div class="d-flex justify-content-center gap-4">
                        <div class="text-center">
                            <div class="bg-white bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 120px; height: 120px;">
                                <i class="fas fa-shopping-basket text-white fa-3x"></i>
                            </div>
                            <h5 class="mt-2 text-white">Buyer App</h5>
                            <p class="text-white opacity-75">Shop farm products</p>
                        </div>
                        <div class="text-center">
                            <div class="bg-white bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 120px; height: 120px;">
                                <i class="fas fa-tractor text-white fa-3x"></i>
                            </div>
                            <h5 class="mt-2 text-white">Seller App</h5>
                            <p class="text-white opacity-75">Sell farm products</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection