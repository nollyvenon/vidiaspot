@extends('layouts.app')

@section('title', 'Sign Language Video Library')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Sign Language Video Library</h1>
        <p class="text-gray-600">Watch tutorials and guides in sign language to help navigate our platform</p>
    </div>

    <!-- Search and Filter Section -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex-1">
                <div class="relative">
                    <input 
                        type="text" 
                        id="search-input" 
                        placeholder="Search sign language videos..." 
                        class="w-full px-4 py-2 pl-10 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                    <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="flex gap-3">
                <select id="category-filter" class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Categories</option>
                    @foreach($categories as $key => $name)
                        <option value="{{ $key }}">{{ $name }}</option>
                    @endforeach
                </select>
                <select id="language-filter" class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Languages</option>
                    <option value="en">English</option>
                    <option value="es">Spanish</option>
                    <option value="fr">French</option>
                    <option value="yo">Yoruba</option>
                    <option value="ig">Igbo</option>
                    <option value="ha">Hausa</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Video Categories -->
    <div class="mb-8">
        <h2 class="text-2xl font-semibold text-gray-800 mb-6">Video Categories</h2>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($categories as $key => $name)
                <a href="{{ route('sign-language-videos.category', $key) }}" 
                   class="block p-4 bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow border border-gray-200 text-center">
                    <div class="text-blue-600 mb-2">
                        <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="font-medium text-gray-900">{{ $name }}</h3>
                    <p class="text-sm text-gray-600 mt-1">Learn about {{ strtolower($name) }}</p>
                </a>
            @endforeach
        </div>
    </div>

    <!-- Trending Videos -->
    <div class="mb-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">Trending Videos</h2>
            <a href="{{ route('sign-language-videos.recommended') }}" class="text-blue-600 hover:text-blue-800 font-medium">View Recommended</a>
        </div>
        
        @if(count($videos) > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($videos as $video)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
                        <div class="relative">
                            <div class="bg-gray-200 border-2 border-dashed rounded-t-lg w-full h-48 flex items-center justify-center">
                                <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="absolute top-2 right-2">
                                <span class="bg-blue-600 text-white text-xs px-2 py-1 rounded">
                                    {{ strtoupper($video['language'] ?? 'EN') }}
                                </span>
                            </div>
                        </div>
                        <div class="p-4">
                            <h3 class="font-semibold text-gray-900 mb-2">{{ $video['title'] ?? 'Untitled Video' }}</h3>
                            <p class="text-sm text-gray-600 mb-3">{{ Str::limit($video['description'] ?? '', 100) }}</p>
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-gray-500">
                                    {{ $categories[$video['category']] ?? $video['category'] }}
                                </span>
                                <button class="view-video-btn text-blue-600 hover:text-blue-800 text-sm font-medium"
                                        data-video-url="{{ $video['url'] ?? '#' }}"
                                        data-video-title="{{ $video['title'] ?? 'Untitled Video' }}">
                                    Watch Video
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <div class="bg-gray-100 rounded-full p-6 inline-block mb-4">
                    <svg class="w-16 h-16 text-gray-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No videos available</h3>
                <p class="text-gray-600">Check back later for new sign language videos.</p>
            </div>
        @endif
    </div>

    <!-- Video Player Modal -->
    <div id="video-modal" class="fixed inset-0 bg-black bg-opacity-75 hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg max-w-4xl w-full max-h-[90vh] overflow-hidden">
            <div class="flex justify-between items-center p-4 border-b">
                <h3 id="video-title" class="text-lg font-semibold"></h3>
                <button id="close-modal" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-4">
                <div id="video-container" class="bg-black w-full aspect-video flex items-center justify-center">
                    <video id="video-player" controls class="w-full h-full max-h-96">
                        <source src="" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-input');
    const categoryFilter = document.getElementById('category-filter');
    const languageFilter = document.getElementById('language-filter');
    const videoButtons = document.querySelectorAll('.view-video-btn');
    const videoModal = document.getElementById('video-modal');
    const videoPlayer = document.getElementById('video-player');
    const videoTitle = document.getElementById('video-title');
    const closeModal = document.getElementById('close-modal');

    // Search functionality
    searchInput.addEventListener('input', function() {
        performSearch();
    });

    // Filter functionality
    categoryFilter.addEventListener('change', function() {
        performSearch();
    });

    languageFilter.addEventListener('change', function() {
        performSearch();
    });

    function performSearch() {
        const query = searchInput.value;
        const category = categoryFilter.value;
        const language = languageFilter.value;

        // In a real implementation, this would make an API call
        // For demo purposes, we'll just log the search parameters
        console.log('Searching for:', { query, category, language });
    }

    // Video modal functionality
    videoButtons.forEach(button => {
        button.addEventListener('click', function() {
            const videoUrl = this.getAttribute('data-video-url');
            const title = this.getAttribute('data-video-title');
            
            videoTitle.textContent = title;
            videoPlayer.src = videoUrl;
            videoModal.classList.remove('hidden');
            
            // Play the video
            videoPlayer.play();
        });
    });

    closeModal.addEventListener('click', function() {
        videoModal.classList.add('hidden');
        videoPlayer.pause();
        videoPlayer.currentTime = 0;
    });

    // Close modal when clicking outside the content
    videoModal.addEventListener('click', function(e) {
        if (e.target === videoModal) {
            videoModal.classList.add('hidden');
            videoPlayer.pause();
            videoPlayer.currentTime = 0;
        }
    });

    // Keyboard shortcut for closing modal
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !videoModal.classList.contains('hidden')) {
            videoModal.classList.add('hidden');
            videoPlayer.pause();
            videoPlayer.currentTime = 0;
        }
    });
});
</script>
@endsection