@extends('layouts.app')

@section('title', $farmProduct->title . ' - Buy from Local Farm')
@section('meta_description', $farmProduct->description)
@section('meta_keywords', $farmProduct->category->name . ', farm products, fresh, organic, ' . $farmProduct->title)

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mt-3">
        <div class="container">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('farm.marketplace') }}">Farm Marketplace</a></li>
                <li class="breadcrumb-item"><a href="{{ route('farm.products.index') }}">Farm Products</a></li>
                @if($farmProduct->category)
                    <li class="breadcrumb-item"><a href="{{ route('farm.products.index', ['category' => $farmProduct->category->slug]) }}">{{ $farmProduct->category->name }}</a></li>
                @endif
                <li class="breadcrumb-item active">{{ $farmProduct->title }}</li>
            </ol>
        </div>
    </nav>

    <!-- Product Detail Section -->
    <section class="py-4">
        <div class="container">
            <div class="row">
                <!-- Product Images -->
                <div class="col-lg-6">
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="position-relative">
                                @if($farmProduct->images->count() > 0)
                                    <img src="{{ $farmProduct->images->first()->image_url }}" 
                                         id="mainImage" 
                                         class="img-fluid rounded" 
                                         alt="{{ $farmProduct->title }}"
                                         style="height: 400px; width: 100%; object-fit: cover;"
                                         onerror="this.src='https://placehold.co/600x400?text=No+Image';">
                                @else
                                    <img src="https://placehold.co/600x400?text=No+Image" 
                                         id="mainImage" 
                                         class="img-fluid rounded" 
                                         style="height: 400px; width: 100%; object-fit: cover;"
                                         alt="No image available">
                                @endif
                                
                                <div class="position-absolute top-0 end-0 m-2">
                                    @if($farmProduct->is_organic)
                                        <span class="badge bg-success">Organic</span>
                                    @endif
                                    @if($farmProduct->certification)
                                        <span class="badge bg-primary ms-1">{{ $farmProduct->certification }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @if($farmProduct->images->count() > 1)
                    <div class="row g-2">
                        @foreach($farmProduct->images as $index => $image)
                            <div class="col-3">
                                <img src="{{ $image->image_url }}" 
                                     class="img-thumbnail img-fluid cursor-pointer" 
                                     alt="Product image {{ $index + 1 }}"
                                     style="height: 80px; object-fit: cover;"
                                     onclick="changeMainImage(this.src)"
                                     onerror="this.src='https://placehold.co/100x80?text=Image';">
                            </div>
                        @endforeach
                    </div>
                    @endif
                </div>
                
                <!-- Product Info -->
                <div class="col-lg-6">
                    <div class="ps-lg-4">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h1 class="fw-bold">{{ $farmProduct->title }}</h1>
                            <span class="badge bg-success fs-6">{{ $farmProduct->condition }}</span>
                        </div>
                        
                        <div class="d-flex align-items-center mb-3">
                            @if($farmProduct->quality_rating)
                                <div class="rating-stars me-3">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= floor($farmProduct->quality_rating) ? 'text-warning' : 'text-muted' }}"></i>
                                    @endfor
                                </div>
                                <span class="text-muted">{{ number_format($farmProduct->quality_rating, 1) }} ({{ $farmProduct->review_count ?? 0 }} reviews)</span>
                            @endif
                        </div>
                        
                        <div class="mb-4">
                            <h2 class="text-success mb-0">₦{{ number_format($farmProduct->price, 2) }}</h2>
                            <p class="text-muted mb-2">{{ $farmProduct->currency_code ?? 'NGN' }}</p>
                            @if($farmProduct->negotiable)
                                <span class="text-success">Price is negotiable</span>
                            @endif
                        </div>
                        
                        <!-- Product Badges -->
                        <div class="d-flex flex-wrap gap-2 mb-4">
                            @if($farmProduct->is_organic)
                                <span class="badge bg-success">
                                    <i class="fas fa-leaf me-1"></i> Organic
                                </span>
                            @endif
                            @if($farmProduct->certification)
                                <span class="badge bg-primary">
                                    <i class="fas fa-certificate me-1"></i> {{ $farmProduct->certification }}
                                </span>
                            @endif
                            @if($farmProduct->harvest_date)
                                <span class="badge bg-info">
                                    <i class="fas fa-calendar-day me-1"></i> 
                                    {{ $farmProduct->harvest_date->diffInDays(now()) }} days old
                                </span>
                            @endif
                        </div>
                        
                        <!-- Product Description -->
                        <div class="mb-4">
                            <h5>Description</h5>
                            <p>{{ $farmProduct->description }}</p>
                        </div>
                        
                        <!-- Product Details -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tbody>
                                        <tr>
                                            <td class="fw-bold">Category:</td>
                                            <td>{{ $farmProduct->category->name ?? 'Uncategorized' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Status:</td>
                                            <td>
                                                <span class="badge {{ $farmProduct->status == 'active' ? 'bg-success' : 'bg-warning' }}">
                                                    {{ ucfirst($farmProduct->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                        @if($farmProduct->harvest_date)
                                        <tr>
                                            <td class="fw-bold">Harvest Date:</td>
                                            <td>{{ $farmProduct->harvest_date->format('M d, Y') }}</td>
                                        </tr>
                                        @endif
                                        @if($farmProduct->harvest_season)
                                        <tr>
                                            <td class="fw-bold">Harvest Season:</td>
                                            <td>{{ ucfirst($farmProduct->harvest_season) }}</td>
                                        </tr>
                                        @endif
                                        @if($farmProduct->freshness_days)
                                        <tr>
                                            <td class="fw-bold">Freshness:</td>
                                            <td>{{ $farmProduct->freshness_days }} days</td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tbody>
                                        @if($farmProduct->farm_size)
                                        <tr>
                                            <td class="fw-bold">Farm Size:</td>
                                            <td>{{ $farmProduct->farm_size }} {{ $farmProduct->size_unit ?: 'acres' }}</td>
                                        </tr>
                                        @endif
                                        @if($farmProduct->sustainability_score)
                                        <tr>
                                            <td class="fw-bold">Sustainability:</td>
                                            <td>
                                                <span class="text-success">
                                                    <i class="fas fa-leaf me-1"></i>
                                                    {{ number_format($farmProduct->sustainability_score, 1) }}/10
                                                </span>
                                            </td>
                                        </tr>
                                        @endif
                                        @if($farmProduct->shelf_life)
                                        <tr>
                                            <td class="fw-bold">Shelf Life:</td>
                                            <td>{{ $farmProduct->shelf_life }} days</td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <td class="fw-bold">Views:</td>
                                            <td>{{ number_format($farmProduct->view_count) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <!-- Farm Location -->
                        <div class="card border-0 bg-light mb-4 p-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-muted">Farm Location</h6>
                                    <p class="mb-0">
                                        <i class="fas fa-map-marker-alt text-success me-2"></i>
                                        {{ $farmProduct->farm_location ?: $farmProduct->location }}
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-muted">Farm Name</h6>
                                    <p class="mb-0">
                                        <i class="fas fa-store text-success me-2"></i>
                                        {{ $farmProduct->farm_name ?: 'Local Farm' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="d-grid gap-3">
                            <button class="btn btn-success btn-lg">
                                <i class="fas fa-shopping-cart me-2"></i>
                                Add to Cart
                            </button>
                            <button class="btn btn-outline-success btn-lg">
                                <i class="fas fa-phone me-2"></i>
                                Contact Farmer
                            </button>
                            <div class="d-flex gap-2">
                                <button class="btn btn-outline-secondary flex-fill">
                                    <i class="fas fa-heart me-2"></i>
                                    Save
                                </button>
                                <button class="btn btn-outline-secondary flex-fill">
                                    <i class="fas fa-share-alt me-2"></i>
                                    Share
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Additional Information Tabs -->
            <div class="row mt-5">
                <div class="col-12">
                    <ul class="nav nav-tabs" id="productTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="details-tab" data-bs-toggle="tab" data-bs-target="#details" type="button" role="tab">
                                <i class="fas fa-info-circle me-2"></i> Details
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="farm-tab" data-bs-toggle="tab" data-bs-target="#farm" type="button" role="tab">
                                <i class="fas fa-tractor me-2"></i> Farm Info
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="delivery-tab" data-bs-toggle="tab" data-bs-target="#delivery" type="button" role="tab">
                                <i class="fas fa-truck me-2"></i> Delivery
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button" role="tab">
                                <i class="fas fa-star me-2"></i> Reviews
                            </button>
                        </li>
                    </ul>
                    
                    <div class="tab-content bg-white p-4 rounded-bottom border-start border-end border-bottom">
                        <!-- Details Tab -->
                        <div class="tab-pane fade show active" id="details" role="tabpanel">
                            <h5>Farm Product Details</h5>
                            <p>{{ $farmProduct->description }}</p>
                            
                            @if($farmProduct->storage_instructions)
                            <h6 class="mt-4">Storage Instructions</h6>
                            <p>{{ $farmProduct->storage_instructions }}</p>
                            @endif
                            
                            @if($farmProduct->seasonal_availability)
                            <h6 class="mt-4">Seasonal Availability</h6>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($farmProduct->seasonal_availability as $season)
                                <span class="badge bg-success text-capitalize">{{ $season }}</span>
                                @endforeach
                            </div>
                            @endif
                        </div>
                        
                        <!-- Farm Info Tab -->
                        <div class="tab-pane fade" id="farm" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5>Farm Information</h5>
                                    <table class="table">
                                        <tbody>
                                            <tr>
                                                <td><strong>Farm Name</strong></td>
                                                <td>{{ $farmProduct->farm_name ?: 'Local Farm' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Farmer</strong></td>
                                                <td>{{ $farmProduct->farmer_name ?: $farmProduct->user->name }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Farm Size</strong></td>
                                                <td>{{ $farmProduct->farm_size ? $farmProduct->farm_size . ' ' . ($farmProduct->size_unit ?: 'acres') : 'Not specified' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Farm Practices</strong></td>
                                                <td>
                                                    @if($farmProduct->farm_practices)
                                                        @foreach($farmProduct->farm_practices as $practice)
                                                            <span class="badge bg-info me-1 text-capitalize">{{ $practice }}</span>
                                                        @endforeach
                                                    @else
                                                        Not specified
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Pesticide Use</strong></td>
                                                <td>
                                                    {{ $farmProduct->pesticide_use ? 'Yes' : 'No' }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h5>Certifications</h5>
                                    @if($farmProduct->certification || $farmProduct->farm_certifications)
                                        <div class="list-group">
                                            @if($farmProduct->certification)
                                                <div class="list-group-item">
                                                    <div class="d-flex w-100 justify-content-between">
                                                        <h6 class="mb-1">{{ $farmProduct->certification }}</h6>
                                                        <small>Certifying Body: {{ $farmProduct->certification_body ?: 'N/A' }}</small>
                                                    </div>
                                                </div>
                                            @endif
                                            
                                            @if($farmProduct->farm_certifications)
                                                @foreach($farmProduct->farm_certifications as $cert)
                                                    <div class="list-group-item">
                                                        <div class="d-flex w-100 justify-content-between">
                                                            <h6 class="mb-1">{{ $cert['name'] ?? $cert }}</h6>
                                                            <small>{{ $cert['date'] ?? 'N/A' }}</small>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                    @else
                                        <p>No certifications specified</p>
                                    @endif
                                    
                                    @if($farmProduct->sustainability_score)
                                    <div class="mt-3">
                                        <h6>Sustainability Score: {{ number_format($farmProduct->sustainability_score, 2) }}/10</h6>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ ($farmProduct->sustainability_score / 10) * 100 }}%">
                                                {{ number_format($farmProduct->sustainability_score, 2) }}/10
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <!-- Delivery Tab -->
                        <div class="tab-pane fade" id="delivery" role="tabpanel">
                            <h5>Delivery & Pickup Options</h5>
                            
                            @if($farmProduct->delivery_options)
                                <div class="row g-3">
                                    @foreach($farmProduct->delivery_options as $option)
                                        <div class="col-md-4">
                                            <div class="card h-100 border-success">
                                                <div class="card-body text-center">
                                                    @switch($option)
                                                        @case('local_delivery')
                                                            <i class="fas fa-truck text-success fa-2x mb-2"></i>
                                                            <h6>Local Delivery</h6>
                                                            <p class="text-muted">Available in {{ $farmProduct->local_delivery_radius ?: '25' }}km radius</p>
                                                            @break
                                                        @case('pickup')
                                                            <i class="fas fa-store text-success fa-2x mb-2"></i>
                                                            <h6>Pickup Available</h6>
                                                            <p class="text-muted">Collect from the farm</p>
                                                            @break
                                                        @case('shipping')
                                                            <i class="fas fa-shipping-fast text-success fa-2x mb-2"></i>
                                                            <h6>Shipping Available</h6>
                                                            <p class="text-muted">Ships nationwide</p>
                                                            @break
                                                        @default
                                                            <i class="fas fa-box text-success fa-2x mb-2"></i>
                                                            <h6>{{ ucfirst(str_replace('_', ' ', $option)) }}</h6>
                                                            <p class="text-muted">Available option</p>
                                                    @endswitch
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted">Delivery options not specified. Contact farmer for details.</p>
                            @endif
                            
                            <div class="mt-4">
                                <h6>Farm Location</h6>
                                <p>
                                    <i class="fas fa-map-marker-alt text-success me-2"></i>
                                    {{ $farmProduct->farm_location ?: $farmProduct->location }}
                                </p>
                                
                                @if($farmProduct->farm_latitude && $farmProduct->farm_longitude)
                                    <div id="farmLocationMap" style="height: 300px; width: 100%;" class="mt-3">
                                        <div class="d-flex align-items-center justify-content-center h-100 bg-light rounded">
                                            <div class="text-center">
                                                <i class="fas fa-map-marked-alt fa-2x text-success mb-2"></i>
                                                <p class="mb-0">Farm location map would appear here</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Reviews Tab -->
                        <div class="tab-pane fade" id="reviews" role="tabpanel">
                            <h5>Reviews & Ratings</h5>
                            @if($farmProduct->review_count > 0)
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="text-center mb-4">
                                            <div class="display-4 text-success">{{ number_format($farmProduct->quality_rating, 1) }}</div>
                                            <div class="d-flex justify-content-center align-items-center">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star {{ $i <= floor($farmProduct->quality_rating) ? 'text-warning' : 'text-muted' }}"></i>
                                                @endfor
                                            </div>
                                            <small>{{ $farmProduct->review_count }} reviews</small>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="progress mb-1">
                                            <div class="progress-bar" role="progressbar" style="width: {{ $farmProduct->five_star_percent ?? 0 }}%"></div>
                                        </div>
                                        <div class="progress mb-1">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $farmProduct->four_star_percent ?? 0 }}%"></div>
                                        </div>
                                        <div class="progress mb-1">
                                            <div class="progress-bar bg-info" role="progressbar" style="width: {{ $farmProduct->three_star_percent ?? 0 }}%"></div>
                                        </div>
                                        <div class="progress mb-1">
                                            <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $farmProduct->two_star_percent ?? 0 }}%"></div>
                                        </div>
                                        <div class="progress">
                                            <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $farmProduct->one_star_percent ?? 0 }}%"></div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mt-4">
                                    <button class="btn btn-outline-success">
                                        <i class="fas fa-star me-2"></i> Write a Review
                                    </button>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-star fa-3x text-muted mb-3"></i>
                                    <h5>No Reviews Yet</h5>
                                    <p class="text-muted">Be the first to review this farm product!</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Related Products Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <h3 class="mb-4">Related Farm Products from {{ $farmProduct->farm_name ?: 'the Farm' }}</h3>
            
            <div class="row">
                @php
                    $relatedProducts = \App\Models\Ad::where('user_id', $farmProduct->user_id)
                        ->where('direct_from_farm', true)
                        ->where('id', '!=', $farmProduct->id)
                        ->limit(4)
                        ->get();
                @endphp
                
                @forelse($relatedProducts as $product)
                <div class="col-lg-3 col-md-6 mb-4">
                    <a href="{{ route('farm.products.show', $product->id) }}" class="text-decoration-none">
                        <div class="card h-100">
                            <div class="position-relative">
                                @if($product->images->count() > 0)
                                    <img src="{{ $product->images->first()->image_url }}" 
                                         class="card-img-top" 
                                         alt="{{ $product->title }}" 
                                         style="height: 180px; object-fit: cover;"
                                         onerror="this.src='https://placehold.co/300x180?text=No+Image';">
                                @else
                                    <img src="https://placehold.co/300x180?text=No+Image" 
                                         class="card-img-top" 
                                         style="height: 180px; object-fit: cover;"
                                         alt="No image">
                                @endif
                                
                                @if($product->is_organic)
                                    <div class="position-absolute top-0 start-0 m-2">
                                        <span class="badge bg-success">Organic</span>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="card-body">
                                <h6 class="card-title">{{ Str::limit($product->title, 30) }}</h6>
                                <p class="card-text text-success fw-bold">₦{{ number_format($product->price, 2) }}</p>
                                <p class="text-muted small mb-0">
                                    <i class="fas fa-store me-1"></i>
                                    {{ $product->farm_name ?: 'Local Farm' }}
                                </p>
                            </div>
                        </div>
                    </a>
                </div>
                @empty
                <div class="col-12 text-center py-4">
                    <p class="text-muted">No other products from this farm currently available.</p>
                </div>
                @endforelse
            </div>
        </div>
    </section>
</div>

<script>
    function changeMainImage(newSrc) {
        document.getElementById('mainImage').src = newSrc;
    }
    
    // Add to cart function
    function addToCart(productId) {
        // In a real implementation, this would add the product to the user's cart
        fetch(`/api/cart/add`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                product_id: productId,
                quantity: 1
            })
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                alert('Product added to cart!');
            } else {
                alert('Error adding product to cart');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error adding product to cart');
        });
    }
</script>

<style>
    .cursor-pointer {
        cursor: pointer;
    }
    
    .rating-stars .fa-star {
        font-size: 1.2em;
    }
    
    .product-card {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0,0,0,0.1) !important;
    }
</style>
@endsection