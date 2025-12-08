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

class ImportCategoriesFromJiji implements ShouldQueue
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
            Log::info('Starting category import from jiji.ng');
            
            // Fetch the main page
            $response = Http::timeout(30)->get($this->url);
            
            if (!$response->successful()) {
                Log::error('Failed to fetch jiji.ng: ' . $response->status());
                return;
            }
            
            $html = $response->body();
            $crawler = new Crawler($html);
            
            // Find category links (this selector is based on typical marketplace layouts)
            // We need to adjust this if jiji.ng has a different structure
            $categoryLinks = $crawler->filter('a[href*="/category/"], a[href*="/c/"], .category-item a, .main-category a');
            
            $categoriesImported = 0;
            
            $categoryLinks->each(function (Crawler $node) use (&$categoriesImported) {
                $href = $node->attr('href');
                $text = trim($node->text());
                
                if (empty($text)) {
                    $text = trim($node->attr('title') ?? $node->attr('data-title') ?? '');
                }
                
                // Process the category
                if (!empty($text) && !empty($href)) {
                    $this->importCategory($text, $href);
                    $categoriesImported++;
                    
                    // Small delay to be respectful to the server
                    sleep(1);
                }
            });
            
            Log::info("Category import completed. Imported {$categoriesImported} categories from jiji.ng");
        } catch (\Exception $e) {
            Log::error('Error importing categories from jiji.ng: ' . $e->getMessage());
            throw $e; // Re-throw to trigger retry logic
        }
    }
    
    protected function importCategory($name, $url)
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
            Log::info("Category already exists: {$name}");
            return;
        }
        
        // Create the new category
        $category = new Category();
        $category->name = $name;
        $category->slug = \Str::slug($name);
        $category->description = "Category imported from jiji.ng"; // Could fetch more details
        $category->parent_id = null; // Assuming top-level categories for now
        $category->status = 'active';
        $category->order = 0; // Default order
        $category->save();
        
        Log::info("Imported category: {$name}");
        
        // Optionally, fetch subcategories from the category page
        $this->importSubcategories($category, $url);
    }
    
    protected function importSubcategories(Category $parentCategory, $categoryUrl)
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
            
            // Look for subcategories (this selector would need adjustment based on real site structure)
            $subCategoryLinks = $crawler->filter('.subcategory a, .sub-category a, .category-list a');
            
            $subCategoryLinks->each(function (Crawler $node) use ($parentCategory) {
                $href = $node->attr('href');
                $text = trim($node->text());
                
                if (empty($text)) {
                    $text = trim($node->attr('title') ?? $node->attr('data-title') ?? '');
                }
                
                if (!empty($text)) {
                    $this->importSubCategory($text, $href, $parentCategory);
                    
                    // Small delay
                    sleep(1);
                }
            });
        } catch (\Exception $e) {
            Log::warning("Error fetching subcategories for {$parentCategory->name}: " . $e->getMessage());
        }
    }
    
    protected function importSubCategory($name, $url, Category $parentCategory)
    {
        $name = trim(strip_tags($name));
        if (empty($name)) {
            return;
        }
        
        // Check if subcategory already exists
        $existingCategory = Category::where('name', $name)
            ->where('parent_id', $parentCategory->id)
            ->first();
            
        if ($existingCategory) {
            return;
        }
        
        // Create the subcategory
        $subcategory = new Category();
        $subcategory->name = $name;
        $subcategory->slug = \Str::slug($name);
        $subcategory->description = "Subcategory imported from jiji.ng";
        $subcategory->parent_id = $parentCategory->id;
        $subcategory->status = 'active';
        $subcategory->order = 0;
        $subcategory->save();
        
        Log::info("Imported subcategory: {$name} under {$parentCategory->name}");
    }
}