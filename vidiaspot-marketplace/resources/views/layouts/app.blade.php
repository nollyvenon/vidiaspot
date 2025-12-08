<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Vidiaspot Marketplace - Buy and Sell Near You')</title>
    <meta name="description" content="@yield('meta_description', 'Find great deals and sell items near you. The easiest way to buy and sell used goods, cars, jobs and services.')">
    <meta name="keywords" content="@yield('meta_keywords', 'marketplace, buy, sell, classified ads, used items, cars, jobs')">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    @yield('styles')
    <style>
        :root {
            --primary-color: #388e3c;
            --secondary-color: #0069d9;
            --accent-color: #ff9800;
        }

        .navbar {
            box-shadow: 0 2px 4px rgba(0,0,0,.1);
        }

        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('https://images.unsplash.com/photo-1504805572947-34fad45aed93?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80') center/cover;
            height: 500px;
            display: flex;
            align-items: center;
            color: white;
        }

        .search-section {
            background-color: var(--primary-color);
            padding: 2rem 0;
        }

        .category-card {
            transition: transform 0.3s, box-shadow 0.3s;
            height: 100%;
            border-radius: 8px;
            overflow: hidden;
        }

        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }

        .category-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .ad-card {
            transition: transform 0.3s;
            height: 100%;
        }

        .ad-card:hover {
            transform: translateY(-3px);
        }

        .featured-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 10;
        }

        .footer {
            background-color: #f8f9fa;
            padding: 3rem 0 1rem;
        }

        .quick-links {
            list-style: none;
            padding: 0;
        }

        .quick-links li {
            margin-bottom: 0.5rem;
        }

        .quick-links a {
            text-decoration: none;
            color: #6c757d;
        }

        .quick-links a:hover {
            color: var(--primary-color);
            text-decoration: underline;
        }

        .trending-tag {
            background-color: var(--accent-color);
            color: white;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: #2e7d32;
            border-color: #2e7d32;
        }

        .search-form {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/">
                <i class="fas fa-store"></i> VIDIASPOT
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('ads') ? 'active' : '' }}" href="/ads">All Ads</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="categoriesDropdown" role="button" data-bs-toggle="dropdown">
                            Categories
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="/category/electronics">Electronics</a></li>
                            <li><a class="dropdown-item" href="/category/vehicles">Vehicles</a></li>
                            <li><a class="dropdown-item" href="/category/furniture">Furniture</a></li>
                            <li><a class="dropdown-item" href="/category/property">Property</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="/categories">View All Categories</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('trending') ? 'active' : '' }}" href="/trending">Trending</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('about') ? 'active' : '' }}" href="/about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('contact') ? 'active' : '' }}" href="/contact">Contact</a>
                    </li>
                </ul>

                <div class="d-flex">
                    @auth
                        <a href="/dashboard" class="btn btn-outline-secondary me-2">Dashboard</a>
                        <a href="/create-ad" class="btn btn-success">Post Ad</a>
                    @else
                        <a href="/login" class="btn btn-outline-primary me-2">Login</a>
                        <a href="/register" class="btn btn-primary">Sign Up</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer mt-auto py-3">
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <h5>Vidiaspot</h5>
                    <p>Your trusted marketplace for buying and selling goods and services in Nigeria.</p>
                    <div class="social-icons">
                        <a href="#" class="text-decoration-none me-2"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="text-decoration-none me-2"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-decoration-none me-2"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-decoration-none"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>
                <div class="col-md-3">
                    <h5>Quick Links</h5>
                    <ul class="quick-links">
                        <li><a href="/about">About Us</a></li>
                        <li><a href="/contact">Contact</a></li>
                        <li><a href="/help">Help & FAQ</a></li>
                        <li><a href="/safety">Safety Tips</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5>For Users</h5>
                    <ul class="quick-links">
                        <li><a href="/how-it-works">How It Works</a></li>
                        <li><a href="/pricing">Pricing</a></li>
                        <li><a href="/my-ads">My Ads</a></li>
                        <li><a href="/saved-searches">Saved Searches</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5>Contact Info</h5>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-map-marker-alt me-2"></i> Lagos, Nigeria</li>
                        <li><i class="fas fa-phone me-2"></i> +234 800 000 000</li>
                        <li><i class="fas fa-envelope me-2"></i> support@vidiaspot.ng</li>
                    </ul>
                </div>
            </div>
            <hr>
            <div class="text-center">
                <p>&copy; 2025 Vidiaspot Marketplace. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    @yield('scripts')
    <script>
        // Smooth scrolling for anchor links
        document.addEventListener('DOMContentLoaded', function() {
            const scrollLinks = document.querySelectorAll('a[href^="#"]');
            for (const link of scrollLinks) {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('href');
                    const targetSection = document.querySelector(targetId);
                    if (targetSection) {
                        targetSection.scrollIntoView({
                            behavior: 'smooth'
                        });
                    }
                });
            }
        });
    </script>
    @stack('scripts')
</body>
</html>