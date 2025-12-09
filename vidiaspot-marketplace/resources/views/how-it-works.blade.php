@extends('layouts.app')

@section('title', 'How It Works - Vidiaspot Marketplace')
@section('meta_description', 'Learn how to buy and sell on Vidiaspot Marketplace. Simple steps to get started with our platform.')

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <h1 class="mb-4">How It Works</h1>
            <p class="lead">Buying and selling on Vidiaspot Marketplace is simple and secure. Follow these easy steps to get started.</p>
            
            <div class="mt-5">
                <h2>How It Works</h2>
                <div class="row mt-4">
                    @forelse($howItWorksSteps as $step)
                    <div class="col-md-4 text-center mb-4">
                        <div class="p-4 border rounded">
                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 60px; height: 60px;">
                                <i class="{{ $step->icon_class }} fs-3"></i>
                            </div>
                            <h4>{{ $step->title }}</h4>
                            <p>{{ $step->description }}</p>
                        </div>
                    </div>
                    @empty
                    <div class="col-12 text-center">
                        <p class="text-muted">No steps defined for "How It Works" section.</p>
                    </div>
                    @endforelse
                </div>
            </div>
            
            <div class="mt-5 p-4 bg-light rounded">
                <h3 class="mb-4">Tips for Success</h3>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><i class="fas fa-check-circle text-success me-2"></i> Take clear, well-lit photos of your items</li>
                    <li class="list-group-item"><i class="fas fa-check-circle text-success me-2"></i> Write detailed descriptions including condition and any defects</li>
                    <li class="list-group-item"><i class="fas fa-check-circle text-success me-2"></i> Set competitive prices by checking similar items</li>
                    <li class="list-group-item"><i class="fas fa-check-circle text-success me-2"></i> Respond to inquiries promptly</li>
                    <li class="list-group-item"><i class="fas fa-check-circle text-success me-2"></i> Follow our safety guidelines for all transactions</li>
                </ul>
            </div>
            
            <div class="mt-5 text-center">
                <a href="/register" class="btn btn-success btn-lg me-2">Sign Up Now</a>
                <a href="/ads" class="btn btn-outline-success btn-lg">Browse Items</a>
            </div>
        </div>
    </div>
</div>
@endsection