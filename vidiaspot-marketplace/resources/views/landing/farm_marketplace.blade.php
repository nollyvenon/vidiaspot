@extends('layouts.app')

@section('title', 'Farm Marketplace - Buy Fresh Products Directly from Local Farmers')
@section('meta_description', 'Explore our farm marketplace where you can buy fresh products directly from local farmers. Organic vegetables, fruits, dairy, and more.')
@section('meta_keywords', 'farm marketplace, buy from farmers, fresh products, organic food, local food, farm to table')

@section('content')
<div class="container-fluid">
    <!-- Hero Section -->
    <section class="hero-section bg-success text-white py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="display-4 fw-bold mb-3">Farm Marketplace</h1>
                    <p class="lead mb-4">Discover fresh, locally grown products directly from our network of trusted farmers.</p>
                    <div class="d-flex gap-3">
                        <a href="#categories" class="btn btn-light btn-lg fw-bold text-success">Browse Products</a>
                        <a href="{{ route('farm.seller.landing') }}" class="btn btn-outline-light btn-lg">Sell Your Products</a>
                    </div>
                </div>
                <div class="col-md-6 text-center">
                    <img src="https://images.unsplash.com/photo-1464226184884-fa280b7dd3bb?auto=format&fit=crop&w=600&h=500&q=80" alt="Farm Marketplace" class="img-fluid rounded shadow-lg">
                </div>
            </div>
        </div>
    </section>

    <!-- Marketplace Stats -->
    <section class="py-4 bg-light">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-3 col-6 mb-3 mb-md-0">
                    <h3 class="text-success fw-bold">500+</h3>
                    <p class="text-muted mb-0">Local Farmers</p>
                </div>
                <div class="col-md-3 col-6 mb-3 mb-md-0">
                    <h3 class="text-success fw-bold">2K+</h3>
                    <p class="text-muted mb-0">Fresh Products</p>
                </div>
                <div class="col-md-3 col-6">
                    <h3 class="text-success fw-bold">98%</h3>
                    <p class="text-muted mb-0">Satisfaction Rate</p>
                </div>
                <div class="col-md-3 col-6">
                    <h3 class="text-success fw-bold">24 Hrs</h3>
                    <p class="text-muted mb-0">From Farm to Table</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section id="categories" class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2 class="fw-bold">Farm Product Categories</h2>
                    <p class="text-muted">Browse by category to find what you're looking for</p>
                </div>
            </div>

            <div class="row g-4">
                @foreach([
                    ['name' => 'Fresh Vegetables', 'icon' => 'fas fa-carrot', 'color' => 'success', 'count' => 150],
                    ['name' => 'Fresh Fruits', 'icon' => 'fas fa-apple-alt', 'color' => 'danger', 'count' => 120],
                    ['name' => 'Organic Products', 'icon' => 'fas fa-leaf', 'color' => 'success', 'count' => 80],
                    ['name' => 'Dairy Products', 'icon' => 'fas fa-glass-whiskey', 'color' => 'primary', 'count' => 60],
                    ['name' => 'Poultry & Eggs', 'icon' => 'fas fa-egg', 'color' => 'warning', 'count' => 90],
                    ['name' => 'Fresh Herbs', 'icon' => 'fas fa-seedling', 'color' => 'success', 'count' => 40],
                    ['name' => 'Grains & Cereals', 'icon' => 'fas fa-wheat-alt', 'color' => 'warning', 'count' => 70],
                    ['name' => 'Livestock', 'icon' => 'fas fa-horse', 'color' => 'secondary', 'count' => 30]
                ] as $index => $category)
                <div class="col-lg-3 col-md-6">
                    <a href="{{ route('farm.products.index') . '?category=' . strtolower(str_replace(' ', '-', $category['name'])) }}" class="text-decoration-none">
                        <div class="card h-100 farm-category-card border-0 shadow-sm">
                            <div class="card-body text-center p-4">
                                <div class="bg-{{ $category['color'] }} bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
                                    <i class="{{ $category['icon'] }} text-{{ $category['color'] }} fa-2x"></i>
                                </div>
                                <h5 class="card-title">{{ $category['name'] }}</h5>
                                <p class="text-muted mb-0">{{ $category['count'] }} products</p>
                            </div>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Featured Farm Products -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2 class="fw-bold">Featured Farm Products</h2>
                    <p class="text-muted">Today's freshest picks from our partner farms</p>
                </div>
            </div>

            <div class="row g-4">
                @for($i = 0; $i < 8; $i++)
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="card h-100 product-card border-0 shadow-sm">
                        <div class="position-relative">
                            <img src="https://images.unsplash.com/photo-1596466596120-2a8e4b5d5b5d?auto=format&fit=crop&w=400&h=250&q=80" 
                                 class="card-img-top img-fluid object-fit-cover" 
                                 alt="Farm Product" 
                                 style="height: 180px;">
                            <div class="position-absolute top-0 end-0 m-2">
                                <span class="badge bg-success">Organic</span>
                            </div>
                        </div>
                        <div class="card-body p-3">
                            <h6 class="card-title mb-1">Organic Tomatoes</h6>
                            <p class="card-text text-muted small mb-2">Vine-ripened, juicy</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-success">₦1,200</span>
                                <small class="text-muted">
                                    <i class="fas fa-map-marker-alt"></i> 5km
                                </small>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <small class="text-muted">
                                    <i class="fas fa-star text-warning"></i> 4.8
                                </small>
                                <small class="text-muted">1 day old</small>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-0 py-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="fas fa-store me-1"></i> Green Valley Farm
                                </small>
                                <a href="#" class="btn btn-sm btn-outline-success">
                                    <i class="fas fa-eye me-1"></i> View
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endfor
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2 class="fw-bold">How Our Farm Marketplace Works</h2>
                    <p class="text-muted">Simple steps to enjoy fresh, locally grown products</p>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-3 text-center">
                    <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
                        <i class="fas fa-search text-success fa-2x"></i>
                    </div>
                    <h5>Browse Products</h5>
                    <p class="text-muted">Find fresh products from local farms in your area</p>
                </div>
                <div class="col-md-3 text-center">
                    <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
                        <i class="fas fa-shopping-cart text-success fa-2x"></i>
                    </div>
                    <h5>Add to Cart</h5>
                    <p class="text-muted">Select products and customize your order</p>
                </div>
                <div class="col-md-3 text-center">
                    <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
                        <i class="fas fa-truck text-success fa-2x"></i>
                    </div>
                    <h5>Delivery Options</h5>
                    <p class="text-muted">Choose pickup or delivery options</p>
                </div>
                <div class="col-md-3 text-center">
                    <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
                        <i class="fas fa-utensils text-success fa-2x"></i>
                    </div>
                    <h5>Enjoy Freshness</h5>
                    <p class="text-muted">Receive the freshest products from farm to table</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Certification Section -->
    <section class="py-5 bg-success text-white">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h3 class="fw-bold mb-3">Quality & Safety Guaranteed</h3>
                    <p class="mb-4 opacity-75">All our farmers follow strict quality standards and many are certified organic.</p>
                    <div class="d-flex flex-wrap gap-3">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle me-2"></i>
                            <span>Organic Certification</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle me-2"></i>
                            <span>Sustainable Practices</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle me-2"></i>
                            <span>Food Safety Standards</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle me-2"></i>
                            <span>Traceability</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-center">
                    <i class="fas fa-seedling fa-5x opacity-50"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- Farmer Spotlight -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2 class="fw-bold">Featured Farmers</h2>
                    <p class="text-muted">Meet our dedicated farmers</p>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="card border-0 shadow">
                        <div class="row g-0">
                            <div class="col-md-4">
                                <img src="https://images.unsplash.com/photo-1501482045694-bd355e6d1d6d?auto=format&fit=crop&w=400&h=300&q=80" 
                                     class="img-fluid h-100 object-fit-cover" 
                                     alt="Green Valley Organic Farm"
                                     style="border-top-left-radius: 0.375rem; border-bottom-left-radius: 0.375rem;">
                            </div>
                            <div class="col-md-8">
                                <div class="card-body p-4">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <h4 class="card-title">Green Valley Organic Farm</h4>
                                        <span class="badge bg-success">Certified Organic</span>
                                    </div>
                                    <p class="card-text text-muted mb-3">
                                        <i class="fas fa-map-marker-alt text-success me-2"></i>
                                        Ibeju-Lekki, Lagos
                                    </p>
                                    <p class="card-text">"We've been growing organic vegetables for over 5 years, focusing on sustainable practices and environmental conservation. Our customers love our fresh, flavorful produce."</p>
                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <div>
                                            <span class="text-muted me-3">
                                                <i class="fas fa-leaf text-success me-1"></i> 12 Years Experience
                                            </span>
                                            <span class="text-muted">
                                                <i class="fas fa-star text-warning me-1"></i> 4.9 Rating
                                            </span>
                                        </div>
                                        <div>
                                            <a href="{{ route('farm.seller.profile', ['id' => 1]) }}" class="btn btn-success btn-sm">View Products</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Download App Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h3 class="fw-bold mb-2">Download Our App for Farm Shopping</h3>
                    <p class="text-muted mb-4">Get fresh product alerts, track your orders, and connect directly with farmers.</p>
                    <div class="d-flex gap-3">
                        <a href="#" class="btn btn-success btn-lg">
                            <i class="fab fa-google-play me-2"></i> Google Play
                        </a>
                        <a href="#" class="btn btn-success btn-lg">
                            <i class="fab fa-app-store-ios me-2"></i> App Store
                        </a>
                    </div>
                </div>
                <div class="col-md-4 text-center">
                    <img src="https://placehold.co/200x400/28a745/ffffff?text=Farm+App" alt="VidiaSpot Farm App" class="img-fluid" style="max-height: 150px;">
                </div>
            </div>
        </div>
    </section>
</div>

<style>
    .farm-category-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .farm-category-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
    
    .product-card {
        transition: transform 0.2s ease;
    }
    
    .product-card:hover {
        transform: translateY(-3px);
    }
    
    .object-fit-cover {
        object-fit: cover;
    }
    
    /* Override default Bootstrap card styles */
    .card {
        border: none;
    }
    
    /* Ensure proper rounding */
    .rounded {
        border-radius: 10px !important;
    }
    
    .card-img-top {
        border-radius: 0.375rem 0.375rem 0 0 !important;
    }
</style>
@endsection