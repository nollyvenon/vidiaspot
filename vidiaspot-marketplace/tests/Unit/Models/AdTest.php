<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Ad;
use App\Models\User;
use App\Models\Category;
use App\Models\AdImage;
use App\Models\Currency;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Factories\Sequence;

class AdTest extends TestCase
{
    use RefreshDatabase;

    public function test_ad_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $ad = Ad::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $ad->user);
        $this->assertEquals($user->id, $ad->user->id);
    }

    public function test_ad_belongs_to_category(): void
    {
        $category = Category::factory()->create();
        $ad = Ad::factory()->create(['category_id' => $category->id]);

        $this->assertInstanceOf(Category::class, $ad->category);
        $this->assertEquals($category->id, $ad->category->id);
    }

    public function test_ad_has_many_images(): void
    {
        $ad = Ad::factory()->create();
        $images = AdImage::factory()->count(3)->create(['ad_id' => $ad->id]);

        $this->assertCount(3, $ad->images);
        $this->assertInstanceOf(AdImage::class, $ad->images->first());
    }

    public function test_ad_belongs_to_currency(): void
    {
        $currency = Currency::factory()->create(['code' => 'USD']);
        $ad = Ad::factory()->create([
            'currency_code' => 'USD'
        ]);

        $this->assertInstanceOf(Currency::class, $ad->currency);
        $this->assertEquals('USD', $ad->currency->code);
    }

    public function test_formatted_price_accessor(): void
    {
        $currency = Currency::factory()->create([
            'code' => 'USD',
            'symbol' => '$',
            'format' => '$%s'
        ]);
        
        $ad = Ad::factory()->create([
            'price' => 123.45,
            'currency_code' => 'USD'
        ]);

        $this->assertEquals('$123.45', $ad->formatted_price);
    }

    public function test_formatted_price_fallback(): void
    {
        $ad = Ad::factory()->create([
            'price' => 123.45,
            'currency_code' => 'NGN' // Currency doesn't exist in DB
        ]);

        $this->assertEquals('NGN 123.45', $ad->formatted_price);
    }

    public function test_convert_price_to_method(): void
    {
        // Mock the CurrencyService to return a specific conversion
        $currencyServiceMock = $this->createMock(\App\Services\CurrencyService::class);
        $currencyServiceMock->method('convert')
                           ->with(100.00, 'USD', 'EUR')
                           ->willReturn(85.00);

        $currencyServiceMock->method('format')
                           ->with(85.00, 'EUR')
                           ->willReturn('€85.00');

        // Replace the service in the container
        $this->app->instance(\App\Services\CurrencyService::class, $currencyServiceMock);

        $ad = Ad::factory()->create([
            'price' => 100.00,
            'currency_code' => 'USD'
        ]);

        $converted = $ad->convertPriceTo('EUR');
        $this->assertEquals('€85.00', $converted);
    }

    public function test_convert_price_to_fallback(): void
    {
        // Mock the CurrencyService to throw an exception
        $currencyServiceMock = $this->createMock(\App\Services\CurrencyService::class);
        $currencyServiceMock->method('convert')
                           ->willThrowException(new \Exception('Conversion error'));

        // Replace the service in the container
        $this->app->instance(\App\Services\CurrencyService::class, $currencyServiceMock);

        $currency = Currency::factory()->create([
            'code' => 'USD',
            'symbol' => '$',
            'format' => '$%s'
        ]);

        $ad = Ad::factory()->create([
            'price' => 100.00,
            'currency_code' => 'USD'
        ]);

        $converted = $ad->convertPriceTo('EUR');
        $this->assertEquals('$100.00', $converted);
    }

    public function test_price_casting(): void
    {
        $ad = Ad::factory()->create(['price' => '123.456']);

        $this->assertEquals(123.46, $ad->price); // Should be cast to 2 decimal places
    }

    public function test_negotiable_casting(): void
    {
        $ad = Ad::factory()->create(['negotiable' => '1']);

        $this->assertTrue($ad->negotiable);
    }

    public function test_view_count_casting(): void
    {
        $ad = Ad::factory()->create(['view_count' => '5']);

        $this->assertEquals(5, $ad->view_count);
    }

    public function test_fillable_attributes(): void
    {
        $fillable = [
            'user_id',
            'category_id',
            'title',
            'description',
            'price',
            'currency_code',
            'condition',
            'status',
            'location',
            'latitude',
            'longitude',
            'contact_phone',
            'negotiable',
            'view_count',
            'expires_at',
        ];

        $ad = new Ad();
        $this->assertEquals($fillable, $ad->getFillable());
    }
}