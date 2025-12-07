<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\User;
use App\Models\Ad;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_has_many_ads(): void
    {
        $user = User::factory()->create();
        $ads = Ad::factory()->count(3)->create(['user_id' => $user->id]);

        $this->assertCount(3, $user->ads);
        $this->assertInstanceOf(Ad::class, $user->ads->first());
    }

    public function test_user_factory_creates_valid_user(): void
    {
        $user = User::factory()->create();

        $this->assertNotNull($user->name);
        $this->assertNotNull($user->email);
        $this->assertTrue(Hash::check('password', $user->password));
        $this->assertNotNull($user->email_verified_at);
        $this->assertNotNull($user->remember_token);
    }

    public function test_user_factory_creates_unverified_user(): void
    {
        $user = User::factory()->unverified()->create();

        $this->assertNotNull($user->name);
        $this->assertNotNull($user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function test_user_fillable_attributes(): void
    {
        $fillable = [
            'name',
            'email',
            'password',
            'phone',
            'city',
            'state',
            'country',
            'address',
            'bio',
            'avatar',
            'role',
            'status',
            'email_verified_at',
            'phone_verified_at',
            'remember_token',
            'last_login_at',
            'last_login_ip',
        ];

        $user = new User();
        $this->assertEquals($fillable, $user->getFillable());
    }

    public function test_user_hidden_attributes(): void
    {
        $hidden = ['password', 'remember_token'];

        $user = new User();
        $this->assertEquals($hidden, $user->getHidden());
    }

    public function test_user_casts(): void
    {
        $casts = [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
        ];

        $user = new User();
        $this->assertEquals($casts, $user->getCasts());
    }

    public function test_user_default_values(): void
    {
        $user = User::factory()->make();

        $this->assertNull($user->id);
        $this->assertEquals('user', $user->role);
        $this->assertEquals('active', $user->status);
        $this->assertNull($user->phone);
        $this->assertNull($user->city);
        $this->assertNull($user->state);
        $this->assertNull($user->country);
        $this->assertNull($user->address);
        $this->assertNull($user->bio);
        $this->assertNull($user->avatar);
        $this->assertNull($user->last_login_at);
        $this->assertNull($user->last_login_ip);
    }
}