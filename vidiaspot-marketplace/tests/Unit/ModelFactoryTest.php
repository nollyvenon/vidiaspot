<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Ad;
use App\Models\User;
use App\Models\Category;
use App\Models\Currency;
use App\Models\ExchangeRate;
use App\Models\Chat;
use App\Models\AdImage;

class ModelFactoryTest extends TestCase
{
    public function test_ad_factory_creates_model(): void
    {
        $ad = Ad::factory()->make();
        
        $this->assertNotNull($ad->title);
        $this->assertNotNull($ad->description);
        $this->assertIsFloat($ad->price);
        $this->assertNotNull($ad->condition);
        $this->assertNotNull($ad->status);
        $this->assertNotNull($ad->location);
        $this->assertIsBool($ad->negotiable);
    }

    public function test_user_factory_creates_model(): void
    {
        $user = User::factory()->make();
        
        $this->assertNotNull($user->name);
        $this->assertNotNull($user->email);
    }

    public function test_category_factory_creates_model(): void
    {
        $category = Category::factory()->make();
        
        $this->assertNotNull($category->name);
        $this->assertNotNull($category->slug);
        $this->assertNotNull($category->status);
    }

    public function test_currency_factory_creates_model(): void
    {
        $currency = Currency::factory()->make();
        
        $this->assertNotNull($currency->code);
        $this->assertNotNull($currency->name);
        $this->assertNotNull($currency->symbol);
        $this->assertIsBool($currency->is_default);
    }

    public function test_exchange_rate_factory_creates_model(): void
    {
        $exchangeRate = ExchangeRate::factory()->make();
        
        $this->assertNotNull($exchangeRate->from_currency_code);
        $this->assertNotNull($exchangeRate->to_currency_code);
        $this->assertIsFloat($exchangeRate->rate);
    }

    public function test_chat_factory_creates_model(): void
    {
        $chat = Chat::factory()->make();
        
        $this->assertNotNull($chat->message);
        $this->assertIsBool($chat->is_read);
        $this->assertIsBool($chat->is_archived);
    }

    public function test_ad_image_factory_creates_model(): void
    {
        $adImage = AdImage::factory()->make();
        
        $this->assertNotNull($adImage->image_path);
        $this->assertIsBool($adImage->is_primary);
        $this->assertIsInt($adImage->order);
    }
}