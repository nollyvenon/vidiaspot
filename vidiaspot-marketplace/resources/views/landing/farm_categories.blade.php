@extends('layouts.app')

@section('title', 'Farm Categories - Browse Fresh Farm Products')
@section('meta_description', 'Browse farm products by category. Find vegetables, fruits, organic products, dairy, poultry, and more from local farmers.')
@section('meta_keywords', 'farm categories, fresh vegetables, fresh fruits, organic products, dairy products, poultry, farm to table, local food')

@section('content')
<div class="container-fluid">
    <!-- Farm Categories Hero Section -->
    <section class="hero-section bg-success text-white py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="display-4 fw-bold mb-3">Farm Product Categories</h1>
                    <p class="lead mb-4">Browse fresh farm products by category. Find the best local, organic, and sustainable products.</p>
                </div>
                <div class="col-md-6 text-center">
                    <img src="https://images.unsplash.com/photo-1464226184884-fa280b7dd3bb?auto=format&fit=crop&w=600&h=400&q=80" alt="Farm Categories" class="img-fluid rounded shadow-lg" style="border-radius: 10px !important;">
                </div>
            </div>
        </div>
    </section>

    <!-- Farm Categories Grid -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2 class="fw-bold">Browse by Category</h2>
                    <p class="text-muted">Find the freshest products from local farms</p>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <a href="/farm-products?category=fresh-vegetables" class="text-decoration-none">
                        <div class="card h-100 farm-category-card border-0 shadow-sm">
                            <div class="position-relative" style="height: 200px; overflow: hidden;">
                                <img src="https://images.unsplash.com/photo-1504672281656-e4981d709d52?auto=format&fit=crop&w=400&h=200&q=80" class="card-img-top img-fluid object-fit-cover" alt="Fresh Vegetables" style="height: 200px; object-fit: cover;">
                                <div class="card-img-overlay d-flex align-items-end bg-gradient-to-t from-black/60 to-transparent">
                                    <h5 class="card-title text-white m-0">Fresh Vegetables</h5>
                                </div>
                            </div>
                            <div class="card-body text-center p-3">
                                <p class="card-text text-muted mb-0">Tomatoes, Peppers, Carrots, Spinach</p>
                                <span class="badge bg-success">{{ rand(10, 100) }} products</span>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <a href="/farm-products?category=fresh-fruits" class="text-decoration-none">
                        <div class="card h-100 farm-category-card border-0 shadow-sm">
                            <div class="position-relative" style="height: 200px; overflow: hidden;">
                                <img src="https://images.unsplash.com/photo-1596466596120-2a8e4b5d5b5d?auto=format&fit=crop&w=400&h=200&q=80" class="card-img-top img-fluid object-fit-cover" alt="Fresh Fruits" style="height: 200px; object-fit: cover;">
                                <div class="card-img-overlay d-flex align-items-end bg-gradient-to-t from-black/60 to-transparent">
                                    <h5 class="card-title text-white m-0">Fresh Fruits</h5>
                                </div>
                            </div>
                            <div class="card-body text-center p-3">
                                <p class="card-text text-muted mb-0">Oranges, Bananas, Pineapples, Mangoes</p>
                                <span class="badge bg-success">{{ rand(5, 80) }} products</span>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <a href="/farm-products?category=organic-products" class="text-decoration-none">
                        <div class="card h-100 farm-category-card border-0 shadow-sm">
                            <div class="position-relative" style="height: 200px; overflow: hidden;">
                                <img src="https://images.unsplash.com/photo-1500462918059-b1a7cb50743d?auto=format&fit=crop&w=400&h=200&q=80" class="card-img-top img-fluid object-fit-cover" alt="Organic Products" style="height: 200px; object-fit: cover;">
                                <div class="card-img-overlay d-flex align-items-end bg-gradient-to-t from-black/60 to-transparent">
                                    <h5 class="card-title text-white m-0">Organic Products</h5>
                                </div>
                            </div>
                            <div class="card-body text-center p-3">
                                <p class="card-text text-muted mb-0">Certified organic vegetables and fruits</p>
                                <span class="badge bg-success">{{ rand(15, 120) }} products</span>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <a href="/farm-products?category=dairy-products" class="text-decoration-none">
                        <div class="card h-100 farm-category-card border-0 shadow-sm">
                            <div class="position-relative" style="height: 200px; overflow: hidden;">
                                <img src="https://images.unsplash.com/photo-1563636619-e9b1b9b0d7a6?auto=format&fit=crop&w=400&h=200&q=80" class="card-img-top img-fluid object-fit-cover" alt="Dairy Products" style="height: 200px; object-fit: cover;">
                                <div class="card-img-overlay d-flex align-items-end bg-gradient-to-t from-black/60 to-transparent">
                                    <h5 class="card-title text-white m-0">Dairy Products</h5>
                                </div>
                            </div>
                            <div class="card-body text-center p-3">
                                <p class="card-text text-muted mb-0">Fresh milk, cheese, butter, yogurt</p>
                                <span class="badge bg-success">{{ rand(8, 60) }} products</span>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <a href="/farm-products?category=poultry-eggs" class="text-decoration-none">
                        <div class="card h-100 farm-category-card border-0 shadow-sm">
                            <div class="position-relative" style="height: 200px; overflow: hidden;">
                                <img src="https://images.unsplash.com/photo-1592878905662-3ad867af5c9e?auto=format&fit=crop&w=400&h=200&q=80" class="card-img-top img-fluid object-fit-cover" alt="Poultry & Eggs" style="height: 200px; object-fit: cover;">
                                <div class="card-img-overlay d-flex align-items-end bg-gradient-to-t from-black/60 to-transparent">
                                    <h5 class="card-title text-white m-0">Poultry & Eggs</h5>
                                </div>
                            </div>
                            <div class="card-body text-center p-3">
                                <p class="card-text text-muted mb-0">Fresh eggs, chicken, turkey</p>
                                <span class="badge bg-success">{{ rand(12, 75) }} products</span>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <a href="/farm-products?category=fresh-herbs" class="text-decoration-none">
                        <div class="card h-100 farm-category-card border-0 shadow-sm">
                            <div class="position-relative" style="height: 200px; overflow: hidden;">
                                <img src="https://images.unsplash.com/photo-1592878905662-5803b6cefcdb?auto=format&fit=crop&w=400&h=200&q=80" class="card-img-top img-fluid object-fit-cover" alt="Fresh Herbs" style="height: 200px; object-fit: cover;">
                                <div class="card-img-overlay d-flex align-items-end bg-gradient-to-t from-black/60 to-transparent">
                                    <h5 class="card-title text-white m-0">Fresh Herbs</h5>
                                </div>
                            </div>
                            <div class="card-body text-center p-3">
                                <p class="card-text text-muted mb-0">Basil, parsley, mint, coriander</p>
                                <span class="badge bg-success">{{ rand(5, 40) }} products</span>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <a href="/farm-products?category=grains-cereals" class="text-decoration-none">
                        <div class="card h-100 farm-category-card border-0 shadow-sm">
                            <div class="position-relative" style="height: 200px; overflow: hidden;">
                                <img src="https://images.unsplash.com/photo-1592878905662-5803b6cefcdb?auto=format&fit=crop&w=400&h=200&q=80" class="card-img-top img-fluid object-fit-cover" alt="Grains & Cereals" style="height: 200px; object-fit: cover;">
                                <div class="card-img-overlay d-flex align-items-end bg-gradient-to-t from-black/60 to-transparent">
                                    <h5 class="card-title text-white m-0">Grains & Cereals</h5>
                                </div>
                            </div>
                            <div class="card-body text-center p-3">
                                <p class="card-text text-muted mb-0">Rice, maize, wheat, millet</p>
                                <span class="badge bg-success">{{ rand(20, 90) }} products</span>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <a href="/farm-products?category=livestock" class="text-decoration-none">
                        <div class="card h-100 farm-category-card border-0 shadow-sm">
                            <div class="position-relative" style="height: 200px; overflow: hidden;">
                                <img src="https://images.unsplash.com/photo-1592878905662-5803b6cefcdb?auto=format&fit=crop&w=400&h=200&q=80" class="card-img-top img-fluid object-fit-cover" alt="Livestock" style="height: 200px; object-fit: cover;">
                                <div class="card-img-overlay d-flex align-items-end bg-gradient-to-t from-black/60 to-transparent">
                                    <h5 class="card-title text-white m-0">Livestock</h5>
                                </div>
                            </div>
                            <div class="card-body text-center p-3">
                                <p class="card-text text-muted mb-0">Cattle, goats, sheep, pigs</p>
                                <span class="badge bg-success">{{ rand(3, 45) }} products</span>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Farm Certification Types -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2 class="fw-bold">Farm Certification Types</h2>
                    <p class="text-muted">Discover products from certified farms</p>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-3">
                    <div class="card text-center h-100 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
                                <i class="fas fa-certificate fa-2x text-success"></i>
                            </div>
                            <h5>Organic Certified</h5>
                            <p class="text-muted">Grown without synthetic pesticides or fertilizers</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center h-100 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
                                <i class="fas fa-leaf fa-2x text-success"></i>
                            </div>
                            <h5>Sustainable Farming</h5>
                            <p class="text-muted">Environmentally friendly farming practices</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center h-100 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
                                <i class="fas fa-handshake fa-2x text-success"></i>
                            </div>
                            <h5>Fair Trade</h5>
                            <p class="text-muted">Ethical farming practices ensuring fair wages</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center h-100 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
                                <i class="fas fa-award fa-2x text-success"></i>
                            </div>
                            <h5>Quality Assured</h5>
                            <p class="text-muted">High standards for freshness and taste</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How to Buy from Local Farms -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2 class="fw-bold">How to Buy from Local Farms</h2>
                    <p class="text-muted">Simple steps to enjoy fresh, local products</p>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="text-center">
                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-search fa-2x"></i>
                        </div>
                        <h5>Browse Local Farms</h5>
                        <p class="text-muted">Find farms in your area with fresh products available</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center">
                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-shopping-cart fa-2x"></i>
                        </div>
                        <h5>Select Products</h5>
                        <p class="text-muted">Choose from fresh, seasonal farm products</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center">
                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-truck fa-2x"></i>
                        </div>
                        <h5>Get Delivered</h5>
                        <p class="text-muted">Receive fresh products from farm to table</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Top Farm Sellers -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2 class="fw-bold">Top Farm Sellers</h2>
                    <p class="text-muted">Trusted farmers in your community</p>
                </div>
            </div>

            <div class="row g-4">
                @for($i = 0; $i < 4; $i++)
                <div class="col-md-3">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 100px; height: 100px;">
                                <i class="fas fa-user fa-3x text-success"></i>
                            </div>
                            <h5 class="card-title">Green Valley Farm</h5>
                            <p class="text-muted mb-0">Lagos, Nigeria</p>
                            <div class="mt-2">
                                <small class="text-muted"><i class="fas fa-star text-warning"></i> 4.8 ({{ rand(100, 500) }}+ reviews)</small>
                            </div>
                            <div class="mt-2">
                                <span class="badge bg-success">Organic Certified</span>
                            </div>
                            <div class="mt-3">
                                <a href="/farmers/1" class="btn btn-outline-success btn-sm">View Products</a>
                            </div>
                        </div>
                    </div>
                </div>
                @endfor
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

    .object-fit-cover {
        object-fit: cover;
    }

    .bg-gradient-to-t {
        background: linear-gradient(to top, rgba(0,0,0,0.6), transparent);
    }

    .from-black\/60 {
        background: linear-gradient(to top, rgba(0,0,0,0.6), transparent);
    }

    .card {
        border: none;
    }
</style>
@endsection