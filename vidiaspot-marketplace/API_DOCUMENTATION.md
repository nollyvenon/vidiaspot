# Vidiaspot Marketplace - Developer Guide & API Documentation

## Table of Contents
1. [Architecture Overview](#architecture-overview)
2. [API Authentication](#api-authentication)
3. [Core API Endpoints](#core-api-endpoints)
4. [Payment Gateway API](#payment-gateway-api)
5. [Ad Management API](#ad-management-api)
6. [User Management API](#user-management-api)
7. [Admin API](#admin-api)
8. [Ad Placement API](#ad-placement-api)
9. [Search API](#search-api)
10. [Error Handling](#error-handling)
11. [Rate Limiting](#rate-limiting)
12. [Testing Guidelines](#testing-guidelines)
13. [Security Best Practices](#security-best-practices)

## Architecture Overview

### Tech Stack
- **Backend**: Laravel 12+
- **Language**: PHP 8.2+
- **Database**: MySQL (production) / SQLite (development)
- **Cache**: Redis
- **Search**: Elasticsearch (planned)
- **Frontend**: React/Vue.js (web), Flutter (mobile)

### Directory Structure
```
├── app/
│   ├── Http/
│   │   ├── Controllers/          # API and web controllers
│   │   ├── Middleware/          # Request middleware
│   │   └── Requests/            # Form request validation
│   ├── Models/                  # Eloquent models
│   ├── Services/                # Business logic services
│   ├── Jobs/                    # Queueable jobs
│   └── Events/                  # Event classes
├── config/                      # Configuration files
├── database/
│   ├── migrations/             # Database migrations
│   └── seeders/                # Database seeders
├── resources/
│   ├── js/                     # JavaScript assets
│   └── views/                  # Blade templates
├── routes/
│   ├── api.php                 # API routes
│   └── web.php                 # Web routes
└── tests/                      # Test files
```

### Service Layer Pattern
The application follows a service layer pattern for business logic:

```php
// Example service class
class PaymentService
{
    public function processPayment($amount, $gateway)
    {
        // Business logic here
    }
}
```

## API Authentication

### Laravel Sanctum
All API endpoints (except public routes) use Laravel Sanctum for authentication.

### Getting an API Token
1. User logs in via the normal authentication flow
2. Request a token:
````
POST /api/auth/token
Content-Type: application/json

{
    "email": "user@example.com",
    "password": "password"
}
```

3. Include token in all authenticated requests:
````
Authorization: Bearer {token}
```

### Public vs Protected Endpoints
- **Public**: Categories, Ads (browse), Pages
- **Protected**: User-specific actions, Admin functions, Payment processing

## Core API Endpoints

### Public Routes

#### Get All Categories
````
GET /api/categories
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Electronics",
      "slug": "electronics",
      "parent_id": null,
      "children": [
        {
          "id": 2,
          "name": "Mobile Phones",
          "slug": "mobile-phones",
          "parent_id": 1
        }
      ]
    }
  ]
}
```

#### Get Ads
````
GET /api/ads
```

**Parameters:**
- `category` (optional): Category ID
- `location` (optional): Location filter
- `q` (optional): Search query
- `price_min`, `price_max` (optional): Price range
- `page` (optional): Pagination page
- `per_page` (optional): Items per page

**Response:**
```json
{
  "success": true,
  "data": {
    "data": [
      {
        "id": 1,
        "title": "iPhone 13 Pro",
        "description": "Brand new iPhone 13 Pro, barely used",
        "price": 450000,
        "currency": "NGN",
        "location": "Lagos",
        "category_id": 2,
        "user_id": 5,
        "images": [
          "https://example.com/image1.jpg"
        ],
        "created_at": "2023-01-15T10:30:00Z"
      }
    ],
    "links": {
      "first": "/api/ads?page=1",
      "last": "/api/ads?page=5",
      "prev": null,
      "next": "/api/ads?page=2"
    },
    "meta": {
      "current_page": 1,
      "from": 1,
      "last_page": 5,
      "path": "/api/ads",
      "per_page": 10,
      "to": 10,
      "total": 50
    }
  }
}
```

#### Get Content Pages
```
GET /api/pages
GET /api/pages/{slug}
GET /api/about
GET /api/contact
GET /api/services
GET /api/privacy
GET /api/terms
```

## Payment Gateway API

### Initialize Payment
````
POST /api/payment/initialize
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "amount": 15000,
  "email": "customer@example.com",
  "payment_gateway": "paystack",  // or "flutterwave"
  "type": "ad_payment",           // ad_payment, featured_ad, premium_subscription, etc.
  "user_id": 123,
  "ad_id": 456,
  "callback_url": "https://yoursite.com/payment/callback",
  "custom_fields": {
    "item_name": "Featured Ad Upgrade",
    "category": "Electronics"
  }
}
```

**Response:**
```json
{
  "success": true,
  "message": "Payment initialized successfully",
  "data": {
    "authorization_url": "https://checkout-url.com",
    "reference": "ref_123456789",
    "payment_gateway": "paystack"
  }
}
```

### Verify Payment
````
POST /api/payment/verify
Content-Type: application/json
```

**Request Body:**
```json
{
  "reference": "ref_123456789",
  "payment_gateway": "paystack"  // or "flutterwave"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Payment verified successfully",
  "data": {
    "id": 12345,
    "status": "success",
    "amount": 15000,
    "currency": "NGN",
    "gateway_response": { ... }
  },
  "transaction": {
    "id": 1,
    "transaction_id": "uuid-string",
    "transaction_reference": "ref_123456789",
    "payment_gateway": "paystack",
    "user_id": 123,
    "ad_id": 456,
    "type": "featured_ad",
    "amount": 15000,
    "currency": "NGN",
    "status": "success",
    "paid_at": "2023-01-15T10:30:00Z",
    "created_at": "2023-01-15T10:25:00Z"
  }
}
```

### Get Transaction Details
````
GET /api/payment/transaction?reference={reference}
Authorization: Bearer {token}
```

## Ad Management API

### Create Ad (Authenticated)
````
POST /api/ads
Authorization: Bearer {token}
Content-Type: multipart/form-data
```

**Form Data:**
- `title`: Ad title
- `description`: Ad description
- `price`: Price
- `category_id`: Category ID
- `location`: Location
- `condition`: Item condition (new, used, etc.)
- `images[]`: Multiple image files
- `negotiable`: Boolean indicating if price is negotiable

**Response:**
```json
{
  "success": true,
  "message": "Ad created successfully",
  "data": {
    "id": 123,
    "title": "iPhone 13 Pro",
    "description": "Brand new iPhone 13 Pro",
    "price": 450000,
    "category_id": 2,
    "user_id": 45,
    "location": "Lagos",
    "images": [
      "https://yoursite.com/storage/ads/image1.jpg"
    ],
    "status": "active"
  }
}
```

### Update Ad (Authenticated)
````
PUT /api/ads/{id}
Authorization: Bearer {token}
Content-Type: multipart/form-data
```

### Delete Ad (Authenticated)
````
DELETE /api/ads/{id}
Authorization: Bearer {token}
```

### Upload Ad Images
````
POST /api/ads/{id}/images
Authorization: Bearer {token}
Content-Type: multipart/form-data
```

**Form Data:**
- `images[]`: Multiple image files

## User Management API

### Get Current User
````
GET /api/user
Authorization: Bearer {token}
```

**Response:**
```json
{
  "id": 1,
  "name": "John Doe",
  "email": "john@example.com",
  "phone": "+2348012345678",
  "location": "Lagos",
  "verification_status": "verified",
  "created_at": "2023-01-15T10:30:00Z"
}
```

### Get User's Ads
````
GET /api/my-ads
Authorization: Bearer {token}
```

### Update User Profile
````
PUT /api/user
Authorization: Bearer {token}
Content-Type: application/json
```

## Admin API

### Admin Dashboard
````
GET /api/admin/dashboard
Authorization: Bearer {token}
```

### Get Analytics
````
GET /api/admin/analytics
Authorization: Bearer {token}
```

### Manage Ads
````
GET /api/admin/ads              # List all ads
GET /api/admin/ads/{id}        # Get specific ad
PUT /api/admin/ads/{id}        # Update ad
PUT /api/admin/ads/{id}/status # Update ad status
GET /api/admin/ads/pending     # Get pending ads
```

### Manage Users
````
GET /api/admin/users              # List all users
GET /api/admin/users/{id}         # Get specific user
PATCH /api/admin/users/{id}/role  # Update user role
GET /api/admin/users/stats        # Get user statistics
```

### Manage Categories
````
GET /api/admin/categories              # List categories
POST /api/admin/categories             # Create category
PUT /api/admin/categories/{id}         # Update category
DELETE /api/admin/categories/{id}      # Delete category
PATCH /api/admin/categories/{id}/toggle-status  # Toggle category status
```

### Manage Ad Placements
````
GET /api/admin/ad-placements        # List ad placements
POST /api/admin/ad-placements       # Create ad placement
PUT /api/admin/ad-placements/{id}   # Update ad placement
DELETE /api/admin/ad-placements/{id} # Delete ad placement
PATCH /api/admin/ad-placements/{id}/toggle-status # Toggle status
GET /api/admin/ad-placements/stats  # Get statistics
```

## Ad Placement API

### Ad Placement Positions
The system supports multiple ad placement positions:

1. **Top Banner**: `position: "top"`
2. **Side Banner**: `position: "side"`
3. **Bottom Banner**: `position: "bottom"`
4. **Between Content**: `position: "content_inline"`
5. **Popup/Interstitial**: `position: "popup"`

### Get Active Ad Placements
````
GET /api/ad-placements/active
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "Premium Electronics",
      "position": "top",
      "content": "<img src='ad-image.jpg' />",
      "target_audience": ["electronics", "mobile_phones"],
      "start_date": "2023-01-01T00:00:00Z",
      "end_date": "2023-12-31T23:59:59Z",
      "impressions": 1250,
      "clicks": 45,
      "ctr": 3.6
    }
  ]
}
```

### Admin Ad Placement Management

#### Create Ad Placement
````
POST /api/admin/ad-placements
Authorization: Bearer {token}
Content-Type: multipart/form-data
```

**Form Data:**
- `title`: Ad placement title
- `position`: Position (`top`, `side`, `bottom`, `content_inline`, `popup`)
- `content`: HTML content or image
- `target_audience[]`: Array of category slugs
- `start_date`: Start date for campaign
- `end_date`: End date for campaign
- `budget`: Campaign budget
- `is_active`: Boolean

## Search API

### Advanced Search
````
GET /api/ads
```

**Query Parameters:**
```
?category=2
&location=Lagos
&q=iphone
&price_min=100000
&price_max=500000
&condition=new
&negotiable=1
&sort=price_asc
&page=1
&per_page=10
```

### Search with Filters
````
GET /api/ads?category=2&location=Lagos&sort=created_at_desc&q=smartphone
```

### Get Search Suggestions
````
GET /api/ads/suggestions?q=iph
```

**Response:**
```json
{
  "success": true,
  "data": [
    "iPhone",
    "iPhone 13",
    "iPhone 14",
    "iPhone Accessories"
  ]
}
```

## Error Handling

### Error Response Format
All error responses follow this format:

```json
{
  "success": false,
  "message": "Error message",
  "errors": {
    "field_name": ["Validation error message"]
  }
}
```

### Common HTTP Status Codes
- `200` - Success
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `422` - Validation Error
- `429` - Too Many Requests
- `500` - Internal Server Error

### Error Examples

**Validation Error:**
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "email": [
      "The email field is required.",
      "The email format is invalid."
    ],
    "password": [
      "The password must be at least 8 characters."
    ]
  }
}
```

**Unauthorized:**
```json
{
  "success": false,
  "message": "Unauthenticated."
}
```

## Rate Limiting

The API implements rate limiting to prevent abuse:
- **General API**: 60 requests per minute per IP
- **Authentication**: 5 attempts per minute per IP
- **Payment**: 10 requests per minute per user

When rate limit is exceeded, you'll receive:
```
HTTP/1.1 429 Too Many Requests
```

## Testing Guidelines

### Writing Tests
```php
// Example API test
public function test_user_can_create_ad()
{
    $user = User::factory()->create();
    $category = Category::factory()->create();
    
    $response = $this->actingAs($user, 'sanctum')
        ->postJson('/api/ads', [
            'title' => 'Test Ad',
            'description' => 'Test Description',
            'price' => 10000,
            'category_id' => $category->id,
            'location' => 'Lagos'
        ]);
    
    $response->assertStatus(201);
    $this->assertDatabaseHas('ads', [
        'title' => 'Test Ad',
        'user_id' => $user->id
    ]);
}
```

### Running Tests
```bash
# Run all tests
php artisan test

# Run only API tests
php artisan test --testsuite=Feature --group=api

# Run with coverage
php artisan test --coverage
```

## Security Best Practices

### Input Validation
All API endpoints should validate input using Form Request classes:

```php
class CreateAdRequest extends FormRequest
{
    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:5000',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'location' => 'required|string|max:255',
            'images' => 'array|max:10',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:5000',
        ];
    }
}
```

### Authentication Middleware
Always protect sensitive routes:
```php
Route::middleware(['auth:sanctum'])->group(function () {
    // Protected routes here
});
```

### API Resource Formatting
Use API Resources for consistent response formatting:
```php
class AdResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'price' => $this->price,
            'images' => $this->images->pluck('url'),
            'user' => new UserResource($this->user),
            'category' => new CategoryResource($this->category),
            'location' => $this->location,
            'created_at' => $this->created_at->toISOString(),
        ];
    }
}
```

## Mobile API Considerations

### Lightweight Responses
For mobile apps, consider using lighter payloads:
````
GET /api/ads?mobile=1
```

This returns a streamlined response with only essential fields.

### Offline Support
Mobile apps should implement:
- Caching strategies
- Offline browsing capabilities
- Queue failed requests for retry

## Versioning Strategy

### API Versioning
The API uses URI versioning:
```
/api/v1/...
```

Current version is v1, with backward compatibility maintained for major features.

### Deprecation Policy
- New API versions announced 3 months in advance
- Old versions deprecated with warning headers
- Full support for 6 months after deprecation

## Webhook Configuration

### Payment Webhooks
The application expects payment webhooks at:
- Paystack: `POST /api/payment/webhook/paystack`
- Flutterwave: `POST /api/payment/webhook/flutterwave`

### Webhook Security
Webhooks are verified using:
- Paystack: HMAC signature verification
- Flutterwave: Secret hash verification

## API Monitoring

### Request Logging
All API requests are logged with:
- Request method and URL
- Response status
- Response time
- User ID (when authenticated)
- IP address

### Performance Monitoring
- Average response times
- Error rates
- Request volumes
- Database query performance

---

*This developer guide is maintained with the codebase and updated with each release.*