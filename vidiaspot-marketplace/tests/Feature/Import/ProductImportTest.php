<?php

namespace Tests\Feature\Import;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\ProductImportSettings;
use App\Jobs\ImportLatestProductsFromJiji;
use App\Jobs\ImportCategoriesFromJiji;

class ProductImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_import_settings_model()
    {
        // Test that the settings model works correctly
        $settings = ProductImportSettings::first();
        
        $this->assertNotNull($settings);
        $this->assertEquals(3, $settings->import_days);
        $this->assertTrue($settings->import_enabled);
        $this->assertEquals(24, $settings->import_interval_hours);
        $this->assertEquals('jiji.ng', $settings->import_source);
        $this->assertTrue($settings->import_images);
        $this->assertTrue($settings->import_duplicate_check);
    }

    public function test_import_jobs_can_be_dispatched()
    {
        // Test that import jobs can be instantiated and dispatched
        $categoryJob = new ImportCategoriesFromJiji();
        $productJob = new ImportLatestProductsFromJiji(3);
        
        $this->assertInstanceOf(ImportCategoriesFromJiji::class, $categoryJob);
        $this->assertInstanceOf(ImportLatestProductsFromJiji::class, $productJob);
        
        // Test that they have expected methods
        $this->assertTrue(method_exists($categoryJob, 'handle'));
        $this->assertTrue(method_exists($productJob, 'handle'));
    }

    public function test_product_import_settings_get_current_settings()
    {
        // Test the method that gets or creates default settings
        $settings = ProductImportSettings::getCurrentSettings();
        
        $this->assertInstanceOf(ProductImportSettings::class, $settings);
        $this->assertEquals(3, $settings->import_days);
        $this->assertTrue($settings->import_duplicate_check);
        
        // Test updating settings
        $settings->import_days = 7;
        $settings->import_duplicate_check = false;
        $settings->save();
        
        $refreshedSettings = ProductImportSettings::getCurrentSettings();
        $this->assertEquals(7, $refreshedSettings->import_days);
        $this->assertFalse($refreshedSettings->import_duplicate_check);
    }

    public function test_product_import_settings_is_time_to_import()
    {
        $settings = ProductImportSettings::getCurrentSettings();
        
        // Initially should be true since last_import_time is null
        $this->assertTrue($settings->isTimeToImport());
        
        // Set last import to now, so it should be false
        $settings->last_import_time = now();
        $settings->save();
        
        $this->assertFalse($settings->isTimeToImport());
        
        // Set import_interval_hours to 0 and enable import
        $settings->import_interval_hours = 0;
        $settings->import_enabled = true;
        $settings->save();
        
        $this->assertTrue($settings->isTimeToImport());
    }
}