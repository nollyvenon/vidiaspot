<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'avatar',
        'is_verified',
        'language_code',
        'subscription_id',
        'subscription_start_date',
        'subscription_end_date',
        'subscription_status',
        'ad_limit',
        'featured_ads_limit',
        'has_priority_support',
        'subscription_features',
    ];

    protected $attributes = [
        'language_code' => 'en', // Default to English
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_verified' => 'boolean',
            'subscription_start_date' => 'datetime',
            'subscription_end_date' => 'datetime',
            'has_priority_support' => 'boolean',
            'ad_limit' => 'integer',
            'featured_ads_limit' => 'integer',
            'subscription_features' => 'array',
        ];
    }

    /**
     * Get the language associated with this user.
     */
    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'language_code', 'code');
    }

    /**
     * Get the roles associated with this user.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    /**
     * Get the vendor profile associated with this user.
     */
    public function vendor(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Vendor::class);
    }

    /**
     * Get the social accounts associated with this user.
     */
    public function socialAccounts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SocialAccount::class);
    }

    /**
     * Get the push notification tokens associated with this user.
     */
    public function pushTokens(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PushToken::class);
    }

    /**
     * Check if the user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Check if the user is a seller.
     */
    public function isSeller(): bool
    {
        return $this->hasRole('seller');
    }

    /**
     * Check if the user is a normal user.
     */
    public function isNormalUser(): bool
    {
        return $this->hasRole('user') && !$this->hasRole('seller') && !$this->hasRole('admin');
    }

    /**
     * Scope to get admin users.
     */
    public function scopeAdmin($query)
    {
        return $query->whereHas('roles', function($q) {
            $q->where('name', 'admin');
        });
    }

    /**
     * Scope to get seller users.
     */
    public function scopeSeller($query)
    {
        return $query->whereHas('roles', function($q) {
            $q->where('name', 'seller');
        });
    }

    /**
     * Scope to get normal users.
     */
    public function scopeNormalUser($query)
    {
        return $query->whereHas('roles', function($q) {
            $q->where('name', 'user');
        })->whereDoesntHave('roles', function($q) {
            $q->whereIn('name', ['admin', 'seller']);
        });
    }

    /**
     * Get the payment transactions made by this user.
     */
    public function paymentTransactions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    /**
     * Get the subscription associated with this user.
     */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    /**
     * Get the ads associated with this user's subscription (feature limit related)
     */
    public function ads(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Ad::class);
    }

    /**
     * Get the vendor store associated with this user.
     */
    public function vendorStore(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(VendorStore::class);
    }

    /**
     * Get the featured ads created by this user.
     */
    public function featuredAds(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(FeaturedAd::class);
    }

    /**
     * Get the blogs created by this user.
     */
    public function blogs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Blog::class, 'user_id');
    }

    /**
     * Check if the user has a specific role.
     */
    public function hasRole($roleName): bool
    {
        if (is_string($roleName)) {
            return $this->roles->contains('name', $roleName);
        }

        return false;
    }

    /**
     * Assign a role to the user.
     */
    public function assignRole($roleName): void
    {
        if (is_string($roleName)) {
            $role = Role::where('name', $roleName)->first();
            if ($role) {
                $this->roles()->attach($role);
            }
        }
    }

    /**
     * Remove a role from the user.
     */
    public function removeRole($roleName): void
    {
        if (is_string($roleName)) {
            $role = Role::where('name', $roleName)->first();
            if ($role) {
                $this->roles()->detach($role);
            }
        }
    }
}
