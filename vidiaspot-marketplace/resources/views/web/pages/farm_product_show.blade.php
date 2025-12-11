@extends('layouts.app')

@section('title', $farmProduct->title . ' - Buy Direct from Farm')
@section('meta_description', $farmProduct->description)
@section('meta_keywords', implode(',', $farmProduct->tags ?? []))

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mt-3">
        <div class="container">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('farm.marketplace') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('farm.products.index') }}">Farm Products</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $farmProduct->title }}</li>
            </ol>
        </div>
    </nav>

    <div class="container py-4">
        <div class="row">
            <!-- Product Images Carousel -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        @if($farmProduct->images->count() > 0)
                            <div id="productCarousel" class="carousel slide" data-bs-ride="carousel">
                                <div class="carousel-inner">
                                    @foreach($farmProduct->images as $index => $image)
                                    <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                        <img src="{{ $image->image_url }}" 
                                             class="d-block w-100" 
                                             style="height: 400px; object-fit: cover;" 
                                             alt="{{ $farmProduct->title }}">
                                    </div>
                                    @endforeach
                                </div>
                                <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Previous</span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Next</span>
                                </button>
                            </div>
                            
                            <!-- Thumbnails -->
                            <div class="d-flex justify-content-center mt-3 gap-2 p-2">
                                @foreach($farmProduct->images as $index => $image)
                                <button class="border-0 p-1" type="button" data-bs-target="#productCarousel" data-bs-slide-to="{{ $index }}">
                                    <img src="{{ $image->image_url }}" 
                                         class="img-thumbnail" 
                                         style="width: 60px; height: 60px; object-fit: cover;" 
                                         alt="Thumbnail {{ $index + 1 }}">
                                </button>
                                @endforeach
                            </div>
                        @else
                            <img src="https://via.placeholder.com/600x400?text=No+Image+Available" 
                                 class="d-block w-100" 
                                 alt="No image available" 
                                 style="height: 400px; object-fit: cover;">
                        @endif
                    </div>
                </div>
            </div>

            <!-- Product Details -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h1 class="fw-bold mb-0">{{ $farmProduct->title }}</h1>
                            <span class="badge bg-success">
                                @if($farmProduct->condition)
                                    {{ ucfirst($farmProduct->condition) }}
                                @else
                                    Fresh
                                @endif
                            </span>
                        </div>

                        <div class="d-flex align-items-center mb-3">
                            @if($farmProduct->quality_rating)
                                <div class="me-3">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= floor($farmProduct->quality_rating) ? 'text-warning' : 'text-muted' }}"></i>
                                    @endfor
                                    <span class="ms-1">{{ $farmProduct->quality_rating }}</span> ({{ $farmProduct->review_count }} reviews)
                                </div>
                            @endif
                            
                            <div class="ms-3">
                                <span class="text-success fw-bold fs-4">₦{{ number_format($farmProduct->price) }}</span>
                                @if($farmProduct->negotiable)
                                    <span class="badge bg-info ms-2">Negotiable</span>
                                @endif
                            </div>
                        </div>

                        <!-- Product Badges -->
                        <div class="mb-3">
                            <div class="d-flex flex-wrap gap-2">
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
                                
                                @if($farmProduct->freshness_days !== null)
                                    <span class="badge bg-info">
                                        <i class="fas fa-clock me-1"></i> {{ $farmProduct->freshness_days }} days old
                                    </span>
                                @endif
                                
                                @if($farmProduct->harvest_date)
                                    <span class="badge bg-warning text-dark">
                                        <i class="fas fa-calendar-alt me-1"></i> Harvested {{ $farmProduct->harvest_date->format('M d, Y') }}
                                    </span>
                                @endif
                                
                                @if($farmProduct->sustainability_score)
                                    <span class="badge bg-success">
                                        <i class="fas fa-leaf me-1"></i> Sustain: {{ number_format($farmProduct->sustainability_score, 1) }}/10
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Farm Info -->
                        <div class="card border-1 bg-light mb-3">
                            <div class="card-body">
                                <h5 class="card-title"><i class="fas fa-tractor text-success me-2"></i> Farm Information</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Farm:</strong> {{ $farmProduct->farm_name ?: 'Local Farm' }}</p>
                                        <p class="mb-1"><strong>Farmer:</strong> {{ $farmProduct->farmer_name ?: 'Local Farmer' }}</p>
                                        <p class="mb-1"><strong>Location:</strong> {{ $farmProduct->farm_location ?: $farmProduct->location }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        @if($farmProduct->farm_size)
                                        <p class="mb-1"><strong>Farm Size:</strong> {{ $farmProduct->farm_size }} {{ $farmProduct->size_unit ?: 'acres' }}</p>
                                        @endif
                                        @if($farmProduct->harvest_season)
                                        <p class="mb-1"><strong>Season:</strong> {{ ucfirst($farmProduct->harvest_season) }}</p>
                                        @endif
                                        @if($farmProduct->certification_type)
                                        <p class="mb-1"><strong>Certification:</strong> {{ $farmProduct->certification_type }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Delivery Options -->
                        <div class="mb-3">
                            <h5><i class="fas fa-truck me-2"></i> Delivery Options</h5>
                            <div class="d-flex flex-wrap gap-2">
                                @if($farmProduct->delivery_options)
                                    @foreach($farmProduct->delivery_options as $option)
                                        @switch($option)
                                            @case('local_delivery')
                                                <span class="badge bg-primary">
                                                    <i class="fas fa-truck me-1"></i> Local Delivery
                                                </span>
                                                @break
                                            @case('pickup')
                                                <span class="badge bg-success">
                                                    <i class="fas fa-store me-1"></i> Pick Up Only
                                                </span>
                                                @break
                                            @case('shipping')
                                                <span class="badge bg-info">
                                                    <i class="fas fa-shipping-fast me-1"></i> Nationwide Shipping
                                                </span>
                                                @break
                                            @case('express_delivery')
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-bolt me-1"></i> Express Delivery
                                                </span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $option)) }}</span>
                                        @endswitch
                                    @endforeach
                                @else
                                    <span class="badge bg-secondary">Local Delivery Available</span>
                                @endif
                                
                                @if($farmProduct->local_delivery_radius)
                                    <span class="badge bg-primary">
                                        <i class="fas fa-map-marker-alt me-1"></i> Within {{ $farmProduct->local_delivery_radius }}km
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Product Description -->
                        <div class="mb-4">
                            <h5>Description</h5>
                            <p>{{ $farmProduct->description }}</p>
                        </div>

                        <!-- Quantity and Purchase -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="quantity" class="form-label">Quantity</label>
                                    <input type="number" id="quantity" class="form-control" value="1" min="1" max="100">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Total Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">₦</span>
                                    <input type="text" id="totalPrice" class="form-control" value="{{ number_format($farmProduct->price) }}" readonly>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-grid gap-3">
                            <button class="btn btn-success btn-lg">
                                <i class="fas fa-shopping-cart me-2"></i> Add to Cart
                            </button>
                            <button class="btn btn-outline-success btn-lg">
                                <i class="fas fa-message me-2"></i> Contact Farmer
                            </button>
                            <div class="d-flex gap-2">
                                <button class="btn btn-outline-secondary flex-fill">
                                    <i class="fas fa-heart me-2"></i> Favorite
                                </button>
                                <button class="btn btn-outline-secondary flex-fill">
                                    <i class="fas fa-share-alt me-2"></i> Share
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Information -->
        <div class="row mt-4">
            <div class="col-lg-12">
                <ul class="nav nav-tabs" id="productInfoTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="product-details-tab" data-bs-toggle="tab" data-bs-target="#product-details" type="button" role="tab">Product Details</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="farm-information-tab" data-bs-toggle="tab" data-bs-target="#farm-information" type="button" role="tab">Farm Information</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="delivery-tab" data-bs-toggle="tab" data-bs-target="#delivery" type="button" role="tab">Delivery & Payment</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button" role="tab">Reviews</button>
                    </li>
                </ul>
                
                <div class="tab-content p-4 border-start border-end border-bottom bg-white shadow-sm rounded-bottom">
                    <!-- Product Details Tab -->
                    <div class="tab-pane fade show active" id="product-details" role="tabpanel">
                        <h5>Product Specifications</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tbody>
                                        <tr>
                                            <th>Category:</th>
                                            <td>{{ $farmProduct->category->name ?? 'Uncategorized' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Condition:</th>
                                            <td>{{ ucfirst($farmProduct->condition ?: 'New') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Product Status:</th>
                                            <td>
                                                <span class="badge {{ $farmProduct->status == 'active' ? 'bg-success' : 'bg-warning' }}">
                                                    {{ ucfirst($farmProduct->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                        @if($farmProduct->harvest_date)
                                        <tr>
                                            <th>Harvest Date:</th>
                                            <td>{{ $farmProduct->harvest_date->format('M d, Y') }}</td>
                                        </tr>
                                        @endif
                                        @if($farmProduct->freshness_days !== null)
                                        <tr>
                                            <th>Freshness:</th>
                                            <td>{{ $farmProduct->freshness_days }} days</td>
                                        </tr>
                                        @endif
                                        @if($farmProduct->shelf_life)
                                        <tr>
                                            <th>Shelf Life:</th>
                                            <td>{{ $farmProduct->shelf_life }} days</td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tbody>
                                        @if($farmProduct->quality_rating)
                                        <tr>
                                            <th>Quality Rating:</th>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <i class="fas fa-star {{ $i <= floor($farmProduct->quality_rating) ? 'text-warning' : 'text-muted' }}"></i>
                                                    @endfor
                                                    <span class="ms-2">{{ number_format($farmProduct->quality_rating, 1) }}/5</span>
                                                </div>
                                            </td>
                                        </tr>
                                        @endif
                                        @if($farmProduct->sustainability_score)
                                        <tr>
                                            <th>Sustainability Score:</th>
                                            <td>
                                                <span class="text-success fw-bold">{{ number_format($farmProduct->sustainability_score, 1) }}/10</span>
                                                <div class="progress mt-1" style="height: 10px;">
                                                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ min(100, $farmProduct->sustainability_score * 10) }}%"></div>
                                                </div>
                                            </td>
                                        </tr>
                                        @endif
                                        @if($farmProduct->carbon_footprint)
                                        <tr>
                                            <th>Carbon Footprint:</th>
                                            <td>{{ $farmProduct->carbon_footprint }} kg CO₂</td>
                                        </tr>
                                        @endif
                                        @if($farmProduct->packaging_type)
                                        <tr>
                                            <th>Packaging:</th>
                                            <td>{{ ucfirst(str_replace('_', ' ', $farmProduct->packaging_type)) }}</td>
                                        </tr>
                                        @endif
                                        @if($farmProduct->storage_instructions)
                                        <tr>
                                            <th>Storage:</th>
                                            <td>{{ $farmProduct->storage_instructions }}</td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        @if($farmProduct->farm_practices)
                        <h6>Farming Practices</h6>
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            @foreach($farmProduct->farm_practices as $practice)
                            <span class="badge bg-success">{{ ucfirst(str_replace('_', ' ', $practice)) }}</span>
                            @endforeach
                        </div>
                        @endif
                        
                        @if($farmProduct->seasonal_availability)
                        <h6>Seasonal Availability</h6>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($farmProduct->seasonal_availability as $season)
                            <span class="badge bg-info">{{ ucfirst($season) }}</span>
                            @endforeach
                        </div>
                        @endif
                    </div>

                    <!-- Farm Information Tab -->
                    <div class="tab-pane fade" id="farm-information" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Farm Details</h5>
                                <table class="table table-borderless">
                                    <tbody>
                                        <tr>
                                            <th>Farm Name:</th>
                                            <td>{{ $farmProduct->farm_name ?: 'Local Farm' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Farmer Name:</th>
                                            <td>{{ $farmProduct->farmer_name ?: 'Local Farmer' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Farm Location:</th>
                                            <td>{{ $farmProduct->farm_location ?: $farmProduct->location }}</td>
                                        </tr>
                                        <tr>
                                            <th>Farm Size:</th>
                                            <td>{{ $farmProduct->farm_size ? $farmProduct->farm_size . ' ' . ($farmProduct->size_unit ?: 'acres') : 'Not specified' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                                
                                @if($farmProduct->farmer_bio)
                                <h6>Farmer Bio</h6>
                                <p>{{ $farmProduct->farmer_bio }}</p>
                                @endif
                                
                                @if($farmProduct->farm_story)
                                <h6>Farm Story</h6>
                                <p>{{ $farmProduct->farm_story }}</p>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <h5>Certifications & Practices</h5>
                                <table class="table table-borderless">
                                    <tbody>
                                        <tr>
                                            <th>Organic Certified:</th>
                                            <td>{{ $farmProduct->is_organic ? 'Yes' : 'No' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Certification Type:</th>
                                            <td>{{ $farmProduct->certification_type ?: 'Not specified' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Certification Body:</th>
                                            <td>{{ $farmProduct->certification_body ?: 'Not specified' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Pesticide Use:</th>
                                            <td>{{ $farmProduct->pesticide_use ? 'Yes' : 'No (Natural Methods)' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Harvest Method:</th>
                                            <td>{{ $farmProduct->harvest_method ? ucfirst(str_replace('_', ' ', $farmProduct->harvest_method)) : 'Not specified' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Irrigation Method:</th>
                                            <td>{{ $farmProduct->irrigation_method ? ucfirst(str_replace('_', ' ', $farmProduct->irrigation_method)) : 'Not specified' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                                
                                @if($farmProduct->farm_certifications)
                                <h6>Farm Certifications</h6>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($farmProduct->farm_certifications as $cert)
                                    <span class="badge bg-primary">{{ $cert }}</span>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-md-12">
                                @if($farmProduct->farm_tour_available)
                                <div class="alert alert-success">
                                    <i class="fas fa-seedling me-2"></i>
                                    <strong>Farm Visit Available</strong> - Contact the farmer to schedule a visit and see the actual farm!
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Delivery Tab -->
                    <div class="tab-pane fade" id="delivery" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Delivery Options</h5>
                                @if($farmProduct->delivery_options && count($farmProduct->delivery_options) > 0)
                                    <ul class="list-group">
                                        @foreach($farmProduct->delivery_options as $option)
                                            @switch($option)
                                                @case('local_delivery')
                                                    <li class="list-group-item">
                                                        <i class="fas fa-truck text-success me-2"></i>
                                                        <strong>Local Delivery</strong>
                                                        <p class="mb-0 mt-1">Delivery available within {{ $farmProduct->local_delivery_radius ?: '25' }}km radius</p>
                                                    </li>
                                                    @break
                                                @case('pickup')
                                                    <li class="list-group-item">
                                                        <i class="fas fa-store text-success me-2"></i>
                                                        <strong>Pick Up Available</strong>
                                                        <p class="mb-0 mt-1">Visit the farm directly to collect your products</p>
                                                    </li>
                                                    @break
                                                @case('shipping')
                                                    <li class="list-group-item">
                                                        <i class="fas fa-shipping-fast text-success me-2"></i>
                                                        <strong>Nationwide Shipping</strong>
                                                        <p class="mb-0 mt-1">Products shipped nationwide (conditions apply)</p>
                                                    </li>
                                                    @break
                                                @case('express_delivery')
                                                    <li class="list-group-item">
                                                        <i class="fas fa-bolt text-success me-2"></i>
                                                        <strong>Express Delivery</strong>
                                                        <p class="mb-0 mt-1">Priority shipping option</p>
                                                    </li>
                                                    @break
                                                @default
                                                    <li class="list-group-item">
                                                        <i class="fas fa-check-circle text-success me-2"></i>
                                                        <strong>{{ ucfirst(str_replace('_', ' ', $option)) }}</strong>
                                                    </li>
                                            @endswitch
                                        @endforeach
                                    </ul>
                                @else
                                    <p>No specific delivery options specified. Contact farmer for details.</p>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <h5>Payment & Pricing</h5>
                                <div class="card border-0 bg-light p-3">
                                    <table class="table mb-0">
                                        <tbody>
                                            <tr>
                                                <td><strong>Base Price:</strong></td>
                                                <td class="text-end text-success">₦{{ number_format($farmProduct->price, 2) }}</td>
                                            </tr>
                                            @if($farmProduct->minimum_order)
                                            <tr>
                                                <td><strong>Minimum Order:</strong></td>
                                                <td class="text-end">₦{{ number_format($farmProduct->minimum_order, 2) }}</td>
                                            </tr>
                                            @endif
                                            <tr>
                                                <td><strong>Unit:</strong></td>
                                                <td class="text-end">{{ $farmProduct->unit_size ?: 'Per Item/Unit' }}</td>
                                            </tr>
                                            <tr class="border-top">
                                                <td><strong>Total (1 unit):</strong></td>
                                                <td class="text-end text-success fw-bold">₦{{ number_format($farmProduct->price, 2) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                
                                @if($farmProduct->shipping_availability || $farmProduct->supply_capacity)
                                <div class="mt-4">
                                    <h6>Availability Information</h6>
                                    <div>
                                        @if($farmProduct->supply_capacity)
                                        <p><i class="fas fa-box text-success me-2"></i> Supply capacity: Up to {{ $farmProduct->supply_capacity }} units per week</p>
                                        @endif
                                        @if($farmProduct->shipping_availability)
                                        <p><i class="fas fa-truck-loading text-success me-2"></i> Shipping available within {{ $farmProduct->shipping_availability }}km radius</p>
                                        @endif
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Reviews Tab -->
                    <div class="tab-pane fade" id="reviews" role="tabpanel">
                        <h5>Product Reviews</h5>
                        @if($farmProduct->review_count > 0)
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <div class="text-center">
                                        <div class="display-4 text-success">{{ number_format($farmProduct->quality_rating, 1) }}</div>
                                        <div class="text-warning mb-2">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star {{ $i <= floor($farmProduct->quality_rating) ? '' : '-o' }}"></i>
                                            @endfor
                                        </div>
                                        <small>{{ $farmProduct->review_count }} reviews</small>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="d-flex align-items-center mb-1">
                                        <span class="me-2">5 stars</span>
                                        <div class="flex-grow-1">
                                            <div class="progress" style="height: 10px;">
                                                <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $farmProduct->five_star_percent ?? 0 }}%"></div>
                                            </div>
                                        </div>
                                        <span class="ms-2">{{ $farmProduct->five_star_percent ?? 0 }}%</span>
                                    </div>
                                    <div class="d-flex align-items-center mb-1">
                                        <span class="me-2">4 stars</span>
                                        <div class="flex-grow-1">
                                            <div class="progress" style="height: 10px;">
                                                <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $farmProduct->four_star_percent ?? 0 }}%"></div>
                                            </div>
                                        </div>
                                        <span class="ms-2">{{ $farmProduct->four_star_percent ?? 0 }}%</span>
                                    </div>
                                    <div class="d-flex align-items-center mb-1">
                                        <span class="me-2">3 stars</span>
                                        <div class="flex-grow-1">
                                            <div class="progress" style="height: 10px;">
                                                <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $farmProduct->three_star_percent ?? 0 }}%"></div>
                                            </div>
                                        </div>
                                        <span class="ms-2">{{ $farmProduct->three_star_percent ?? 0 }}%</span>
                                    </div>
                                    <div class="d-flex align-items-center mb-1">
                                        <span class="me-2">2 stars</span>
                                        <div class="flex-grow-1">
                                            <div class="progress" style="height: 10px;">
                                                <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $farmProduct->two_star_percent ?? 0 }}%"></div>
                                            </div>
                                        </div>
                                        <span class="ms-2">{{ $farmProduct->two_star_percent ?? 0 }}%</span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <span class="me-2">1 star</span>
                                        <div class="flex-grow-1">
                                            <div class="progress" style="height: 10px;">
                                                <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $farmProduct->one_star_percent ?? 0 }}%"></div>
                                            </div>
                                        </div>
                                        <span class="ms-2">{{ $farmProduct->one_star_percent ?? 0 }}%</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Note:</strong> Reviews and ratings for farm products coming soon. This feature is currently under development.
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-star-half-alt fa-3x text-muted mb-3"></i>
                                <h6>No reviews yet</h6>
                                <p class="text-muted">Be the first to review this farm product!</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Update total price when quantity changes
    document.getElementById('quantity').addEventListener('input', function() {
        const quantity = parseInt(this.value) || 1;
        const unitPrice = parseFloat('{{ $farmProduct->price }}');
        const totalPrice = quantity * unitPrice;
        
        document.getElementById('totalPrice').value = totalPrice.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    });
    
    // Initialize tabs
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-scroll to product details tab if hash is present
        if (window.location.hash) {
            const tabName = window.location.hash.replace('#', '');
            const tabElement = document.getElementById(tabName + '-tab');
            if (tabElement) {
                tabElement.click();
            }
        }
    });
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
    
    .object-fit-cover {
        object-fit: cover;
    }
    
    .img-thumbnail {
        transition: all 0.2s;
    }
    
    .img-thumbnail:hover {
        transform: scale(1.05);
        border-color: #28a745;
    }
</style>
@endsection