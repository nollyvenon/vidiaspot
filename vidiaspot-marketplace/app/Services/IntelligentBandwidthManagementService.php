<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class IntelligentBandwidthManagementService
{
    /**
     * Bandwidth optimization strategies
     */
    private array $optimizationStrategies = [
        'adaptive_streaming' => [
            'name' => 'Adaptive Streaming',
            'description' => 'Adjust quality based on current network conditions',
            'applicable_to' => ['video', 'audio'],
            'bandwidth_savings' => '20-40%',
            'implementation' => 'real-time_quality_adjustment',
        ],
        'progressive_loading' => [
            'name' => 'Progressive Loading',
            'description' => 'Load content in chunks to prevent overwhelming connection',
            'applicable_to' => ['images', 'documents', 'web_pages'],
            'bandwidth_savings' => '30-50%',
            'implementation' => 'chunked_content_delivery',
        ],
        'content_caching' => [
            'name' => 'Intelligent Caching',
            'description' => 'Cache frequently accessed content at strategic locations',
            'applicable_to' => ['all'],
            'bandwidth_savings' => '40-70%',
            'implementation' => 'edge_caching_with_priority',
        ],
        'resource_compression' => [
            'name' => 'Resource Compression',
            'description' => 'Compress resources before transmission',
            'applicable_to' => ['all'],
            'bandwidth_savings' => '30-60%',
            'implementation' => 'lossless_and_lossy_compression',
        ],
        'lazy_loading' => [
            'name' => 'Lazy Loading',
            'description' => 'Load resources only when needed',
            'applicable_to' => ['images', 'videos', 'components'],
            'bandwidth_savings' => '20-60%',
            'implementation' => 'intersection_observer_based',
        ],
        'prefetching' => [
            'name' => 'Smart Prefetching',
            'description' => 'Predictively load likely-to-be-needed resources',
            'applicable_to' => ['pages', 'assets'],
            'bandwidth_savings' => 'Reduce latency by 30%',
            'implementation' => 'machine_learning_prediction',
        ],
        'data_deduplication' => [
            'name' => 'Data Deduplication',
            'description' => 'Eliminate repetitive data transfers',
            'applicable_to' => ['all'],
            'bandwidth_savings' => '15-30%',
            'implementation' => 'content_fingerprinting',
        ],
        'protocol_optimization' => [
            'name' => 'Protocol Optimization',
            'description' => 'Use efficient protocols like HTTP/3, QUIC',
            'applicable_to' => ['all'],
            'bandwidth_savings' => '20-35%',
            'implementation' => 'advanced_protocol_implementation',
        ],
    ];

    /**
     * Connection quality indicators
     */
    private array $connectionQualities = [
        'unknown' => [
            'name' => 'Unknown',
            'speed_range' => '0-∞',
            'recommended_strategies' => ['progressive_loading', 'content_caching'],
        ],
        'offline' => [
            'name' => 'Offline',
            'speed_range' => '0',
            'recommended_strategies' => ['offline_mode', 'cached_content'],
        ],
        'very_poor' => [
            'name' => 'Very Poor',
            'speed_range' => '0-0.1 Mbps',
            'recommended_strategies' => [
                'progressive_loading', 
                'resource_compression', 
                'text_only_mode', 
                'bandwidth_friendly_format'
            ],
        ],
        'poor' => [
            'name' => 'Poor',
            'speed_range' => '0.1-0.5 Mbps',
            'recommended_strategies' => [
                'progressive_loading', 
                'resource_compression', 
                'low_quality_mode',
            ],
        ],
        'limited' => [
            'name' => 'Limited',
            'speed_range' => '0.5-2 Mbps',
            'recommended_strategies' => [
                'progressive_loading', 
                'resource_compression', 
                'adaptive_streaming'
            ],
        ],
        'moderate' => [
            'name' => 'Moderate',
            'speed_range' => '2-5 Mbps',
            'recommended_strategies' => [
                'content_caching', 
                'lazy_loading',
                'prefetching'
            ],
        ],
        'good' => [
            'name' => 'Good',
            'speed_range' => '5-10 Mbps',
            'recommended_strategies' => [
                'content_caching',
                'prefetching',
                'adaptive_streaming'
            ],
        ],
        'excellent' => [
            'name' => 'Excellent',
            'speed_range' => '10+ Mbps',
            'recommended_strategies' => [
                'full_quality',
                'prefetching',
                'protocol_optimization'
            ],
        ],
    ];

    /**
     * Bandwidth profiles with specific optimizations
     */
    private array $bandwidthProfiles = [
        'emergency_mode' => [
            'name' => 'Emergency Bandwidth Mode',
            'description' => 'Minimum data usage mode for very limited connections',
            'target_bandwidth_reduction' => '70-80%',
            'features' => [
                'text_only_mode',
                'remove_images',
                'disable_auto_play_videos',
                'minimal_css',
                'no_javascript',
                'compressed_content_only',
            ],
            'strategies' => ['resource_compression', 'progressive_loading'],
        ],
        'data_saver' => [
            'name' => 'Data Saver',
            'description' => 'Reduce data usage while maintaining functionality',
            'target_bandwidth_reduction' => '40-60%',
            'features' => [
                'compressed_images',
                'low_quality_video',
                'deferred_script_loading',
                'minified_css_js',
            ],
            'strategies' => ['resource_compression', 'lazy_loading'],
        ],
        'balanced' => [
            'name' => 'Balanced Mode',
            'description' => 'Balance between performance and user experience',
            'target_bandwidth_reduction' => '20-30%',
            'features' => [
                'moderate_compression',
                'progressive_image_loading',
                'adaptive_video_quality',
            ],
            'strategies' => ['content_caching', 'lazy_loading'],
        ],
        'performance' => [
            'name' => 'Performance Mode',
            'description' => 'Prioritize speed with moderate data efficiency',
            'target_bandwidth_reduction' => '10-20%',
            'features' => [
                'prefetching',
                'preloading',
                'full_quality_images',
            ],
            'strategies' => ['prefetching', 'protocol_optimization'],
        ],
        'unrestricted' => [
            'name' => 'Unrestricted Mode',
            'description' => 'No bandwidth limitations',
            'target_bandwidth_reduction' => '0%',
            'features' => [
                'full_quality_everything',
                'automatic_prefetching',
                'rich_multimedia',
            ],
            'strategies' => ['full_quality'],
        ],
    ];

    /**
     * Content types and their bandwidth optimization options
     */
    private array $contentOptimizationOptions = [
        'images' => [
            'compress' => true,
            'lazy_load' => true,
            'responsive_sizes' => true,
            'format_conversion' => ['webp', 'avif'],
            'quality_levels' => [
                'emergency_mode' => 30,
                'data_saver' => 50,
                'balanced' => 75,
                'performance' => 90,
                'unrestricted' => 95,
            ],
            'size_limits' => [
                'emergency_mode' => 'small',
                'data_saver' => 'medium',
                'balanced' => 'large',
                'performance' => 'original',
                'unrestricted' => 'original',
            ],
        ],
        'videos' => [
            'compress' => true,
            'adaptive_quality' => true,
            'preload' => false,
            'format_conversion' => ['mp4', 'webm'],
            'bitrate_options' => [
                'emergency_mode' => '200k',
                'data_saver' => '600k',
                'balanced' => '1000k',
                'performance' => '1500k',
                'unrestricted' => '2000k',
            ],
            'resolution_limits' => [
                'emergency_mode' => '240p',
                'data_saver' => '480p',
                'balanced' => '720p',
                'performance' => '1080p',
                'unrestricted' => 'original',
            ],
        ],
        'documents' => [
            'compress' => true,
            'optimization_level' => [
                'emergency_mode' => 'high',
                'data_saver' => 'medium',
                'balanced' => 'low',
                'performance' => 'none',
                'unrestricted' => 'none',
            ],
            'download_modes' => [
                'emergency_mode' => 'chunks',
                'data_saver' => 'chunks',
                'balanced' => 'full',
                'performance' => 'full',
                'unrestricted' => 'full',
            ],
        ],
        'javascript' => [
            'minify' => true,
            'bundle' => true,
            'defer_load' => true,
            'gzip_compress' => true,
            'load_strategies' => [
                'emergency_mode' => 'defer',
                'data_saver' => 'defer',
                'balanced' => 'normal',
                'performance' => 'async',
                'unrestricted' => 'normal',
            ],
        ],
        'css' => [
            'minify' => true,
            'bundle' => true,
            'preload_critical' => true,
            'gzip_compress' => true,
            'inline_critical' => [
                'emergency_mode' => true,
                'data_saver' => true,
                'balanced' => false,
                'performance' => false,
                'unrestricted' => false,
            ],
        ],
    ];

    /**
     * Get available optimization strategies
     */
    public function getOptimizationStrategies(): array
    {
        return $this->optimizationStrategies;
    }

    /**
     * Get connection quality definitions
     */
    public function getConnectionQualities(): array
    {
        return $this->connectionQualities;
    }

    /**
     * Get bandwidth profiles
     */
    public function getBandwidthProfiles(): array
    {
        return $this->bandwidthProfiles;
    }

    /**
     * Get content optimization options
     */
    public function getContentOptimizationOptions(): array
    {
        return $this->contentOptimizationOptions;
    }

    /**
     * Assess connection quality for a user
     */
    public function assessConnectionQuality(string $userId, array $connectionData = []): array
    {
        $quality = 'unknown';

        // Assess based on various factors
        if (isset($connectionData['network_type'])) {
            switch ($connectionData['network_type']) {
                case '4g':
                    $quality = 'good';
                    break;
                case '3g':
                    $quality = 'limited';
                    break;
                case '2g':
                    $quality = 'poor';
                    break;
                case 'wifi':
                    if (isset($connectionData['signal_strength'])) {
                        $quality = $connectionData['signal_strength'] > 80 ? 'excellent' : 'good';
                    } else {
                        $quality = 'moderate'; // Default for WiFi
                    }
                    break;
                case 'ethernet':
                    $quality = 'excellent';
                    break;
                case 'offline':
                    $quality = 'offline';
                    break;
            }
        }

        // Assess based on speed if provided
        if (isset($connectionData['download_speed_mbps'])) {
            $speed = $connectionData['download_speed_mbps'];
            
            if ($speed < 0.1) {
                $quality = 'very_poor';
            } elseif ($speed < 0.5) {
                $quality = 'poor';
            } elseif ($speed < 2) {
                $quality = 'limited';
            } elseif ($speed < 5) {
                $quality = 'moderate';
            } elseif ($speed < 10) {
                $quality = 'good';
            } else {
                $quality = 'excellent';
            }
        }

        // Store connection quality assessment
        $cacheKey = "user_connection_quality_{$userId}";
        $assessment = [
            'quality' => $quality,
            'assessed_at' => now()->toISOString(),
            'connection_data' => $connectionData,
        ];
        
        Cache::put($cacheKey, $assessment, now()->addMinutes(30));

        return [
            'user_id' => $userId,
            'connection_quality' => $quality,
            'quality_definition' => $this->connectionQualities[$quality] ?? $this->connectionQualities['unknown'],
            'recommended_strategies' => $this->connectionQualities[$quality]['recommended_strategies'] ?? [],
            'assessed_at' => now()->toISOString(),
            'connection_data' => $connectionData,
        ];
    }

    /**
     * Get bandwidth optimization settings for a user
     */
    public function getBandwidthOptimizationSettings(string $userId, string $mode = 'auto'): array
    {
        // If mode is auto, determine from connection quality assessment
        if ($mode === 'auto') {
            $qualityAssessment = $this->getConnectionQuality($userId);
            $quality = $qualityAssessment['connection_quality'];
            
            // Map connection quality to bandwidth profile
            $mode = $this->mapQualityToProfile($quality);
        }

        $profile = $this->bandwidthProfiles[$mode] ?? $this->bandwidthProfiles['balanced'];
        $contentOptions = $this->contentOptimizationOptions;

        // Customize content options based on selected mode
        $optimizedOptions = [];
        foreach ($contentOptions as $contentType => $options) {
            $optimizedOptions[$contentType] = $options;
            
            // Apply profile-specific settings
            if (isset($options['quality_levels'][$mode])) {
                $optimizedOptions[$contentType]['quality_level'] = $options['quality_levels'][$mode];
            }
            if (isset($options['size_limits'][$mode])) {
                $optimizedOptions[$contentType]['size_limit'] = $options['size_limits'][$mode];
            }
            if (isset($options['bitrate_options'][$mode])) {
                $optimizedOptions[$contentType]['bitrate_option'] = $options['bitrate_options'][$mode];
            }
            if (isset($options['optimization_level'][$mode])) {
                $optimizedOptions[$contentType]['optimization_level'] = $options['optimization_level'][$mode];
            }
        }

        // Store settings for user
        $settingsKey = "bandwidth_settings_{$userId}";
        $settings = [
            'mode' => $mode,
            'profile' => $profile,
            'content_options' => $optimizedOptions,
            'applied_at' => now()->toISOString(),
        ];
        
        Cache::put($settingsKey, $settings, now()->addHours(2));

        return [
            'settings' => $settings,
            'user_id' => $userId,
            'message' => "Bandwidth optimization settings configured for {$mode} mode"
        ];
    }

    /**
     * Map connection quality to appropriate profile
     */
    private function mapQualityToProfile(string $quality): string
    {
        return match($quality) {
            'offline', 'very_poor' => 'emergency_mode',
            'poor' => 'data_saver',
            'limited' => 'data_saver',
            'moderate' => 'balanced',
            'good' => 'balanced',
            'excellent' => 'performance',
            default => 'balanced',
        };
    }

    /**
     * Optimize content for specific bandwidth conditions
     */
    public function optimizeContentForBandwidth(string $contentId, string $contentType, string $userId, string $bandwidthMode = 'auto'): array
    {
        if ($bandwidthMode === 'auto') {
            $settings = $this->getBandwidthOptimizationSettings($userId);
            $bandwidthMode = $settings['settings']['mode'];
        }

        $contentOptions = $this->contentOptimizationOptions[$contentType] ?? [];
        $profileOptions = $this->bandwidthProfiles[$bandwidthMode];

        // Determine optimization parameters based on mode and content type
        $optimizationParams = [
            'content_id' => $contentId,
            'content_type' => $contentType,
            'bandwidth_mode' => $bandwidthMode,
            'should_compress' => $contentOptions['compress'] ?? false,
            'quality_level' => $contentOptions['quality_levels'][$bandwidthMode] ?? 75,
            'size_limit' => $contentOptions['size_limits'][$bandwidthMode] ?? 'large',
            'strategies' => $profileOptions['strategies'],
            'features' => $profileOptions['features'],
        ];

        // For images
        if ($contentType === 'images') {
            $optimizationParams['format'] = $this->getOptimalImageFormat($bandwidthMode);
            $optimizationParams['should_lazy_load'] = $contentOptions['lazy_load'] ?? true;
        }

        // For videos
        if ($contentType === 'videos') {
            $optimizationParams['bitrate'] = $contentOptions['bitrate_options'][$bandwidthMode] ?? '1000k';
            $optimizationParams['resolution'] = $contentOptions['resolution_limits'][$bandwidthMode] ?? '720p';
        }

        // For documents
        if ($contentType === 'documents') {
            $optimizationParams['optimization_level'] = $contentOptions['optimization_level'][$bandwidthMode] ?? 'medium';
        }

        // Store optimization decision
        $decisionKey = "content_optimization_decision_{$contentId}_{$userId}";
        Cache::put($decisionKey, $optimizationParams, now()->addHours(1));

        return [
            'optimized_params' => $optimizationParams,
            'content_id' => $contentId,
            'content_type' => $contentType,
            'user_id' => $userId,
            'bandwidth_mode' => $bandwidthMode,
            'message' => "Content optimization parameters determined for {$contentType} in {$bandwidthMode} mode"
        ];
    }

    /**
     * Get optimal image format based on bandwidth mode
     */
    private function getOptimalImageFormat(string $bandwidthMode): string
    {
        return match($bandwidthMode) {
            'emergency_mode', 'data_saver' => 'webp', // Smallest file size
            'balanced' => 'webp', // Good balance of quality and size
            'performance', 'unrestricted' => 'jpeg', // For compatibility
            default => 'webp',
        };
    }

    /**
     * Get user's connection quality
     */
    private function getConnectionQuality(string $userId): array
    {
        $cacheKey = "user_connection_quality_{$userId}";
        return \Cache::get($cacheKey, [
            'quality' => 'unknown',
            'assessed_at' => now()->toISOString(),
            'connection_data' => [],
        ]);
    }

    /**
     * Calculate potential bandwidth savings
     */
    public function calculateBandwidthSavings(array $contentItems, string $bandwidthMode = 'balanced'): array
    {
        $totalOriginalSize = 0;
        $totalOptimizedSize = 0;
        
        $savings = [];
        
        foreach ($contentItems as $item) {
            $originalSize = $item['size_bytes'] ?? 0;
            
            if ($originalSize === 0) {
                continue; // Skip if no size provided
            }
            
            // Estimate optimized size based on content type and bandwidth mode
            $sizeReductionFactor = $this->getSizeReductionFactor($item['type'], $bandwidthMode);
            $optimizedSize = $originalSize * $sizeReductionFactor;
            
            $savings[] = [
                'content_id' => $item['id'],
                'content_type' => $item['type'],
                'original_size_bytes' => $originalSize,
                'optimized_size_bytes' => intval($optimizedSize),
                'bandwidth_saved_bytes' => $originalSize - intval($optimizedSize),
                'bandwidth_saved_mb' => round(($originalSize - $optimizedSize) / (1024 * 1024), 3),
                'estimated_savings_percent' => round((($originalSize - $optimizedSize) / $originalSize) * 100, 2),
            ];
            
            $totalOriginalSize += $originalSize;
            $totalOptimizedSize += $optimizedSize;
        }

        return [
            'savings_breakdown' => $savings,
            'total_items' => count($savings),
            'total_original_size_mb' => round($totalOriginalSize / (1024 * 1024), 3),
            'total_optimized_size_mb' => round($totalOptimizedSize / (1024 * 1024), 3),
            'total_bandwidth_saved_mb' => round(($totalOriginalSize - $totalOptimizedSize) / (1024 * 1024), 3),
            'total_savings_percent' => $totalOriginalSize > 0 ? 
                                     round((($totalOriginalSize - $totalOptimizedSize) / $totalOriginalSize) * 100, 2) : 0,
            'bandwidth_mode' => $bandwidthMode,
            'estimated_cost_savings' => round(($totalOriginalSize - $totalOptimizedSize) / (1024 * 1024) * 0.05, 2), // Assuming $0.05 per MB of data transfer
        ];
    }

    /**
     * Get size reduction factor based on content type and bandwidth mode
     */
    private function getSizeReductionFactor(string $contentType, string $bandwidthMode): float
    {
        $reductionFactors = [
            'images' => [
                'emergency_mode' => 0.2,    // 80% reduction
                'data_saver' => 0.3,       // 70% reduction
                'balanced' => 0.5,         // 50% reduction
                'performance' => 0.7,      // 30% reduction
                'unrestricted' => 0.9,     // 10% reduction
            ],
            'videos' => [
                'emergency_mode' => 0.1,   // 90% reduction (much lower quality)
                'data_saver' => 0.2,       // 80% reduction
                'balanced' => 0.4,         // 60% reduction
                'performance' => 0.7,      // 30% reduction
                'unrestricted' => 0.95,    // 5% reduction (high quality)
            ],
            'documents' => [
                'emergency_mode' => 0.3,   // 70% reduction
                'data_saver' => 0.4,       // 60% reduction
                'balanced' => 0.6,         // 40% reduction
                'performance' => 0.8,      // 20% reduction
                'unrestricted' => 0.95,    // 5% reduction
            ],
            'javascript' => [
                'emergency_mode' => 0.4,   // Minification + deferred loading
                'data_saver' => 0.5,       // Minification + bundling
                'balanced' => 0.7,         // Some minification
                'performance' => 0.7,      // Standard minification
                'unrestricted' => 0.85,    // Minimal optimization
            ],
            'css' => [
                'emergency_mode' => 0.3,   // Heavy minification + critical CSS inlining
                'data_saver' => 0.4,       // Minification + bundling
                'balanced' => 0.65,        // Standard optimization
                'performance' => 0.7,      // Standard optimization
                'unrestricted' => 0.85,    // Minimal optimization
            ],
        ];

        return $reductionFactors[$contentType][$bandwidthMode] ?? 0.7; // Default to balanced if not found
    }

    /**
     * Get bandwidth optimization recommendations for content
     */
    public function getOptimizationRecommendations(array $contentItems, string $userId, string $bandwidthMode = 'auto'): array
    {
        if ($bandwidthMode === 'auto') {
            $settings = $this->getBandwidthOptimizationSettings($userId);
            $bandwidthMode = $settings['settings']['mode'];
        }

        $recommendations = [];
        
        foreach ($contentItems as $item) {
            $rec = [
                'content_id' => $item['id'],
                'content_type' => $item['type'],
                'original_size_mb' => round(($item['size_bytes'] ?? 0) / (1024 * 1024), 3),
                'recommendation' => 'none',
                'suggested_optimization' => null,
                'estimated_savings_mb' => 0,
                'priority' => 'low',
            ];

            // Determine recommendation based on content type, size, and bandwidth mode
            if (isset($item['size_bytes'])) {
                $sizeMb = $item['size_bytes'] / (1024 * 1024);
                
                if ($item['type'] === 'images') {
                    if ($sizeMb > 2) { // Large images
                        $rec['recommendation'] = 'compress_heavily';
                        $rec['suggested_optimization'] = [
                            'format' => 'webp',
                            'quality' => $this->contentOptimizationOptions['images']['quality_levels'][$bandwidthMode] ?? 75,
                        ];
                        $rec['estimated_savings_mb'] = $sizeMb * 0.6; // Estimate 60% savings
                        $rec['priority'] = 'high';
                    } elseif ($sizeMb > 1) { // Medium images
                        $rec['recommendation'] = 'compress_moderately';
                        $rec['suggested_optimization'] = [
                            'format' => $bandwidthMode === 'emergency_mode' ? 'webp' : 'jpeg',
                            'quality' => $this->contentOptimizationOptions['images']['quality_levels'][$bandwidthMode] ?? 75,
                        ];
                        $rec['estimated_savings_mb'] = $sizeMb * 0.4; // Estimate 40% savings
                        $rec['priority'] = 'medium';
                    }
                } elseif ($item['type'] === 'videos') {
                    if ($sizeMb > 50 || $bandwidthMode !== 'unrestricted') { // Videos in any mode except unrestricted
                        $rec['recommendation'] = 'adaptive_streaming';
                        $rec['suggested_optimization'] = [
                            'bitrate' => $this->contentOptimizationOptions['videos']['bitrate_options'][$bandwidthMode] ?? '1000k',
                            'resolution' => $this->contentOptimizationOptions['videos']['resolution_limits'][$bandwidthMode] ?? '720p',
                        ];
                        $rec['estimated_savings_mb'] = $sizeMb * 0.5; // Estimate 50% savings
                        $rec['priority'] = 'high';
                    }
                } elseif ($item['type'] === 'documents') {
                    if ($sizeMb > 5) { // Large documents
                        $rec['recommendation'] = 'compress_document';
                        $rec['suggested_optimization'] = [
                            'optimization_level' => $this->contentOptimizationOptions['documents']['optimization_level'][$bandwidthMode] ?? 'medium',
                        ];
                        $rec['estimated_savings_mb'] = $sizeMb * 0.3; // Estimate 30% savings
                        $rec['priority'] = 'medium';
                    }
                }
            }

            $recommendations[] = $rec;
        }

        return [
            'recommendations' => $recommendations,
            'user_id' => $userId,
            'bandwidth_mode' => $bandwidthMode,
            'total_recommendations' => count($recommendations),
            'high_priority_count' => count(array_filter($recommendations, fn($r) => $r['priority'] === 'high')),
            'total_estimated_savings_mb' => array_sum(array_column($recommendations, 'estimated_savings_mb')),
        ];
    }

    /**
     * Get dynamic quality settings for media based on real-time connection
     */
    public function getDynamicQualitySettings(string $userId, string $contentType, array $context = []): array
    {
        // Get current connection quality assessment
        $qualityAssessment = $this->getConnectionQuality($userId);
        $connectionQuality = $qualityAssessment['quality'];

        // Determine appropriate settings based on connection and content type
        $qualitySettings = [
            'image_quality' => 85,
            'video_bitrate' => '1000k',
            'video_resolution' => '720p',
            'compression_level' => 'medium',
            'adaptive_streaming' => true,
        ];

        // Adjust settings based on connection quality
        switch ($connectionQuality) {
            case 'offline':
                return [
                    'streaming_method' => 'cached_only',
                    'quality' => 'none',
                    'message' => 'Offline mode - using cached content only',
                ];
            case 'very_poor':
                $qualitySettings = [
                    'image_quality' => 30,
                    'video_bitrate' => '200k',
                    'video_resolution' => '240p',
                    'compression_level' => 'high',
                    'adaptive_streaming' => false,
                ];
                break;
            case 'poor':
                $qualitySettings = [
                    'image_quality' => 45,
                    'video_bitrate' => '400k',
                    'video_resolution' => '360p',
                    'compression_level' => 'high',
                    'adaptive_streaming' => false,
                ];
                break;
            case 'limited':
                $qualitySettings = [
                    'image_quality' => 60,
                    'video_bitrate' => '600k',
                    'video_resolution' => '480p',
                    'compression_level' => 'medium',
                    'adaptive_streaming' => true,
                ];
                break;
            case 'moderate':
                $qualitySettings = [
                    'image_quality' => 75,
                    'video_bitrate' => '800k',
                    'video_resolution' => '720p',
                    'compression_level' => 'medium',
                    'adaptive_streaming' => true,
                ];
                break;
            case 'good':
                $qualitySettings = [
                    'image_quality' => 85,
                    'video_bitrate' => '1200k',
                    'video_resolution' => '720p',
                    'compression_level' => 'low',
                    'adaptive_streaming' => true,
                ];
                break;
            case 'excellent':
                $qualitySettings = [
                    'image_quality' => 95,
                    'video_bitrate' => '2000k',
                    'video_resolution' => '1080p',
                    'compression_level' => 'none',
                    'adaptive_streaming' => true,
                ];
                break;
        }

        // Store these settings for the user session
        $settingsKey = "dynamic_quality_settings_{$userId}_{$contentType}";
        \Cache::put($settingsKey, $qualitySettings, now()->addMinutes(15));

        return [
            'quality_settings' => $qualitySettings,
            'connection_quality' => $connectionQuality,
            'content_type' => $contentType,
            'user_id' => $userId,
            'context' => $context,
            'valid_until' => now()->addMinutes(15)->toISOString(),
        ];
    }

    /**
     * Optimize API responses based on bandwidth
     */
    public function optimizeApiResponse(array $responseData, string $userId): array
    {
        $settings = $this->getBandwidthOptimizationSettings($userId);

        $bandwidthMode = $settings['settings']['mode'];
        $profile = $settings['settings']['profile'];

        // For emergency mode, return minimal data
        if ($bandwidthMode === 'emergency_mode') {
            return $this->stripApiResponse($responseData);
        }

        // For data saver mode, reduce image URLs, limit text length, etc.
        if ($bandwidthMode === 'data_saver') {
            return $this->compressApiResponse($responseData, $bandwidthMode);
        }

        // For other modes, apply conditional optimizations
        if (in_array($bandwidthMode, ['data_saver', 'balanced'])) {
            return $this->optimizeApiResponseContent($responseData, $bandwidthMode);
        }

        // Otherwise return original response
        return $responseData;
    }

    /**
     * Strip API response for minimal bandwidth usage (emergency mode)
     */
    private function stripApiResponse(array $response): array
    {
        // Remove image URLs and other heavy data
        return $this->transformRecursively($response, function($item, $key) {
            if (is_string($item) && (str_contains($item, 'http') || str_contains($item, 'www'))) {
                // If it's a URL (especially image/video), remove it in emergency mode
                if (preg_match('/\.(jpg|jpeg|png|gif|webp|bmp|svg|mp4|webm|avi)$/', $item)) {
                    return null;
                }
            }
            
            if (is_string($item) && strlen($item) > 500) {
                // Truncate long text in emergency mode
                return substr($item, 0, 100) . '...';
            }
            
            return $item;
        });
    }

    /**
     * Compress API response (data saver mode)
     */
    private function compressApiResponse(array $response, string $mode): array
    {
        return $this->transformRecursively($response, function($item, $key) use ($mode) {
            if (is_string($item) && str_contains($item, 'http')) {
                // For images, convert to smaller format
                if (preg_match('/\.(jpg|jpeg|png)/', $item)) {
                    return str_replace(['.jpg', '.jpeg', '.png'], ['.webp', '.webp', '.webp'], $item);
                }
            }
            
            if (is_string($item) && strlen($item) > 500) {
                // Truncate long text in data saver mode
                return substr($item, 0, 200) . '...';
            }
            
            return $item;
        });
    }

    /**
     * Optimize API response content (balanced/other modes)
     */
    private function optimizeApiResponseContent(array $response, string $mode): array
    {
        return $this->transformRecursively($response, function($item, $key) use ($mode) {
            if (is_string($item) && str_contains($item, 'http')) {
                // For images, potentially redirect to optimized versions
                if (preg_match('/\.(jpg|jpeg|png|gif)/', $item)) {
                    if (in_array($mode, ['balanced', 'data_saver'])) {
                        // Add optimization parameters to the URL
                        $separator = strpos($item, '?') !== false ? '&' : '?';
                        return $item . "{$separator}optimize=true&format=webp&q=" . $this->getQualitySetting($mode);
                    }
                }
            }
            
            return $item;
        });
    }

    /**
     * Helper to transform arrays recursively
     */
    private function transformRecursively(array $array, callable $callback): array
    {
        $result = [];
        
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $result[$key] = $this->transformRecursively($value, $callback);
            } else {
                $result[$key] = $callback($value, $key);
            }
        }
        
        return $result;
    }

    /**
     * Get quality setting for mode
     */
    private function getQualitySetting(string $mode): int
    {
        $qualities = [
            'emergency_mode' => 30,
            'data_saver' => 50,
            'balanced' => 75,
            'performance' => 90,
            'unrestricted' => 95,
        ];
        
        return $qualities[$mode] ?? 75;
    }

    /**
     * Get bandwidth usage report for a user
     */
    public function getBandwidthUsageReport(string $userId, string $period = 'monthly'): array
    {
        // In a real implementation, this would aggregate actual bandwidth usage
        // For this implementation, we'll provide simulated data
        
        $usageData = [
            'period' => $period,
            'start_date' => now()->startOfMonth()->toISOString(),
            'end_date' => now()->toISOString(),
            'total_bandwidth_used_mb' => mt_rand(500, 5000),
            'bandwidth_saved_mb' => mt_rand(200, 2000),
            'savings_percentage' => mt_rand(15, 60) . '%',
            'content_types' => [
                'images' => [
                    'used_mb' => mt_rand(200, 1500),
                    'saved_mb' => mt_rand(50, 500),
                    'savings_percentage' => mt_rand(20, 40) . '%',
                ],
                'videos' => [
                    'used_mb' => mt_rand(100, 1000),
                    'saved_mb' => mt_rand(100, 600),
                    'savings_percentage' => mt_rand(30, 70) . '%',
                ],
                'documents' => [
                    'used_mb' => mt_rand(50, 300),
                    'saved_mb' => mt_rand(20, 150),
                    'savings_percentage' => mt_rand(10, 40) . '%',
                ],
                'api_requests' => [
                    'used_mb' => mt_rand(100, 500),
                    'saved_mb' => mt_rand(30, 200),
                    'savings_percentage' => mt_rand(15, 35) . '%',
                ],
            ],
            'optimization_strategies_used' => [
                'content_caching' => true,
                'resource_compression' => true,
                'lazy_loading' => true,
                'adaptive_streaming' => $period === 'monthly', // Only show for longer periods
                'progressive_loading' => true,
            ],
            'estimated_cost_savings' => round(
                mt_rand(200, 2000) * 0.05, 2 // Assuming $0.05 per MB
            ),
            'user_id' => $userId,
        ];

        return [
            'report' => $usageData,
            'message' => "Bandwidth usage report for {$period} period"
        ];
    }

    /**
     * Get network-aware content recommendations
     */
    public function getNetworkAwareRecommendations(string $userId, array $contentPreferences = []): array
    {
        $qualityAssessment = $this->getConnectionQuality($userId);
        $connectionQuality = $qualityAssessment['quality'];
        $bandwidthMode = $this->mapQualityToProfile($connectionQuality);

        // Simulate getting content recommendations that are aware of the network quality
        $recommendations = [
            'content_type' => 'image-rich',
            'bandwidth_aware' => true,
            'network_quality' => $connectionQuality,
            'suggested_bandwidth_mode' => $bandwidthMode,
            'recommendations' => [
                [
                    'id' => 'rec-' . Str::random(8),
                    'type' => 'product_image',
                    'title' => 'Optimized Product Images',
                    'description' => 'Image recommendations optimized for your network conditions',
                    'format' => 'webp',
                    'size_estimate_mb' => 0.5,
                    'quality_setting' => $this->getQualitySetting($bandwidthMode),
                ],
                [
                    'id' => 'rec-' . Str::random(8),
                    'type' => 'video_preview',
                    'title' => 'Video Previews',
                    'description' => 'Short previews optimized for quick loading',
                    'format' => 'mp4',
                    'size_estimate_mb' => 2.0,
                    'quality_setting' => $this->getQualitySetting($bandwidthMode),
                ],
                [
                    'id' => 'rec-' . Str::random(8),
                    'type' => 'progressive_content',
                    'title' => 'Progressive Loading Content',
                    'description' => 'Content loaded in stages based on your connection',
                    'format' => 'progressive',
                    'size_estimate_mb' => 1.2,
                    'quality_setting' => $this->getQualitySetting($bandwidthMode),
                ],
            ]
        ];

        return [
            'recommendations' => $recommendations,
            'user_id' => $userId,
            'connection_quality' => $connectionQuality,
            'suggested_mode' => $bandwidthMode,
        ];
    }
}