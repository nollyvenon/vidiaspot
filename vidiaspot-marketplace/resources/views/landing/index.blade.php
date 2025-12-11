@extends('layouts.app')

@section('title', 'Vidiaspot Marketplace - Buy and Sell Near You')
@section('meta_description', 'Find great deals and sell items near you. The easiest way to buy and sell used goods, cars, jobs and services.')
@section('meta_keywords', 'marketplace, buy, sell, classified ads, used items, cars, jobs')

@section('content')
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container text-center">
            <h1 class="display-4 fw-bold mb-3">Buy and Sell Near You</h1>
            <p class="lead mb-4">Find great deals or sell items to people in your community</p>
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <form action="/search" method="GET" class="d-flex">
                        <input type="text" name="q" class="form-control form-control-lg" placeholder="What are you looking for?" aria-label="Search">
                        <button class="btn btn-warning btn-lg" type="submit"><i class="fas fa-search"></i></button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Search Section -->
    <section class="search-section">
        <div class="container">
            <div class="row g-3">
                <div class="col-md-3 col-sm-6">
                    <select class="form-select" id="locationSelect">
                        <option selected>All Locations</option>
                        <option value="lagos">Lagos</option>
                        <option value="abuja">Abuja</option>
                        <option value="kano">Kano</option>
                        <option value="ibadan">Ibadan</option>
                    </select>
                </div>
                <div class="col-md-3 col-sm-6">
                    <select class="form-select" id="categorySelect">
                        <option selected>All Categories</option>
                        <option value="vehicles">Vehicles</option>
                        <option value="electronics">Electronics</option>
                        <option value="property">Property</option>
                        <option value="furniture">Furniture</option>
                    </select>
                </div>
                <div class="col-md-3 col-sm-6">
                    <input type="number" class="form-control" placeholder="Min Price">
                </div>
                <div class="col-md-3 col-sm-6">
                    <input type="number" class="form-control" placeholder="Max Price">
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <main class="container my-5">
        <!-- Personalized Experience Section for Logged-in Users -->
        @auth
        <section class="mb-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>
                    @if($moodState && $moodState !== 'normal')
                        {{ ucfirst($moodState) }} Mode Recommendations
                    @else
                        Personalized For You
                    @endif
                </h2>
                <a href="/user/feed" class="text-primary">View All <i class="fas fa-arrow-right"></i></a>
            </div>

            @if($personalizedAds && $personalizedAds->count() > 0)
            <div class="row g-4">
                @foreach($personalizedAds as $ad)
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="card ad-card position-relative">
                        <img src="{{ $ad->images->first()->url ?? 'https://via.placeholder.com/300x200' }}" class="card-img-top" alt="{{ $ad->title }}" onerror="this.src='https://via.placeholder.com/300x200';">
                        <div class="card-body">
                            <h6 class="card-title">{{ $ad->title }}</h6>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-bold text-success">₦ {{ number_format($ad->price) }}</span>
                                <small class="text-muted"><i class="fas fa-map-marker-alt"></i> {{ $ad->location }}</small>
                            </div>
                            <div class="d-flex justify-content-between text-muted">
                                <small><i class="far fa-clock"></i> {{ $ad->created_at->diffForHumans() }}</small>
                                <small><i class="fas fa-heart"></i> {{ $ad->like_count ?? 0 }}</small>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="alert alert-info">
                <p class="mb-0">No personalized recommendations yet. Start browsing categories to get recommendations tailored to your interests.</p>
            </div>
            @endif
        </section>
        @endauth

        <!-- Popular Categories -->
        <section class="mb-5">
            <h2 class="mb-4">Popular Categories</h2>
            <div class="row g-4">
                <div class="col-md-2 col-sm-4 col-6">
                    <a href="/category/vehicles" class="text-decoration-none">
                        <div class="card category-card text-center p-3">
                            <div class="category-icon text-primary">
                                <i class="fas fa-car"></i>
                            </div>
                            <div class="card-body p-2">
                                <h6 class="card-title mb-0">Vehicles</h6>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-2 col-sm-4 col-6">
                    <a href="/category/electronics" class="text-decoration-none">
                        <div class="card category-card text-center p-3">
                            <div class="category-icon text-primary">
                                <i class="fas fa-laptop"></i>
                            </div>
                            <div class="card-body p-2">
                                <h6 class="card-title mb-0">Electronics</h6>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-2 col-sm-4 col-6">
                    <a href="/category/furniture" class="text-decoration-none">
                        <div class="card category-card text-center p-3">
                            <div class="category-icon text-primary">
                                <i class="fas fa-couch"></i>
                            </div>
                            <div class="card-body p-2">
                                <h6 class="card-title mb-0">Furniture</h6>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-2 col-sm-4 col-6">
                    <a href="/category/property" class="text-decoration-none">
                        <div class="card category-card text-center p-3">
                            <div class="category-icon text-primary">
                                <i class="fas fa-home"></i>
                            </div>
                            <div class="card-body p-2">
                                <h6 class="card-title mb-0">Property</h6>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-2 col-sm-4 col-6">
                    <a href="/category/jobs" class="text-decoration-none">
                        <div class="card category-card text-center p-3">
                            <div class="category-icon text-primary">
                                <i class="fas fa-briefcase"></i>
                            </div>
                            <div class="card-body p-2">
                                <h6 class="card-title mb-0">Jobs</h6>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-2 col-sm-4 col-6">
                    <a href="/category/services" class="text-decoration-none">
                        <div class="card category-card text-center p-3">
                            <div class="category-icon text-primary">
                                <i class="fas fa-hands-helping"></i>
                            </div>
                            <div class="card-body p-2">
                                <h6 class="card-title mb-0">Services</h6>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </section>

        <!-- Featured Ads -->
        <section class="mb-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Featured Ads</h2>
                <a href="/ads?featured=1" class="text-primary">View All <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="row g-4">
                @for($i = 0; $i < 4; $i++)
                <div class="col-lg-3 col-md-6">
                    <div class="card ad-card position-relative">
                        <span class="badge bg-warning text-dark featured-badge">FEATURED</span>
                        <img src="https://via.placeholder.com/300x200" class="card-img-top" alt="Ad Image">
                        <div class="card-body">
                            <h6 class="card-title">Premium Laptop - Core i7, 16GB RAM</h6>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-bold text-success">₦ 250,000</span>
                                <small class="text-muted"><i class="fas fa-map-marker-alt"></i> Lagos</small>
                            </div>
                            <div class="d-flex justify-content-between text-muted">
                                <small><i class="far fa-clock"></i> 2 days ago</small>
                                <small><i class="fas fa-heart"></i> 24</small>
                            </div>
                        </div>
                    </div>
                </div>
                @endfor
            </div>
        </section>

        <!-- Latest Ads -->
        <section class="mb-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Latest Ads</h2>
                <a href="/ads" class="text-primary">View All <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="row g-4">
                @for($i = 0; $i < 6; $i++)
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <div class="card ad-card h-100">
                        <img src="https://via.placeholder.com/300x200" class="card-img-top" alt="Ad Image" style="height: 150px; object-fit: cover;">
                        <div class="card-body p-2">
                            <h6 class="card-title mb-1" style="font-size: 0.9rem; height: 3rem; overflow: hidden;">iPhone 13 Pro Max 256GB</h6>
                            <div class="d-flex justify-content-between">
                                <span class="fw-bold text-success" style="font-size: 0.9rem;">₦ 420,000</span>
                                <span class="trending-tag">NEW</span>
                            </div>
                        </div>
                    </div>
                </div>
                @endfor
            </div>
        </section>

        <!-- How It Works -->
        <section class="mb-5">
            <h2 class="mb-4">How It Works</h2>
            <div class="row g-4">
                @forelse($howItWorksSteps as $step)
                <div class="col-md-4">
                    <div class="card h-100 text-center border-0">
                        <div class="card-body">
                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 60px; height: 60px;">
                                <i class="{{ $step->icon_class }} fs-4"></i>
                            </div>
                            <h5 class="card-title">{{ $step->title }}</h5>
                            <p class="card-text">{{ $step->description }}</p>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center">
                    <p class="text-muted">No steps defined for "How It Works" section.</p>
                </div>
                @endforelse
            </div>
            <div class="text-center mt-4">
                <a href="/how-it-works" class="btn btn-outline-success">Learn More</a>
            </div>
        </section>

        <!-- Trending Searches -->
        <section class="mb-5">
            <h2 class="mb-4">Trending Searches</h2>
            <div class="row">
                <div class="col-md-8">
                    <div class="search-form">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <h6>Mobile Phones</h6>
                                <ul class="list-unstyled mb-0">
                                    @foreach($trendingByCategory['mobile_phones'] as $search)
                                    <li><a href="/search?q={{ urlencode($search->query) }}" class="text-decoration-none">{{ $search->query }}
                                        <small class="text-muted">({{ $search->count }} searches)</small></a></li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <h6>Laptops & Computers</h6>
                                <ul class="list-unstyled mb-0">
                                    @foreach($trendingByCategory['laptops'] as $search)
                                    <li><a href="/search?q={{ urlencode($search->query) }}" class="text-decoration-none">{{ $search->query }}
                                        <small class="text-muted">({{ $search->count }} searches)</small></a></li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <h6>Vehicles</h6>
                                <ul class="list-unstyled mb-0">
                                    @foreach($trendingByCategory['vehicles'] as $search)
                                    <li><a href="/search?q={{ urlencode($search->query) }}" class="text-decoration-none">{{ $search->query }}
                                        <small class="text-muted">({{ $search->count }} searches)</small></a></li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">Post Your Ad</h5>
                        </div>
                        <div class="card-body">
                            <p class="card-text">Sell your items fast and easily. Reach thousands of buyers in your area.</p>
                            <a href="/create-ad" class="btn btn-success w-100">Post Free Ad</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Farm Products Section -->
        <section class="py-5 bg-success text-white">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h2 class="display-5 fw-bold mb-3">Fresh Products from Local Farms</h2>
                        <p class="lead mb-4">Connect directly with local farmers and buy fresh, organic products straight from the source.</p>
                        <div class="d-flex flex-wrap gap-3">
                            <a href="{{ route('farm.buyer.landing') }}" class="btn btn-light btn-lg">
                                <i class="fas fa-shopping-basket me-2"></i> Shop Farm Products
                            </a>
                            <a href="{{ route('farm.seller.landing') }}" class="btn btn-outline-light btn-lg">
                                <i class="fas fa-tractor me-2"></i> Sell Farm Products
                            </a>
                        </div>
                    </div>
                    <div class="col-md-6 text-center">
                        <img src="https://images.unsplash.com/photo-1464226184884-fa280b7dd3bb?auto=format&fit=crop&w=500&h=400&q=80" alt="Farm Products" class="img-fluid rounded shadow-lg" style="border-radius: 10px !important;">
                    </div>
                </div>
            </div>
        </section>

        <!-- Farm Categories Section -->
        <section class="py-5">
            <div class="container">
                <div class="row">
                    <div class="col-12 text-center mb-5">
                        <h2 class="fw-bold">Farm Products</h2>
                        <p class="text-muted">Fresh products directly from local farms</p>
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-md-3">
                        <a href="{{ route('farm.products.index') . '?category=fresh-vegetables' }}" class="text-decoration-none">
                            <div class="card h-100 border-0 shadow-sm text-center">
                                <div class="card-body p-4">
                                    <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center p-3 mb-3" style="width: 80px; height: 80px;">
                                        <i class="fas fa-carrot fa-2x text-success"></i>
                                    </div>
                                    <h5 class="card-title">Fresh Vegetables</h5>
                                    <p class="card-text text-muted">Harvested today, delivered to you</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('farm.products.index') . '?category=fresh-fruits' }}" class="text-decoration-none">
                            <div class="card h-100 border-0 shadow-sm text-center">
                                <div class="card-body p-4">
                                    <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center p-3 mb-3" style="width: 80px; height: 80px;">
                                        <i class="fas fa-apple-alt fa-2x text-success"></i>
                                    </div>
                                    <h5 class="card-title">Fresh Fruits</h5>
                                    <p class="card-text text-muted">Juicy fruits from local orchards</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('farm.products.index') . '?category=organic-products' }}" class="text-decoration-none">
                            <div class="card h-100 border-0 shadow-sm text-center">
                                <div class="card-body p-4">
                                    <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center p-3 mb-3" style="width: 80px; height: 80px;">
                                        <i class="fas fa-leaf fa-2x text-success"></i>
                                    </div>
                                    <h5 class="card-title">Organic Products</h5>
                                    <p class="card-text text-muted">Certified organic, chemical-free</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('farm.products.index') . '?category=dairy-products' }}" class="text-decoration-none">
                            <div class="card h-100 border-0 shadow-sm text-center">
                                <div class="card-body p-4">
                                    <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center p-3 mb-3" style="width: 80px; height: 80px;">
                                        <i class="fas fa-glass-whiskey fa-2x text-success"></i>
                                    </div>
                                    <h5 class="card-title">Dairy Products</h5>
                                    <p class="card-text text-muted">Fresh milk, cheese & yogurt</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- How Farm Marketplace Works -->
        <section class="py-5 bg-light">
            <div class="container">
                <div class="row">
                    <div class="col-12 text-center mb-5">
                        <h2 class="fw-bold">How Farm Marketplace Works</h2>
                        <p class="text-muted">Connecting farmers and buyers directly</p>
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-md-4 text-center">
                        <div class="mb-3">
                            <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mx-auto" style="width: 80px; height: 80px;">
                                <i class="fas fa-farm fa-2x"></i>
                            </div>
                        </div>
                        <h5>Farmers List Products</h5>
                        <p class="text-muted">Farmers list their products with details about harvest date, farming practices, and location</p>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="mb-3">
                            <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mx-auto" style="width: 80px; height: 80px;">
                                <i class="fas fa-search fa-2x"></i>
                            </div>
                        </div>
                        <h5>Buyers Connect</h5>
                        <p class="text-muted">Buyers find fresh products directly from local farms in their area</p>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="mb-3">
                            <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mx-auto" style="width: 80px; height: 80px;">
                                <i class="fas fa-truck fa-2x"></i>
                            </div>
                        </div>
                        <h5>Direct Delivery</h5>
                        <p class="text-muted">Products delivered directly from farm to consumer</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- App Download Section -->
        <section class="py-5">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6 mb-4 mb-md-0">
                        <h3 class="fw-bold mb-3">Download Our Apps</h3>
                        <p class="lead mb-4">Experience the best farm-to-table marketplace with our dedicated mobile apps</p>
                        <div class="d-flex flex-wrap gap-3">
                            <a href="#" class="btn btn-success btn-lg">
                                <i class="fab fa-google-play me-2"></i> Google Play
                            </a>
                            <a href="#" class="btn btn-success btn-lg">
                                <i class="fab fa-app-store-ios me-2"></i> App Store
                            </a>
                        </div>
                    </div>
                    <div class="col-md-6 text-center">
                        <img src="https://placehold.co/300x600/28a745/ffffff?text=VidiaSpot+Farm+App" alt="VidiaSpot Farm App" class="img-fluid" style="max-width: 200px;">
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection

@section('scripts')
<script>
    // Track user behavior for personalization
    document.addEventListener('DOMContentLoaded', function() {
        // Track ad clicks
        const adCards = document.querySelectorAll('.ad-card');
        adCards.forEach(card => {
            card.addEventListener('click', function() {
                if (card.querySelector('img')) {
                    const adId = card.dataset.adId;
                    if (adId) {
                        // Send behavior tracking to server
                        fetch('/user/behavior', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({
                                behavior_type: 'click',
                                target_type: 'ad',
                                target_id: adId,
                                metadata: {
                                    timestamp: new Date().toISOString(),
                                    page: 'landing'
                                }
                            })
                        })
                        .catch(error => console.error('Error tracking behavior:', error));
                    }
                }
            });
        });

        // Track search behavior
        const searchForm = document.querySelector('form[action="/search"]');
        if (searchForm) {
            searchForm.addEventListener('submit', function() {
                const searchTerm = searchForm.querySelector('input[name="q"]').value;
                if (searchTerm) {
                    fetch('/user/behavior', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            behavior_type: 'search',
                            target_type: 'search',
                            target_id: 0,
                            metadata: {
                                query: searchTerm,
                                timestamp: new Date().toISOString()
                            }
                        })
                    })
                    .catch(error => console.error('Error tracking search:', error));
                }
            });
        }
    });
</script>
@endsection