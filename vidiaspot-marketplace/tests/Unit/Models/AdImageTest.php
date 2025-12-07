<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\AdImage;
use App\Models\Ad;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdImageTest extends TestCase
{
    use RefreshDatabase;

    public function test_ad_image_belongs_to_ad(): void
    {
        $ad = Ad::factory()->create();
        $adImage = AdImage::factory()->create(['ad_id' => $ad->id]);

        $this->assertInstanceOf(Ad::class, $adImage->ad);
        $this->assertEquals($ad->id, $adImage->ad->id);
    }

    public function test_ad_image_casts(): void
    {
        $adImage = AdImage::factory()->create([
            'ad_id' => '1',
            'is_primary' => '1',
            'order' => '2'
        ]);

        $this->assertIsInt($adImage->ad_id);
        $this->assertIsBool($adImage->is_primary);
        $this->assertIsInt($adImage->order);

        $this->assertEquals(1, $adImage->ad_id);
        $this->assertTrue($adImage->is_primary);
        $this->assertEquals(2, $adImage->order);
    }

    public function test_ad_image_fillable_attributes(): void
    {
        $fillable = [
            'ad_id',
            'image_path',
            'image_url',
            'is_primary',
            'order',
        ];

        $adImage = new AdImage();
        $this->assertEquals($fillable, $adImage->getFillable());
    }

    public function test_ad_image_default_values(): void
    {
        $adImage = AdImage::factory()->make();

        $this->assertNull($adImage->id);
        $this->assertNull($adImage->ad_id);
        $this->assertNull($adImage->image_path);
        $this->assertNull($adImage->image_url);
        $this->assertFalse($adImage->is_primary);
        $this->assertEquals(0, $adImage->order);
    }
}