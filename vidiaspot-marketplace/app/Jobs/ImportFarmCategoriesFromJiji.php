<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Category;
use Symfony\Component\DomCrawler\Crawler;

class ImportFarmCategoriesFromJiji implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $url;

    public function __construct($url = 'https://jiji.ng')
    {
        $this->url = $url;
    }

    public function handle()
    {
        try {
            Log::info('Starting farm category import from jiji.ng');

            // Fetch the main page
            $response = Http::timeout(30)->get($this->url . '/agriculture');

            if (!$response->successful()) {
                // If specific agriculture page doesn't exist, try main page
                $response = Http::timeout(30)->get($this->url);
                
                if (!$response->successful()) {
                    Log::error('Failed to fetch jiji.ng: ' . $response->status());
                    return;
                }
            }

            $html = $response->body();
            $crawler = new Crawler($html);

            // Look for agriculture/farm related categories specifically
            // This selector would need to match jiji.ng's actual structure
            $potentialFarmLinks = $crawler->filter('a:contains("Farm"), a:contains("Agriculture"), a:contains("Garden"), a:contains("Crops"), a:contains("Livestock"), a:contains("Poultry"), a:contains("Vegetable"), a:contains("Fruits")');

            // Alternative selectors for farm-related categories
            $farmSelectors = [
                'a[href*="farm"]',
                'a[href*="agriculture"]',
                'a[href*="livestock"]',
                'a[href*="poultry"]',
                'a[href*="vegetables"]',
                'a[href*="fruits"]',
                'a[href*="crops"]',
                'a[href*="garden"]',
                '.category-item a:contains("Farm"):icontains',
                '.main-category a:contains("Agriculture"):icontains'
            ];

            $farmCategories = collect();

            foreach ($farmSelectors as $selector) {
                try {
                    $links = $crawler->filter($selector);
                    $links->each(function (Crawler $node) use ($farmCategories) {
                        $href = $node->attr('href');
                        $text = trim($node->text());

                        if (empty($text)) {
                            $text = trim($node->attr('title') ?? $node->attr('data-title') ?? '');
                        }

                        if (!empty($text) && !empty($href)) {
                            $farmCategories->push(['name' => $text, 'url' => $href]);
                        }
                    });
                } catch (\Exception $e) {
                    // Continue to next selector if current one fails
                    continue;
                }
            }

            // If we didn't find specific farm categories, look for broader categories that might contain farm products
            if ($farmCategories->isEmpty()) {
                // Common categories that often contain farm products
                $commonSelectors = [
                    'a:contains("Electronics"):not(:contains("Farm")):not(:contains("Fruit")):not(:contains("Vegetables")):not(:contains("Livestock")):not(:contains("Poultry"))',
                    'a:contains("Vehicles"):not(:contains("Farm")):not(:contains("Tractors"))',
                    'a:contains("Property"):not(:contains("Farm")):not(:contains("Land"))',
                    'a:contains("Furniture")',
                    'a:contains("Fashion")',
                ];

                $allCategories = $crawler->filter('a[href*="/category/"], a[href*="/c/"], .category-item a, .main-category a');
                
                $allCategories->each(function (Crawler $node) use ($farmCategories) {
                    $href = $node->attr('href');
                    $text = trim($node->text());

                    if (empty($text)) {
                        $text = trim($node->attr('title') ?? $node->attr('data-title') ?? '');
                    }

                    // Check if this category likely contains farm products
                    if (!empty($text) && !empty($href) && $this->isLikelyFarmRelated($text)) {
                        $farmCategories->push(['name' => $text, 'url' => $href]);
                    }
                });
            }

            $categoriesImported = 0;
            
            foreach ($farmCategories as $farmCat) {
                $this->importFarmCategory($farmCat['name'], $farmCat['url']);
                $categoriesImported++;
                
                // Small delay to be respectful to the server
                sleep(1);
            }

            Log::info("Farm category import completed. Imported {$categoriesImported} farm-related categories from jiji.ng");
        } catch (\Exception $e) {
            Log::error('Error importing farm categories from jiji.ng: ' . $e->getMessage());
            throw $e; // Re-throw to trigger retry logic
        }
    }

    protected function isLikelyFarmRelated($categoryName)
    {
        $farmKeywords = [
            'Farm', 'Farming', 'Agriculture', 'Agricultural', 'Garden', 'Gardening',
            'Vegetable', 'Vegetables', 'Fruit', 'Fruits', 'Crop', 'Crops',
            'Livestock', 'Poultry', 'Eggs', 'Meat', 'Dairy', 'Milk', 'Chicken',
            'Fish', 'Seafood', 'Grains', 'Rice', 'Maize', 'Cassava', 'Yam',
            'Plantain', 'Banana', 'Tree', 'Nursery', 'Seeds', 'Fertilizer',
            'Tractor', 'Farm Equipment', 'Agric', 'Livestock', 'Cattle', 'Goat',
            'Sheep', 'Pig', 'Poultry', 'Breed', 'Seedlings', 'Herbs', 'Spices'
        ];

        $lowerCategory = strtolower($categoryName);
        foreach ($farmKeywords as $keyword) {
            if (strpos($lowerCategory, strtolower($keyword)) !== false) {
                return true;
            }
        }
        return false;
    }

    protected function importFarmCategory($name, $url)
    {
        // Normalize the category name
        $name = trim(strip_tags($name));
        if (empty($name)) {
            return;
        }

        // Check if category already exists
        $existingCategory = Category::where('name', $name)
            ->orWhere('slug', \Str::slug($name))
            ->first();

        if ($existingCategory) {
            Log::info("Farm category already exists: {$name}");
            return $existingCategory; // Return existing category
        }

        // Create the new farm-related category
        $category = new Category();
        $category->name = $name;
        $category->slug = \Str::slug($name);
        $category->description = "Farm category imported from jiji.ng";
        $category->parent_id = null; // Top-level farm category
        $category->status = 'active';
        $category->order = 0; // Default order
        $category->save();

        Log::info("Imported farm category: {$name}");

        // Optionally, fetch subcategories from the category page
        $this->importFarmSubcategories($category, $url);

        return $category;
    }

    protected function importFarmSubcategories(Category $parentCategory, $categoryUrl)
    {
        try {
            // Only proceed if we have a complete URL
            if (filter_var($categoryUrl, FILTER_VALIDATE_URL)) {
                $fullUrl = $categoryUrl;
            } else {
                $fullUrl = rtrim($this->url, '/') . '/' . ltrim($categoryUrl, '/');
            }

            $response = Http::timeout(30)->get($fullUrl);

            if (!$response->successful()) {
                return;
            }

            $html = $response->body();
            $crawler = new Crawler($html);

            // Look for subcategories that are farm-specific
            $subCategoryLinks = $crawler->filter('.subcategory a, .sub-category a, .category-list a');

            $subCategoryLinks->each(function (Crawler $node) use ($parentCategory) {
                $text = trim($node->text());

                if (empty($text)) {
                    $text = trim($node->attr('title') ?? $node->attr('data-title') ?? '');
                }

                if (!empty($text) && $this->isLikelyFarmRelated($text)) {
                    $href = $node->attr('href');
                    $this->importFarmSubCategory($text, $href, $parentCategory);
                    
                    // Small delay
                    sleep(1);
                }
            });
        } catch (\Exception $e) {
            Log::warning("Error fetching farm subcategories for {$parentCategory->name}: " . $e->getMessage());
        }
    }

    protected function importFarmSubCategory($name, $url, Category $parentCategory)
    {
        $name = trim(strip_tags($name));
        if (empty($name)) {
            return;
        }

        // Check if subcategory already exists within this parent
        $existingCategory = Category::where('name', $name)
            ->where('parent_id', $parentCategory->id)
            ->first();

        if ($existingCategory) {
            Log::info("Farm subcategory already exists: {$name} under {$parentCategory->name}");
            return $existingCategory;
        }

        // Check if this subcategory exists as a main category (to avoid duplication)
        $existingMainCategory = Category::where('name', $name)
            ->whereNull('parent_id')
            ->first();

        if ($existingMainCategory) {
            Log::info("Farm subcategory {$name} already exists as main category, linking instead");
            $existingMainCategory->parent_id = $parentCategory->id;
            $existingMainCategory->save();
            return $existingMainCategory;
        }

        // Create the farm subcategory
        $subcategory = new Category();
        $subcategory->name = $name;
        $subcategory->slug = \Str::slug($name);
        $subcategory->description = "Farm subcategory imported from jiji.ng under {$parentCategory->name}";
        $subcategory->parent_id = $parentCategory->id;
        $subcategory->status = 'active';
        $subcategory->order = 0;
        $subcategory->save();

        Log::info("Imported farm subcategory: {$name} under {$parentCategory->name}");

        return $subcategory;
    }
}