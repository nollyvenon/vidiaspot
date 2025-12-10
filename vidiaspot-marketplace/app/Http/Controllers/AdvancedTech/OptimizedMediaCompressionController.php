<?php

namespace App\Http\Controllers;

use App\Services\OptimizedMediaCompressionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OptimizedMediaCompressionController extends Controller
{
    private OptimizedMediaCompressionService $compressionService;

    public function __construct()
    {
        $this->compressionService = new OptimizedMediaCompressionService();
    }

    /**
     * Compress an image file.
     */
    public function compressImage(Request $request)
    {
        $request->validate([
            'image_file' => 'required|file|mimes:jpeg,png,jpg,gif,webp,bmp,tiff',
            'format' => 'string|in:webp,jpeg,png,avif',
            'quality' => 'integer|min:1|max:100',
            'max_width' => 'integer|min:100|max:5000',
            'max_height' => 'integer|min:100|max:5000',
            'sharpen' => 'boolean',
            'destination' => 'string',
        ]);

        try {
            $options = $request->only([
                'format', 'quality', 'max_width', 'max_height', 'sharpen', 'destination'
            ]);

            $result = $this->compressionService->compressImage($request->file('image_file'), $options);

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Batch compress images.
     */
    public function batchCompressImages(Request $request)
    {
        $request->validate([
            'image_files' => 'required|array|min:1|max:10',
            'image_files.*' => 'file|mimes:jpeg,png,jpg,gif,webp,bmp,tiff',
            'format' => 'string|in:webp,jpeg,png,avif',
            'quality' => 'integer|min:1|max:100',
            'max_width' => 'integer|min:100|max:5000',
            'max_height' => 'integer|min:100|max:5000',
        ]);

        try {
            $options = $request->only(['format', 'quality', 'max_width', 'max_height']);

            $result = $this->compressionService->batchCompressImages($request->file('image_files'), $options);

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Compress video file.
     */
    public function compressVideo(Request $request)
    {
        $request->validate([
            'video_file' => 'required|file|mimes:mp4,mov,avi,wmv,flv,webm,mkv',
            'format' => 'string|in:mp4,webm,mov',
            'bitrate' => 'string',
            'resolution' => 'string|in:480p,720p,1080p',
            'bandwidth_profile' => 'string|in:low,medium,high,unlimited',
        ]);

        try {
            $options = $request->only(['format', 'bitrate', 'resolution', 'bandwidth_profile']);

            $result = $this->compressionService->compressVideo($request->file('video_file'), $options);

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Compress document file.
     */
    public function compressDocument(Request $request)
    {
        $request->validate([
            'document_file' => 'required|file|mimes:pdf,doc,docx',
            'compression_level' => 'string|in:low,medium,high',
            'format' => 'string|in:pdf',
        ]);

        try {
            $options = $request->only(['compression_level', 'format']);

            $result = $this->compressionService->compressDocument($request->file('document_file'), $options);

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Optimize image for specific device.
     */
    public function optimizeImageForDevice(Request $request)
    {
        $request->validate([
            'image_file' => 'required|file|mimes:jpeg,png,jpg,gif,webp',
            'device_type' => 'string|in:mobile,tablet,desktop',
            'bandwidth_profile' => 'string|in:low,medium,high,unlimited',
        ]);

        try {
            $deviceType = $request->device_type ?? 'mobile';
            $bandwidthProfile = $request->bandwidth_profile ?? 'medium';

            $result = $this->compressionService->optimizeImageForDevice(
                $request->file('image_file'),
                $deviceType,
                $bandwidthProfile
            );

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get optimized media URL.
     */
    public function getOptimizedMediaUrl(Request $request)
    {
        $request->validate([
            'original_url' => 'required|url',
            'width' => 'integer|min:100|max:5000',
            'height' => 'integer|min:100|max:5000',
            'quality' => 'integer|min:1|max:100',
            'format' => 'string|in:webp,jpeg,png,avif,auto',
            'bandwidth_profile' => 'string|in:low,medium,high,unlimited,auto',
        ]);

        $params = $request->only(['width', 'height', 'quality', 'format', 'bandwidth_profile']);
        
        $optimizedUrl = $this->compressionService->getOptimizedMediaUrl($request->original_url, $params);

        return response()->json([
            'optimized_url' => $optimizedUrl,
            'original_url' => $request->original_url,
            'parameters' => $params,
            'message' => 'Optimized media URL generated successfully'
        ]);
    }

    /**
     * Get bandwidth optimization suggestions.
     */
    public function getBandwidthOptimizationSuggestions(Request $request)
    {
        $request->validate([
            'files' => 'required|array|min:1',
            'files.*.path' => 'required|string',
            'files.*.size' => 'required|integer',
        ]);

        $suggestions = $this->compressionService->getBandwidthOptimizationSuggestions($request->files);

        return response()->json([
            'suggestions' => $suggestions,
            'message' => 'Bandwidth optimization suggestions generated successfully'
        ]);
    }

    /**
     * Get compression quality recommendations.
     */
    public function getQualityRecommendations(Request $request)
    {
        $request->validate([
            'file_type' => 'required|string|in:image,video,document',
            'bandwidth_profile' => 'string|in:low,medium,high,unlimited',
        ]);

        $recommendations = $this->compressionService->getQualityRecommendations(
            $request->file_type,
            $request->bandwidth_profile ?? 'medium'
        );

        return response()->json([
            'recommendations' => $recommendations,
            'message' => 'Quality recommendations retrieved successfully'
        ]);
    }

    /**
     * Get auto-optimized media based on user context.
     */
    public function autoOptimizeMedia(Request $request)
    {
        $request->validate([
            'media_url' => 'required|url',
            'device_type' => 'string|in:mobile,tablet,desktop',
            'connection_quality' => 'string|in:poor,limited,good,excellent',
            'data_savings_mode' => 'boolean',
        ]);

        $userContext = $request->only(['device_type', 'connection_quality', 'data_savings_mode']);

        $result = $this->compressionService->autoOptimizeMedia($request->media_url, $userContext);

        return response()->json([
            'result' => $result,
            'message' => 'Media auto-optimized successfully'
        ]);
    }

    /**
     * Get bandwidth savings report.
     */
    public function getBandwidthSavingsReport(Request $request)
    {
        $request->validate([
            'period' => 'string|in:daily,weekly,monthly',
        ]);

        $report = $this->compressionService->getBandwidthSavingsReport($request->period ?? 'monthly');

        return response()->json([
            'report' => $report,
            'message' => 'Bandwidth savings report retrieved successfully'
        ]);
    }

    /**
     * Get supported formats for compression.
     */
    public function getSupportedFormats()
    {
        $formats = $this->compressionService->getSupportedFormats();

        return response()->json([
            'formats' => $formats,
            'message' => 'Supported formats retrieved successfully'
        ]);
    }

    /**
     * Get available bandwidth profiles.
     */
    public function getBandwidthProfiles()
    {
        $profiles = $this->compressionService->getBandwidthProfiles();

        return response()->json([
            'profiles' => $profiles,
            'message' => 'Bandwidth profiles retrieved successfully'
        ]);
    }

    /**
     * Get compression settings for a media type.
     */
    public function getCompressionSettings(Request $request)
    {
        $request->validate([
            'media_type' => 'required|string|in:images,videos,documents',
            'format' => 'string',
        ]);

        $settings = $this->compressionService->getCompressionSettings(
            $request->media_type,
            $request->format
        );

        return response()->json([
            'settings' => $settings,
            'message' => 'Compression settings retrieved successfully'
        ]);
    }
}