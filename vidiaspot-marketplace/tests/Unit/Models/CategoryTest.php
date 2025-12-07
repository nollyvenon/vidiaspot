<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Category;
use App\Models\Ad;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_has_many_ads(): void
    {
        $category = Category::factory()->create();
        $ads = Ad::factory()->count(3)->create(['category_id' => $category->id]);

        $this->assertCount(3, $category->ads);
        $this->assertInstanceOf(Ad::class, $category->ads->first());
    }

    public function test_category_scope_active(): void
    {
        Category::factory()->create(['status' => 'active']);
        Category::factory()->create(['status' => 'inactive']);
        Category::factory()->create(['status' => 'active']);

        $activeCategories = Category::active()->get();

        $this->assertCount(2, $activeCategories);
        $this->assertTrue($activeCategories->every(fn($category) => $category->status === 'active'));
    }

    public function test_category_scope_inactive(): void
    {
        Category::factory()->create(['status' => 'active']);
        Category::factory()->create(['status' => 'inactive']);
        Category::factory()->create(['status' => 'inactive']);

        $inactiveCategories = Category::inactive()->get();

        $this->assertCount(2, $inactiveCategories);
        $this->assertTrue($inactiveCategories->every(fn($category) => $category->status === 'inactive'));
    }

    public function test_category_fillable_attributes(): void
    {
        $fillable = [
            'name',
            'slug',
            'description',
            'parent_id',
            'status',
            'order',
            'icon',
            'image_url',
            'meta_title',
            'meta_description',
            'is_featured',
        ];

        $category = new Category();
        $this->assertEquals($fillable, $category->getFillable());
    }

    public function test_category_default_values(): void
    {
        $category = Category::factory()->make();

        $this->assertNull($category->id);
        $this->assertNull($category->parent_id);
        $this->assertEquals('active', $category->status);
        $this->assertEquals(0, $category->order);
        $this->assertNull($category->icon);
        $this->assertNull($category->image_url);
        $this->assertNull($category->meta_title);
        $this->assertNull($category->meta_description);
        $this->assertFalse($category->is_featured);
    }
}