# VidiaSpot Marketplace API Documentation

## Base URL
```
Production: https://api.vidiaspot.com/v1
Development: http://localhost:8000/api
```

## Authentication
All API requests require authentication except for public endpoints like login, register, and product listings.

### Headers
```
Content-Type: application/json
Accept: application/json
Authorization: Bearer {jwt_token}
```

## Authentication Endpoints

### Login
```
POST /api/login
```

#### Request
```json
{
  "email": "user@example.com",
  "password": "password123"
}
```

#### Response
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "user@example.com",
      "role": "customer"
    },
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
  }
}
```

### Register
```
POST /api/register
```

#### Request
```json
{
  "name": "John Doe",
  "email": "user@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "role": "customer"
}
```

#### Response
```json
{
  "success": true,
  "message": "Registration successful",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "user@example.com",
      "role": "customer"
    },
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
  }
}
```

## Stores Endpoints

### Get All Stores
```
GET /api/stores
```

#### Query Parameters
```
page - Page number (default: 1)
per_page - Number of items per page (default: 15, max: 100)
search - Search term to filter stores
cuisine - Filter by cuisine type
location - Filter by location
sort_by - Sort by field (name, rating, date, etc.)
order - Sort order (asc, desc)
```

#### Response
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "name": "Tech Gadgets Store",
        "slug": "tech-gadgets-store",
        "description": "Best tech gadgets and accessories",
        "logo": "https://example.com/images/store-logo.jpg",
        "rating": 4.5,
        "total_reviews": 120,
        "is_active": true,
        "created_at": "2023-01-15T10:30:00Z"
      }
    ],
    "first_page_url": "http://localhost:8000/api/stores?page=1",
    "from": 1,
    "last_page": 1,
    "last_page_url": "http://localhost:8000/api/stores?page=1",
    "next_page_url": null,
    "path": "http://localhost:8000/api/stores",
    "per_page": 15,
    "prev_page_url": null,
    "to": 1,
    "total": 1
  }
}
```

### Create Store
```
POST /api/stores
```

#### Request
```json
{
  "name": "My New Store",
  "slug": "my-new-store",
  "description": "Description of my new store",
  "contact_email": "contact@mynewstore.com",
  "contact_phone": "+1234567890",
  "address": "123 Main St, City, Country",
  "timezone": "America/New_York",
  "currency": "USD",
  "logo": "base64_image_data_or_file_upload"
}
```

#### Response
```json
{
  "success": true,
  "message": "Store created successfully",
  "data": {
    "id": 2,
    "name": "My New Store",
    "slug": "my-new-store",
    "description": "Description of my new store",
    "contact_email": "contact@mynewstore.com",
    "contact_phone": "+1234567890",
    "address": "123 Main St, City, Country",
    "timezone": "America/New_York",
    "currency": "USD",
    "logo": "https://example.com/storage/store-logos/store-logo.jpg",
    "is_active": true,
    "created_at": "2023-12-10T10:30:00Z"
  }
}
```

### Get Store Details
```
GET /api/stores/{id}
```

## Products Endpoints

### Get Store Products
```
GET /api/stores/{storeId}/products
```

#### Query Parameters
```
page - Page number (default: 1)
per_page - Number of items per page (default: 15, max: 100)
search - Search term to filter products
category - Filter by category slug
min_price - Minimum price filter
max_price - Maximum price filter
in_stock - Filter by stock availability (true/false)
sort_by - Sort by field (name, price, date, popularity)
order - Sort order (asc, desc)
```

#### Response
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "name": "Wireless Headphones",
        "description": "High-quality wireless headphones with noise cancellation",
        "price": 99.99,
        "currency": "USD",
        "inventory": 45,
        "category": "Electronics",
        "images": [
          "https://example.com/images/headphones1.jpg",
          "https://example.com/images/headphones2.jpg"
        ],
        "is_active": true,
        "created_at": "2023-01-15T10:30:00Z"
      }
    ],
    "first_page_url": "http://localhost:8000/api/stores/1/products?page=1",
    "from": 1,
    "last_page": 1,
    "last_page_url": "http://localhost:8000/api/stores/1/products?page=1",
    "next_page_url": null,
    "path": "http://localhost:8000/api/stores/1/products",
    "per_page": 15,
    "prev_page_url": null,
    "to": 1,
    "total": 1
  }
}
```

### Create Product
```
POST /api/stores/{storeId}/products
```

#### Request
```json
{
  "name": "Wireless Headphones",
  "description": "High-quality wireless headphones with noise cancellation",
  "price": 99.99,
  "currency": "USD",
  "inventory": 50,
  "category": "Electronics",
  "sku": "WH-001",
  "weight": 0.5,
  "dimensions": "7 x 6 x 3 inches",
  "variations": [
    {
      "name": "Color",
      "options": [
        {
          "name": "Black",
          "price_delta": 0,
          "sku": "WH-001-BLK",
          "inventory": 25
        },
        {
          "name": "White",
          "price_delta": 5,
          "sku": "WH-001-WHT",
          "inventory": 25
        }
      ]
    }
  ],
  "images": ["base64_image_data"],
  "is_active": true
}
```

## Orders Endpoints

### Create Order
```
POST /api/orders
```

#### Request
```json
{
  "items": [
    {
      "product_id": 1,
      "quantity": 2,
      "variation_id": null,
      "special_instructions": "Handle with care"
    }
  ],
  "shipping_address": {
    "street": "123 Main St",
    "city": "New York",
    "state": "NY",
    "zip": "10001",
    "country": "USA"
  },
  "payment_method": "card",
  "notes": "Leave at door",
  "estimated_delivery_date": "2023-12-15T10:00:00Z"
}
```

#### Response
```json
{
  "success": true,
  "message": "Order created successfully",
  "data": {
    "id": 1,
    "order_number": "ORD-2023-00001",
    "items": [
      {
        "product_id": 1,
        "quantity": 2,
        "price": 99.99,
        "total": 199.98,
        "product_name": "Wireless Headphones"
      }
    ],
    "total_amount": 199.98,
    "currency": "USD",
    "status": "pending",
    "payment_status": "pending",
    "shipping_address": {
      "street": "123 Main St",
      "city": "New York",
      "state": "NY",
      "zip": "10001",
      "country": "USA"
    },
    "estimated_delivery_date": "2023-12-15T10:00:00Z",
    "created_at": "2023-12-10T10:30:00Z",
    "updated_at": "2023-12-10T10:30:00Z",
    "tracking_number": "TRK-1234567890"
  }
}
```

## Food Ordering Endpoints

### Get Food Vendors
```
GET /api/food/vendors
```

#### Query Parameters
```
page - Page number (default: 1)
per_page - Number of items per page (default: 15, max: 100)
search - Search term (vendor name or cuisine)
cuisine - Filter by cuisine type (italian, chinese, mexican, etc.)
location - Filter by location
is_open - Filter by open status (true/false)
sort_by - Sort by (name, rating, distance)
order - Sort order (asc, desc)
```

#### Response
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "name": "Italian Delight",
        "description": "Authentic Italian cuisine",
        "cuisine_type": "Italian",
        "address": "456 Food Street, City",
        "phone": "+1234567890",
        "rating": 4.8,
        "total_reviews": 245,
        "minimum_order": 15.00,
        "delivery_fee": 3.99,
        "delivery_time": "30-45 mins",
        "is_open": true,
        "hours": {
          "monday": "10:00-22:00",
          "tuesday": "10:00-22:00",
          "wednesday": "10:00-22:00",
          "thursday": "10:00-22:00",
          "friday": "10:00-23:00",
          "saturday": "11:00-23:00",
          "sunday": "11:00-21:00"
        },
        "logo": "https://example.com/images/vendor-logo.jpg",
        "cover_image": "https://example.com/images/vendor-cover.jpg"
      }
    ],
    "total": 1
  }
}
```

### Get Vendor Menu
```
GET /api/food/vendors/{vendorId}/menu
```

#### Response
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Margherita Pizza",
      "description": "Fresh tomatoes, mozzarella, basil, olive oil",
      "price": 12.99,
      "category": "Pizzas",
      "ingredients": ["tomatoes", "mozzarella", "basil", "olive oil"],
      "dietary_info": ["vegetarian"],
      "image": "https://example.com/images/margherita.jpg",
      "is_available": true,
      "preparation_time": 15
    },
    {
      "id": 2,
      "name": "Caesar Salad",
      "description": "Fresh romaine lettuce, parmesan cheese, croutons, caesar dressing",
      "price": 9.99,
      "category": "Salads",
      "ingredients": ["romaine lettuce", "parmesan cheese", "croutons", "caesar dressing"],
      "dietary_info": ["vegetarian"],
      "image": "https://example.com/images/caesar.jpg",
      "is_available": true,
      "preparation_time": 5
    }
  ]
}
```

### Create Food Order
```
POST /api/food/orders
```

#### Request
```json
{
  "vendor_id": 1,
  "items": [
    {
      "menu_item_id": 1,
      "quantity": 1,
      "customization": "Extra cheese",
      "special_instructions": "No onions please"
    },
    {
      "menu_item_id": 2,
      "quantity": 2,
      "customization": "Dressing on side",
      "special_instructions": ""
    }
  ],
  "delivery_address": {
    "street": "789 Delivery Ave",
    "city": "New York",
    "state": "NY",
    "zip": "10001",
    "country": "USA"
  },
  "delivery_time": "asap", // "asap", "2023-12-10T14:30:00Z"
  "tip_amount": 2.00,
  "payment_method": "card",
  "notes": "Ring doorbell twice"
}
```

## Affiliate/MLM Endpoints

### Get Affiliate Status
```
GET /api/affiliate/current-user
```

#### Response
```json
{
  "success": true,
  "data": {
    "is_affiliate": true,
    "referral_code": "AFF-USER12345",
    "commission_rate": 0.05,
    "total_referrals": 12,
    "total_earnings": 124.50,
    "total_paid": 75.25,
    "pending_payouts": 49.25,
    "is_approved": true
  }
}
```

### Apply for Affiliate Program
```
POST /api/affiliate/apply
```

#### Request
```json
{
  "business_type": "online_store",
  "marketing_channels": ["social_media", "content_marketing", "influencer"],
  "expected_monthly_traffic": 1000,
  "business_description": "I run a blog about food and tech products",
  "social_media_links": [
    "https://instagram.com/myblog",
    "https://youtube.com/mychannel"
  ]
}
```

### Get Affiliate Downline (Up to 20 levels)
```
GET /api/affiliate/{id}/downline
```

#### Query Parameters
```
depth - Number of levels to retrieve (1-20, default: 3)
level - Specific level to retrieve (1-20)
```

#### Response
```json
{
  "success": true,
  "data": {
    "level_1": [
      {
        "id": 101,
        "name": "Jane Smith",
        "email": "jane@example.com",
        "total_referrals": 3,
        "total_commissions": 45.20,
        "joined_at": "2023-11-15T10:30:00Z"
      }
    ],
    "level_2": [
      {
        "id": 102,
        "name": "Bob Johnson",
        "email": "bob@example.com",
        "total_referrals": 1,
        "total_commissions": 12.50,
        "joined_at": "2023-11-20T14:20:00Z",
        "sponsor_id": 101
      }
    ],
    "level_3": [
      {
        "id": 103,
        "name": "Alice Williams",
        "email": "alice@example.com",
        "total_referrals": 0,
        "total_commissions": 0.00,
        "joined_at": "2023-12-01T09:15:00Z",
        "sponsor_id": 102
      }
    ],
    // Up to level 20...
    "summary": {
      "total_downline_members": 185,
      "total_commissions_earned": 1245.75,
      "total_direct_referrals": 12
    }
  }
}
```

### Get Affiliate Commissions
```
GET /api/affiliate/{id}/commissions
```

#### Query Parameters
```
page - Page number (default: 1)
per_page - Number of items per page (default: 15)
start_date - Filter from date (YYYY-MM-DD)
end_date - Filter to date (YYYY-MM-DD)
type - Filter by type (direct, upline)
level - Filter by level (1-20)
```

#### Response
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "order_id": 123,
        "customer_id": 456,
        "referral_user_id": 789,
        "amount": 4.99,
        "rate": 0.05,
        "type": "direct",
        "level": 1,
        "commission_date": "2023-12-10T10:30:00Z",
        "order_details": {
          "order_number": "ORD-2023-00123",
          "total_amount": 99.99
        }
      }
    ],
    "total": 1
  }
}
```

## Import/Export Endpoints

### Import from Shopify
```
POST /api/import-export/shopify
```

#### Request
```json
{
  "url": "https://myshopifyshop.myshopify.com",
  "api_key": "your_shopify_api_key",
  "password": "your_shopify_password",
  "import_products": true,
  "import_customers": true,
  "import_orders": false,
  "create_categories": true,
  "map_categories": true,
  "duplicate_handling": "skip" // skip, overwrite, merge
}
```

### Import from WooCommerce
```
POST /api/import-export/woocommerce
```

#### Request
```json
{
  "url": "https://mywoocommerce.com",
  "consumer_key": "ck_your_consumer_key",
  "consumer_secret": "cs_your_consumer_secret",
  "import_products": true,
  "import_categories": true,
  "import_inventory": true,
  "create_new_store": false,
  "target_store_id": 1,
  "duplicate_handling": "merge",
  "update_existing": true
}
```

### Export to CSV
```
POST /api/import-export/export-csv
```

#### Request
```json
{
  "entity_type": "products", // products, customers, orders, categories
  "store_id": 1,
  "fields": ["name", "description", "price", "inventory", "category"],
  "filters": {
    "date_range": {
      "start": "2023-01-01",
      "end": "2023-12-10"
    },
    "categories": ["electronics", "clothing"],
    "status": ["active"]
  }
}
```

### Get Import/Export Status
```
GET /api/import-export/{operationId}/status
```

#### Response
```json
{
  "success": true,
  "data": {
    "id": 1,
    "status": "completed", // pending, processing, completed, failed
    "entity_type": "products",
    "platform": "shopify",
    "action": "import", // import or export
    "total_records": 1250,
    "processed_records": 1250,
    "failed_records": 2,
    "success_rate": 99.84,
    "started_at": "2023-12-10T10:30:00Z",
    "completed_at": "2023-12-10T10:45:00Z",
    "error_details": [
      {
        "record_id": "prod-123",
        "error_message": "Invalid price format"
      }
    ],
    "summary": {
      "new_records_created": 1248,
      "existing_records_updated": 0,
      "records_skipped": 2
    }
  }
}
```

## Analytics/Reporting Endpoints

### Get Store Analytics
```
GET /api/stores/{storeId}/analytics
```

#### Query Parameters
```
period - Time period (daily, weekly, monthly, quarterly, yearly)
start_date - Start date (YYYY-MM-DD)
end_date - End date (YYYY-MM-DD)
metric - Specific metric to retrieve (revenue, orders, customers, etc.)
```

#### Response
```json
{
  "success": true,
  "data": {
    "summary": {
      "total_revenue": 12450.75,
      "total_orders": 324,
      "avg_order_value": 38.43,
      "new_customers": 45,
      "conversion_rate": 2.5
    },
    "trends": {
      "revenue_trend": [
        {"date": "2023-12-01", "amount": 320.50},
        {"date": "2023-12-02", "amount": 285.75}
      ],
      "order_trend": [
        {"date": "2023-12-01", "count": 8},
        {"date": "2023-12-02", "count": 6}
      ]
    },
    "top_products": [
      {
        "id": 1,
        "name": "Wireless Headphones",
        "units_sold": 45,
        "revenue": 4495.50
      }
    ]
  }
}
```

### Get Operational Analytics
```
GET /api/analytics/operational
```

#### Query Parameters
```
start_date - Start date (YYYY-MM-DD)
end_date - End date (YYYY-MM-DD)
store_id - Filter by store ID
```

#### Response
```json
{
  "success": true,
  "data": {
    "fleet_utilization": {
      "total_vehicles": 15,
      "active_vehicles": 12,
      "utilization_rate": 80.0,
      "distance_traveled": 1245.6,
      "fuel_consumed": 120.5
    },
    "driver_performance": {
      "total_drivers": 18,
      "active_drivers": 15,
      "avg_delivery_time": 32,
      "on_time_delivery_rate": 94.5,
      "customer_satisfaction": 4.8
    },
    "delivery_metrics": {
      "total_deliveries": 324,
      "successful_deliveries": 320,
      "failed_deliveries": 4,
      "avg_package_value": 38.43
    }
  }
}
```

## Error Handling

### Common Error Responses

#### Validation Error
```json
{
  "success": false,
  "message": "Validation failed",
  "data": {
    "errors": {
      "email": ["The email field is required."],
      "password": ["The password must be at least 8 characters."]
    }
  }
}
```

#### Unauthorized Error
```json
{
  "success": false,
  "message": "Unauthenticated.",
  "data": {}
}
```

#### Not Found Error
```json
{
  "success": false,
  "message": "Resource not found.",
  "data": {}
}
```

#### Server Error
```json
{
  "success": false,
  "message": "An error occurred while processing your request.",
  "data": {}
}
```

## Rate Limiting

All authenticated endpoints are limited to 60 requests per minute per IP. Exceeding this limit will return:

```json
{
  "message": "Too Many Attempts.",
  "exception": "Symfony\\Component\\HttpKernel\\Exception\\TooManyRequestsHttpException"
}
```

## Versioning

All endpoints are versioned with the `/api` prefix. Breaking changes will result in a new major version (e.g., `/api/v2`). Non-breaking additions will be added to the current version.

## Status Codes

- `200` - OK: Successful request
- `201` - Created: Resource created successfully
- `400` - Bad Request: Invalid request format
- `401` - Unauthorized: Authentication required
- `403` - Forbidden: Insufficient permissions
- `404` - Not Found: Resource not found
- `422` - Unprocessable Entity: Validation error
- `429` - Too Many Requests: Rate limit exceeded
- `500` - Internal Server Error: Server error

## SDKs and Libraries

### JavaScript/React SDK
```javascript
import { VidiaSpotAPI } from './sdk';

const api = new VidiaSpotAPI({
  baseURL: 'https://api.vidiaspot.com/v1',
  token: 'your_jwt_token'
});

// Create a product
const product = await api.stores.createProduct(storeId, productData);

// Get affiliate downline
const downline = await api.affiliate.getDownline(userId, { depth: 10 });
```

### PHP SDK (Backend)
```php
use VidiaSpot\VidiaSpotClient;

$client = new VidiaSpotClient([
    'base_uri' => 'https://api.vidiaspot.com/v1',
    'token' => 'your_jwt_token'
]);

$products = $client->stores->getProducts($storeId);
$downline = $client->affiliate->getDownline($userId, ['depth' => 10]);
```