@extends('layouts.app')

@section('title', 'Contact Us - Vidiaspot Marketplace')
@section('meta_description', 'Get in touch with Vidiaspot Marketplace. We are here to help with any questions or concerns.')

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <h1 class="mb-4">Contact Us</h1>
            <p class="lead">Have questions? We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>
            
            <div class="row mt-5">
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fas fa-envelope text-success me-2"></i>Email Us</h5>
                            <p class="card-text">
                                <a href="mailto:support@vidiaspot.ng">support@vidiaspot.ng</a><br>
                                <small class="text-muted">General inquiries and support</small>
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fas fa-phone text-success me-2"></i>Call Us</h5>
                            <p class="card-text">
                                <a href="tel:+2348000000000">+234 800 000 0000</a><br>
                                <small class="text-muted">Mon-Fri, 8am-6pm WAT</small>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row mt-4">
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fas fa-map-marker-alt text-success me-2"></i>Visit Us</h5>
                            <p class="card-text">
                                123 Innovation Hub<br>
                                Lagos, Nigeria<br>
                                <small class="text-muted">Mon-Fri, 9am-5pm WAT</small>
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fas fa-comments text-success me-2"></i>Live Chat</h5>
                            <p class="card-text">
                                <a href="#">Start a Live Chat</a><br>
                                <small class="text-muted">Instant responses during business hours</small>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-5">
                <h3>Send us a Message</h3>
                <form class="mt-4">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="subject" class="form-label">Subject</label>
                        <input type="text" class="form-control" id="subject" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="message" class="form-label">Message</label>
                        <textarea class="form-control" id="message" rows="5" required></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-success">Send Message</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection