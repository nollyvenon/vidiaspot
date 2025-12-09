@extends('layouts.app')

@section('title', 'Search Results - Vidiaspot Marketplace')
@section('meta_description', 'Search results for your query on Vidiaspot Marketplace. Find great deals and items near you.')

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">Search Results for "{{ $query }}"</h1>

            @if($ads && $ads->count() > 0)
                <p class="text-muted">Found {{ $ads->total() }} result(s) for "{{ $query }}"</p>

                <div class="row g-4">
                    @foreach($ads as $ad)
                    <div class="col-md-3">
                        <div class="card ad-card h-100">
                            <img src="https://via.placeholder.com/300x200" class="card-img-top" alt="{{ $ad->title }}" style="height: 150px; object-fit: cover;">
                            <div class="card-body p-2">
                                <h6 class="card-title mb-1" style="font-size: 0.9rem; height: 3rem; overflow: hidden;">{{ $ad->title }}</h6>
                                <div class="d-flex justify-content-between">
                                    <span class="fw-bold text-success" style="font-size: 0.9rem;">₦ {{ number_format($ad->price ?? 0) }}</span>
                                </div>
                                <div class="mt-1">
                                    <small class="text-muted"><i class="fas fa-map-marker-alt me-1"></i>{{ $ad->location ?? 'N/A' }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="mt-4">
                    {{ $ads->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h3>No results found</h3>
                    <p class="text-muted">We couldn't find any ads matching your search for "{{ $query }}".</p>
                    <a href="/" class="btn btn-success">Browse All Ads</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection