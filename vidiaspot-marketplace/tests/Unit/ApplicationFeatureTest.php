<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\CurrencyService;
use App\Services\RecommendationService;
use App\Services\ChatbotService;
use App\Models\Ad;
use App\Models\Currency;
use App\Models\ExchangeRate;
use App\Models\Chat;

class ApplicationFeatureTest extends TestCase
{
    public function test_ad_model_creation_and_properties(): void
    {
        $ad = new Ad([
            'title' => 'Test Ad',
            'description' => 'Test Description',
            'price' => 100.50,
            'condition' => 'good',
            'status' => 'active',
            'location' => 'Test Location',
            'negotiable' => true,
            'view_count' => 5
        ]);

        $this->assertEquals('Test Ad', $ad->title);
        $this->assertEquals('Test Description', $ad->description);
        $this->assertEquals(100.50, $ad->price);
        $this->assertEquals('good', $ad->condition);
        $this->assertEquals('active', $ad->status);
        $this->assertEquals('Test Location', $ad->location);
        $this->assertTrue($ad->negotiable);
        $this->assertEquals(5, $ad->view_count);
    }

    public function test_ad_model_fillable_attributes(): void
    {
        $ad = new Ad();
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

        $this->assertEquals($fillable, $ad->getFillable());
    }

    public function test_currency_model_fillable_attributes(): void
    {
        $currency = new Currency();
        $fillable = [
            'code',
            'name',
            'symbol',
            'country',
            'precision',
            'format',
            'is_active',
            'is_default',
            'minor_unit',
        ];

        $this->assertEquals($fillable, $currency->getFillable());
    }

    public function test_exchange_rate_model_fillable_attributes(): void
    {
        $exchangeRate = new ExchangeRate();
        $fillable = [
            'from_currency_code',
            'to_currency_code',
            'rate',
            'last_updated',
            'provider',
        ];

        $this->assertEquals($fillable, $exchangeRate->getFillable());
    }

    public function test_chat_model_fillable_attributes(): void
    {
        $chat = new Chat();
        $fillable = [
            'sender_id',
            'receiver_id',
            'message',
            'is_read',
            'is_archived',
            'messageable_type',
            'messageable_id',
            'metadata',
        ];

        $this->assertEquals($fillable, $chat->getFillable());
    }

    public function test_currency_model_casts(): void
    {
        $currency = new Currency();
        $casts = $currency->getCasts();

        $this->assertArrayHasKey('precision', $casts);
        $this->assertArrayHasKey('is_active', $casts);
        $this->assertArrayHasKey('is_default', $casts);
        $this->assertArrayHasKey('minor_unit', $casts);
        $this->assertEquals('integer', $casts['precision']);
        $this->assertEquals('boolean', $casts['is_active']);
        $this->assertEquals('boolean', $casts['is_default']);
        $this->assertEquals('integer', $casts['minor_unit']);
    }

    public function test_exchange_rate_model_casts(): void
    {
        $exchangeRate = new ExchangeRate();
        $casts = $exchangeRate->getCasts();

        $this->assertArrayHasKey('rate', $casts);
        $this->assertArrayHasKey('last_updated', $casts);
        $this->assertEquals('decimal:8', $casts['rate']);
        $this->assertEquals('datetime', $casts['last_updated']);
    }

    public function test_ad_model_casts(): void
    {
        $ad = new Ad();
        $casts = $ad->getCasts();

        $this->assertArrayHasKey('price', $casts);
        $this->assertArrayHasKey('latitude', $casts);
        $this->assertArrayHasKey('longitude', $casts);
        $this->assertArrayHasKey('negotiable', $casts);
        $this->assertArrayHasKey('view_count', $casts);
        $this->assertArrayHasKey('expires_at', $casts);
        $this->assertEquals('decimal:2', $casts['price']);
        $this->assertEquals('boolean', $casts['negotiable']);
        $this->assertEquals('integer', $casts['view_count']);
    }

    public function test_chat_model_casts(): void
    {
        $chat = new Chat();
        $casts = $chat->getCasts();

        $this->assertArrayHasKey('is_read', $casts);
        $this->assertArrayHasKey('is_archived', $casts);
        $this->assertArrayHasKey('metadata', $casts);
        $this->assertEquals('boolean', $casts['is_read']);
        $this->assertEquals('boolean', $casts['is_archived']);
        $this->assertEquals('array', $casts['metadata']);
    }

    public function test_currency_format_method(): void
    {
        $currency = new Currency([
            'symbol' => '₦',
            'precision' => 2
        ]);

        $result = $currency->formatAmount(1250.75);
        // The formatAmount method uses number_format which adds commas for thousands
        $this->assertEquals('₦1,250.75', $result);
    }

    public function test_currency_format_without_decimals(): void
    {
        $currency = new Currency([
            'symbol' => '$',
            'precision' => 0
        ]);

        $result = $currency->formatAmount(1250.75);
        // The formatAmount method uses number_format which adds commas for thousands
        $this->assertEquals('$1,251', $result); // Should round up
    }

    public function test_exchange_rate_convert_method(): void
    {
        $exchangeRate = new ExchangeRate([
            'rate' => 0.85
        ]);

        $result = $exchangeRate->convert(100.00);
        $this->assertEquals(85.00, $result);
    }

    public function test_exchange_rate_scope_methods_exist(): void
    {
        $exchangeRate = new ExchangeRate();
        
        // Check that scope methods exist by testing that they can be called (will return query builder)
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Builder::class, $exchangeRate->newQuery()->fromCurrency('USD'));
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Builder::class, $exchangeRate->newQuery()->toCurrency('EUR'));
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Builder::class, $exchangeRate->newQuery()->forConversion('USD', 'EUR'));
    }

    public function test_currency_scope_methods_exist(): void
    {
        $currency = new Currency();
        
        // Check that scope methods exist by testing that they can be called (will return query builder)
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Builder::class, $currency->newQuery()->active());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Builder::class, $currency->newQuery()->default());
    }

    public function test_chat_scope_methods_exist(): void
    {
        $chat = new Chat();
        
        // Check that scope methods exist by testing that they can be called (will return query builder)
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Builder::class, $chat->newQuery()->byUser(1));
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Builder::class, $chat->newQuery()->unreadByUser(1));
    }
}