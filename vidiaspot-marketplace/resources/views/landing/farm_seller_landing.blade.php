@extends('layouts.app')

@section('title', 'Farm Seller - Sell Your Farm Products Directly to Customers')
@section('meta_description', 'Join our farm-to-consumer marketplace. Sell your farm products directly to customers, increase profits, and connect with buyers.')
@section('meta_keywords', 'sell farm products, farm to consumer, farm marketplace, direct farm sales, sell vegetables, sell fruits, organic products, farmers market')

@section('content')
<div class="container-fluid">
    <!-- Farm Seller Hero Section -->
    <section class="hero-section bg-success text-white py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="display-4 fw-bold mb-3">Sell Your Farm Products Directly</h1>
                    <p class="lead mb-4">Grow your farm business by selling directly to consumers. Cut out middlemen and earn up to 40% more profit.</p>
                    <div class="d-flex gap-3">
                        <a href="#how-it-works" class="btn btn-light btn-lg text-success fw-bold">Learn More</a>
                        <a href="/register" class="btn btn-outline-light btn-lg">Start Selling</a>
                    </div>
                </div>
                <div class="col-md-6 text-center">
                    <img src="https://images.unsplash.com/photo-1464226184884-fa280b7dd3bb?auto=format&fit=crop&w=600&h=400&q=80" alt="Farm Seller" class="img-fluid rounded shadow-lg" style="border-radius: 10px !important;">
                </div>
            </div>
        </div>
    </section>

    <!-- Benefits for Farm Sellers -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2 class="fw-bold">Why Sell Through Our Platform?</h2>
                    <p class="text-muted">Maximize your profits and reach more customers</p>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-md-4 text-center">
                    <div class="card h-100 border-0">
                        <div class="card-body">
                            <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center p-4 mx-auto mb-3" style="width: 100px; height: 100px;">
                                <i class="fas fa-percentage text-success fa-3x"></i>
                            </div>
                            <h5 class="card-title">Higher Profits</h5>
                            <p class="card-text">Earn more by selling directly to consumers without intermediaries taking a cut.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-center">
                    <div class="card h-100 border-0">
                        <div class="card-body">
                            <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center p-4 mx-auto mb-3" style="width: 100px; height: 100px;">
                                <i class="fas fa-users text-success fa-3x"></i>
                            </div>
                            <h5 class="card-title">Broader Reach</h5>
                            <p class="card-text">Connect with customers beyond your local area who value quality, fresh, and sustainably grown products.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-center">
                    <div class="card h-100 border-0">
                        <div class="card-body">
                            <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center p-4 mx-auto mb-3" style="width: 100px; height: 100px;">
                                <i class="fas fa-chart-line text-success fa-3x"></i>
                            </div>
                            <h5 class="card-title">Business Growth</h5>
                            <p class="card-text">Scale your farming business with access to analytics, inventory management, and customer insights.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works for Farm Sellers -->
    <section id="how-it-works" class="py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2 class="fw-bold">How Farm Selling Works</h2>
                    <p class="text-muted">Simple steps to start selling your farm products</p>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-user-plus fa-xl"></i>
                        </div>
                        <h5>Register Your Farm</h5>
                        <p class="text-muted">Create your farm profile and verify your farm details</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-box-open fa-xl"></i>
                        </div>
                        <h5>Add Products</h5>
                        <p class="text-muted">Upload photos and details of your farm products</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-tags fa-xl"></i>
                        </div>
                        <h5>Set Pricing</h5>
                        <p class="text-muted">Set competitive prices and delivery options</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-dollar-sign fa-xl"></i>
                        </div>
                        <h5>Start Selling</h5>
                        <p class="text-muted">Get orders and grow your direct-to-consumer business</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Farm Product Features -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2 class="fw-bold">Features for Farm Sellers</h2>
                    <p class="text-muted">Everything you need to run your farm business</p>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="card h-100 border-0 shadow-sm p-4">
                        <div class="row g-4">
                            <div class="col-md-2 text-center">
                                <i class="fas fa-bar-chart text-success fa-3x"></i>
                            </div>
                            <div class="col-md-10">
                                <h5>Business Analytics</h5>
                                <p class="text-muted">Track sales, customer demographics, and product performance to make data-driven decisions.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card h-100 border-0 shadow-sm p-4">
                        <div class="row g-4">
                            <div class="col-md-2 text-center">
                                <i class="fas fa-truck text-success fa-3x"></i>
                            </div>
                            <div class="col-md-10">
                                <h5>Flexible Delivery</h5>
                                <p class="text-muted">Offer pickup, local delivery, or shipping options that work best for your farm and customers.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card h-100 border-0 shadow-sm p-4">
                        <div class="row g-4">
                            <div class="col-md-2 text-center">
                                <i class="fas fa-certificate text-success fa-3x"></i>
                            </div>
                            <div class="col-md-10">
                                <h5>Certification Showcase</h5>
                                <p class="text-muted">Display your farm certifications (organic, sustainable, etc.) to build customer trust.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card h-100 border-0 shadow-sm p-4">
                        <div class="row g-4">
                            <div class="col-md-2 text-center">
                                <i class="fas fa-comment-dots text-success fa-3x"></i>
                            </div>
                            <div class="col-md-10">
                                <h5>Direct Customer Communication</h5>
                                <p class="text-muted">Connect directly with customers, answer questions, and build loyal relationships.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Farm Seller Success Stories -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2 class="fw-bold">Success Stories from Farmers</h2>
                    <p class="text-muted">Real farmers making a difference</p>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="Farmer" class="rounded-circle me-3" width="60" height="60">
                                <div>
                                    <h6 class="mb-0">Sarah Johnson</h6>
                                    <p class="text-muted mb-0 small">Organic Vegetable Farmer</p>
                                </div>
                            </div>
                            <p class="card-text text-muted">"Since joining this platform, my farm's monthly revenue has increased by 40%. I love connecting directly with customers who appreciate quality produce."</p>
                            <div class="d-flex">
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Farmer" class="rounded-circle me-3" width="60" height="60">
                                <div>
                                    <h6 class="mb-0">Michael Chen</h6>
                                    <p class="text-muted mb-0 small">Fruit Orchard Owner</p>
                                </div>
                            </div>
                            <p class="card-text text-muted">"The analytics tools helped me understand which products sell best. My customer base has expanded significantly beyond my local market."</p>
                            <div class="d-flex">
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <img src="https://randomuser.me/api/portraits/women/68.jpg" alt="Farmer" class="rounded-circle me-3" width="60" height="60">
                                <div>
                                    <h6 class="mb-0">Elena Rodriguez</h6>
                                    <p class="text-muted mb-0 small">Herb & Spice Farmer</p>
                                </div>
                            </div>
                            <p class="card-text text-muted">"I can now sell my premium herbs to customers across the region. The platform has connected me with chefs and health-conscious consumers."</p>
                            <div class="d-flex">
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Farm Certification Section -->
    <section class="py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 mb-4 mb-md-0">
                    <h2 class="fw-bold mb-3">Boost Your Credibility with Certifications</h2>
                    <p class="text-muted">Highlight your farm's certifications and sustainable practices to attract more customers and command premium prices.</p>

                    <div class="d-flex flex-wrap gap-3 mt-4">
                        <div class="d-flex align-items-center">
                            <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 30px; height: 30px;">
                                <i class="fas fa-check text-success"></i>
                            </div>
                            <span>Organic Certification</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 30px; height: 30px;">
                                <i class="fas fa-check text-success"></i>
                            </div>
                            <span>Sustainable Farming</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 30px; height: 30px;">
                                <i class="fas fa-check text-success"></i>
                            </div>
                            <span>Local Grown</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 30px; height: 30px;">
                                <i class="fas fa-check text-success"></i>
                            </div>
                            <span>Fair Trade</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow">
                        <div class="card-body p-4">
                            <h5 class="card-title mb-3">Farm Profile Showcase</h5>
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 25px; height: 25px;">
                                            <i class="fas fa-check text-success fs-6"></i>
                                        </div>
                                        <small>Verified</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 25px; height: 25px;">
                                            <i class="fas fa-check text-success fs-6"></i>
                                        </div>
                                        <small>Certified Organic</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 25px; height: 25px;">
                                            <i class="fas fa-check text-success fs-6"></i>
                                        </div>
                                        <small>Sustainable Practices</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 25px; height: 25px;">
                                            <i class="fas fa-check text-success fs-6"></i>
                                        </div>
                                        <small>5 Years Experience</small>
                                    </div>
                                </div>
                            </div>
                            <div class="progress mb-2" style="height: 8px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: 85%;">
                                    85% Trust Score
                                </div>
                            </div>
                            <small class="text-muted">Farm Trust Score: 8.5/10</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="py-5 bg-success text-white">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h3 class="fw-bold mb-2">Ready to Start Selling Your Farm Products?</h3>
                    <p class="opacity-75 mb-4">Join thousands of farmers already growing their businesses with direct-to-consumer sales.</p>
                    <div class="d-flex gap-3">
                        <a href="/register" class="btn btn-light text-success fw-bold px-4">Register as Farm Seller</a>
                        <a href="/contact" class="btn btn-outline-light px-4">Contact Sales Team</a>
                    </div>
                </div>
                <div class="col-md-4 text-center">
                    <div class="bg-white bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 120px; height: 120px;">
                        <i class="fas fa-tractor text-white fa-3x"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2 class="fw-bold">Frequently Asked Questions</h2>
                    <p class="text-muted">Everything you need to know about selling on our platform</p>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingOne">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                    How do I register as a farm seller?
                                </button>
                            </h2>
                            <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Simply click on the "Register as Farm Seller" button and complete the registration process. You'll need to provide information about your farm, upload your farm credentials, and verify your identity.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingTwo">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                    What fees are involved?
                                </button>
                            </h2>
                            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    We charge a competitive commission only when you make a sale. There are no upfront fees to list your products. Detailed fee structure is available in our seller agreement.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingThree">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                    How do I manage my product listings?
                                </button>
                            </h2>
                            <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Once registered, you'll have access to your seller dashboard where you can add, edit, and manage all your product listings. The platform provides easy-to-use tools for inventory management.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingFour">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                    Can I offer delivery to customers?
                                </button>
                            </h2>
                            <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Yes! You can choose to offer pickup only, local delivery service, or arrange shipping for certain products. The platform allows you to set your delivery zones and pricing.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2 class="fw-bold">What Our Farmers Say</h2>
                    <p class="text-muted">Join hundreds of successful farmers</p>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8 mx-auto">
                    <div class="text-center">
                        <div class="d-flex justify-content-center mb-3">
                            <i class="fas fa-quote-left text-success fa-2x me-2"></i>
                        </div>
                        <blockquote class="blockquote">
                            <p class="mb-0 fst-italic">"This platform changed everything for us. Our organic vegetables now reach customers across three states, and we're earning twice as much as we did through traditional markets."</p>
                        </blockquote>
                        <figcaption class="blockquote-footer mt-2">
                            John Doe, <cite title="Source Title">Green Valley Organic Farm</cite>
                        </figcaption>
                    </div>
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