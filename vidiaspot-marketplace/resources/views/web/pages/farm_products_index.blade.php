@extends('layouts.app')

@section('title', 'Farm Products - Fresh from Local Farms')
@section('meta_description', 'Browse fresh farm products directly from local farmers. Find organic vegetables, fruits, dairy, and more from trusted farms in your area.')
@section('meta_keywords', 'farm products, fresh vegetables, organic food, buy from farmers, farm to table, fresh fruits, dairy products, local food')

@section('content')
<div class="container-fluid">
    <!-- Farm Products Hero Section -->
    <section class="hero-section bg-success text-white py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 text-center">
                    <h1 class="display-4 fw-bold mb-3">Fresh From Local Farms</h1>
                    <p class="lead mb-4">Buy directly from farmers. Know your farmer, trust your food.</p>
                    <form action="{{ route('farm.products.index') }}" method="GET" class="d-flex">
                        <input type="text" name="search" class="form-control form-control-lg" placeholder="Search farm products..." value="{{ request('search') }}">
                        <button class="btn btn-light btn-lg" type="submit">
                            <i class="fas fa-search me-2"></i> Search
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Filters Section -->
    <section class="py-4 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <form id="farm-filter-form" method="GET" action="{{ route('farm.products.index') }}">
                        <div class="row g-3 align-items-center">
                            <div class="col-md-2">
                                <label for="category" class="form-label">Category</label>
                                <select name="category" id="category" class="form-select">
                                    <option value="">All Categories</option>
                                    @foreach($farmCategories as $category)
                                        <option value="{{ $category->slug }}" {{ request('category') == $category->slug ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="organic" class="form-label">Organic</label>
                                <select name="organic" id="organic" class="form-select">
                                    <option value="">All Products</option>
                                    <option value="1" {{ request('organic') == '1' ? 'selected' : '' }}>Organic Only</option>
                                    <option value="0" {{ request('organic') == '0' ? 'selected' : '' }}>Conventional Only</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="season" class="form-label">Harvest Season</label>
                                <select name="season" id="season" class="form-select">
                                    <option value="">All Seasons</option>
                                    <option value="spring" {{ request('season') == 'spring' ? 'selected' : '' }}>Spring</option>
                                    <option value="summer" {{ request('season') == 'summer' ? 'selected' : '' }}>Summer</option>
                                    <option value="fall" {{ request('season') == 'fall' ? 'selected' : '' }}>Fall</option>
                                    <option value="winter" {{ request('season') == 'winter' ? 'selected' : '' }}>Winter</option>
                                    <option value="all" {{ request('season') == 'all' ? 'selected' : '' }}>All Season</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="location" class="form-label">Farm Location</label>
                                <input type="text" name="location" id="location" class="form-control" placeholder="Enter location" value="{{ request('location') }}">
                            </div>
                            <div class="col-md-2">
                                <label for="max_price" class="form-label">Max Price</label>
                                <input type="number" name="max_price" id="max_price" class="form-control" placeholder="₦" value="{{ request('max_price') }}">
                            </div>
                            <div class="col-md-1 mt-4">
                                <button type="submit" class="btn btn-success w-100">Filter</button>
                            </div>
                        </div>
                        
                        <!-- Proximity Search Toggle -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="useProximity" onchange="toggleProximitySearch()">
                                    <label class="form-check-label" for="useProximity">Search near me</label>
                                </div>
                                <div id="proximityControls" class="mt-2" style="display: none;">
                                    <div class="row g-2">
                                        <div class="col-md-3">
                                            <label for="radius" class="form-label">Search Radius (km)</label>
                                            <select name="radius" id="radius" class="form-select">
                                                <option value="5" {{ request('radius') == 5 ? 'selected' : '' }}>5 km</option>
                                                <option value="10" {{ request('radius') == 10 ? 'selected' : '' }}>10 km</option>
                                                <option value="25" {{ request('radius') == 25 ? 'selected' : '' }}>25 km</option>
                                                <option value="50" {{ request('radius', 50) == 50 ? 'selected' : '' }}>50 km</option>
                                                <option value="100" {{ request('radius') == 100 ? 'selected' : '' }}>100 km</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="distance_lat" class="form-label">Your Latitude</label>
                                            <input type="text" name="lat" id="distance_lat" class="form-control" placeholder="e.g., 6.4527" value="{{ request('lat') }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="distance_lng" class="form-label">Longitude</label>
                                            <input type="text" name="lng" id="distance_lng" class="form-control" placeholder="e.g., 3.3927" value="{{ request('lng') }}">
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <button type="button" class="btn btn-outline-success btn-sm" onclick="getCurrentLocation()">Use Current Location</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Farm Products Section -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="mb-0">Farm Products ({{ $farmProducts->total() }})</h2>
                        <div class="d-flex gap-2">
                            <select id="sortOptions" class="form-select" onchange="updateSort()">
                                <option value="created_at-desc" {{ request('order_by') == 'created_at' && request('order_direction', 'desc') == 'desc' ? 'selected' : '' }}>Newest First</option>
                                <option value="created_at-asc" {{ request('order_by') == 'created_at' && request('order_direction') == 'asc' ? 'selected' : '' }}>Oldest First</option>
                                <option value="price-asc" {{ request('order_by') == 'price' && request('order_direction', 'asc') == 'asc' ? 'selected' : '' }}>Price: Low to High</option>
                                <option value="price-desc" {{ request('order_by') == 'price' && request('order_direction', 'desc') == 'desc' ? 'selected' : '' }}>Price: High to Low</option>
                                <option value="quality-desc" {{ request('order_by') == 'quality_rating' && request('order_direction', 'desc') == 'desc' ? 'selected' : '' }}>Best Quality</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            @if($farmProducts->count() > 0)
            <div class="row g-4">
                @foreach($farmProducts as $product)
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="card h-100 product-card border-0 shadow-sm">
                        <div class="position-relative">
                            @if($product->images->count() > 0)
                                <img src="{{ $product->images->first()->image_url }}" 
                                     class="card-img-top img-fluid" 
                                     alt="{{ $product->title }}" 
                                     style="height: 200px; object-fit: cover;"
                                     onerror="this.src='https://via.placeholder.com/300x200?text=No+Image';">
                            @else
                                <img src="https://via.placeholder.com/300x200?text=No+Image" 
                                     class="card-img-top img-fluid" 
                                     style="height: 200px; object-fit: cover;"
                                     alt="No image available">
                            @endif
                            
                            <div class="position-absolute top-0 end-0 m-2">
                                @if($product->is_organic)
                                    <span class="badge bg-success">Organic</span>
                                @endif
                                @if($product->certification)
                                    <span class="badge bg-info ms-1">Certified</span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="card-title mb-0">{{ Str::limit($product->title, 40) }}</h6>
                                @if($product->distance)
                                    <small class="text-muted">
                                        <i class="fas fa-map-marker-alt"></i> {{ number_format($product->distance, 1) }}km
                                    </small>
                                @endif
                            </div>
                            
                            <p class="card-text text-muted small mb-2">{{ Str::limit($product->description, 60) }}</p>
                            
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-bold text-success">₦{{ number_format($product->price, 2) }}</span>
                                <div>
                                    @if($product->quality_rating)
                                        <span class="text-warning">
                                            <i class="fas fa-star"></i> {{ number_format($product->quality_rating, 1) }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <small class="text-muted">
                                    @if($product->freshness_days !== null)
                                        <i class="fas fa-tint me-1"></i> {{ $product->freshness_days }}d old
                                    @endif
                                </small>
                                <small class="text-muted">
                                    @if($product->farm_name)
                                        <i class="fas fa-store me-1"></i> {{ Str::limit($product->farm_name, 15) }}
                                    @else
                                        {{ Str::limit($product->user->name ?? 'Farmer', 15) }}
                                    @endif
                                </small>
                            </div>
                        </div>
                        
                        <div class="card-footer bg-transparent border-0 py-2">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted small">
                                    <i class="fas fa-map-marker-alt me-1"></i> {{ Str::limit($product->farm_location ?: $product->location, 20) }}
                                </span>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-outline-success">
                                        <i class="fas fa-heart"></i>
                                    </button>
                                    <a href="{{ route('farm.products.show', $product->id) }}" class="btn btn-sm btn-success">
                                        <i class="fas fa-eye me-1"></i> View
                                    </a>
                                </div>
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
                        {{ $farmProducts->withQueryString()->links() }}
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

    <!-- Quality & Sustainability Section -->
    <section class="py-5 bg-success text-white">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <h3 class="fw-bold mb-3">Quality & Sustainability Commitment</h3>
                    <p class="mb-0">At VidiaSpot Farm Marketplace, we're committed to connecting you with the highest quality farm products while promoting sustainable farming practices.</p>
                </div>
                <div class="col-md-4 text-center">
                    <i class="fas fa-leaf fa-5x opacity-50"></i>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    function toggleProximitySearch() {
        const useProximity = document.getElementById('useProximity');
        const proximityControls = document.getElementById('proximityControls');
        
        if (useProximity.checked) {
            proximityControls.style.display = 'block';
        } else {
            proximityControls.style.display = 'none';
        }
    }
    
    function getCurrentLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                document.getElementById('distance_lat').value = position.coords.latitude.toFixed(6);
                document.getElementById('distance_lng').value = position.coords.longitude.toFixed(6);
                
                // Enable the check box if not already checked
                if (!document.getElementById('useProximity').checked) {
                    document.getElementById('useProximity').checked = true;
                    toggleProximitySearch();
                }
            }, function() {
                alert("Could not get your location. Please enter manually.");
            });
        } else {
            alert("Geolocation is not supported by this browser.");
        }
    }
    
    function updateSort() {
        const sortValue = document.getElementById('sortOptions').value;
        const [field, direction] = sortValue.split('-');
        
        // Add sort parameters to the current URL
        const url = new URL(window.location);
        url.searchParams.set('order_by', field);
        url.searchParams.set('order_direction', direction);
        
        window.location = url.toString();
    }
</script>

<style>
    .product-card {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0,0,0,0.1) !important;
    }
    
    .card {
        border: none;
    }
</style>
@endsection