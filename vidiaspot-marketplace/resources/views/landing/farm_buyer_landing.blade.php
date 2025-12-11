@extends('layouts.app')

@section('title', 'Farm Buyer - Buy Fresh Products Directly from Farmers')
@section('meta_description', 'Buy fresh farm products directly from local farmers. Discover organic vegetables, fruits, dairy, and more from trusted farms in your area.')
@section('meta_keywords', 'farm products, fresh vegetables, organic food, buy from farmers, farm to table, fresh fruits, dairy products, local food')

@section('content')
<div class="container-fluid">
    <!-- Farm Hero Section -->
    <section class="hero-section bg-success text-white py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="display-4 fw-bold mb-3">Fresh From the Farm to You</h1>
                    <p class="lead mb-4">Buy fresh, organic farm products directly from local farmers. Know your farmer, trust your food.</p>
                    <div class="d-flex gap-3">
                        <a href="#farm-products" class="btn btn-light btn-lg text-success fw-bold">Shop Now</a>
                        <a href="/register" class="btn btn-outline-light btn-lg">Become a Member</a>
                    </div>
                </div>
                <div class="col-md-6 text-center">
                    <img src="https://images.unsplash.com/photo-1464226184884-fa280b7dd3bb?auto=format&fit=crop&w=600&h=500&q=80" alt="Fresh Farm Products" class="img-fluid rounded shadow-lg" style="border-radius: 10px !important;">
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose Farm Products -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2 class="fw-bold">Why Shop Direct from Farms?</h2>
                    <p class="text-muted">Discover the benefits of farm-fresh products</p>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-md-4 text-center">
                    <div class="card h-100 border-0">
                        <div class="card-body">
                            <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center p-4 mb-3">
                                <i class="fas fa-leaf text-success fa-3x"></i>
                            </div>
                            <h5 class="card-title">100% Organic & Fresh</h5>
                            <p class="card-text">Products picked at peak ripeness, delivered fresh from farm to your doorstep.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-center">
                    <div class="card h-100 border-0">
                        <div class="card-body">
                            <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center p-4 mb-3">
                                <i class="fas fa-hand-holding-heart text-success fa-3x"></i>
                            </div>
                            <h5 class="card-title">Support Local Farmers</h5>
                            <p class="card-text">Buy directly from farmers, ensuring fair compensation and sustainable farming.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-center">
                    <div class="card h-100 border-0">
                        <div class="card-body">
                            <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center p-4 mb-3">
                                <i class="fas fa-shield-alt text-success fa-3x"></i>
                            </div>
                            <h5 class="card-title">Traceable & Safe</h5>
                            <p class="card-text">Know exactly where your food comes from with transparent farm profiles.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Farm Product Categories -->
    <section id="farm-products" class="py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2 class="fw-bold">Farm Product Categories</h2>
                    <p class="text-muted">Browse our wide variety of fresh farm products</p>
                </div>
            </div>
            
            <div class="row g-4">
                <div class="col-lg-3 col-md-6">
                    <a href="/farm-products?category=fresh-vegetables" class="text-decoration-none">
                        <div class="card h-100 farm-category-card border-0 shadow-sm">
                            <div class="position-relative overflow-hidden" style="height: 200px;">
                                <img src="https://images.unsplash.com/photo-1596466596120-2a8e4b5d5b5d?auto=format&fit=crop&w=400&h=200&q=80" class="card-img-top img-fluid object-fit-cover" alt="Fresh Vegetables">
                                <div class="card-img-overlay d-flex align-items-end bg-gradient-to-t from-black/60 to-transparent">
                                    <h5 class="card-title text-white ms-3 mb-3">Fresh Vegetables</h5>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-3 col-md-6">
                    <a href="/farm-products?category=fresh-fruits" class="text-decoration-none">
                        <div class="card h-100 farm-category-card border-0 shadow-sm">
                            <div class="position-relative overflow-hidden" style="height: 200px;">
                                <img src="https://images.unsplash.com/photo-1481349518771-20055b2a7b24?auto=format&fit=crop&w=400&h=200&q=80" class="card-img-top img-fluid object-fit-cover" alt="Fresh Fruits">
                                <div class="card-img-overlay d-flex align-items-end bg-gradient-to-t from-black/60 to-transparent">
                                    <h5 class="card-title text-white ms-3 mb-3">Fresh Fruits</h5>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-3 col-md-6">
                    <a href="/farm-products?category=organic-products" class="text-decoration-none">
                        <div class="card h-100 farm-category-card border-0 shadow-sm">
                            <div class="position-relative overflow-hidden" style="height: 200px;">
                                <img src="https://images.unsplash.com/photo-1500462918059-b1a7cb50743d?auto=format&fit=crop&w=400&h=200&q=80" class="card-img-top img-fluid object-fit-cover" alt="Organic Products">
                                <div class="card-img-overlay d-flex align-items-end bg-gradient-to-t from-black/60 to-transparent">
                                    <h5 class="card-title text-white ms-3 mb-3">Organic Products</h5>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-3 col-md-6">
                    <a href="/farm-products?category=dairy-products" class="text-decoration-none">
                        <div class="card h-100 farm-category-card border-0 shadow-sm">
                            <div class="position-relative overflow-hidden" style="height: 200px;">
                                <img src="https://images.unsplash.com/photo-1555446413-cc6f79ca8489?auto=format&fit=crop&w=400&h=200&q=80" class="card-img-top img-fluid object-fit-cover" alt="Dairy Products">
                                <div class="card-img-overlay d-flex align-items-end bg-gradient-to-t from-black/60 to-transparent">
                                    <h5 class="card-title text-white ms-3 mb-3">Dairy Products</h5>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-3 col-md-6">
                    <a href="/farm-products?category=poultry-eggs" class="text-decoration-none">
                        <div class="card h-100 farm-category-card border-0 shadow-sm">
                            <div class="position-relative overflow-hidden" style="height: 200px;">
                                <img src="https://images.unsplash.com/photo-1603894584373-5ac82b2ae568?auto=format&fit=crop&w=400&h=200&q=80" class="card-img-top img-fluid object-fit-cover" alt="Poultry & Eggs">
                                <div class="card-img-overlay d-flex align-items-end bg-gradient-to-t from-black/60 to-transparent">
                                    <h5 class="card-title text-white ms-3 mb-3">Poultry & Eggs</h5>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-3 col-md-6">
                    <a href="/farm-products?category=fresh-herbs" class="text-decoration-none">
                        <div class="card h-100 farm-category-card border-0 shadow-sm">
                            <div class="position-relative overflow-hidden" style="height: 200px;">
                                <img src="https://images.unsplash.com/photo-1592878905662-5803b6cefcdb?auto=format&fit=crop&w=400&h=200&q=80" class="card-img-top img-fluid object-fit-cover" alt="Fresh Herbs">
                                <div class="card-img-overlay d-flex align-items-end bg-gradient-to-t from-black/60 to-transparent">
                                    <h5 class="card-title text-white ms-3 mb-3">Fresh Herbs</h5>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-3 col-md-6">
                    <a href="/farm-products?category=grains-cereals" class="text-decoration-none">
                        <div class="card h-100 farm-category-card border-0 shadow-sm">
                            <div class="position-relative overflow-hidden" style="height: 200px;">
                                <img src="https://images.unsplash.com/photo-1601565007409-0382cfec05bc?auto=format&fit=crop&w=400&h=200&q=80" class="card-img-top img-fluid object-fit-cover" alt="Grains & Cereals">
                                <div class="card-img-overlay d-flex align-items-end bg-gradient-to-t from-black/60 to-transparent">
                                    <h5 class="card-title text-white ms-3 mb-3">Grains & Cereals</h5>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-3 col-md-6">
                    <a href="/farm-products?category=livestock" class="text-decoration-none">
                        <div class="card h-100 farm-category-card border-0 shadow-sm">
                            <div class="position-relative overflow-hidden" style="height: 200px;">
                                <img src="https://images.unsplash.com/photo-1592406096470-3b513a092300?auto=format&fit=crop&w=400&h=200&q=80" class="card-img-top img-fluid object-fit-cover" alt="Livestock">
                                <div class="card-img-overlay d-flex align-items-end bg-gradient-to-t from-black/60 to-transparent">
                                    <h5 class="card-title text-white ms-3 mb-3">Livestock</h5>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Farm Products -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2 class="fw-bold">Featured Farm Products</h2>
                    <p class="text-muted">Today's freshest picks from our partner farms</p>
                </div>
            </div>
            
            <div class="row g-4">
                @for($i = 0; $i < 6; $i++)
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <div class="card h-100 product-card border-0 shadow-sm">
                        <div class="position-relative">
                            <img src="https://images.unsplash.com/photo-1603569283847-aa795beeabf1?auto=format&fit=crop&w=300&h=200&q=80" class="card-img-top img-fluid object-fit-cover" alt="Farm Product" style="height: 150px;">
                            <div class="position-absolute top-0 end-0 m-2">
                                <span class="badge bg-success">Organic</span>
                            </div>
                        </div>
                        <div class="card-body p-3">
                            <h6 class="card-title mb-1">Organic Tomatoes</h6>
                            <p class="card-text text-muted small mb-1">Vine-ripened, juicy</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-success">₦1,200</span>
                                <small class="text-muted"><i class="fas fa-map-marker-alt"></i> 5km</small>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <small class="text-muted"><i class="fas fa-star text-warning"></i> 4.8</small>
                                <small class="text-muted">1 day old</small>
                            </div>
                        </div>
                    </div>
                </div>
                @endfor
            </div>
            
            <div class="text-center mt-4">
                <a href="/farm-products" class="btn btn-outline-success btn-lg">View All Farm Products</a>
            </div>
        </div>
    </section>

    <!-- How It Works for Farm Buyers -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2 class="fw-bold">How to Shop from Local Farms</h2>
                    <p class="text-muted">Simple steps to enjoy fresh farm products</p>
                </div>
            </div>
            
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 60px; height: 60px;">
                            <i class="fas fa-search fa-xl"></i>
                        </div>
                        <h5>Find Nearby Farms</h5>
                        <p class="text-muted">Browse farms in your area with fresh products</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 60px; height: 60px;">
                            <i class="fas fa-shopping-cart fa-xl"></i>
                        </div>
                        <h5>Select Products</h5>
                        <p class="text-muted">Choose fresh, organic products</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 60px; height: 60px;">
                            <i class="fas fa-truck fa-xl"></i>
                        </div>
                        <h5>Fast Delivery</h5>
                        <p class="text-muted">Get products delivered fresh to your door</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 60px; height: 60px;">
                            <i class="fas fa-utensils fa-xl"></i>
                        </div>
                        <h5>Enjoy Freshness</h5>
                        <p class="text-muted">Taste the difference of fresh, local produce</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Farmer Spotlight -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2 class="fw-bold">Farmer Spotlight</h2>
                    <p class="text-muted">Meet our dedicated farmers</p>
                </div>
            </div>
            
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="card border-0 shadow">
                        <div class="row g-0 align-items-center">
                            <div class="col-md-4">
                                <img src="https://images.unsplash.com/photo-1501482045694-bd355e6d1d6d?auto=format&fit=crop&w=400&h=300&q=80" class="img-fluid rounded-start" alt="Farmer John Doe">
                            </div>
                            <div class="col-md-8">
                                <div class="card-body p-5">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <h4 class="card-title mb-0">Green Valley Organic Farm</h4>
                                        <span class="badge bg-success">Certified Organic</span>
                                    </div>
                                    <p class="card-text text-muted mb-3">
                                        <i class="fas fa-map-marker-alt text-success me-1"></i>
                                        Ibeju-Lekki, Lagos
                                    </p>
                                    <p class="card-text">"We've been growing organic vegetables for over 5 years, focusing on sustainable practices and environmental conservation."</p>
                                    <div class="d-flex gap-3 mt-3">
                                        <span class="text-muted"><i class="fas fa-leaf text-success"></i> 12 Years Experience</span>
                                        <span class="text-muted"><i class="fas fa-star text-warning"></i> 4.9 Rating</span>
                                        <span class="text-muted"><i class="fas fa-check-circle text-success"></i> Verified</span>
                                    </div>
                                    <div class="mt-3">
                                        <a href="#" class="btn btn-outline-success">View Products</a>
                                        <a href="#" class="btn btn-success">Contact Farmer</a>
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
    <section class="py-5 bg-success text-white">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h3 class="fw-bold mb-2">Download Our App for the Best Farm Experience</h3>
                    <p class="opacity-75 mb-4">Get notifications for fresh products, track your orders, and connect directly with farmers.</p>
                    <div class="d-flex gap-3">
                        <a href="#" class="btn btn-light text-success fw-bold">
                            <i class="fab fa-google-play me-2"></i> Google Play
                        </a>
                        <a href="#" class="btn btn-outline-light">
                            <i class="fab fa-apple me-2"></i> App Store
                        </a>
                    </div>
                </div>
                <div class="col-md-4 text-center">
                    <img src="https://placehold.co/200x400/ffffff/000000?text=Farm+App" alt="VidiaSpot Farm App" class="img-fluid" style="max-height: 150px;">
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
    
    .from-black\/60 {
        background-image: linear-gradient(to top, rgba(0,0,0,0.6), transparent);
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