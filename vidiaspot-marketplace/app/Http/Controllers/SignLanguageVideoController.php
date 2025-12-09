<?php

namespace App\Http\Controllers;

use App\Services\SignLanguageVideoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SignLanguageVideoController extends Controller
{
    private SignLanguageVideoService $signLanguageVideoService;

    public function __construct()
    {
        $this->signLanguageVideoService = new SignLanguageVideoService();
    }

    /**
     * Display the sign language video library.
     */
    public function index()
    {
        $categories = $this->signLanguageVideoService->getCategories();
        $videos = $this->signLanguageVideoService->getTrendingVideos(12);
        
        return view('sign-language-videos.index', [
            'categories' => $categories,
            'videos' => $videos
        ]);
    }

    /**
     * Upload a new sign language video.
     */
    public function upload(Request $request)
    {
        $request->validate([
            'video' => 'required|file|mimetypes:video/mp4,video/webm,video/ogg|max:51200', // Max 50MB
            'title' => 'required|string|max:255',
            'category' => 'required|string|in:' . implode(',', array_keys($this->signLanguageVideoService->getCategories())),
            'description' => 'nullable|string',
            'language' => 'nullable|string|in:en,es,fr,de,pt,ar,ja,zh,yo,ig,ha',
            'transcript' => 'nullable|string',
        ]);

        try {
            $metadata = [
                'description' => $request->description ?? '',
                'language' => $request->language ?? 'en',
                'transcript' => $request->transcript ?? '',
                'duration' => $request->has('duration') ? $request->duration : 0,
                'keywords' => $request->has('keywords') ? explode(',', $request->keywords) : [],
            ];

            $result = $this->signLanguageVideoService->uploadVideo(
                $request->file('video'),
                $request->category,
                $request->title,
                $metadata
            );

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get a specific sign language video.
     */
    public function show(string $videoId)
    {
        $video = $this->signLanguageVideoService->getVideo($videoId);
        
        if (!$video) {
            return response()->json(['error' => 'Video not found'], 404);
        }

        // Mark as viewed
        if (Auth::check()) {
            $this->signLanguageVideoService->markAsViewed($videoId, Auth::id());
        }

        return response()->json($video);
    }

    /**
     * Search for sign language videos.
     */
    public function search(Request $request)
    {
        $request->validate([
            'query' => 'nullable|string',
            'category' => 'nullable|string',
            'language' => 'nullable|string',
        ]);

        $videos = $this->signLanguageVideoService->searchVideos(
            $request->query('query', ''),
            $request->query('category', ''),
            $request->query('language', '')
        );

        return response()->json($videos);
    }

    /**
     * Get videos by category.
     */
    public function getByCategory(string $category)
    {
        $categories = array_keys($this->signLanguageVideoService->getCategories());
        
        if (!in_array($category, $categories)) {
            return response()->json(['error' => 'Invalid category'], 400);
        }

        $videos = $this->signLanguageVideoService->getVideosByCategory($category);

        return response()->json($videos);
    }

    /**
     * Get recommended videos for the authenticated user.
     */
    public function getRecommended()
    {
        $userPreferences = [];
        
        if (Auth::check()) {
            // Get user preferences from profile
            $userPreferences = [
                'preferred_language' => Auth::user()->language_code ?? 'en',
                'preferred_categories' => [] // Could be stored in user preferences
            ];
        }

        $videos = $this->signLanguageVideoService->getRecommendedVideos($userPreferences);

        return response()->json($videos);
    }

    /**
     * Get all available categories.
     */
    public function getCategories()
    {
        $categories = $this->signLanguageVideoService->getCategories();

        return response()->json($categories);
    }

    /**
     * Get trending videos.
     */
    public function getTrending()
    {
        $videos = $this->signLanguageVideoService->getTrendingVideos(10);

        return response()->json($videos);
    }

    /**
     * Get user's viewing history.
     */
    public function getViewingHistory()
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Authentication required'], 401);
        }

        $history = $this->signLanguageVideoService->getUserViewingHistory(Auth::id());

        return response()->json($history);
    }
}