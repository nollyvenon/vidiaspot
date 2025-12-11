@extends('static_pages.base')

@section('title', App\Models\StaticPage::getTitleByKey('about', 'en', 'About Us'))
@section('meta_description', 'Learn about VidiaSpot Marketplace - the leading platform for buying and selling locally, including farm products directly from farmers.')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                            <i class="fas fa-info-circle text-success"></i>
                        </div>
                        <div>
                            <h1 class="fw-bold mb-1">About Us</h1>
                            <p class="text-muted mb-0">Connecting buyers and sellers nationwide</p>
                        </div>
                    </div>
                    
                    <div class="static-page-content">
                        @php
                            $aboutPage = App\Models\StaticPage::where('page_key', 'about')->where('locale', 'en')->where('status', 'active')->first();
                            echo $aboutPage ? $aboutPage->content : '
                        <h2>Connecting Communities Through Commerce</h2>
                        <p>VidiaSpot Marketplace is Nigeria\'s premier online classified ads and e-commerce platform, connecting millions of buyers and sellers nationwide. Founded with the mission to make buying and selling easier, faster, and more accessible, we\'ve transformed the way Nigerians trade goods and services.</p>
                        
                        <h3>Our Story</h3>
                        <p>Born from the need to bridge the gap between buyers and sellers in local communities, VidiaSpot started as a simple platform to buy and sell used goods. Today, we\'ve evolved into a comprehensive marketplace featuring everything from vehicles and electronics to our pioneering farm-to-consumer initiatives.</p>
                        
                        <h3>Farm-to-Consumer Initiative</h3>
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <div class="card border-0 bg-success bg-opacity-10 p-4">
                                    <h4><i class="fas fa-leaf text-success me-2"></i> Direct Farm Connections</h4>
                                    <p>Our innovative farm-to-consumer platform connects local farmers directly with buyers, eliminating middlemen and ensuring fresh products from farm to table.</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-0 bg-success bg-opacity-10 p-4">
                                    <h4><i class="fas fa-seedling text-success me-2"></i> Sustainability Focus</h4>
                                    <p>We prioritize sustainable farming practices and environmentally conscious products, helping build a healthier food ecosystem.</p>
                                </div>
                            </div>
                        </div>
                        
                        <h3>Our Mission</h3>
                        <p>To empower individuals and businesses by providing a seamless, secure, and sustainable platform for buying and selling goods and services, with a special focus on connecting consumers directly with local producers and farmers.</p>
                        
                        <h3>Our Vision</h3>
                        <p>To become Africa\'s leading marketplace that champions local businesses, sustainable farming, and community-based commerce, fostering economic growth at grassroots levels.</p>
                        
                        <h3>Values We Stand For</h3>
                        <div class="row g-4">
                            <div class="col-md-4">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body text-center">
                                        <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 60px; height: 60px;">
                                            <i class="fas fa-globe-africa text-success fa-2x"></i>
                                        </div>
                                        <h5>Community-Centric</h5>
                                        <p class="text-muted">Focused on strengthening local economies and communities</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body text-center">
                                        <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 60px; height: 60px;">
                                            <i class="fas fa-leaf text-success fa-2x"></i>
                                        </div>
                                        <h5>Sustainability</h5>
                                        <p class="text-muted">Promoting eco-friendly and sustainable business practices</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body text-center">
                                        <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 60px; height: 60px;">
                                            <i class="fas fa-trust text-success fa-2x"></i>
                                        </div>
                                        <h5>Transparency</h5>
                                        <p class="text-muted">Building trust through transparent transactions and verified sellers</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <h3 class="mt-4">Our Impact</h3>
                        <div class="row g-4 text-center">
                            <div class="col-md-3 col-6 mb-4 mb-md-0">
                                <h3 class="text-success fw-bold">500K+</h3>
                                <p class="text-muted">Monthly Active Users</p>
                            </div>
                            <div class="col-md-3 col-6 mb-4 mb-md-0">
                                <h3 class="text-success fw-bold">50K+</h3>
                                <p class="text-muted">Active Listings</p>
                            </div>
                            <div class="col-md-3 col-6">
                                <h3 class="text-success fw-bold">10K+</h3>
                                <p class="text-muted">Registered Farmers</p>
                            </div>
                            <div class="col-md-3 col-6">
                                <h3 class="text-success fw-bold">98%</h3>
                                <p class="text-muted">Customer Satisfaction</p>
                            </div>
                        </div>
                        
                        <h3>Technology Infrastructure</h3>
                        <p>Our platform leverages cutting-edge technology including AI-powered recommendations, real-time data synchronization, blockchain verification systems, and advanced logistics management to ensure seamless transactions between buyers and sellers.</p>
                        
                        <h3>Supporting Nigerian Farmers</h3>
                        <p>Through our direct farm sales platform, we support over 10,000 farmers across Nigeria, helping them gain better prices for their produce while reducing food waste through improved supply chains. Our farmers enjoy features like:</p>
                        <ul>
                            <li>Direct connection to consumers without middlemen</li>
                            <li>Quality and sustainability ratings</li>
                            <li>Harvest date and freshness tracking</li>
                            <li>Organic certification verification</li>
                            <li>GPS-enabled location services</li>
                            <li>Analytics and performance insights</li>
                        </ul>
                        
                        <h3>Get In Touch</h3>
                        <p>Interested in learning more about our platform? Whether you\'re a consumer looking for fresh farm products or a farmer wanting to expand your sales, we\'d love to hear from you.</p>
                        <div class="d-flex gap-3">
                            <a href="/contact" class="btn btn-success">Contact Us</a>
                            <a href="/register" class="btn btn-outline-success">Join Today</a>
                        </div>
                        ';
                        @endphp
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection