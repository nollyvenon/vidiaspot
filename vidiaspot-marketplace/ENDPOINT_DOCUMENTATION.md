# VidiaSpot Marketplace - API Endpoints Documentation

## Table of Contents
1. [Public Endpoints](#public-endpoints)
2. [Authenticated Endpoints](#authenticated-endpoints)
3. [Admin Endpoints](#admin-endpoints)
4. [Authentication](#authentication)
5. [Response Format](#response-format)
6. [Error Codes](#error-codes)

---

## Public Endpoints

### Ads

#### Get All Ads
- **Method**: `GET`
- **Endpoint**: `/api/ads`
- **Description**: Retrieve a paginated list of all active ads with optional filtering
- **Authentication**: No

**Query Parameters**:
- `category_id` (integer, optional) - Filter by category ID
- `location` (string, optional) - Filter by location
- `min_price` (numeric, optional) - Filter by minimum price
- `max_price` (numeric, optional) - Filter by maximum price  
- `condition` (string, optional) - Filter by condition (new, like_new, good, fair, poor)
- `search` (string, optional) - Search in title and description
- `order_by` (string, optional) - Sort field (default: created_at)
- `order_direction` (string, optional) - Sort direction (asc/desc, default: desc)
- `per_page` (integer, optional) - Results per page (default: 15)

**Example Request**:
```
GET /api/ads?category_id=5&location=New%20York&min_price=100&max_price=500&order_by=price&order_direction=asc
Authorization: Bearer 
```

**Example Response** (Status: 200):
```json
{
  "success": true,
  "data": {
    "data": [
      {
        "id": 1,
        "title": "Vintage Guitar",
        "description": "Well-maintained vintage guitar for sale",
        "price": 250.00,
        "currency_code": "USD",
        "condition": "good",
        "location": "New York, NY",
        "negotiable": true,
        "status": "active",
        "view_count": 24,
        "created_at": "2023-10-15T10:30:00.000000Z",
        "updated_at": "2023-10-16T14:20:00.000000Z",
        "user": {
          "id": 2,
          "name": "John Smith",
          "email": "john@example.com"
        },
        "category": {
          "id": 5,
          "name": "Musical Instruments",
          "slug": "musical-instruments"
        },
        "images": [
          {
            "id": 1,
            "image_url": "http://localhost:8000/storage/ads/1/image1.jpg",
            "is_primary": true,
            "order": 0
          }
        ]
      }
    ],
    "links": {
      "first": "http://localhost:8000/api/ads?page=1",
      "last": "http://localhost:8000/api/ads?page=10",
      "prev": null,
      "next": "http://localhost:8000/api/ads?page=2"
    },
    "meta": {
      "current_page": 1,
      "from": 1,
      "last_page": 10,
      "links": [
        {
          "url": null,
          "label": "&laquo; Previous",
          "active": false
        },
        {
          "url": "http://localhost:8000/api/ads?page=1",
          "label": "1",
          "active": true
        }
      ],
      "path": "http://localhost:8000/api/ads",
      "per_page": 15,
      "to": 15,
      "total": 142
    }
  }
}
```

#### Get Single Ad
- **Method**: `GET`
- **Endpoint**: `/api/ads/{id}`
- **Description**: Retrieve details for a specific ad
- **Note**: This endpoint increments the view count for the ad
- **Authentication**: No

**Example Request**:
```
GET /api/ads/1
Authorization: Bearer 
```

**Example Response** (Status: 200):
```json
{
  "success": true,
  "data": {
    "id": 1,
    "title": "Vintage Guitar",
    "description": "Well-maintained vintage guitar for sale",
    "price": 250.00,
    "currency_code": "USD",
    "condition": "good",
    "location": "New York, NY",
    "negotiable": true,
    "status": "active",
    "view_count": 25,
    "created_at": "2023-10-15T10:30:00.000000Z",
    "updated_at": "2023-10-16T14:20:00.000000Z",
    "user": {
      "id": 2,
      "name": "John Smith",
      "email": "john@example.com",
      "phone": "+1234567890"
    },
    "category": {
      "id": 5,
      "name": "Musical Instruments",
      "slug": "musical-instruments"
    },
    "images": [
      {
        "id": 1,
        "image_url": "http://localhost:8000/storage/ads/1/image1.jpg",
        "is_primary": true,
        "order": 0
      }
    ]
  }
}
```

### Categories

#### Get All Categories
- **Method**: `GET`
- **Endpoint**: `/api/categories`
- **Description**: Retrieve all available categories
- **Authentication**: No

**Example Request**:
```
GET /api/categories
Authorization: Bearer 
```

**Example Response** (Status: 200):
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Electronics",
      "slug": "electronics",
      "description": "Electronic devices and accessories",
      "parent_id": null,
      "status": "active",
      "created_at": "2023-10-15T10:30:00.000000Z",
      "updated_at": "2023-10-15T10:30:00.000000Z"
    },
    {
      "id": 2,
      "name": "Books",
      "slug": "books",
      "description": "New and used books",
      "parent_id": null,
      "status": "active",
      "created_at": "2023-10-15T10:30:00.000000Z",
      "updated_at": "2023-10-15T10:30:00.000000Z"
    }
  ]
}
```

### Content Pages

#### Get All Content Pages
- **Method**: `GET`
- **Endpoint**: `/api/pages`
- **Description**: Retrieve all available content pages
- **Authentication**: No

#### Get Content Page by Slug
- **Method**: `GET`
- **Endpoint**: `/api/pages/{slug}`
- **Description**: Retrieve a specific content page by slug
- **Authentication**: No

**Example Request**:
```
GET /api/pages/about-us
Authorization: Bearer 
```

#### Specific Static Pages
- `GET /api/about` - Get about page content
- `GET /api/contact` - Get contact page content  
- `GET /api/services` - Get services page content
- `GET /api/privacy` - Get privacy policy
- `GET /api/terms` - Get terms of service

### Recommendations

#### Get Trending Ads
- **Method**: `GET`
- **Endpoint**: `/api/recommendations/trending`
- **Description**: Get trending ads based on view count and recency
- **Authentication**: No

#### Get Similar Ads
- **Method**: `GET`
- **Endpoint**: `/api/ads/{id}/similar`
- **Description**: Get ads similar to the specified ad
- **Authentication**: No

**Example Request**:
```
GET /api/ads/1/similar
Authorization: Bearer 
```

### Social Authentication

#### Redirect to Social Provider
- **Method**: `GET`
- **Endpoint**: `/api/auth/{provider}`
- **Description**: Redirect user to social provider for authentication
- **Providers**: google, facebook, twitter
- **Authentication**: No

**Example Request**:
```
GET /api/auth/google
Authorization: Bearer 
```

#### Handle Social Callback
- **Method**: `GET`
- **Endpoint**: `/api/auth/{provider}/callback`
- **Description**: Handle callback from social provider
- **Authentication**: No

#### Get Available Social Providers
- **Method**: `GET`
- **Endpoint**: `/api/auth/providers`
- **Description**: Get list of enabled social authentication providers
- **Authentication**: No

---

## Authenticated Endpoints

### Ads Management

#### Create Ad
- **Method**: `POST`
- **Endpoint**: `/api/ads`
- **Description**: Create a new ad listing
- **Authentication**: Required

**Request Headers**:
- `Authorization: Bearer {token}`
- `Content-Type: multipart/form-data` (for file uploads) or `application/json`

**Required Fields**:
- `title` (string, max 255) - Ad title
- `description` (string) - Ad description  
- `price` (numeric, min 0) - Price amount
- `category_id` (integer, exists in categories) - Category ID
- `condition` (string, in: new, like_new, good, fair, poor) - Item condition
- `location` (string, max 255) - Item location

**Optional Fields**:
- `currency_code` (string, size: 3, exists in currencies, default: NGN) - Currency code
- `negotiable` (boolean) - Whether price is negotiable
- `contact_phone` (string, max 20, nullable) - Contact phone number
- `images` (array, max 10) - Array of images
- `images.*` (image, mimes: jpeg,png,jpg,gif,svg, max: 10240) - Individual image validation

**Example Request** (multipart/form-data):
```
POST /api/ads
Authorization: Bearer your-token-here
Content-Type: multipart/form-data

title=Vintage Guitar
description=Well-maintained vintage guitar for sale
price=250.00
category_id=5
condition=good
location=New York, NY
negotiable=1
contact_phone=+1234567890
images[0]=@image1.jpg
images[1]=@image2.jpg
```

**Example Request** (application/json):
```json
POST /api/ads
Authorization: Bearer your-token-here
Content-Type: application/json

{
  "title": "Vintage Guitar",
  "description": "Well-maintained vintage guitar for sale",
  "price": 250.00,
  "currency_code": "USD",
  "category_id": 5,
  "condition": "good",
  "location": "New York, NY",
  "negotiable": true,
  "contact_phone": "+1234567890"
}
```

**Successful Response** (Status: 201):
```json
{
  "success": true,
  "data": {
    "id": 1,
    "title": "Vintage Guitar",
    "description": "Well-maintained vintage guitar for sale",
    "price": 250.00,
    "currency_code": "USD",
    "condition": "good",
    "location": "New York, NY",
    "negotiable": true,
    "status": "active",
    "created_at": "2023-10-15T10:30:00.000000Z",
    "updated_at": "2023-10-15T10:30:00.000000Z",
    "user": {
      "id": 2,
      "name": "John Smith",
      "email": "john@example.com"
    },
    "category": {
      "id": 5,
      "name": "Musical Instruments",
      "slug": "musical-instruments"
    },
    "images": [
      {
        "id": 1,
        "image_path": "ads/1/image1.jpg",
        "image_url": "http://localhost:8000/storage/ads/1/image1.jpg",
        "is_primary": true,
        "order": 0
      }
    ]
  }
}
```

**Validation Error Response** (Status: 422):
```json
{
  "success": false,
  "message": "The given data was invalid.",
  "errors": {
    "title": [
      "The title field is required."
    ],
    "price": [
      "The price field is required.",
      "The price must be a number."
    ]
  }
}
```

#### Update Ad
- **Method**: `PUT`
- **Endpoint**: `/api/ads/{id}`
- **Description**: Update an existing ad
- **Authentication**: Required

**Request Headers**:
- `Authorization: Bearer {token}`
- `Content-Type: application/json` or `multipart/form-data`

**Fields**: Same as Create Ad, but all optional

**Example Request**:
```json
PUT /api/ads/1
Authorization: Bearer your-token-here
Content-Type: application/json

{
  "title": "Updated Guitar",
  "price": 275.00
}
```

**Response** (Status: 200):
```json
{
  "success": true,
  "data": {
    "id": 1,
    "title": "Updated Guitar",
    "description": "Well-maintained vintage guitar for sale",
    "price": 275.00,
    "currency_code": "USD",
    "condition": "good",
    "location": "New York, NY",
    "negotiable": true,
    "status": "active",
    "created_at": "2023-10-15T10:30:00.000000Z",
    "updated_at": "2023-10-16T15:45:00.000000Z",
    "user": {
      "id": 2,
      "name": "John Smith",
      "email": "john@example.com"
    },
    "category": {
      "id": 5,
      "name": "Musical Instruments",
      "slug": "musical-instruments"
    },
    "images": [
      {
        "id": 1,
        "image_path": "ads/1/image1.jpg",
        "image_url": "http://localhost:8000/storage/ads/1/image1.jpg",
        "is_primary": true,
        "order": 0
      }
    ]
  }
}
```

#### Delete Ad
- **Method**: `DELETE`
- **Endpoint**: `/api/ads/{id}`
- **Description**: Delete an ad
- **Authentication**: Required
- **Authorization**: Only ad owner can delete their ads

**Example Request**:
```
DELETE /api/ads/1
Authorization: Bearer your-token-here
```

**Response** (Status: 200):
```json
{
  "success": true,
  "message": "Ad deleted successfully"
}
```

#### Get My Ads
- **Method**: `GET`
- **Endpoint**: `/api/my-ads`
- **Description**: Get ads created by the authenticated user
- **Authentication**: Required

**Query Parameters**:
- `per_page` (integer, optional) - Results per page (default: 15)

**Example Request**:
```
GET /api/my-ads
Authorization: Bearer your-token-here
```

**Response** (Status: 200):
```json
{
  "success": true,
  "data": {
    "data": [
      {
        "id": 1,
        "title": "Vintage Guitar",
        "description": "Well-maintained vintage guitar for sale",
        "price": 250.00,
        "currency_code": "USD",
        "condition": "good",
        "location": "New York, NY",
        "negotiable": true,
        "status": "active",
        "view_count": 24,
        "created_at": "2023-10-15T10:30:00.000000Z",
        "updated_at": "2023-10-16T14:20:00.000000Z",
        "category": {
          "id": 5,
          "name": "Musical Instruments",
          "slug": "musical-instruments"
        },
        "images": [
          {
            "id": 1,
            "image_path": "ads/1/image1.jpg",
            "image_url": "http://localhost:8000/storage/ads/1/image1.jpg",
            "is_primary": true,
            "order": 0
          }
        ]
      }
    ],
    "links": {
      "first": "http://localhost:8000/api/my-ads?page=1",
      "last": "http://localhost:8000/api/my-ads?page=1",
      "prev": null,
      "next": null
    },
    "meta": {
      "current_page": 1,
      "from": 1,
      "last_page": 1,
      "links": [
        {
          "url": null,
          "label": "&laquo; Previous",
          "active": false
        },
        {
          "url": "http://localhost:8000/api/my-ads?page=1",
          "label": "1",
          "active": true
        },
        {
          "url": null,
          "label": "&raquo; Next",
          "active": false
        }
      ],
      "path": "http://localhost:8000/api/my-ads",
      "per_page": 15,
      "to": 1,
      "total": 1
    }
  }
}
```

#### Add Images to Ad
- **Method**: `POST`
- **Endpoint**: `/api/ads/{id}/images`
- **Description**: Add images to an existing ad
- **Authentication**: Required
- **Authorization**: Only ad owner can add images

**Request Headers**:
- `Authorization: Bearer {token}`
- `Content-Type: multipart/form-data`

**Required Fields**:
- `images` (array, min 1, max 10) - Array of images to add
- `images.*` (image, mimes: jpeg,png,jpg,gif,svg, max: 10240) - Individual image validation

**Example Request**:
```
POST /api/ads/1/images
Authorization: Bearer your-token-here
Content-Type: multipart/form-data

images[0]=@image3.jpg
images[1]=@image4.jpg
```

**Response** (Status: 200):
```json
{
  "success": true,
  "data": {
    "id": 1,
    "title": "Vintage Guitar",
    "description": "Well-maintained vintage guitar for sale",
    "price": 250.00,
    "currency_code": "USD",
    "condition": "good",
    "location": "New York, NY",
    "negotiable": true,
    "status": "active",
    "created_at": "2023-10-15T10:30:00.000000Z",
    "updated_at": "2023-10-16T15:45:00.000000Z",
    "user": {
      "id": 2,
      "name": "John Smith",
      "email": "john@example.com"
    },
    "category": {
      "id": 5,
      "name": "Musical Instruments",
      "slug": "musical-instruments"
    },
    "images": [
      {
        "id": 1,
        "image_path": "ads/1/image1.jpg",
        "image_url": "http://localhost:8000/storage/ads/1/image1.jpg",
        "is_primary": true,
        "order": 0
      },
      {
        "id": 2,
        "image_path": "ads/1/image2.jpg",
        "image_url": "http://localhost:8000/storage/ads/1/image2.jpg",
        "is_primary": false,
        "order": 1
      },
      {
        "id": 3,
        "image_path": "ads/1/image3.jpg",
        "image_url": "http://localhost:8000/storage/ads/1/image3.jpg",
        "is_primary": false,
        "order": 2
      }
    ]
  }
}
```

### Messages

#### Get Messages
- **Method**: `GET`
- **Endpoint**: `/api/messages`
- **Description**: Get user's messages
- **Authentication**: Required
- **Query Parameters**:
  - `partner_id` (integer, optional) - Filter messages with specific user
  - `per_page` (integer, optional) - Results per page

**Example Request**:
```
GET /api/messages
Authorization: Bearer your-token-here
```

#### Send Message
- **Method**: `POST`
- **Endpoint**: `/api/messages`
- **Description**: Send a new message
- **Authentication**: Required

**Request Headers**:
- `Authorization: Bearer {token}`
- `Content-Type: application/json`

**Required Fields**:
- `receiver_id` (integer, exists in users) - ID of message recipient
- `message` (string, max 1000) - Message content

**Optional Fields**:
- `messageable_type` (string) - Type of related object (e.g., 'ad')
- `messageable_id` (integer) - ID of related object

**Example Request**:
```json
POST /api/messages
Authorization: Bearer your-token-here
Content-Type: application/json

{
  "receiver_id": 3,
  "message": "Hi, I'm interested in your guitar. Is it still available?",
  "messageable_type": "ad",
  "messageable_id": 1
}
```

**Response** (Status: 201):
```json
{
  "success": true,
  "message": "Message sent successfully",
  "chat": {
    "id": 15,
    "sender_id": 2,
    "receiver_id": 3,
    "message": "Hi, I'm interested in your guitar. Is it still available?",
    "messageable_type": "ad",
    "messageable_id": 1,
    "is_read": false,
    "created_at": "2023-10-16T16:30:00.000000Z",
    "updated_at": "2023-10-16T16:30:00.000000Z",
    "sender": {
      "id": 2,
      "name": "John Smith",
      "email": "john@example.com"
    },
    "receiver": {
      "id": 3,
      "name": "Jane Doe",
      "email": "jane@example.com"
    }
  }
}
```

#### Get Conversations
- **Method**: `GET`
- **Endpoint**: `/api/messages/conversations`
- **Description**: Get list of users the authenticated user has chatted with
- **Authentication**: Required

**Example Request**:
```
GET /api/messages/conversations
Authorization: Bearer your-token-here
```

**Response** (Status: 200):
```json
{
  "success": true,
  "users": [
    {
      "id": 3,
      "name": "Jane Doe",
      "email": "jane@example.com",
      "unread_count": 2
    },
    {
      "id": 4,
      "name": "Bob Johnson",
      "email": "bob@example.com",
      "unread_count": 0
    }
  ]
}
```

#### Mark Message as Read
- **Method**: `PUT`
- **Endpoint**: `/api/messages/{id}/mark-as-read`
- **Description**: Mark a specific message as read
- **Authentication**: Required

**Example Request**:
```
PUT /api/messages/15/mark-as-read
Authorization: Bearer your-token-here
```

**Response** (Status: 200):
```json
{
  "success": true,
  "message": "Messages marked as read"
}
```

### Categories

#### Get Category Tree
- **Method**: `GET`
- **Endpoint**: `/api/categories/tree`
- **Description**: Get hierarchical category tree
- **Authentication**: Required

**Example Request**:
```
GET /api/categories/tree
Authorization: Bearer your-token-here
```

### Recommendations

#### Get Personalized Recommendations
- **Method**: `GET`
- **Endpoint**: `/api/recommendations`
- **Description**: Get personalized recommendations for the authenticated user
- **Authentication**: Required

**Query Parameters**:
- `limit` (integer, optional) - Number of recommendations to return

**Example Request**:
```
GET /api/recommendations?limit=10
Authorization: Bearer your-token-here
```

---

## Admin Endpoints

All admin endpoints are prefixed with `/api/admin/` and require admin role authentication.

### Dashboard & Analytics

#### Get Dashboard Data
- **Method**: `GET`
- **Endpoint**: `/api/admin/dashboard`
- **Description**: Get admin dashboard statistics
- **Authentication**: Required (Admin role)

**Example Request**:
```
GET /api/admin/dashboard
Authorization: Bearer admin-token-here
```

**Response** (Status: 200):
```json
{
  "success": true,
  "data": {
    "total_users": 150,
    "total_ads": 42,
    "total_active_ads": 38,
    "total_pending_ads": 4,
    "total_revenue": 1250.75,
    "recent_signups": [
      {
        "id": 12,
        "name": "Alice Smith",
        "email": "alice@example.com",
        "created_at": "2023-10-16T15:30:00.000000Z"
      }
    ],
    "recent_ads": [
      {
        "id": 42,
        "title": "New Laptop",
        "user": {
          "id": 12,
          "name": "Alice Smith"
        },
        "created_at": "2023-10-16T14:45:00.000000Z"
      }
    ]
  }
}
```

#### Get Analytics
- **Method**: `GET`
- **Endpoint**: `/api/admin/analytics`
- **Description**: Get detailed analytics and statistics
- **Authentication**: Required (Admin role)

**Query Parameters**:
- `start_date` (date, optional) - Start date for analytics
- `end_date` (date, optional) - End date for analytics
- `period` (string, optional) - Time period (daily, weekly, monthly, yearly)

### Ads Management

#### Get All Ads
- **Method**: `GET`
- **Endpoint**: `/api/admin/ads`
- **Description**: List all ads with pagination
- **Authentication**: Required (Admin role)

**Query Parameters**:
- `status` (string, optional) - Filter by status (active, inactive, sold, pending)
- `user_id` (integer, optional) - Filter by user ID
- `category_id` (integer, optional) - Filter by category ID
- `per_page` (integer, optional) - Results per page

#### Get Ad Details
- **Method**: `GET`
- **Endpoint**: `/api/admin/ads/{id}`
- **Description**: Get specific ad details
- **Authentication**: Required (Admin role)

#### Create Ad (Admin)
- **Method**: `POST`
- **Endpoint**: `/api/admin/ads`
- **Description**: Create an ad on behalf of a user (Admin function)
- **Authentication**: Required (Admin role)

#### Update Ad (Admin)
- **Method**: `PUT`
- **Endpoint**: `/api/admin/ads/{id}`
- **Description**: Update an ad (Admin function)
- **Authentication**: Required (Admin role)

#### Delete Ad (Admin)
- **Method**: `DELETE`
- **Endpoint**: `/api/admin/ads/{id}`
- **Description**: Delete an ad (Admin function)
- **Authentication**: Required (Admin role)

#### Update Ad Status
- **Method**: `PUT`
- **Endpoint**: `/api/admin/ads/{id}/status`
- **Description**: Update ad status
- **Authentication**: Required (Admin role)

**Request Body**:
```json
{
  "status": "active" // Options: active, inactive, sold, pending, rejected
}
```

#### Get Pending Ads
- **Method**: `GET`
- **Endpoint**: `/api/admin/ads/pending`
- **Description**: Get ads pending admin review
- **Authentication**: Required (Admin role)

### Users Management

#### Get All Users
- **Method**: `GET`
- **Endpoint**: `/api/admin/users`
- **Description**: List all users with pagination
- **Authentication**: Required (Admin role)

**Query Parameters**:
- `role` (string, optional) - Filter by role
- `status` (string, optional) - Filter by account status
- `search` (string, optional) - Search by name or email

#### Get User Details
- **Method**: `GET`
- **Endpoint**: `/api/admin/users/{id}`
- **Description**: Get specific user details
- **Authentication**: Required (Admin role)

#### Update User
- **Method**: `PUT`
- **Endpoint**: `/api/admin/users/{id}`
- **Description**: Update user information
- **Authentication**: Required (Admin role)

#### Delete User
- **Method**: `DELETE`
- **Endpoint**: `/api/admin/users/{id}`
- **Description**: Delete a user account
- **Authentication**: Required (Admin role)

#### Assign User Role
- **Method**: `PATCH`
- **Endpoint**: `/api/admin/users/{id}/role`
- **Description**: Assign a role to a user
- **Authentication**: Required (Admin role)

**Request Body**:
```json
{
  "role": "vendor" // Options: user, vendor, admin
}
```

#### Get User Statistics
- **Method**: `GET`
- **Endpoint**: `/api/admin/users/stats`
- **Description**: Get user statistics and analytics
- **Authentication**: Required (Admin role)

### Categories Management

#### Get All Categories
- **Method**: `GET`
- **Endpoint**: `/api/admin/categories`
- **Description**: List all categories with pagination
- **Authentication**: Required (Admin role)

#### Create Category
- **Method**: `POST`
- **Endpoint**: `/api/admin/categories`
- **Description**: Create a new category
- **Authentication**: Required (Admin role)

**Request Body**:
```json
{
  "name": "Sports & Outdoors",
  "slug": "sports-outdoors", 
  "description": "Sports equipment and outdoor gear",
  "parent_id": null,
  "status": "active"
}
```

#### Get Category Details
- **Method**: `GET`
- **Endpoint**: `/api/admin/categories/{id}`
- **Description**: Get specific category details
- **Authentication**: Required (Admin role)

#### Update Category
- **Method**: `PUT`
- **Endpoint**: `/api/admin/categories/{id}`
- **Description**: Update category information
- **Authentication**: Required (Admin role)

#### Delete Category
- **Method**: `DELETE`
- **Endpoint**: `/api/admin/categories/{id}`
- **Description**: Delete a category
- **Authentication**: Required (Admin role)

#### Toggle Category Status
- **Method**: `PATCH`
- **Endpoint**: `/api/admin/categories/{id}/toggle-status`
- **Description**: Toggle category active/inactive status
- **Authentication**: Required (Admin role)

### Payments Management

#### Get All Payments
- **Method**: `GET`
- **Endpoint**: `/api/admin/payments`
- **Description**: List all payments with pagination
- **Authentication**: Required (Admin role)

#### Get Payment Details
- **Method**: `GET`
- **Endpoint**: `/api/admin/payments/{id}`
- **Description**: Get specific payment details
- **Authentication**: Required (Admin role)

#### Update Payment Status
- **Method**: `PUT`
- **Endpoint**: `/api/admin/payments/{id}/status`
- **Description**: Update payment status
- **Authentication**: Required (Admin role)

#### Get Payment Statistics
- **Method**: `GET`
- **Endpoint**: `/api/admin/payments/stats`
- **Description**: Get payment statistics and analytics
- **Authentication**: Required (Admin role)

### Subscriptions Management

#### Get All Subscriptions
- **Method**: `GET`
- **Endpoint**: `/api/admin/subscriptions`
- **Description**: List all subscriptions with pagination
- **Authentication**: Required (Admin role)

#### Get Subscription Details
- **Method**: `GET`
- **Endpoint**: `/api/admin/subscriptions/{id}`
- **Description**: Get specific subscription details
- **Authentication**: Required (Admin role)

#### Create Subscription
- **Method**: `POST`
- **Endpoint**: `/api/admin/subscriptions`
- **Description**: Create a new subscription
- **Authentication**: Required (Admin role)

#### Update Subscription
- **Method**: `PUT`
- **Endpoint**: `/api/admin/subscriptions/{id}`
- **Description**: Update subscription information
- **Authentication**: Required (Admin role)

#### Delete Subscription
- **Method**: `DELETE`
- **Endpoint**: `/api/admin/subscriptions/{id}`
- **Description**: Delete a subscription
- **Authentication**: Required (Admin role)

#### Toggle Subscription Status
- **Method**: `PATCH`
- **Endpoint**: `/api/admin/subscriptions/{id}/toggle-status`
- **Description**: Toggle subscription active/inactive status
- **Authentication**: Required (Admin role)

#### Get Subscription Statistics
- **Method**: `GET`
- **Endpoint**: `/api/admin/subscriptions/stats`
- **Description**: Get subscription statistics and analytics
- **Authentication**: Required (Admin role)

### Vendors Management

#### Get All Vendors
- **Method**: `GET`
- **Endpoint**: `/api/admin/vendors`
- **Description**: List all vendors with pagination
- **Authentication**: Required (Admin role)

#### Get Vendor Details
- **Method**: `GET`
- **Endpoint**: `/api/admin/vendors/{id}`
- **Description**: Get specific vendor details
- **Authentication**: Required (Admin role)

#### Create Vendor
- **Method**: `POST`
- **Endpoint**: `/api/admin/vendors`
- **Description**: Create a new vendor
- **Authentication**: Required (Admin role)

#### Update Vendor
- **Method**: `PUT`
- **Endpoint**: `/api/admin/vendors/{id}`
- **Description**: Update vendor information
- **Authentication**: Required (Admin role)

#### Delete Vendor
- **Method**: `DELETE`
- **Endpoint**: `/api/admin/vendors/{id}`
- **Description**: Delete a vendor
- **Authentication**: Required (Admin role)

#### Approve Vendor
- **Method**: `PATCH`
- **Endpoint**: `/api/admin/vendors/{id}/approve`
- **Description**: Approve a vendor account
- **Authentication**: Required (Admin role)

#### Reject Vendor
- **Method**: `PATCH`
- **Endpoint**: `/api/admin/vendors/{id}/reject`
- **Description**: Reject a vendor account
- **Authentication**: Required (Admin role)

#### Suspend Vendor
- **Method**: `PATCH`
- **Endpoint**: `/api/admin/vendors/{id}/suspend`
- **Description**: Suspend a vendor account
- **Authentication**: Required (Admin role)

#### Toggle Vendor Verification
- **Method**: `PATCH`
- **Endpoint**: `/api/admin/vendors/{id}/toggle-verification`
- **Description**: Toggle vendor verification status
- **Authentication**: Required (Admin role)

#### Toggle Featured Vendor
- **Method**: `PATCH`
- **Endpoint**: `/api/admin/vendors/{id}/toggle-featured`
- **Description**: Toggle featured vendor status
- **Authentication**: Required (Admin role)

#### Get Vendor Statistics
- **Method**: `GET`
- **Endpoint**: `/api/admin/vendors/stats`
- **Description**: Get vendor statistics and analytics
- **Authentication**: Required (Admin role)

### Locations Management

#### Get Countries
- **Method**: `GET`
- **Endpoint**: `/api/admin/locations/countries`
- **Description**: Get all countries
- **Authentication**: Required (Admin role)

#### Create Country
- **Method**: `POST`
- **Endpoint**: `/api/admin/locations/countries`
- **Description**: Create a new country
- **Authentication**: Required (Admin role)

#### Update Country
- **Method**: `PUT`
- **Endpoint**: `/api/admin/locations/countries/{id}`
- **Description**: Update country information
- **Authentication**: Required (Admin role)

#### Toggle Country Status
- **Method**: `PATCH`
- **Endpoint**: `/api/admin/locations/countries/{id}/toggle-status`
- **Description**: Toggle country active/inactive status
- **Authentication**: Required (Admin role)

#### Get States for Country
- **Method**: `GET`
- **Endpoint**: `/api/admin/locations/states/{countryId}`
- **Description**: Get all states for a specific country
- **Authentication**: Required (Admin role)

#### Create State
- **Method**: `POST`
- **Endpoint**: `/api/admin/locations/states/{countryId}`
- **Description**: Create a new state for a country
- **Authentication**: Required (Admin role)

#### Update State
- **Method**: `PUT`
- **Endpoint**: `/api/admin/locations/states/{id}`
- **Description**: Update state information
- **Authentication**: Required (Admin role)

#### Toggle State Status
- **Method**: `PATCH`
- **Endpoint**: `/api/admin/locations/states/{id}/toggle-status`
- **Description**: Toggle state active/inactive status
- **Authentication**: Required (Admin role)

#### Get Cities for State
- **Method**: `GET`
- **Endpoint**: `/api/admin/locations/cities/{stateId}`
- **Description**: Get all cities for a specific state
- **Authentication**: Required (Admin role)

#### Create City
- **Method**: `POST`
- **Endpoint**: `/api/admin/locations/cities/{stateId}`
- **Description**: Create a new city for a state
- **Authentication**: Required (Admin role)

#### Update City
- **Method**: `PUT`
- **Endpoint**: `/api/admin/locations/cities/{id}`
- **Description**: Update city information
- **Authentication**: Required (Admin role)

#### Toggle City Status
- **Method**: `PATCH`
- **Endpoint**: `/api/admin/locations/cities/{id}/toggle-status`
- **Description**: Toggle city active/inactive status
- **Authentication**: Required (Admin role)

#### Get Location Statistics
- **Method**: `GET`
- **Endpoint**: `/api/admin/locations/stats`
- **Description**: Get location statistics and analytics
- **Authentication**: Required (Admin role)

### Featured Ads Management

#### Get All Featured Ads
- **Method**: `GET`
- **Endpoint**: `/api/admin/featured-ads`
- **Description**: List all featured ads with pagination
- **Authentication**: Required (Admin role)

#### Get Featured Ad Details
- **Method**: `GET`
- **Endpoint**: `/api/admin/featured-ads/{id}`
- **Description**: Get specific featured ad details
- **Authentication**: Required (Admin role)

#### Create Featured Ad
- **Method**: `POST`
- **Endpoint**: `/api/admin/featured-ads`
- **Description**: Create a new featured ad
- **Authentication**: Required (Admin role)

#### Update Featured Ad
- **Method**: `PUT`
- **Endpoint**: `/api/admin/featured-ads/{id}`
- **Description**: Update featured ad information
- **Authentication**: Required (Admin role)

#### Delete Featured Ad
- **Method**: `DELETE`
- **Endpoint**: `/api/admin/featured-ads/{id}`
- **Description**: Delete a featured ad
- **Authentication**: Required (Admin role)

#### Cancel Featured Ad
- **Method**: `PATCH`
- **Endpoint**: `/api/admin/featured-ads/{id}/cancel`
- **Description**: Cancel a featured ad
- **Authentication**: Required (Admin role)

#### Extend Featured Ad Duration
- **Method**: `PATCH`
- **Endpoint**: `/api/admin/featured-ads/{id}/extend`
- **Description**: Extend featured ad duration
- **Authentication**: Required (Admin role)

#### Get Featured Ads Statistics
- **Method**: `GET`
- **Endpoint**: `/api/admin/featured-ads/stats`
- **Description**: Get featured ads statistics and analytics
- **Authentication**: Required (Admin role)

### Blogs Management

#### Get All Blogs
- **Method**: `GET`
- **Endpoint**: `/api/admin/blogs`
- **Description**: List all blogs with pagination
- **Authentication**: Required (Admin role)

#### Get Blog Details
- **Method**: `GET`
- **Endpoint**: `/api/admin/blogs/{id}`
- **Description**: Get specific blog details
- **Authentication**: Required (Admin role)

#### Create Blog
- **Method**: `POST`
- **Endpoint**: `/api/admin/blogs`
- **Description**: Create a new blog post
- **Authentication**: Required (Admin role)

#### Update Blog
- **Method**: `PUT`
- **Endpoint**: `/api/admin/blogs/{id}`
- **Description**: Update blog information
- **Authentication**: Required (Admin role)

#### Delete Blog
- **Method**: `DELETE`
- **Endpoint**: `/api/admin/blogs/{id}`
- **Description**: Delete a blog post
- **Authentication**: Required (Admin role)

#### Publish Blog
- **Method**: `PATCH`
- **Endpoint**: `/api/admin/blogs/{id}/publish`
- **Description**: Publish a blog post
- **Authentication**: Required (Admin role)

#### Unpublish Blog
- **Method**: `PATCH`
- **Endpoint**: `/api/admin/blogs/{id}/unpublish`
- **Description**: Unpublish a blog post
- **Authentication**: Required (Admin role)

#### Toggle Featured Blog
- **Method**: `PATCH`
- **Endpoint**: `/api/admin/blogs/{id}/toggle-featured`
- **Description**: Toggle featured blog status
- **Authentication**: Required (Admin role)

#### Get Blog Statistics
- **Method**: `GET`
- **Endpoint**: `/api/admin/blogs/stats`
- **Description**: Get blog statistics and analytics
- **Authentication**: Required (Admin role)

### Payment Gateways Management

#### Get All Payment Gateways
- **Method**: `GET`
- **Endpoint**: `/api/admin/payments/gateways`
- **Description**: Get all payment gateway configurations
- **Authentication**: Required (Admin role)

#### Update Payment Gateway Settings
- **Method**: `PUT`
- **Endpoint**: `/api/admin/payments/gateways/{gateway}`
- **Description**: Update payment gateway settings
- **Authentication**: Required (Admin role)
- **Gateway Options**: paystack, flutterwave, stripe, paypal

#### Toggle Payment Gateway Status
- **Method**: `PATCH`
- **Endpoint**: `/api/admin/payments/gateways/{gateway}/toggle-status`
- **Description**: Toggle payment gateway active/inactive status
- **Authentication**: Required (Admin role)

#### Get Supported Payment Gateways
- **Method**: `GET`
- **Endpoint**: `/api/admin/payments/gateways/support`
- **Description**: Get list of supported payment gateways
- **Authentication**: Required (Admin role)

### Testimonials Management

#### Get All Testimonials
- **Method**: `GET`
- **Endpoint**: `/api/admin/testimonials`
- **Description**: List all testimonials with pagination
- **Authentication**: Required (Admin role)

#### Get Testimonial Details
- **Method**: `GET`
- **Endpoint**: `/api/admin/testimonials/{id}`
- **Description**: Get specific testimonial details
- **Authentication**: Required (Admin role)

#### Create Testimonial
- **Method**: `POST`
- **Endpoint**: `/api/admin/testimonials`
- **Description**: Create a new testimonial
- **Authentication**: Required (Admin role)

#### Update Testimonial
- **Method**: `PUT`
- **Endpoint**: `/api/admin/testimonials/{id}`
- **Description**: Update testimonial information
- **Authentication**: Required (Admin role)

#### Delete Testimonial
- **Method**: `DELETE`
- **Endpoint**: `/api/admin/testimonials/{id}`
- **Description**: Delete a testimonial
- **Authentication**: Required (Admin role)

#### Toggle Testimonial Status
- **Method**: `PATCH`
- **Endpoint**: `/api/admin/testimonials/{id}/toggle-status`
- **Description**: Toggle testimonial active/inactive status
- **Authentication**: Required (Admin role)

#### Toggle Featured Testimonial
- **Method**: `PATCH`
- **Endpoint**: `/api/admin/testimonials/{id}/toggle-featured`
- **Description**: Toggle featured testimonial status
- **Authentication**: Required (Admin role)

### Careers Management

#### Get All Career Opportunities
- **Method**: `GET`
- **Endpoint**: `/api/admin/careers`
- **Description**: List all career opportunities with pagination
- **Authentication**: Required (Admin role)

#### Get Career Details
- **Method**: `GET`
- **Endpoint**: `/api/admin/careers/{id}`
- **Description**: Get specific career details
- **Authentication**: Required (Admin role)

#### Create Career Opportunity
- **Method**: `POST`
- **Endpoint**: `/api/admin/careers`
- **Description**: Create a new career opportunity
- **Authentication**: Required (Admin role)

#### Update Career
- **Method**: `PUT`
- **Endpoint**: `/api/admin/careers/{id}`
- **Description**: Update career information
- **Authentication**: Required (Admin role)

#### Delete Career
- **Method**: `DELETE`
- **Endpoint**: `/api/admin/careers/{id}`
- **Description**: Delete a career opportunity
- **Authentication**: Required (Admin role)

#### Publish Career
- **Method**: `PATCH`
- **Endpoint**: `/api/admin/careers/{id}/publish`
- **Description**: Publish a career opportunity
- **Authentication**: Required (Admin role)

#### Unpublish Career
- **Method**: `PATCH`
- **Endpoint**: `/api/admin/careers/{id}/unpublish`
- **Description**: Unpublish a career opportunity
- **Authentication**: Required (Admin role)

### Ad Placements Management

#### Get All Ad Placements
- **Method**: `GET`
- **Endpoint**: `/api/admin/ad-placements`
- **Description**: List all ad placements with pagination
- **Authentication**: Required (Admin role)

#### Get Ad Placement Details
- **Method**: `GET`
- **Endpoint**: `/api/admin/ad-placements/{id}`
- **Description**: Get specific ad placement details
- **Authentication**: Required (Admin role)

#### Create Ad Placement
- **Method**: `POST`
- **Endpoint**: `/api/admin/ad-placements`
- **Description**: Create a new ad placement
- **Authentication**: Required (Admin role)

#### Update Ad Placement
- **Method**: `PUT`
- **Endpoint**: `/api/admin/ad-placements/{id}`
- **Description**: Update ad placement information
- **Authentication**: Required (Admin role)

#### Delete Ad Placement
- **Method**: `DELETE`
- **Endpoint**: `/api/admin/ad-placements/{id}`
- **Description**: Delete an ad placement
- **Authentication**: Required (Admin role)

#### Toggle Ad Placement Status
- **Method**: `PATCH`
- **Endpoint**: `/api/admin/ad-placements/{id}/toggle-status`
- **Description**: Toggle ad placement active/inactive status
- **Authentication**: Required (Admin role)

#### Get Ad Placement Statistics
- **Method**: `GET`
- **Endpoint**: `/api/admin/ad-placements/stats`
- **Description**: Get ad placement statistics and analytics
- **Authentication**: Required (Admin role)

### Premium Ads Management

#### Get All Premium Ads
- **Method**: `GET`
- **Endpoint**: `/api/admin/premium-ads`
- **Description**: List all premium ads with pagination
- **Authentication**: Required (Admin role)

#### Get Premium Ad Details
- **Method**: `GET`
- **Endpoint**: `/api/admin/premium-ads/{id}`
- **Description**: Get specific premium ad details
- **Authentication**: Required (Admin role)

#### Create Premium Ad
- **Method**: `POST`
- **Endpoint**: `/api/admin/premium-ads`
- **Description**: Create a new premium ad
- **Authentication**: Required (Admin role)

#### Update Premium Ad
- **Method**: `PUT`
- **Endpoint**: `/api/admin/premium-ads/{id}`
- **Description**: Update premium ad information
- **Authentication**: Required (Admin role)

#### Delete Premium Ad
- **Method**: `DELETE`
- **Endpoint**: `/api/admin/premium-ads/{id}`
- **Description**: Delete a premium ad
- **Authentication**: Required (Admin role)

#### Activate Premium Ad
- **Method**: `PATCH`
- **Endpoint**: `/api/admin/premium-ads/{id}/activate`
- **Description**: Activate a premium ad
- **Authentication**: Required (Admin role)

#### Pause Premium Ad
- **Method**: `PATCH`
- **Endpoint**: `/api/admin/premium-ads/{id}/pause`
- **Description**: Pause a premium ad
- **Authentication**: Required (Admin role)

#### Cancel Premium Ad
- **Method**: `PATCH`
- **Endpoint**: `/api/admin/premium-ads/{id}/cancel`
- **Description**: Cancel a premium ad
- **Authentication**: Required (Admin role)

#### Get Premium Ads Statistics
- **Method**: `GET`
- **Endpoint**: `/api/admin/premium-ads/stats`
- **Description**: Get premium ads statistics and analytics
- **Authentication**: Required (Admin role)

---

## Authentication

### Get Current User
- **Method**: `GET`
- **Endpoint**: `/api/user`
- **Description**: Get authenticated user information
- **Authentication**: Required (Bearer token)

**Example Request**:
```
GET /api/user
Authorization: Bearer your-token-here
```

**Response** (Status: 200):
```json
{
  "id": 2,
  "name": "John Smith",
  "email": "john@example.com",
  "email_verified_at": "2023-10-15T10:30:00.000000Z",
  "created_at": "2023-10-15T10:30:00.000000Z",
  "updated_at": "2023-10-16T14:20:00.000000Z"
}
```

---

## Response Format

All API responses follow the same format:

**Success Response**:
```json
{
  "success": true,
  "data": {
    // Response data
  }
}
```

**Error Response**:
```json
{
  "success": false,
  "message": "Error message",
  "errors": {
    // Validation errors if applicable
  }
}
```

---

## Error Codes

| HTTP Status Code | Description | Meaning |
|------------------|-------------|---------|
| 200 | OK | Request successful |
| 201 | Created | Resource created successfully |
| 400 | Bad Request | Invalid request format |
| 401 | Unauthorized | Authentication required or failed |
| 403 | Forbidden | Insufficient permissions |
| 404 | Not Found | Requested resource not found |
| 405 | Method Not Allowed | HTTP method not allowed for endpoint |
| 422 | Unprocessable Entity | Validation errors in request data |
| 429 | Too Many Requests | Rate limit exceeded |
| 500 | Internal Server Error | Server-side error |
| 503 | Service Unavailable | Service temporarily unavailable |

### Common Error Messages

**Authentication Errors**:
- `"Unauthenticated."` - Missing or invalid authentication token
- `"This action is unauthorized."` - Insufficient permissions for the request

**Validation Errors**:
- `"The given data was invalid."` - Validation errors present in request data
- `"The title field is required."` - Required field missing
- `"The price must be a number."` - Field type validation failed

**Resource Errors**:
- `"Record not found."` - Requested resource does not exist
- `"The ad could not be found."` - Specific resource not found