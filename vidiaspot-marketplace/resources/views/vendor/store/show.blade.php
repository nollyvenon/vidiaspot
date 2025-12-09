@extends('layouts.app')

@section('title', $store->store_name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Store Header -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        @if($store->banner_url)
            <div class="h-48 bg-gray-200" style="background-image: url({{ Storage::url($store->banner_url) }}); background-size: cover; background-position: center;">
            </div>
        @else
            <div class="h-48 bg-gray-200 flex items-center justify-center">
                <span class="text-gray-500">No banner image</span>
            </div>
        @endif
        
        <div class="p-6">
            <div class="flex items-start">
                @if($store->logo_url)
                    <img src="{{ Storage::url($store->logo_url) }}" alt="{{ $store->store_name }}" class="w-24 h-24 rounded-full mr-4 object-cover">
                @else
                    <div class="w-24 h-24 rounded-full bg-gray-200 flex items-center justify-center mr-4">
                        <span class="text-gray-500">{{ strtoupper(substr($store->store_name, 0, 1)) }}</span>
                    </div>
                @endif
                
                <div class="flex-1">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $store->store_name }}</h1>
                    @if($store->description)
                        <p class="text-gray-600 mt-2">{{ $store->description }}</p>
                    @endif
                    
                    <div class="mt-3 flex flex-wrap gap-2">
                        @if($store->contact_email)
                            <span class="inline-flex items-center text-sm text-gray-500">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                {{ $store->contact_email }}
                            </span>
                        @endif
                        
                        @if($store->contact_phone)
                            <span class="inline-flex items-center text-sm text-gray-500">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                                {{ $store->contact_phone }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Store Products/Ads -->
    <div>
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Store Products</h2>
        
        @if($ads->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($ads as $ad)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                        @if($ad->images->first())
                            <img src="{{ Storage::url($ad->images->first()->image_path) }}" 
                                 alt="{{ $ad->title }}" 
                                 class="w-full h-48 object-cover">
                        @else
                            <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                <span class="text-gray-500">No image</span>
                            </div>
                        @endif
                        
                        <div class="p-4">
                            <h3 class="font-semibold text-lg text-gray-900 mb-1">{{ $ad->title }}</h3>
                            <p class="text-gray-600 text-sm mb-2">{{ Str::limit($ad->description, 80) }}</p>
                            
                            <div class="flex justify-between items-center mt-3">
                                <span class="text-lg font-bold text-indigo-600">{{ $ad->formatted_price }}</span>
                                <a href="{{ route('ads.show', $ad->id) }}" 
                                   class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="mt-6">
                {{ $ads->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No products yet</h3>
                <p class="mt-1 text-sm text-gray-500">This store doesn't have any products posted yet.</p>
            </div>
        @endif
    </div>
</div>
@endsection