@extends('static_pages.base')

@section('title', App\Models\StaticPage::getTitleByKey('contact_us', 'en', 'Contact Us'))
@section('meta_description', 'Get in touch with VidiaSpot Marketplace. Find our contact information, office locations, and customer support details.')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                            <i class="fas fa-envelope text-success"></i>
                        </div>
                        <div>
                            <h1 class="fw-bold mb-1">Contact Us</h1>
                            <p class="text-muted mb-0">We're here to help you</p>
                        </div>
                    </div>
                    
                    <div class="static-page-content">
                        @php
                            $contactPage = App\Models\StaticPage::where('page_key', 'contact_us')->where('locale', 'en')->where('status', 'active')->first();
                            echo $contactPage ? $contactPage->content : '
                            <h2>Get in Touch with Us</h2>
                            <p>We value your feedback and are committed to providing excellent customer service. Please reach out to us using any of the channels below.</p>
                            
                            <h3>Contact Information</h3>
                            <div class="row g-4 mb-4">
                                <div class="col-md-4">
                                    <div class="card border-0 bg-light p-3">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-map-marker-alt text-success me-3 fa-2x"></i>
                                            <div>
                                                <h5>Office Address</h5>
                                                <p class="mb-0">1234 Innovation Hub<br>
                                                Victoria Island, Lagos<br>
                                                Nigeria</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card border-0 bg-light p-3">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-phone text-success me-3 fa-2x"></i>
                                            <div>
                                                <h5>Phone Number</h5>
                                                <p class="mb-0">+234 800 000 0000<br>
                                                Mon-Fri, 8AM-5PM WAT</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card border-0 bg-light p-3">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-envelope text-success me-3 fa-2x"></i>
                                            <div>
                                                <h5>Email Support</h5>
                                                <p class="mb-0"><a href="mailto:support@vidiaspot.ng">support@vidiaspot.ng</a><br>
                                                For all inquiries</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <h3>Send us a Message</h3>
                            <form id="contactForm">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="name" class="form-label">Your Name</label>
                                        <input type="text" class="form-control" id="name" placeholder="Enter your name">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="email" class="form-label">Email Address</label>
                                        <input type="email" class="form-control" id="email" placeholder="Enter your email">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="subject" class="form-label">Subject</label>
                                        <input type="text" class="form-control" id="subject" placeholder="What is this regarding?">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="category" class="form-label">Inquiry Type</label>
                                        <select class="form-select" id="category">
                                            <option selected>General Inquiry</option>
                                            <option>Account Issue</option>
                                            <option>Technical Support</option>
                                            <option>Business Partnership</option>
                                            <option>Advertisement</option>
                                            <option>Feedback</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label for="message" class="form-label">Your Message</label>
                                        <textarea class="form-control" id="message" rows="5" placeholder="Please share your thoughts or concerns..."></textarea>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-success btn-lg">Send Message</button>
                                    </div>
                                </div>
                            </form>
                            
                            <h3 class="mt-5">Frequently Visited Locations</h3>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="card border-0">
                                        <div class="card-body">
                                            <h5>Lagos Office</h5>
                                            <p class="text-muted mb-1"><i class="fas fa-map-marker-alt me-2"></i> 1234 Innovation Hub, Victoria Island, Lagos</p>
                                            <p class="text-muted mb-0"><i class="fas fa-phone me-2"></i> +234 800 000 0000</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card border-0">
                                        <div class="card-body">
                                            <h5>Abuja Office</h5>
                                            <p class="text-muted mb-1"><i class="fas fa-map-marker-alt me-2"></i> Garki, Abuja, Nigeria</p>
                                            <p class="text-muted mb-0"><i class="fas fa-phone me-2"></i> +234 900 000 0000</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            ';
                        @endphp
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('contactForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Get form values
        const name = document.getElementById('name').value;
        const email = document.getElementById('email').value;
        const subject = document.getElementById('subject').value;
        const message = document.getElementById('message').value;
        
        // Simple validation
        if (!name || !email || !subject || !message) {
            alert('Please fill in all required fields');
            return;
        }
        
        // In a real app, submit to backend
        alert('Thank you for contacting us! We will get back to you soon.');
        
        // Reset form
        document.getElementById('contactForm').reset();
    });
</script>

@endsection