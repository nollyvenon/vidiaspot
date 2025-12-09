<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SignLanguageVideoService
{
    /**
     * Store for sign language videos
     */
    private string $storagePath = 'sign-language-videos';

    /**
     * Supported video formats
     */
    private array $supportedFormats = ['mp4', 'webm', 'ogg'];

    /**
     * Upload a sign language video
     */
    public function uploadVideo($file, string $category, string $title, array $metadata = []): array
    {
        // Validate file
        if (!$file || !$file->isValid()) {
            throw new \Exception('Invalid file upload');
        }

        $extension = $file->getClientOriginalExtension();
        if (!in_array(strtolower($extension), $this->supportedFormats)) {
            throw new \Exception('Unsupported video format. Supported formats: ' . implode(', ', $this->supportedFormats));
        }

        // Generate unique filename
        $filename = Str::uuid() . '.' . $extension;
        $path = $this->storagePath . '/' . $category . '/' . $filename;

        // Store the file
        $storedPath = $file->storeAs(
            $this->storagePath . '/' . $category,
            $filename,
            'public'
        );

        // Create video record
        $videoData = [
            'id' => (string) Str::uuid(),
            'filename' => $filename,
            'original_name' => $file->getClientOriginalName(),
            'category' => $category,
            'title' => $title,
            'description' => $metadata['description'] ?? '',
            'language' => $metadata['language'] ?? 'en',
            'duration' => $metadata['duration'] ?? 0,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'storage_path' => $storedPath,
            'url' => Storage::url($storedPath),
            'transcript' => $metadata['transcript'] ?? '',
            'keywords' => $metadata['keywords'] ?? [],
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        // Store video metadata in database
        $this->storeVideoMetadata($videoData);

        return [
            'success' => true,
            'video' => $videoData,
            'message' => 'Sign language video uploaded successfully'
        ];
    }

    /**
     * Store video metadata in the database
     */
    private function storeVideoMetadata(array $videoData): void
    {
        // In a real implementation, this would insert into a videos table
        // For now, we'll use cache as a placeholder
        $cacheKey = "sign_language_video_{$videoData['id']}";
        \Cache::put($cacheKey, $videoData, now()->addYears(5));
    }

    /**
     * Get a sign language video by ID
     */
    public function getVideo(string $videoId): ?array
    {
        $cacheKey = "sign_language_video_{$videoId}";
        return \Cache::get($cacheKey);
    }

    /**
     * Search for sign language videos
     */
    public function searchVideos(string $query = '', string $category = '', string $language = ''): array
    {
        // In a real implementation, this would query a database
        // For now, we'll retrieve from cache
        $allVideos = [];
        
        // This is a simplified approach - in a real app you would query a database
        $keys = \Cache::get("sign_language_videos_list", []);
        
        foreach ($keys as $key) {
            $video = \Cache::get($key);
            if ($video && $this->matchesSearchCriteria($video, $query, $category, $language)) {
                $allVideos[] = $video;
            }
        }
        
        return $allVideos;
    }

    /**
     * Check if a video matches search criteria
     */
    private function matchesSearchCriteria(array $video, string $query, string $category, string $language): bool
    {
        $matchesQuery = true;
        $matchesCategory = true;
        $matchesLanguage = true;
        
        if (!empty($query)) {
            $query = strtolower($query);
            $matchesQuery = (stripos($video['title'], $query) !== false) ||
                           (stripos($video['description'], $query) !== false) ||
                           (stripos($video['transcript'], $query) !== false);
        }
        
        if (!empty($category)) {
            $matchesCategory = $video['category'] === $category;
        }
        
        if (!empty($language)) {
            $matchesLanguage = $video['language'] === $language;
        }
        
        return $matchesQuery && $matchesCategory && $matchesLanguage;
    }

    /**
     * Get videos by category
     */
    public function getVideosByCategory(string $category): array
    {
        // In a real implementation, this would query the database
        // For now, we'll use cache
        $allKeys = \Cache::get("sign_language_videos_list", []);
        $categoryVideos = [];
        
        foreach ($allKeys as $key) {
            $video = \Cache::get($key);
            if ($video && $video['category'] === $category) {
                $categoryVideos[] = $video;
            }
        }
        
        return $categoryVideos;
    }

    /**
     * Get recommended videos based on user preferences
     */
    public function getRecommendedVideos(array $userPreferences = []): array
    {
        // In a real implementation, this would use AI to recommend videos
        // based on user preferences and viewing history
        
        $recommended = [];
        $allKeys = \Cache::get("sign_language_videos_list", []);
        
        foreach ($allKeys as $key) {
            $video = \Cache::get($key);
            if ($video && $this->isRecommended($video, $userPreferences)) {
                $recommended[] = $video;
            }
        }
        
        return array_slice($recommended, 0, 10); // Return top 10
    }

    /**
     * Check if a video is recommended for the user
     */
    private function isRecommended(array $video, array $userPreferences): bool
    {
        // Simple recommendation logic based on preferences
        if (isset($userPreferences['preferred_language']) && 
            $userPreferences['preferred_language'] !== $video['language']) {
            return false;
        }
        
        if (isset($userPreferences['preferred_categories']) && 
            !in_array($video['category'], $userPreferences['preferred_categories'])) {
            return false;
        }
        
        return true;
    }

    /**
     * Get video categories
     */
    public function getCategories(): array
    {
        // In a real implementation, this would come from the database
        return [
            'greetings' => 'Greetings & Introductions',
            'navigation' => 'Website Navigation',
            'product' => 'Product Information',
            'transaction' => 'Transaction Process',
            'profile' => 'Profile Management',
            'search' => 'Search Features',
            'accessibility' => 'Accessibility Features',
            'general' => 'General Information'
        ];
    }

    /**
     * Get trending sign language videos
     */
    public function getTrendingVideos(int $limit = 10): array
    {
        // In a real implementation, this would be based on view counts
        // For now, return a selection of videos
        $allKeys = \Cache::get("sign_language_videos_list", []);
        $trending = [];
        
        foreach ($allKeys as $key) {
            $video = \Cache::get($key);
            if ($video) {
                $trending[] = $video;
                if (count($trending) >= $limit) {
                    break;
                }
            }
        }
        
        return $trending;
    }

    /**
     * Mark a video as viewed
     */
    public function markAsViewed(string $videoId, string $userId): void
    {
        // In a real implementation, this would update view counts
        // and user viewing history in the database
        $viewRecord = [
            'video_id' => $videoId,
            'user_id' => $userId,
            'viewed_at' => now(),
            'session_id' => session()->getId() ?? null
        ];
        
        // Store in cache as placeholder
        $cacheKey = "video_views_{$userId}_{$videoId}";
        \Cache::put($cacheKey, $viewRecord, now()->addHours(24));
    }

    /**
     * Get user's viewing history
     */
    public function getUserViewingHistory(string $userId, int $limit = 20): array
    {
        // In a real implementation, this would query user viewing history
        // from the database
        $history = [];
        $allKeys = \Cache::get("sign_language_videos_list", []);
        
        foreach ($allKeys as $key) {
            $video = \Cache::get($key);
            if ($video) {
                $viewKey = "video_views_{$userId}_" . $video['id'];
                $viewRecord = \Cache::get($viewKey);
                if ($viewRecord) {
                    $video['viewed_at'] = $viewRecord['viewed_at'];
                    $history[] = $video;
                }
            }
        }
        
        // Sort by viewed date, most recent first
        usort($history, function ($a, $b) {
            return strtotime($b['viewed_at'] ?? '') - strtotime($a['viewed_at'] ?? '');
        });
        
        return array_slice($history, 0, $limit);
    }
}