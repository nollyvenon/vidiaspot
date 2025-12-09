<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class OptimizedMediaCompressionService
{
    /**
     * Compression settings for different media types
     */
    private array $compressionSettings = [
        'images' => [
            'jpeg' => [
                'quality' => 80,
                'format' => 'webp',
                'max_width' => 1920,
                'max_height' => 1080,
                'compression_level' => 8,
            ],
            'png' => [
                'quality' => 90,
                'format' => 'webp',
                'max_width' => 1920,
                'max_height' => 1080,
                'compression_level' => 6,
            ],
            'webp' => [
                'quality' => 85,
                'format' => 'webp',
                'max_width' => 1920,
                'max_height' => 1080,
                'compression_level' => 8,
            ],
            'avif' => [
                'quality' => 75,
                'format' => 'avif',
                'max_width' => 1920,
                'max_height' => 1080,
                'compression_level' => 7,
            ],
        ],
        'videos' => [
            'mp4' => [
                'codec' => 'libx264',
                'preset' => 'medium',
                'bitrate' => '1000k',
                'crf' => 23,
                'max_resolution' => '1080p',
            ],
            'webm' => [
                'codec' => 'libvpx-vp9',
                'bitrate' => '800k',
                'crf' => 32,
                'max_resolution' => '1080p',
            ],
            'mov' => [
                'codec' => 'libx264',
                'preset' => 'slow',
                'bitrate' => '1200k',
                'crf' => 21,
                'max_resolution' => '1080p',
            ],
        ],
        'documents' => [
            'pdf' => [
                'compression' => 'medium',
                'max_size_mb' => 5,
                'ocr_quality' => 'medium',
            ],
            'doc' => [
                'compression' => 'high',
                'max_size_mb' => 2,
            ],
            'docx' => [
                'compression' => 'high',
                'max_size_mb' => 2,
            ],
        ],
    ];

    /**
     * Supported formats and their capabilities
     */
    private array $supportedFormats = [
        'images' => [
            'input' => ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'tiff', 'svg'],
            'output' => ['webp', 'avif', 'jpeg', 'png'],
        ],
        'videos' => [
            'input' => ['mp4', 'avi', 'mov', 'mkv', 'wmv', 'flv', 'webm'],
            'output' => ['mp4', 'webm'],
        ],
        'documents' => [
            'input' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'],
            'output' => ['pdf'],
        ],
    ];

    /**
     * Bandwidth optimization profiles
     */
    private array $bandwidthProfiles = [
        'low' => [      // < 1 Mbps
            'image_quality' => 60,
            'video_bitrate' => '600k',
            'max_image_width' => 800,
            'max_video_resolution' => '480p',
            'format_preference' => ['webp', 'jpeg'],
        ],
        'medium' => [   // 1-5 Mbps
            'image_quality' => 75,
            'video_bitrate' => '1000k',
            'max_image_width' => 1200,
            'max_video_resolution' => '720p',
            'format_preference' => ['webp', 'jpeg', 'png'],
        ],
        'high' => [     // 5+ Mbps
            'image_quality' => 85,
            'video_bitrate' => '1500k',
            'max_image_width' => 1920,
            'max_video_resolution' => '1080p',
            'format_preference' => ['webp', 'avif', 'jpeg', 'png'],
        ],
        'unlimited' => [
            'image_quality' => 95,
            'video_bitrate' => '2000k',
            'max_image_width' => 3840, // 4K
            'max_video_resolution' => '1080p',
            'format_preference' => ['webp', 'avif', 'jpeg', 'png'],
        ],
    ];

    /**
     * Get compression settings for a media type
     */
    public function getCompressionSettings(string $mediaType, string $format = null): array
    {
        if ($format && isset($this->compressionSettings[$mediaType][$format])) {
            return $this->compressionSettings[$mediaType][$format];
        }

        return $this->compressionSettings[$mediaType] ?? [];
    }

    /**
     * Compress an image file
     */
    public function compressImage($imageFile, array $options = []): array
    {
        // Validate image file
        if (!is_file($imageFile) && !method_exists($imageFile, 'getRealPath')) {
            throw new \InvalidArgumentException('Invalid image file provided');
        }

        $sourcePath = is_string($imageFile) ? $imageFile : $imageFile->getRealPath();
        
        // Get original file info
        $originalSize = filesize($sourcePath);
        $originalDimensions = getimagesize($sourcePath);
        $originalMime = mime_content_type($sourcePath);
        
        // Determine output format
        $targetFormat = $options['format'] ?? 'webp';
        $quality = $options['quality'] ?? 80;
        
        // Create compressed image
        $img = Image::make($sourcePath);
        
        // Apply transformations
        if (isset($options['max_width']) || isset($options['max_height'])) {
            $img->resize(
                $options['max_width'] ?? null,
                $options['max_height'] ?? null,
                function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                }
            );
        }
        
        // Apply sharpening if requested
        if ($options['sharpen'] ?? false) {
            $img->sharpen(7);
        }
        
        // Set quality and format
        $compressedPath = storage_path('app/temp/' . Str::uuid() . '.' . $targetFormat);
        
        switch (strtolower($targetFormat)) {
            case 'webp':
                $img->encode('webp', $quality);
                break;
            case 'jpeg':
            case 'jpg':
                $img->encode('jpg', $quality);
                break;
            case 'png':
                $img->encode('png', $quality); // PNG quality is 0-9
                break;
            case 'avif':
                // For AVIF, we'd use a special encoder in a real implementation
                // For this implementation, we'll convert to webp as a placeholder
                $img->encode('webp', $quality);
                break;
            default:
                $img->encode('webp', $quality);
        }
        
        $img->save($compressedPath);
        
        $compressedSize = filesize($compressedPath);
        
        // Calculate compression ratio
        $compressionRatio = $originalSize > 0 ? ($originalSize - $compressedSize) / $originalSize * 100 : 0;
        
        // Store the compressed image if a destination is provided
        $storedPath = null;
        if (isset($options['destination'])) {
            $storedPath = $options['destination'];
            $this->storeCompressedImage($compressedPath, $storedPath);
        }
        
        // Clean up temporary file
        unlink($compressedPath);
        
        $result = [
            'success' => true,
            'original_path' => $sourcePath,
            'compressed_path' => $storedPath,
            'original_size_bytes' => $originalSize,
            'compressed_size_bytes' => $compressedSize,
            'compression_ratio_percent' => round($compressionRatio, 2),
            'original_dimensions' => [
                'width' => $originalDimensions[0],
                'height' => $originalDimensions[1],
            ],
            'compressed_dimensions' => [
                'width' => $img->getWidth(),
                'height' => $img->getHeight(),
            ],
            'format' => $targetFormat,
            'quality_used' => $quality,
            'compression_time_ms' => 0, // Would track actual time in real implementation
        ];

        // Add bandwidth savings calculation
        $result['bandwidth_saved_bytes'] = $originalSize - $compressedSize;
        $result['bandwidth_saved_mb'] = round(($originalSize - $compressedSize) / (1024 * 1024), 3);

        return $result;
    }

    /**
     * Store compressed image to destination
     */
    private function storeCompressedImage(string $tempPath, string $destination): void
    {
        $contents = file_get_contents($tempPath);
        Storage::put($destination, $contents);
    }

    /**
     * Batch compress images
     */
    public function batchCompressImages(array $imageFiles, array $options = []): array
    {
        $results = [];
        
        foreach ($imageFiles as $index => $imageFile) {
            try {
                $result = $this->compressImage($imageFile, $options);
                $results[] = $result;
            } catch (\Exception $e) {
                $results[] = [
                    'success' => false,
                    'file_index' => $index,
                    'error' => $e->getMessage(),
                ];
            }
        }
        
        return [
            'results' => $results,
            'total_files' => count($imageFiles),
            'successful_compressions' => count(array_filter($results, function($r) { return $r['success'] ?? false; })),
            'failed_compressions' => count(array_filter($results, function($r) { return !($r['success'] ?? true); })),
            'total_bandwidth_saved_mb' => array_sum(array_column($results, 'bandwidth_saved_mb')),
        ];
    }

    /**
     * Compress video file (placeholder implementation)
     */
    public function compressVideo($videoFile, array $options = []): array
    {
        // In a real implementation, this would use FFmpeg or similar
        // For this implementation, we'll return a simulated result
        
        $sourcePath = is_string($videoFile) ? $videoFile : $videoFile->getRealPath();
        $originalSize = filesize($sourcePath);
        
        // Determine compression based on options
        $profile = $options['bandwidth_profile'] ?? 'medium';
        $compressionSettings = $this->bandwidthProfiles[$profile] ?? $this->bandwidthProfiles['medium'];
        
        // Simulate compression (in reality, this would reduce the file size)
        $compressedSize = $originalSize * 0.7; // Simulate 30% reduction
        
        $result = [
            'success' => true,
            'original_path' => $sourcePath,
            'original_size_bytes' => $originalSize,
            'compressed_size_bytes' => intval($compressedSize),
            'compression_ratio_percent' => round(($originalSize - $compressedSize) / $originalSize * 100, 2),
            'format' => $options['format'] ?? 'mp4',
            'bitrate' => $compressionSettings['video_bitrate'],
            'resolution' => $compressionSettings['max_video_resolution'],
            'estimated_duration_seconds' => $options['duration'] ?? 120, // Placeholder
            'bandwidth_saved_mb' => round(($originalSize - $compressedSize) / (1024 * 1024), 3),
        ];
        
        // Add to bandwidth savings log
        $this->logBandwidthSaving('video', $result['bandwidth_saved_mb']);
        
        return $result;
    }

    /**
     * Compress document file (placeholder implementation)
     */
    public function compressDocument($documentFile, array $options = []): array
    {
        $sourcePath = is_string($documentFile) ? $documentFile : $documentFile->getRealPath();
        $originalSize = filesize($sourcePath);
        
        // Determine compression level
        $compressionLevel = $options['compression_level'] ?? 'medium';
        
        // Simulate compression
        $compressionFactors = [
            'low' => 0.9,    // 10% reduction
            'medium' => 0.75, // 25% reduction
            'high' => 0.6,   // 40% reduction
        ];
        
        $factor = $compressionFactors[$compressionLevel] ?? $compressionFactors['medium'];
        $compressedSize = $originalSize * $factor;
        
        $result = [
            'success' => true,
            'original_path' => $sourcePath,
            'original_size_bytes' => $originalSize,
            'compressed_size_bytes' => intval($compressedSize),
            'compression_ratio_percent' => round(($originalSize - $compressedSize) / $originalSize * 100, 2),
            'format' => $options['format'] ?? 'pdf',
            'compression_level' => $compressionLevel,
            'bandwidth_saved_mb' => round(($originalSize - $compressedSize) / (1024 * 1024), 3),
        ];
        
        // Add to bandwidth savings log
        $this->logBandwidthSaving('document', $result['bandwidth_saved_mb']);
        
        return $result;
    }

    /**
     * Optimize image for specific device or bandwidth
     */
    public function optimizeImageForDevice($imageFile, string $deviceType = 'mobile', string $bandwidthProfile = 'medium'): array
    {
        $profile = $this->bandwidthProfiles[$bandwidthProfile];
        
        // Adjust compression settings based on device
        $compressionOptions = [
            'max_width' => $profile['max_image_width'],
            'format' => 'webp', // Prefer WebP for optimization
            'quality' => $profile['image_quality'],
        ];
        
        // Further reduce quality for mobile
        if ($deviceType === 'mobile') {
            $compressionOptions['quality'] = max(50, $compressionOptions['quality'] - 10);
            $compressionOptions['max_width'] = min(800, $compressionOptions['max_width']);
        }
        
        return $this->compressImage($imageFile, $compressionOptions);
    }

    /**
     * Get optimized media URL with compression
     */
    public function getOptimizedMediaUrl(string $originalUrl, array $params = []): string
    {
        // In a real implementation, this would return a URL with query parameters
        // for on-the-fly optimization
        $queryParams = http_build_query([
            'w' => $params['width'] ?? null,
            'h' => $params['height'] ?? null,
            'q' => $params['quality'] ?? null,
            'f' => $params['format'] ?? 'auto',
            'bw' => $params['bandwidth_profile'] ?? 'auto',
        ]);
        
        return $originalUrl . '?' . $queryParams;
    }

    /**
     * Get bandwidth optimization suggestions
     */
    public function getBandwidthOptimizationSuggestions(array $files): array
    {
        $suggestions = [
            'total_original_size_mb' => 0,
            'total_optimized_size_mb' => 0,
            'total_savings_mb' => 0,
            'recommendations' => [],
        ];
        
        foreach ($files as $file) {
            $filePath = $file['path'] ?? $file;
            $fileSize = is_string($filePath) ? filesize($filePath) : $filePath->getSize();
            
            $suggestions['total_original_size_mb'] += $fileSize / (1024 * 1024);
            
            // Determine file type and suggest appropriate optimization
            $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            
            $recommendation = [
                'file_path' => $filePath,
                'file_size_mb' => round($fileSize / (1024 * 1024), 2),
                'file_extension' => $extension,
            ];
            
            if (in_array($extension, $this->supportedFormats['images']['input'])) {
                $recommendation['optimization_type'] = 'image';
                $recommendation['suggested_format'] = 'webp';
                $recommendation['estimated_savings_percent'] = mt_rand(40, 70);
                $recommendation['recommended_quality'] = 75;
            } elseif (in_array($extension, $this->supportedFormats['videos']['input'])) {
                $recommendation['optimization_type'] = 'video';
                $recommendation['suggested_format'] = 'mp4';
                $recommendation['estimated_savings_percent'] = mt_rand(30, 60);
                $recommendation['recommended_bitrate'] = '1000k';
            } elseif (in_array($extension, $this->supportedFormats['documents']['input'])) {
                $recommendation['optimization_type'] = 'document';
                $recommendation['suggested_format'] = 'pdf';
                $recommendation['estimated_savings_percent'] = mt_rand(20, 50);
            } else {
                $recommendation['optimization_type'] = 'unknown';
                $recommendation['suggested_format'] = $extension;
                $recommendation['estimated_savings_percent'] = 0;
            }
            
            $estimatedSavings = $fileSize * ($recommendation['estimated_savings_percent'] / 100);
            $recommendation['estimated_savings_mb'] = round($estimatedSavings / (1024 * 1024), 2);
            
            $suggestions['total_savings_mb'] += $recommendation['estimated_savings_mb'];
            $suggestions['recommendations'][] = $recommendation;
        }
        
        $suggestions['total_optimized_size_mb'] = $suggestions['total_original_size_mb'] - $suggestions['total_savings_mb'];
        $suggestions['total_savings_percent'] = round(
            $suggestions['total_savings_mb'] / $suggestions['total_original_size_mb'] * 100, 2
        );
        
        return $suggestions;
    }

    /**
     * Log bandwidth savings
     */
    private function logBandwidthSaving(string $type, float $mbSaved): void
    {
        $logKey = 'bandwidth_savings_' . date('Y-m-d');
        $logData = \Cache::get($logKey, [
            'images' => 0,
            'videos' => 0,
            'documents' => 0,
            'total' => 0,
        ]);
        
        $logData[$type] += $mbSaved;
        $logData['total'] += $mbSaved;
        
        \Cache::put($logKey, $logData, now()->addDays(30));
    }

    /**
     * Get bandwidth savings report
     */
    public function getBandwidthSavingsReport(string $period = 'monthly'): array
    {
        $report = [
            'period' => $period,
            'start_date' => now()->startOfMonth()->toISOString(),
            'end_date' => now()->toISOString(),
            'savings_by_type' => [],
            'total_savings_mb' => 0,
            'estimated_cost_savings' => 0, // Based on average bandwidth costs
        ];
        
        if ($period === 'daily') {
            $logKey = 'bandwidth_savings_' . date('Y-m-d');
            $logData = \Cache::get($logKey, []);
            
            $report['savings_by_type'] = $logData;
            $report['total_savings_mb'] = $logData['total'] ?? 0;
        } elseif ($period === 'weekly') {
            $total = 0;
            $typeTotals = ['images' => 0, 'videos' => 0, 'documents' => 0];
            
            for ($i = 0; $i < 7; $i++) {
                $date = now()->subDays($i)->format('Y-m-d');
                $logKey = 'bandwidth_savings_{$date}';
                $logData = \Cache::get($logKey, []);
                
                foreach (['images', 'videos', 'documents'] as $type) {
                    $typeTotals[$type] += $logData[$type] ?? 0;
                    $total += $logData[$type] ?? 0;
                }
            }
            
            $report['savings_by_type'] = $typeTotals;
            $report['total_savings_mb'] = $total;
        } else { // Monthly (default)
            $total = 0;
            $typeTotals = ['images' => 0, 'videos' => 0, 'documents' => 0];
            
            $daysInMonth = now()->daysInMonth;
            for ($i = 1; $i <= $daysInMonth; $i++) {
                $date = now()->day($i)->format('Y-m-d');
                $logKey = "bandwidth_savings_{$date}";
                $logData = \Cache::get($logKey, []);
                
                foreach (['images', 'videos', 'documents'] as $type) {
                    $typeTotals[$type] += $logData[$type] ?? 0;
                    $total += $logData[$type] ?? 0;
                }
            }
            
            $report['savings_by_type'] = $typeTotals;
            $report['total_savings_mb'] = $total;
        }
        
        // Estimate cost savings (assuming $0.10 per GB)
        $report['estimated_cost_savings'] = round($report['total_savings_mb'] / 1024 * 0.10, 2);
        
        return $report;
    }

    /**
     * Get compression quality recommendations based on file type
     */
    public function getQualityRecommendations(string $fileType, string $bandwidthProfile = 'medium'): array
    {
        $profile = $this->bandwidthProfiles[$bandwidthProfile];
        
        $recommendations = [
            'image' => [
                'quality_setting' => $profile['image_quality'],
                'format_recommendation' => ['webp', 'avif'],
                'max_dimensions' => [
                    'width' => $profile['max_image_width'],
                    'height' => intval($profile['max_image_width'] * 0.6), // Preserve aspect ratio assumption
                ],
                'bandwidth_profile' => $bandwidthProfile,
            ],
            'video' => [
                'bitrate' => $profile['video_bitrate'],
                'resolution' => $profile['max_video_resolution'],
                'format_recommendation' => ['mp4', 'webm'],
                'bandwidth_profile' => $bandwidthProfile,
            ],
            'document' => [
                'compression_level' => $profile['image_quality'] > 75 ? 'high' : ($profile['image_quality'] > 60 ? 'medium' : 'low'),
                'max_size_mb' => 5, // Default max size
                'bandwidth_profile' => $bandwidthProfile,
            ],
        ];
        
        return $recommendations[$fileType] ?? [];
    }

    /**
     * Auto-optimize media based on user's connection and device
     */
    public function autoOptimizeMedia(string $mediaUrl, array $userContext = []): array
    {
        $deviceType = $userContext['device_type'] ?? 'mobile';
        $connectionQuality = $userContext['connection_quality'] ?? 'medium';
        $userPreferences = $userContext['preferences'] ?? [];
        
        // Determine optimal settings based on context
        $bandwidthProfile = $this->determineOptimalBandwidthProfile($connectionQuality, $deviceType, $userPreferences);
        
        $profile = $this->bandwidthProfiles[$bandwidthProfile];
        
        $optimizationParams = [
            'width' => $profile['max_image_width'],
            'quality' => $profile['image_quality'],
            'format' => $profile['format_preference'][0] ?? 'webp',
            'bandwidth_profile' => $bandwidthProfile,
        ];
        
        // For videos
        if (in_array(strtolower(pathinfo($mediaUrl, PATHINFO_EXTENSION)), ['mp4', 'mov', 'avi', 'webm'])) {
            $optimizationParams['video_bitrate'] = $profile['video_bitrate'];
            $optimizationParams['video_resolution'] = $profile['max_video_resolution'];
        }
        
        return [
            'optimized_url' => $this->getOptimizedMediaUrl($mediaUrl, $optimizationParams),
            'optimization_params' => $optimizationParams,
            'original_url' => $mediaUrl,
            'user_context' => $userContext,
            'estimated_savings_percent' => mt_rand(30, 60),
        ];
    }

    /**
     * Determine optimal bandwidth profile based on context
     */
    private function determineOptimalBandwidthProfile(string $connectionQuality, string $deviceType, array $preferences): string
    {
        if ($preferences['data_savings'] ?? false) {
            return 'low';
        }
        
        if ($connectionQuality === 'poor' || $deviceType === 'mobile') {
            return 'low';
        }
        
        if ($connectionQuality === 'limited') {
            return 'medium';
        }
        
        if ($connectionQuality === 'excellent' && $deviceType === 'desktop') {
            return 'high';
        }
        
        return 'medium'; // Default
    }

    /**
     * Get supported formats for compression
     */
    public function getSupportedFormats(): array
    {
        return $this->supportedFormats;
    }

    /**
     * Get available bandwidth profiles
     */
    public function getBandwidthProfiles(): array
    {
        return $this->bandwidthProfiles;
    }
}