@extends('layouts.app')

@section('title', 'Farmer Profile - ' . $farmSeller->name)
@section('meta_description', 'Explore products from ' . $farmSeller->name . ' - a trusted farmer on our platform.')
@section('meta_keywords', 'farmer profile, '. $farmSeller->name .', farm products, local farmer, organic products')

@section('content')
<div class="container-fluid">
    <!-- Farmer Profile Header -->
    <section class="hero-section bg-success text-white py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-3 text-center mb-4 mb-md-0">
                    <div class="mx-auto" style="width: 150px; height: 150px;">
                        <img src="{{ $farmSeller->profile_image ?: 'https://via.placeholder.com/150' }}" 
                             class="rounded-circle img-fluid" 
                             alt="{{ $farmSeller->name }}"
                             style="width: 150px; height: 150px; object-fit: cover;"
                             onerror="this.src='https://via.placeholder.com/150';">
                    </div>
                </div>
                <div class="col-md-9">
                    <h1 class="fw-bold">{{ $farmSeller->name }}</h1>
                    <p class="lead mb-2">
                        <i class="fas fa-store me-2"></i>
                        <span class="text-warning">{{ $farmSeller->farm_name ?: 'Farm' }}</span>
                    </p>
                    <div class="d-flex flex-wrap gap-3 mb-2">
                        <span class="d-flex align-items-center">
                            <i class="fas fa-map-marker-alt me-1"></i>
                            {{ $farmSeller->address ?? 'Location not specified' }}
                        </span>
                        <span class="d-flex align-items-center">
                            <i class="fas fa-phone me-1"></i>
                            {{ $farmSeller->phone ?? 'Phone not provided' }}
                        </span>
                        <span class="d-flex align-items-center">
                            <i class="fas fa-envelope me-1"></i>
                            {{ $farmSeller->email }}
                        </span>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        @if($farmSeller->is_verified)
                        <span class="badge bg-primary">
                            <i class="fas fa-check-circle me-1"></i>Verified Farmer
                        </span>
                        @endif
                        @if($farmSeller->is_organic_certified)
                        <span class="badge bg-success">
                            <i class="fas fa-leaf me-1"></i>Organic Certified
                        </span>
                        @endif
                        <span class="badge bg-info">
                            Member Since {{ $farmSeller->created_at->format('Y') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Farmer Bio Section -->
    <section class="py-4 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-md-8 mx-auto">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h4>About {{ $farmSeller->name }}</h4>
                            <p class="text-muted">
                                {{ $farmSeller->bio ?: 'No bio information provided by the farmer.' }}
                            </p>
                            
                            @if($farmSeller->farm_size || $farmSeller->years_experience || $farmSeller->farm_practices)
                            <div class="row mt-4">
                                @if($farmSeller->farm_size)
                                <div class="col-md-4">
                                    <h6><i class="fas fa-ruler-combined text-success me-2"></i>Farm Size</h6>
                                    <p>{{ $farmSeller->farm_size }} {{ $farmSeller->size_unit ?: 'acres' }}</p>
                                </div>
                                @endif
                                
                                @if($farmSeller->years_experience)
                                <div class="col-md-4">
                                    <h6><i class="fas fa-calendar-alt text-success me-2"></i>Experience</h6>
                                    <p>{{ $farmSeller->years_experience }} years</p>
                                </div>
                                @endif
                                
                                @if($farmSeller->farm_practices)
                                <div class="col-md-4">
                                    <h6><i class="fas fa-seedling text-success me-2"></i>Practices</h6>
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach($farmSeller->farm_practices as $practice)
                                        <span class="badge bg-success">{{ ucfirst(str_replace('_', ' ', $practice)) }}</span>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Products from this Farmer -->
    <section class="py-5">
        <div class="container">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h2>Products from {{ $farmSeller->name }}</h2>
                    <p class="text-muted">{{ count($farmProducts) }} products available</p>
                </div>
            </div>
            
            @if(count($farmProducts) > 0)
            <div class="row g-4">
                @foreach($farmProducts as $product)
                <div class="col-lg-3 col-md-6">
                    <div class="card h-100 product-card border-0 shadow-sm">
                        <div class="position-relative">
                            @if($product->images->count() > 0)
                                <img src="{{ $product->images->first()->image_url }}" 
                                     class="card-img-top img-fluid object-fit-cover" 
                                     alt="{{ $product->title }}" 
                                     style="height: 200px;" 
                                     onerror="this.src='https://placehold.co/300x200?text=No+Image';">
                            @else
                                <img src="https://placehold.co/300x200?text=No+Image" 
                                     class="card-img-top img-fluid object-fit-cover" 
                                     style="height: 200px;" 
                                     alt="No image">
                            @endif
                            
                            <div class="position-absolute top-0 end-0 m-2">
                                @if($product->is_organic)
                                    <span class="badge bg-success">Organic</span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="card-body p-3">
                            <h6 class="card-title mb-1">{{ Str::limit($product->title, 40) }}</h6>
                            <p class="card-text text-muted small mb-2">{{ Str::limit($product->description, 60) }}</p>
                            
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-bold text-success">₦{{ number_format($product->price) }}</span>
                                <small class="text-muted">
                                    <i class="fas fa-map-marker-alt"></i> 
                                    {{ Str::limit($product->location, 15) }}
                                </small>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    @if($product->quality_rating)
                                        <i class="fas fa-star text-warning me-1"></i>
                                        <span class="text-muted small">{{ number_format($product->quality_rating, 1) }}</span>
                                    @endif
                                </div>
                                
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-eye text-muted me-1"></i>
                                    <span class="text-muted small">{{ $product->view_count }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-footer bg-transparent border-0 py-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="fas fa-store me-1"></i> 
                                    {{ $product->farm_name ?: $product->user->name ?? 'Unknown Farmer' }}
                                </small>
                                <a href="{{ route('farm.products.show', $product->id) }}" class="btn btn-sm btn-outline-success">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="row">
                <div class="col-12 text-center py-5">
                    <i class="fas fa-seedling fa-3x text-success mb-3"></i>
                    <h4>No products available</h4>
                    <p class="text-muted">This farmer doesn't have any products listed at the moment.</p>
                </div>
            </div>
            @endif
        </div>
    </section>

    <!-- Farmer Stats Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2>Farmer Statistics</h2>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-3 col-6 mb-4 mb-md-0 text-center">
                    <div class="bg-white rounded p-4 shadow-sm">
                        <i class="fas fa-box-open fa-2x text-success mb-3"></i>
                        <h3 class="fw-bold">{{ count($farmProducts) }}</h3>
                        <p class="text-muted mb-0">Products Listed</p>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-4 mb-md-0 text-center">
                    <div class="bg-white rounded p-4 shadow-sm">
                        <i class="fas fa-shopping-cart fa-2x text-success mb-3"></i>
                        <h3 class="fw-bold">42</h3>
                        <p class="text-muted mb-0">Orders Completed</p>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="bg-white rounded p-4 shadow-sm">
                        <i class="fas fa-star fa-2x text-success mb-3"></i>
                        <h3 class="fw-bold">4.8</h3>
                        <p class="text-muted mb-0">Average Rating</p>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="bg-white rounded p-4 shadow-sm">
                        <i class="fas fa-calendar-check fa-2x text-success mb-3"></i>
                        <h3 class="fw-bold">3.5Y</h3>
                        <p class="text-muted mb-0">Selling Time</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="card border-0 shadow">
                        <div class="card-body p-5">
                            <h3 class="text-center mb-4">Contact {{ $farmSeller->name }}</h3>
                            
                            <div class="text-center mb-4">
                                <p>Would you like to discuss this farmer's products?</p>
                                <button class="btn btn-success btn-lg">
                                    <i class="fas fa-envelope me-2"></i>
                                    Send Message
                                </button>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <h5><i class="fas fa-phone me-2 text-success"></i>Phone</h5>
                                    <p>{{ $farmSeller->phone ?: 'Phone number not provided' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <h5><i class="fas fa-envelope me-2 text-success"></i>Email</h5>
                                    <p>{{ $farmSeller->email }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <h5><i class="fas fa-map-marker-alt me-2 text-success"></i>Address</h5>
                                    <p>{{ $farmSeller->address ?: 'Address not provided' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <h5><i class="fas fa-clock me-2 text-success"></i>Operating Hours</h5>
                                    <p>{{ $farmSeller->operating_hours ?: 'Hours not specified' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
    .product-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0,0,0,0.1) !important;
    }
    
    .object-fit-cover {
        object-fit: cover;
    }
    
    .card {
        border: none;
        border-radius: 10px;
    }
    
    .shadow-sm {
        box-shadow: 0 2px 4px rgba(0,0,0,0.08) !important;
    }
</style>
@endsection