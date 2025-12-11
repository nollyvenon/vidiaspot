@extends('layouts.app')

@section('title', 'Farm Products - Buy Fresh Products from Local Farms')
@section('meta_description', 'Buy fresh farm products directly from local farmers. Find organic vegetables, fruits, dairy, and more from trusted farms in your area.')
@section('meta_keywords', 'farm products, fresh vegetables, organic food, buy from farmers, farm to table, fresh fruits, dairy products, local food')

@section('content')
<div class="container-fluid">
    <!-- Hero Section -->
    <section class="hero-section bg-success text-white py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="display-4 fw-bold mb-3">Fresh Farm Products</h1>
                    <p class="lead mb-4">Buy directly from local farmers. Know your farmer, trust your food.</p>
                    <div class="d-flex gap-3 flex-wrap">
                        <a href="#farm-products" class="btn btn-light btn-lg text-success fw-bold">Shop Now</a>
                        <a href="{{ route('farm.seller.landing') }}" class="btn btn-outline-light btn-lg">Sell Your Products</a>
                    </div>
                </div>
                <div class="col-md-6 text-center">
                    <img src="https://images.unsplash.com/photo-1464226184884-fa280b7dd3bb?auto=format&fit=crop&w=600&h=500&q=80" alt="Fresh Farm Products" class="img-fluid rounded shadow-lg" style="border-radius: 10px !important;">
                </div>
            </div>
        </div>
    </section>

    <!-- Search and Filter Section -->
    <section class="py-4 bg-light">
        <div class="container">
            <div class="row g-3">
                <div class="col-md-12">
                    <form method="GET" action="{{ route('farm.products.index') }}">
                        <div class="row g-3 align-items-center">
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control form-control-lg" placeholder="Search farm products..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <select name="category" class="form-select form-select-lg">
                                    <option value="">All Categories</option>
                                    @foreach($farmCategories as $category)
                                        <option value="{{ $category->slug }}" {{ request('category') == $category->slug ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="organic" class="form-select form-select-lg">
                                    <option value="">All Products</option>
                                    <option value="1" {{ request('organic') == '1' ? 'selected' : '' }}>Organic Only</option>
                                    <option value="0" {{ request('organic') == '0' ? 'selected' : '' }}>Non-Organic</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="season" class="form-select form-select-lg">
                                    <option value="">Any Season</option>
                                    <option value="spring" {{ request('season') == 'spring' ? 'selected' : '' }}>Spring</option>
                                    <option value="summer" {{ request('season') == 'summer' ? 'selected' : '' }}>Summer</option>
                                    <option value="fall" {{ request('season') == 'fall' ? 'selected' : '' }}>Fall</option>
                                    <option value="winter" {{ request('season') == 'winter' ? 'selected' : '' }}>Winter</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-success btn-lg w-100">
                                    <i class="fas fa-search me-1"></i> Search
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Products Section -->
    <section id="farm-products" class="py-5">
        <div class="container">
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="mb-0">Farm Products</h2>
                        <div class="d-flex gap-2">
                            <span class="text-muted">{{ $farmProducts->total() }} products</span>
                            <select class="form-select" onchange="location = this.value;">
                                <option value="{{ request()->fullUrlWithQuery(['sort' => 'newest']) }}" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest</option>
                                <option value="{{ request()->fullUrlWithQuery(['sort' => 'oldest']) }}" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest</option>
                                <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_low_high']) }}" {{ request('sort') == 'price_low_high' ? 'selected' : '' }}>Price: Low to High</option>
                                <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_high_low']) }}" {{ request('sort') == 'price_high_low' ? 'selected' : '' }}>Price: High to Low</option>
                                <option value="{{ request()->fullUrlWithQuery(['sort' => 'distance']) }}" {{ request('sort') == 'distance' ? 'selected' : '' }}>Closest</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            @if($farmProducts->count() > 0)
            <div class="row g-4">
                @foreach($farmProducts as $product)
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="card product-card border-0 shadow-sm h-100">
                        <div class="position-relative">
                            @if($product->images->count() > 0)
                                <img src="{{ $product->images->first()->image_url }}" 
                                     class="card-img-top img-fluid object-fit-cover" 
                                     alt="{{ $product->title }}" 
                                     style="height: 200px; width: 100%; object-fit: cover;"
                                     onerror="this.src='https://placehold.co/300x200?text=No+Image';">
                            @else
                                <img src="https://placehold.co/300x200?text=No+Image" 
                                     class="card-img-top img-fluid object-fit-cover" 
                                     style="height: 200px; width: 100%; object-fit: cover;"
                                     alt="No image available">
                            @endif
                            
                            <div class="position-absolute top-0 end-0 m-2">
                                @if($product->is_organic)
                                    <span class="badge bg-success">Organic</span>
                                @else
                                    <span class="badge bg-warning">Conventional</span>
                                @endif
                            </div>
                            <div class="position-absolute bottom-0 start-0 m-2">
                                @if($product->farm_tour_available)
                                    <span class="badge bg-info">Farm Tour</span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="card-body p-3">
                            <h6 class="card-title mb-1">{{ Str::limit($product->title, 40) }}</h6>
                            <p class="card-text text-muted small mb-2">{{ Str::limit($product->description, 60) }}</p>
                            
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-bold text-success">₦{{ number_format($product->price, 2) }}</span>
                                @if($product->distance)
                                    <small class="text-muted">
                                        <i class="fas fa-map-marker-alt me-1"></i>
                                        {{ number_format($product->distance, 1) }}km
                                    </small>
                                @else
                                    <small class="text-muted">
                                        <i class="fas fa-map-marker-alt me-1"></i>
                                        {{ Str::limit($product->location, 15) }}
                                    </small>
                                @endif
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    @if($product->quality_rating)
                                        <i class="fas fa-star text-warning me-1"></i>
                                        <span class="text-muted small">{{ number_format($product->quality_rating, 1) }}</span>
                                    @endif
                                    @if($product->freshness_days !== null && $product->freshness_days < 3)
                                        <span class="badge bg-success ms-2">Fresh</span>
                                    @elseif($product->freshness_days < 7)
                                        <span class="badge bg-warning ms-2">Good</span>
                                    @else
                                        <span class="badge bg-info ms-2">Aged</span>
                                    @endif
                                </div>
                                
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-eye text-muted me-1"></i>
                                    <span class="text-muted small">{{ $product->view_count }}</span>
                                </div>
                            </div>
                            
                            @if($product->harvest_date)
                                <small class="text-muted d-block mt-1">
                                    <i class="fas fa-calendar me-1"></i>
                                    Harvested {{ $product->harvest_date->diffInDays(now()) }} days ago
                                </small>
                            @endif
                        </div>
                        
                        <div class="card-footer bg-transparent border-0 py-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="fas fa-store me-1"></i>
                                    {{ $product->farm_name ?: $product->user->name }}
                                </small>
                                <a href="{{ route('farm.products.show', $product->id) }}" class="btn btn-sm btn-outline-success">
                                    <i class="fas fa-eye me-1"></i> View
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="d-flex justify-content-center">
                        {{ $farmProducts->links() }}
                    </div>
                </div>
            </div>
            @else
            <div class="row">
                <div class="col-12 text-center py-5">
                    <i class="fas fa-seedling fa-4x text-success mb-3"></i>
                    <h4>No farm products found</h4>
                    <p class="text-muted">Try adjusting your search criteria or check back later for new listings</p>
                    <a href="{{ route('farm.products.index') }}" class="btn btn-outline-success">Reset Filters</a>
                </div>
            </div>
            @endif
        </div>
    </section>

    <!-- Farm Values Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2 class="fw-bold">Our Farm Values</h2>
                    <p class="text-muted">What makes farm products special</p>
                </div>
            </div>
            
            <div class="row g-4">
                <div class="col-md-3 col-6 text-center">
                    <div class="p-4">
                        <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-leaf text-success fa-2x"></i>
                        </div>
                        <h6>Organic & Sustainable</h6>
                        <p class="text-muted small">Grown with sustainable practices</p>
                    </div>
                </div>
                <div class="col-md-3 col-6 text-center">
                    <div class="p-4">
                        <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-seedling text-success fa-2x"></i>
                        </div>
                        <h6>Fresh & Healthy</h6>
                        <p class="text-muted small">Harvested at peak ripeness</p>
                    </div>
                </div>
                <div class="col-md-3 col-6 text-center">
                    <div class="p-4">
                        <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-hands-helping text-success fa-2x"></i>
                        </div>
                        <h6>Support Local Farmers</h6>
                        <p class="text-muted small">Fair compensation for farmers</p>
                    </div>
                </div>
                <div class="col-md-3 col-6 text-center">
                    <div class="p-4">
                        <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-truck text-success fa-2x"></i>
                        </div>
                        <h6>Fast Delivery</h6>
                        <p class="text-muted small">From farm to table quickly</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
    .product-card {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
    
    .card {
        border: none;
        border-radius: 10px;
    }
    
    .object-fit-cover {
        object-fit: cover;
    }
    
    /* Override default Bootstrap card styles */
    .card {
        border: none;
    }
</style>
@endsection