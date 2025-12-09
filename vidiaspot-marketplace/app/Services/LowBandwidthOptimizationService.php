<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LowBandwidthOptimizationService
{
    /**
     * Image optimization settings
     */
    private array $imageCompressionSettings = [
        'webp' => ['quality' => 70, 'format' => 'webp'],
        'jpeg' => ['quality' => 60, 'format' => 'jpeg'],
        'png' => ['quality' => 60, 'format' => 'png'],
    ];

    /**
     * Determine if low bandwidth mode should be activated
     */
    public function shouldActivateLowBandwidthMode(Request $request): bool
    {
        // Check if explicitly enabled by user
        if ($request->session()->get('low_bandwidth_mode', false)) {
            return true;
        }

        // Check user preference
        if (auth()->check() && auth()->user()->hasLowBandwidthPreference()) {
            return true;
        }

        // Could also check based on network type, device type, etc.
        return false;
    }

    /**
     * Optimize an image for low bandwidth
     */
    public function optimizeImage(string $imagePath, array $options = []): array
    {
        $opts = array_merge([
            'max_width' => 800,
            'max_height' => 600,
            'quality' => 60,
            'format' => 'webp',
            'resize_mode' => 'fit', // fit, resize, or crop
        ], $options);

        // In a real implementation, this would use an image processing library like Intervention Image
        // For this implementation, we'll just return optimized path information
        $optimizedImage = [
            'original_path' => $imagePath,
            'optimized_path' => $this->generateOptimizedImagePath($imagePath, $opts),
            'format' => $opts['format'],
            'quality' => $opts['quality'],
            'size_reduction' => 0.5, // 50% size reduction example
            'estimated_size' => $this->estimateOptimizedSize($imagePath, $opts),
        ];

        return $optimizedImage;
    }

    /**
     * Generate optimized image path
     */
    private function generateOptimizedImagePath(string $originalPath, array $options): string
    {
        $pathInfo = pathinfo($originalPath);
        $optimizedPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . 
                        '_optimized_q' . $options['quality'] . 
                        '.' . $options['format'];

        return $optimizedPath;
    }

    /**
     * Estimate the size of an optimized image
     */
    private function estimateOptimizedSize(string $originalPath, array $options): int
    {
        // In a real implementation, this would calculate actual optimized size
        // For this example, we'll return a placeholder value
        return 50000; // 50KB placeholder
    }

    /**
     * Optimize text content for low bandwidth
     */
    public function optimizeTextContent(string $content, array $options = []): array
    {
        $opts = array_merge([
            'remove_extra_whitespace' => true,
            'minify_html' => true,
            'remove_comments' => true,
            'compress_content' => true,
        ], $options);

        $optimized = $content;

        // Remove extra whitespace
        if ($opts['remove_extra_whitespace']) {
            $optimized = preg_replace('/\s+/', ' ', $optimized);
        }

        // Minify HTML if it contains HTML
        if ($opts['minify_html'] && $this->containsHtml($content)) {
            $optimized = $this->minifyHtml($optimized);
        }

        // Remove comments
        if ($opts['remove_comments']) {
            $optimized = $this->removeComments($optimized);
        }

        // Compress content
        if ($opts['compress_content']) {
            $optimized = gzcompress($optimized); // For storage, would need gzuncompress when serving
        }

        return [
            'original_size' => strlen($content),
            'optimized_content' => $optimized,
            'optimized_size' => strlen($optimized),
            'compression_ratio' => strlen($content) > 0 ? (1 - strlen($optimized) / strlen($content)) : 0,
        ];
    }

    /**
     * Minify HTML content
     */
    private function minifyHtml(string $html): string
    {
        // Basic HTML minification
        $search = [
            '/\>[^\S ]+/s',  // strip whitespaces after tags, except space
            '/[^\S ]+\</s',  // strip whitespaces before tags, except space
            '/(\s)+/s'       // shorten multiple whitespace sequences
        ];

        $replace = ['>', '<', '\\1'];

        return preg_replace($search, $replace, $html);
    }

    /**
     * Remove comments from content
     */
    private function removeComments(string $content): string
    {
        // Remove HTML comments
        $content = preg_replace('/<!--(.|\s)*?-->/', '', $content);
        
        // Remove CSS comments
        $content = preg_replace('/\/\*.*?\*\//', '', $content);
        
        return $content;
    }

    /**
     * Check if content contains HTML tags
     */
    private function containsHtml(string $content): bool
    {
        return strlen($content) !== strlen(strip_tags($content));
    }

    /**
     * Optimize response for low bandwidth
     */
    public function optimizeResponse(array $responseData, array $options = []): array
    {
        $opts = array_merge([
            'remove_images' => false,
            'compress_data' => true,
            'reduce_precision' => true, // Reduce decimal precision in numbers
            'filter_optional_fields' => true,
        ], $options);

        $optimized = $responseData;

        // Remove images if requested
        if ($opts['remove_images']) {
            $optimized = $this->removeImageFields($optimized);
        }

        // Reduce precision of decimal numbers
        if ($opts['reduce_precision']) {
            $optimized = $this->reducePrecision($optimized);
        }

        // Filter optional fields
        if ($opts['filter_optional_fields']) {
            $optimized = $this->filterOptionalFields($optimized);
        }

        // Compress data
        if ($opts['compress_data']) {
            $optimized = $this->compressData($optimized);
        }

        return [
            'optimized_data' => $optimized,
            'original_size' => strlen(json_encode($responseData)),
            'optimized_size' => strlen(json_encode($optimized)),
        ];
    }

    /**
     * Remove image-related fields from data
     */
    private function removeImageFields(array $data): array
    {
        $result = [];

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $result[$key] = $this->removeImageFields($value);
            } elseif (!preg_match('/(image|photo|avatar|thumbnail|screenshot)/i', $key)) {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * Reduce precision of decimal numbers
     */
    private function reducePrecision(array $data): array
    {
        $result = [];

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $result[$key] = $this->reducePrecision($value);
            } elseif (is_float($value)) {
                $result[$key] = round($value, 2); // Reduce to 2 decimal places
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * Filter optional fields to reduce response size
     */
    private function filterOptionalFields(array $data): array
    {
        $result = [];

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $result[$key] = $this->filterOptionalFields($value);
            } else {
                // Only include essential fields (this is application-specific logic)
                // For this example, we'll include most fields but could filter based on importance
                if ($this->isEssentialField($key)) {
                    $result[$key] = $value;
                }
            }
        }

        return $result;
    }

    /**
     * Determine if a field is essential (for bandwidth optimization)
     */
    private function isEssentialField(string $fieldName): bool
    {
        // Define essential fields (application-specific)
        $essentialFields = [
            'id', 'name', 'title', 'description', 'price', 'status',
            'created_at', 'updated_at', 'user_id', 'main_image'
        ];
        
        return in_array($fieldName, $essentialFields) || 
               preg_match('/^id$|^name$|^title$|^price$|^description$/', $fieldName);
    }

    /**
     * Compress data array
     */
    private function compressData(array $data): array
    {
        // For this implementation, we'll just return the data as-is
        // In a real implementation, you might want to remove certain fields or reduce data precision
        return $data;
    }

    /**
     * Generate low-bandwidth version of a page
     */
    public function generateLowBandwidthPage(string $htmlContent, array $options = []): string
    {
        $opts = array_merge([
            'remove_images' => true,
            'remove_videos' => true,
            'simplify_css' => true,
            'remove_non_essential_elements' => true,
            'text_only_mode' => false,
        ], $options);

        $optimized = $htmlContent;

        // Remove images
        if ($opts['remove_images']) {
            $optimized = preg_replace('/<img[^>]*>/i', '', $optimized);
        }

        // Remove videos
        if ($opts['remove_videos']) {
            $optimized = preg_replace('/<video[^>]*>.*?<\/video>/i', '', $optimized);
            $optimized = preg_replace('/<iframe[^>]*>.*?<\/iframe>/i', '', $optimized);
        }

        // Simplify CSS
        if ($opts['simplify_css']) {
            $optimized = $this->simplifyCss($optimized);
        }

        // Remove non-essential elements
        if ($opts['remove_non_essential_elements']) {
            $optimized = $this->removeNonEssentialElements($optimized);
        }

        // If text-only mode, extract just the text content
        if ($opts['text_only_mode']) {
            $optimized = strip_tags($optimized, '<p><h1><h2><h3><h4><h5><h6><a>');
        }

        return $optimized;
    }

    /**
     * Simplify CSS in HTML content
     */
    private function simplifyCss(string $html): string
    {
        // Remove style tags with complex CSS
        $html = preg_replace('/<style[^>]*>(.*?)<\/style>/is', '<style>/* CSS simplified for low bandwidth */</style>', $html);
        
        // Remove complex inline styles
        $html = preg_replace('/style="[^"]*"/i', '', $html);
        
        return $html;
    }

    /**
     * Remove non-essential elements from HTML
     */
    private function removeNonEssentialElements(string $html): string
    {
        // Remove script tags
        $html = preg_replace('/<script[^>]*>.*?<\/script>/is', '', $html);
        
        // Remove complex interactive elements that may not be essential
        $html = preg_replace('/<canvas[^>]*>.*?<\/canvas>/is', '', $html);
        $html = preg_replace('/<svg[^>]*>.*?<\/svg>/is', '', $html);
        
        return $html;
    }

    /**
     * Get bandwidth optimization recommendations
     */
    public function getOptimizationRecommendations(array $pageData): array
    {
        $recommendations = [
            'image_optimization' => [
                'count' => $this->countImages($pageData),
                'potential_savings' => '60-80% file size reduction',
                'suggestion' => 'Use WebP format and appropriate compression'
            ],
            'text_compression' => [
                'potential_savings' => '20-30% for HTML/CSS/JS',
                'suggestion' => 'Enable GZIP compression on server'
            ],
            'resource_reduction' => [
                'non_essential_resources' => $this->countNonEssentialResources($pageData),
                'suggestion' => 'Load non-essential resources conditionally'
            ],
            'caching' => [
                'suggestion' => 'Implement aggressive caching for static resources'
            ]
        ];

        return [
            'recommendations' => $recommendations,
            'overall_optimization_potential' => 'Significant',
            'estimated_bandwidth_savings' => '40-60%',
            'implementation_priority' => 'High'
        ];
    }

    /**
     * Count images in page data
     */
    private function countImages(array $data): int
    {
        $count = 0;
        array_walk_recursive($data, function($item, $key) use (&$count) {
            if (is_string($item) && preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $item)) {
                $count++;
            }
        });
        return $count;
    }

    /**
     * Count non-essential resources in page data
     */
    private function countNonEssentialResources(array $data): int
    {
        // Count items that are not essential for core functionality
        $count = 0;
        array_walk_recursive($data, function($item, $key) use (&$count) {
            if (is_string($item) && preg_match('/(analytics|tracking|social|widget)/i', $item)) {
                $count++;
            }
        });
        return $count;
    }

    /**
     * Activate low bandwidth mode for a user
     */
    public function activateLowBandwidthMode(string $userId, bool $activate = true): void
    {
        Cache::put("low_bandwidth_mode_{$userId}", $activate, now()->addMonth());
    }

    /**
     * Check if low bandwidth mode is active for a user
     */
    public function isLowBandwidthModeActive(string $userId): bool
    {
        return Cache::get("low_bandwidth_mode_{$userId}", false);
    }
}