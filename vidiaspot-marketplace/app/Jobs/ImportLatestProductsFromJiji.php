<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Ad;
use App\Models\User;
use App\Models\Category;
use Carbon\Carbon;
use Symfony\Component\DomCrawler\Crawler;

class ImportLatestProductsFromJiji implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $days;
    protected $url;
    protected $settings;
    
    public function __construct($days = 3, $url = 'https://jiji.ng', $settings = [])
    {
        $this->days = $days;
        $this->url = $url;
        $this->settings = $settings;
    }

    public function handle()
    {
        try {
            Log::info("Starting product import from jiji.ng for last {$this->days} days");
            
            // Calculate the date threshold
            $dateThreshold = Carbon::now()->subDays($this->days);
            
            // Fetch categories to map to imported products
            $categories = Category::select('id', 'name', 'slug')->get()->keyBy('name');
            
            // Fetch the main page or a specific page to get listings
            $listings = $this->fetchProductListings($dateThreshold);
            
            $productsImported = 0;
            
            // Get settings for duplicate check
            $settings = ProductImportSettings::getCurrentSettings();

            foreach ($listings as $listing) {
                if ($this->shouldImportProduct($listing, $dateThreshold, $settings)) {
                    $this->importProduct($listing, $categories);
                    $productsImported++;

                    // Small delay to be respectful to the server
                    usleep(500000); // 0.5 second
                }
            }
            
            Log::info("Product import completed. Imported {$productsImported} products from jiji.ng for last {$this->days} days");
        } catch (\Exception $e) {
            Log::error('Error importing products from jiji.ng: ' . $e->getMessage());
            throw $e; // Re-throw to trigger retry logic
        }
    }
    
    protected function fetchProductListings($dateThreshold)
    {
        $listings = [];
        $page = 1;
        $continue = true;
        
        while ($continue && $page <= 10) { // Limit to 10 pages to avoid infinite loops
            $pageUrl = $this->url . '/?page=' . $page;
            
            try {
                $response = Http::timeout(30)->get($pageUrl);
                
                if (!$response->successful()) {
                    Log::error('Failed to fetch jiji.ng page: ' . $pageUrl . ' - ' . $response->status());
                    break;
                }
                
                $html = $response->body();
                $crawler = new Crawler($html);
                
                // Find product listings (this selector would need to match jiji.ng's actual structure)
                $productElements = $crawler->filter('.search-item, .b-advert, .item-card, .product-card');
                
                if ($productElements->count() === 0) {
                    // If no products found, break the loop
                    break;
                }
                
                $pageHasRecentProducts = false;
                
                $productElements->each(function (Crawler $node) use (&$listings, &$pageHasRecentProducts, $dateThreshold) {
                    $listing = $this->extractProductData($node);
                    
                    if ($listing) {
                        // Check if product is within our date threshold
                        $productDate = $listing['created_at'] ?? Carbon::now();
                        
                        if ($productDate->gte($dateThreshold)) {
                            $listings[] = $listing;
                            $pageHasRecentProducts = true;
                        } else {
                            // If a product is older than our threshold, 
                            // we might stop if the listings are sorted by date
                            $continue = false;
                        }
                    }
                });
                
                if (!$pageHasRecentProducts) {
                    // If no recent products on this page, stop
                    break;
                }
                
                $page++;
            } catch (\Exception $e) {
                Log::warning("Error fetching page {$page} from jiji.ng: " . $e->getMessage());
                break;
            }
        }
        
        return $listings;
    }
    
    protected function extractProductData(Crawler $node)
    {
        try {
            // This is a template. In real implementation, we'd need to match jiji.ng's structure
            $title = $node->filter('.title, .name, .b-advert__title, .search-item__title')->first()->text();
            $priceText = $node->filter('.price, .b-price__value, .search-item__price')->first()->text();
            $location = $node->filter('.location, .b-location__value, .search-item__location')->first()->text();
            $imageUrl = $node->filter('img')->first()->attr('src') ?? '';
            $productUrl = $node->filter('a')->first()->attr('href');
            
            // Extract and normalize price
            $price = $this->extractPrice($priceText);
            
            // Normalize product URL
            if (!filter_var($productUrl, FILTER_VALIDATE_URL)) {
                $productUrl = $this->url . $productUrl;
            }
            
            return [
                'title' => trim($title),
                'price' => $price,
                'location' => trim($location),
                'description' => $this->fetchProductDetails($productUrl), // Would fetch more details
                'image_url' => $imageUrl,
                'external_url' => $productUrl,
                'category' => $this->guessCategory($title), // Guess category from title
                'created_at' => Carbon::now() // This would come from actual posting date
            ];
        } catch (\Exception $e) {
            Log::warning("Error extracting product data: " . $e->getMessage());
            return null;
        }
    }
    
    protected function extractPrice($priceText)
    {
        // Extract numeric value from price string like "NGN 150,000" or "₦150,000"
        $priceText = preg_replace('/[^\d,.]/', '', $priceText);
        $priceText = str_replace(',', '', $priceText);
        $price = floatval($priceText);
        
        return $price > 0 ? $price : 0;
    }
    
    protected function guessCategory($title)
    {
        // Simple category guessing based on title keywords
        $titleLower = strtolower($title);
        
        $categoryKeywords = [
            'phone' => 'Mobile Phones',
            'car' => 'Cars',
            'laptop' => 'Laptops',
            'computer' => 'Computers',
            'furniture' => 'Furniture',
            'electronics' => 'Electronics',
            'book' => 'Books',
            'clothes' => 'Clothing',
            'shoes' => 'Footwear',
            'property' => 'Property',
            'real estate' => 'Property',
            'bike' => 'Bicycles',
            'motorcycle' => 'Motorcycles',
        ];
        
        foreach ($categoryKeywords as $keyword => $category) {
            if (strpos($titleLower, $keyword) !== false) {
                return $category;
            }
        }
        
        return 'Other';
    }
    
    protected function fetchProductDetails($url)
    {
        try {
            // Fetch details from product page
            $response = Http::timeout(30)->get($url);
            
            if (!$response->successful()) {
                return "Description not available";
            }
            
            $html = $response->body();
            $crawler = new Crawler($html);
            
            // Look for description in common selectors
            $descriptionSelectors = [
                '.description', '.product-description', '.item-description',
                '.b-advert__description', '.search-item__description', 'p'
            ];
            
            foreach ($descriptionSelectors as $selector) {
                $element = $crawler->filter($selector)->first();
                if ($element->count() > 0) {
                    return trim($element->text());
                }
            }
            
            return "Description not available";
        } catch (\Exception $e) {
            Log::warning("Error fetching product details from {$url}: " . $e->getMessage());
            return "Description not available";
        }
    }
    
    protected function shouldImportProduct($listing, $dateThreshold, $settings)
    {
        // Check if duplicate check is enabled
        if (!$settings->import_duplicate_check) {
            // If duplicate check is disabled, just check the date
            $productDate = $listing['created_at'];
            return $productDate->gte($dateThreshold);
        }

        // Check if we already have this product imported based on external URL or similar attributes
        $existingProduct = Ad::where('external_url', $listing['external_url'])
            ->first();

        if ($existingProduct) {
            return false; // Don't import duplicates based on external URL
        }

        // Additional check: if there's no external URL, check by title, price, location
        if (empty($listing['external_url'])) {
            $existingProduct = Ad::where('title', $listing['title'])
                ->where('price', $listing['price'])
                ->where('location', $listing['location'])
                ->first();

            if ($existingProduct) {
                return false; // Don't import duplicates
            }
        }

        // Check if product date is within threshold
        $productDate = $listing['created_at'];
        return $productDate->gte($dateThreshold);
    }
    
    protected function importProduct($listing, $categories)
    {
        // Find or create user for imported products
        $user = User::firstOrCreate(
            ['email' => 'imported@jiji.ng'],
            [
                'name' => 'Jiji Import Bot',
                'email_verified_at' => now(),
                'password' => bcrypt('imported_password'), // Secure password for bot account
            ]
        );

        // Find or create category with subcategory support
        $category = $this->findOrCreateCategory($listing['category'], $categories);

        // Check if this exact ad already exists to prevent any duplicates
        $existingAd = Ad::where('external_url', $listing['external_url'])
            ->orWhere(function($query) use ($listing) {
                $query->where('title', $listing['title'])
                      ->where('price', $listing['price'])
                      ->where('location', $listing['location']);
            })
            ->first();

        if ($existingAd) {
            Log::info("Skipping duplicate product: {$listing['title']}");
            return;
        }

        // Create the ad
        $ad = new Ad();
        $ad->user_id = $user->id;
        $ad->category_id = $category->id;
        $ad->title = $listing['title'];
        $ad->description = $listing['description'];
        $ad->price = $listing['price'];
        $ad->location = $listing['location'];
        $ad->status = 'active'; // Imported ads are active by default
        $ad->is_featured = false;
        $ad->negotiable = true; // Assume most items are negotiable
        $ad->currency_code = 'NGN'; // Nigerian naira
        $ad->external_source = 'jiji.ng';
        $ad->external_url = $listing['external_url'];
        $ad->save();

        // Import image if available
        if (!empty($listing['image_url'])) {
            $this->importProductImage($ad, $listing['image_url']);
        }

        Log::info("Imported product: {$listing['title']} in category {$category->name} from jiji.ng");
    }

    protected function findOrCreateCategory($categoryName, $categories)
    {
        // Check in the passed categories collection first
        $existingCategory = null;

        // Look for an exact match first
        foreach ($categories as $cat) {
            if ($cat->name === $categoryName) {
                $existingCategory = $cat;
                break;
            }
        }

        if (!$existingCategory) {
            // Look in database
            $existingCategory = Category::where('name', $categoryName)->first();
        }

        if ($existingCategory) {
            return $existingCategory;
        }

        // Split category name to handle potential subcategories (e.g., "Electronics > Phones")
        $categoryParts = array_map('trim', explode('>', $categoryName));

        if (count($categoryParts) > 1) {
            // Handle nested categories like "Electronics > Phones"
            $parentName = $categoryParts[0];
            $childName = $categoryParts[1];

            // Find or create parent category
            $parentCategory = Category::firstOrCreate(
                ['name' => $parentName],
                [
                    'slug' => \Str::slug($parentName),
                    'description' => "Parent category for imported items",
                    'status' => 'active',
                    'parent_id' => null,
                ]
            );

            // Find or create child category with parent
            $category = Category::firstOrCreate(
                ['name' => $childName, 'parent_id' => $parentCategory->id],
                [
                    'slug' => \Str::slug($childName),
                    'description' => "Subcategory under {$parentCategory->name}",
                    'status' => 'active',
                ]
            );
        } else {
            // Single category - find or create as main category
            $category = Category::firstOrCreate(
                ['name' => $categoryName],
                [
                    'slug' => \Str::slug($categoryName),
                    'description' => 'Category created during Jiji import',
                    'status' => 'active',
                    'parent_id' => null,
                ]
            );
        }

        return $category;
    }
    
    protected function importProductImage($ad, $imageUrl)
    {
        try {
            // Download and save the image
            $imageContent = Http::timeout(30)->get($imageUrl);
            
            if ($imageContent->successful()) {
                // Save the image to local storage
                $filename = 'imported_' . $ad->id . '_' . time() . '.' . pathinfo($imageUrl, PATHINFO_EXTENSION) ?: 'jpg';
                $path = 'imported_products/' . $filename;
                
                // Save to storage
                \Storage::disk('public')->put($path, $imageContent->body());
                
                // Create the ad image record
                $ad->images()->create([
                    'url' => $path,
                    'alt_text' => $ad->title . ' - Imported from jiji.ng',
                    'is_primary' => true,
                ]);
            }
        } catch (\Exception $e) {
            Log::warning("Error importing image for ad {$ad->id}: " . $e->getMessage());
        }
    }
}