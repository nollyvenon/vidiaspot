<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ProcessImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $imagePath;
    protected $operations; // Array of operations to perform on the image

    public function __construct(string $imagePath, array $operations = [])
    {
        $this->imagePath = $imagePath;
        $this->operations = $operations;
    }

    public function handle()
    {
        try {
            // Check if image exists
            if (!Storage::exists($this->imagePath)) {
                throw new \Exception("Image not found: {$this->imagePath}");
            }

            // Get image path
            $fullPath = Storage::disk('public')->path($this->imagePath);
            
            // Process image with Intervention Image
            $image = Image::make($fullPath);

            // Apply operations
            foreach ($this->operations as $operation => $params) {
                switch ($operation) {
                    case 'resize':
                        $width = $params['width'] ?? 800;
                        $height = $params['height'] ?? 600;
                        $image->resize($width, $height, function ($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        });
                        break;
                        
                    case 'crop':
                        $width = $params['width'] ?? 300;
                        $height = $params['height'] ?? 300;
                        $x = $params['x'] ?? 0;
                        $y = $params['y'] ?? 0;
                        $image->crop($width, $height, $x, $y);
                        break;
                        
                    case 'watermark':
                        if (isset($params['watermark_path'])) {
                            $watermark = Image::make(storage_path("app/public/{$params['watermark_path']}"));
                            $position = $params['position'] ?? 'bottom-right';
                            
                            $image->insert($watermark, $position, 10, 10);
                        }
                        break;
                        
                    case 'optimize':
                        // Just keep the current image for now
                        break;
                        
                    case 'thumbnail':
                        $thumbWidth = $params['width'] ?? 200;
                        $thumbHeight = $params['height'] ?? 200;
                        $image->fit($thumbWidth, $thumbHeight);
                        
                        // Save thumbnail
                        $pathInfo = pathinfo($this->imagePath);
                        $thumbPath = "{$pathInfo['dirname']}/thumb_{$pathInfo['basename']}";
                        $image->save(Storage::disk('public')->path($thumbPath));
                        break;
                        
                    case 'quality':
                        $quality = $params['quality'] ?? 80;
                        break;
                        
                    case 'format':
                        // Change format if needed
                        break;
                        
                    default:
                        \Log::warning("Unknown image operation: {$operation}");
                        break;
                }
            }

            // Apply default optimizations
            if (empty($this->operations) || !in_array('optimize', array_keys($this->operations))) {
                // Resize if too large
                if ($image->width() > 1920 || $image->height() > 1080) {
                    $image->resize(1920, 1080, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                }
            }
            
            // Save image with optimized quality
            $image->save($fullPath, 85);
            
            \Log::info("Image processed successfully: {$this->imagePath}");
        } catch (\Exception $e) {
            \Log::error("Failed to process image {$this->imagePath}: " . $e->getMessage());
            $this->fail($e);
        }
    }
}