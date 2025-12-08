@extends('layouts.app')

@section('title', $ad->title)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <!-- Ad Images -->
            <div class="h-96 bg-gray-200">
                @if($ad->images->first())
                    <img src="{{ Storage::url($ad->images->first()->image_path) }}" 
                         alt="{{ $ad->title }}" 
                         class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center">
                        <span class="text-gray-500">No image available</span>
                    </div>
                @endif
            </div>
            
            <div class="p-6">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $ad->title }}</h1>
                        <p class="text-lg text-indigo-600 font-semibold mt-2">{{ $ad->formatted_price }}</p>
                    </div>
                    
                    @if($ad->user->id == Auth::id())
                        <div class="flex space-x-2">
                            <a href="{{ route('ads.edit', $ad->id) }}" 
                               class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Edit
                            </a>
                        </div>
                    @endif
                </div>
                
                <div class="mt-4">
                    <p class="text-gray-600">{{ $ad->description }}</p>
                </div>
                
                <!-- Custom Fields Section -->
                @if($ad->customFields->count() > 0)
                    <div class="mt-6 border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Additional Details</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($ad->customFields as $field)
                                <div class="border border-gray-200 rounded-md p-3">
                                    <p class="text-sm font-medium text-gray-500">{{ $field->field_label }}</p>
                                    <p class="text-gray-900">{{ $field->field_value }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
                
                <div class="mt-6 border-t border-gray-200 pt-6">
                    <div class="flex flex-wrap gap-4 text-sm text-gray-600">
                        @if($ad->condition)
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 0h-4m4 0l-5-5"></path>
                                </svg>
                                Condition: {{ ucfirst($ad->condition) }}
                            </span>
                        @endif
                        @if($ad->location)
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                {{ $ad->location }}
                            </span>
                        @endif
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Posted: {{ $ad->created_at->format('M d, Y') }}
                        </span>
                    </div>
                    
                    <div class="mt-6 flex space-x-4">
                        <button id="contactSellerBtn"
                                class="flex-1 bg-indigo-600 text-white py-3 px-4 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Contact Seller
                        </button>
                        
                        <!-- Insurance Option -->
                        <button id="getInsuranceBtn"
                                class="bg-gray-200 text-gray-800 py-3 px-4 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('contactSellerBtn').addEventListener('click', function() {
    alert('Contact seller functionality would open a chat or show contact information');
});

document.getElementById('getInsuranceBtn').addEventListener('click', function() {
    // Open insurance modal/popup
    if (confirm('Would you like to get insurance for this item?')) {
        // Redirect to insurance page or open modal
        window.location.href = `/store/${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}`;
    }
});
</script>
@endsection