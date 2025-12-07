# VidiaSpot Marketplace API Documentation

## Overview

The VidiaSpot Marketplace API provides a comprehensive interface for interacting with the marketplace platform. It follows RESTful principles and returns JSON responses for all endpoints.

## Base URL

- **Development**: `http://localhost:8000/api`
- **Production**: `https://yourdomain.com/api`

## Authentication

The API uses Laravel Sanctum for authentication. All protected endpoints require a valid API token in the Authorization header.

### Getting an API Token

1. **Login to get token**:
   ```
   POST /api/login
   Content-Type: application/json

   {
     "email": "user@example.com",
     "password": "password"
   }
   ```

2. **Response**:
   ```json
   {
     "token": "your-api-token",
     "user": {
       "id": 1,
       "name": "John Doe",
       "email": "user@example.com"
     }
   }
   ```

3. **Use the token in subsequent requests**:
   ```
   Authorization: Bearer your-api-token
   ```

## Response Format

All API responses follow a consistent format:

Successful responses:
```json
{
  "success": true,
  "data": {
    // Response data
  }
}
```

Error responses:
```json
{
  "success": false,
  "message": "Error message",
  "errors": {
    // Validation errors if applicable
  }
}
```

## Public Endpoints (No Authentication Required)

### Ads

#### Get All Ads
- **Endpoint**: `GET /api/ads`
- **Description**: Retrieve a paginated list of all active ads
- **Query Parameters**:
  - `category_id`: Filter by category ID
  - `location`: Filter by location name
  - `min_price`: Minimum price filter
  - `max_price`: Maximum price filter
  - `condition`: Filter by condition (new, like_new, good, fair, poor)
  - `search`: Search in title and description
  - `order_by`: Sort field (default: created_at)
  - `order_direction`: Sort direction (asc/desc, default: desc)
  - `per_page`: Results per page (default: 15)

**Example Request**:
```
GET /api/ads?category_id=5&location=New%20York&min_price=100&max_price=500&order_by=price&order_direction=asc
```

**Example Response**:
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
- **Endpoint**: `GET /api/ads/{id}`
- **Description**: Retrieve details for a specific ad
- **Note**: This endpoint increments the view count for the ad

**Example Request**:
```
GET /api/ads/1
```

**Example Response**:
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
    "view_count": 25, // Incremented after this request
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
- **Endpoint**: `GET /api/categories`
- **Description**: Retrieve all available categories

**Example Request**:
```
GET /api/categories
```

**Example Response**:
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
- **Endpoint**: `GET /api/pages`
- **Description**: Retrieve all available content pages

#### Get Content Page by Slug
- **Endpoint**: `GET /api/pages/{slug}`
- **Description**: Retrieve a specific content page

**Example Request**:
```
GET /api/pages/about-us
```

#### Specific Static Pages
- `GET /api/about`: Get about page content
- `GET /api/contact`: Get contact page content
- `GET /api/services`: Get services page content
- `GET /api/privacy`: Get privacy policy
- `GET /api/terms`: Get terms of service

### Recommendations

#### Get Trending Ads
- **Endpoint**: `GET /api/recommendations/trending`
- **Description**: Get trending ads based on view count and recency

#### Get Similar Ads
- **Endpoint**: `GET /api/ads/{id}/similar`
- **Description**: Get ads similar to the specified ad

**Example Request**:
```
GET /api/ads/1/similar
```

### Social Authentication

#### Redirect to Social Provider
- **Endpoint**: `GET /api/auth/{provider}`
- **Description**: Redirect user to social provider for authentication
- **Providers**: google, facebook, twitter

**Example Request**:
```
GET /api/auth/google
```

#### Handle Social Callback
- **Endpoint**: `GET /api/auth/{provider}/callback`
- **Description**: Handle callback from social provider

#### Get Available Social Providers
- **Endpoint**: `GET /api/auth/providers`
- **Description**: Get list of enabled social authentication providers

## Protected Endpoints (Authentication Required)

### Ads Management

#### Create Ad
- **Endpoint**: `POST /api/ads`
- **Description**: Create a new ad listing
- **Authentication**: Required
- **Content-Type**: `multipart/form-data` (for file uploads) or `application/json`

**Required Fields**:
- `title` (string, max 255)
- `description` (string)
- `price` (numeric, min 0)
- `category_id` (exists in categories table)
- `condition` (in: new, like_new, good, fair, poor)
- `location` (string, max 255)

**Optional Fields**:
- `currency_code` (default: NGN)
- `negotiable` (boolean)
- `contact_phone` (string, max 20)
- `images` (array of up to 10 images)

**Example Request** (multipart/form-data):
```
POST /api/ads
Authorization: Bearer YOUR_API_TOKEN
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
Authorization: Bearer YOUR_API_TOKEN
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

**Example Response**:
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
    "images": []
  }
}
```

#### Update Ad
- **Endpoint**: `PUT /api/ads/{id}`
- **Description**: Update an existing ad
- **Authentication**: Required

**Request Body**: Same validation rules as POST but all fields optional

#### Delete Ad
- **Endpoint**: `DELETE /api/ads/{id}`
- **Description**: Delete an ad
- **Authentication**: Required

#### Get My Ads
- **Endpoint**: `GET /api/my-ads`
- **Description**: Get ads created by the authenticated user
- **Authentication**: Required

#### Add Images to Ad
- **Endpoint**: `POST /api/ads/{id}/images`
- **Description**: Add images to an existing ad
- **Authentication**: Required
- **Content-Type**: `multipart/form-data`

**Example Request**:
```
POST /api/ads/1/images
Authorization: Bearer YOUR_API_TOKEN
Content-Type: multipart/form-data

images[0]=@image3.jpg
images[1]=@image4.jpg
```

### Messages

#### Get Messages
- **Endpoint**: `GET /api/messages`
- **Description**: Get user's messages
- **Authentication**: Required

#### Send Message
- **Endpoint**: `POST /api/messages`
- **Description**: Send a new message
- **Authentication**: Required

**Request Body**:
- `receiver_id` (required, exists in users table)
- `message` (required, string max 1000)
- `messageable_type` (optional, string)
- `messageable_id` (optional, integer)

**Example Request**:
```json
POST /api/messages
Authorization: Bearer YOUR_API_TOKEN
Content-Type: application/json

{
  "receiver_id": 3,
  "message": "Hi, I'm interested in your guitar. Is it still available?",
  "messageable_type": "ad",
  "messageable_id": 1
}
```

#### Get Conversations
- **Endpoint**: `GET /api/messages/conversations`
- **Description**: Get list of users the authenticated user has chatted with
- **Authentication**: Required

#### Mark Message as Read
- **Endpoint**: `PUT /api/messages/{id}/mark-as-read`
- **Description**: Mark a specific message as read
- **Authentication**: Required

### Categories (User)

#### Get Category Tree
- **Endpoint**: `GET /api/categories/tree`
- **Description**: Get hierarchical category tree
- **Authentication**: Required

### Recommendations (Personalized)
- **Endpoint**: `GET /api/recommendations`
- **Description**: Get personalized recommendations for the authenticated user
- **Authentication**: Required

## Admin Endpoints (Admin Role Required)

All admin endpoints are prefixed with `/api/admin/`

### Dashboard & Analytics
- **GET /api/admin/dashboard**: Admin dashboard data
- **GET /api/admin/analytics**: Analytics and statistics

### Ads Management
- **GET /api/admin/ads**: List all ads (with pagination)
- **GET /api/admin/ads/{id}**: Get specific ad details
- **POST /api/admin/ads**: Create ad (as admin)
- **PUT /api/admin/ads/{id}**: Update ad (as admin)
- **DELETE /api/admin/ads/{id}**: Delete ad (as admin)
- **PUT /api/admin/ads/{id}/status**: Update ad status
- **GET /api/admin/ads/pending**: Get pending ads for review

### Users Management
- **GET /api/admin/users**: List users
- **GET /api/admin/users/{id}**: Get user details
- **PUT /api/admin/users/{id}**: Update user
- **DELETE /api/admin/users/{id}**: Delete user
- **PATCH /api/admin/users/{id}/role**: Assign user role
- **GET /api/admin/users/stats**: User statistics

### Categories Management
- **GET /api/admin/categories**: List categories
- **POST /api/admin/categories**: Create category
- **GET /api/admin/categories/{id}**: Get category details
- **PUT /api/admin/categories/{id}**: Update category
- **DELETE /api/admin/categories/{id}**: Delete category
- **PATCH /api/admin/categories/{id}/toggle-status**: Toggle category status

### Payments Management
- **GET /api/admin/payments**: List payments
- **GET /api/admin/payments/{id}**: Get payment details
- **PUT /api/admin/payments/{id}/status**: Update payment status
- **GET /api/admin/payments/stats**: Payment statistics

### Subscriptions Management
- **GET /api/admin/subscriptions**: List subscriptions
- **POST /api/admin/subscriptions**: Create subscription
- **GET /api/admin/subscriptions/{id}**: Get subscription details
- **PUT /api/admin/subscriptions/{id}**: Update subscription
- **DELETE /api/admin/subscriptions/{id}**: Delete subscription
- **PATCH /api/admin/subscriptions/{id}/toggle-status**: Toggle subscription status
- **GET /api/admin/subscriptions/stats**: Subscription statistics

### Vendors Management
- **GET /api/admin/vendors**: List vendors
- **POST /api/admin/vendors**: Create vendor
- **GET /api/admin/vendors/{id}**: Get vendor details
- **PUT /api/admin/vendors/{id}**: Update vendor
- **DELETE /api/admin/vendors/{id}**: Delete vendor
- **PATCH /api/admin/vendors/{id}/approve**: Approve vendor
- **PATCH /api/admin/vendors/{id}/reject**: Reject vendor
- **PATCH /api/admin/vendors/{id}/suspend**: Suspend vendor
- **PATCH /api/admin/vendors/{id}/toggle-verification**: Toggle vendor verification
- **PATCH /api/admin/vendors/{id}/toggle-featured**: Toggle featured status
- **GET /api/admin/vendors/stats**: Vendor statistics

### Locations Management
- **GET /api/admin/locations/countries**: Get countries
- **POST /api/admin/locations/countries**: Create country
- **PUT /api/admin/locations/countries/{id}**: Update country
- **PATCH /api/admin/locations/countries/{id}/toggle-status**: Toggle country status
- **GET /api/admin/locations/states/{countryId}**: Get states for country
- **POST /api/admin/locations/states/{countryId}**: Create state
- **PUT /api/admin/locations/states/{id}**: Update state
- **PATCH /api/admin/locations/states/{id}/toggle-status**: Toggle state status
- **GET /api/admin/locations/cities/{stateId}**: Get cities for state
- **POST /api/admin/locations/cities/{stateId}**: Create city
- **PUT /api/admin/locations/cities/{id}**: Update city
- **PATCH /api/admin/locations/cities/{id}/toggle-status**: Toggle city status
- **GET /api/admin/locations/stats**: Location statistics

### Featured Ads Management
- **GET /api/admin/featured-ads**: List featured ads
- **POST /api/admin/featured-ads**: Create featured ad
- **GET /api/admin/featured-ads/{id}**: Get featured ad details
- **PUT /api/admin/featured-ads/{id}**: Update featured ad
- **DELETE /api/admin/featured-ads/{id}**: Delete featured ad
- **PATCH /api/admin/featured-ads/{id}/cancel**: Cancel featured ad
- **PATCH /api/admin/featured-ads/{id}/extend**: Extend featured ad duration
- **GET /api/admin/featured-ads/stats**: Featured ads statistics

### Blogs Management
- **GET /api/admin/blogs**: List blogs
- **POST /api/admin/blogs**: Create blog
- **GET /api/admin/blogs/{id}**: Get blog details
- **PUT /api/admin/blogs/{id}**: Update blog
- **DELETE /api/admin/blogs/{id}**: Delete blog
- **PATCH /api/admin/blogs/{id}/publish**: Publish blog
- **PATCH /api/admin/blogs/{id}/unpublish**: Unpublish blog
- **PATCH /api/admin/blogs/{id}/toggle-featured**: Toggle featured status
- **GET /api/admin/blogs/stats**: Blog statistics

### Payment Gateways Management
- **GET /api/admin/payments/gateways**: List payment gateways
- **PUT /api/admin/payments/gateways/{gateway}**: Update payment gateway settings
- **PATCH /api/admin/payments/gateways/{gateway}/toggle-status**: Toggle payment gateway status
- **GET /api/admin/payments/gateways/support**: Get supported payment gateways

### Testimonials Management
- **GET /api/admin/testimonials**: List testimonials
- **POST /api/admin/testimonials**: Create testimonial
- **GET /api/admin/testimonials/{id}**: Get testimonial details
- **PUT /api/admin/testimonials/{id}**: Update testimonial
- **DELETE /api/admin/testimonials/{id}**: Delete testimonial
- **PATCH /api/admin/testimonials/{id}/toggle-status**: Toggle testimonial status
- **PATCH /api/admin/testimonials/{id}/toggle-featured**: Toggle featured status

### Careers Management
- **GET /api/admin/careers**: List career opportunities
- **POST /api/admin/careers**: Create career opportunity
- **GET /api/admin/careers/{id}**: Get career details
- **PUT /api/admin/careers/{id}**: Update career
- **DELETE /api/admin/careers/{id}**: Delete career
- **PATCH /api/admin/careers/{id}/publish**: Publish career
- **PATCH /api/admin/careers/{id}/unpublish**: Unpublish career

### Ad Placements Management
- **GET /api/admin/ad-placements**: List ad placements
- **POST /api/admin/ad-placements**: Create ad placement
- **GET /api/admin/ad-placements/{id}**: Get ad placement details
- **PUT /api/admin/ad-placements/{id}**: Update ad placement
- **DELETE /api/admin/ad-placements/{id}**: Delete ad placement
- **PATCH /api/admin/ad-placements/{id}/toggle-status**: Toggle ad placement status
- **GET /api/admin/ad-placements/stats**: Ad placement statistics

### Premium Ads Management
- **GET /api/admin/premium-ads**: List premium ads
- **POST /api/admin/premium-ads**: Create premium ad
- **GET /api/admin/premium-ads/{id}**: Get premium ad details
- **PUT /api/admin/premium-ads/{id}**: Update premium ad
- **DELETE /api/admin/premium-ads/{id}**: Delete premium ad
- **PATCH /api/admin/premium-ads/{id}/activate**: Activate premium ad
- **PATCH /api/admin/premium-ads/{id}/pause**: Pause premium ad
- **PATCH /api/admin/premium-ads/{id}/cancel**: Cancel premium ad
- **GET /api/admin/premium-ads/stats**: Premium ads statistics

## Common HTTP Status Codes

- `200`: Success - Request completed successfully
- `201`: Created - Resource created successfully
- `400`: Bad Request - Validation errors or malformed request
- `401`: Unauthorized - Authentication required or invalid token
- `403`: Forbidden - Insufficient permissions for the request
- `404`: Not Found - Requested resource does not exist
- `422`: Unprocessable Entity - Validation errors in request data
- `500`: Internal Server Error - Unexpected server error

## Rate Limiting

The API implements rate limiting to prevent abuse:
- Public endpoints: 60 requests per minute per IP
- Authenticated endpoints: 100 requests per minute per user
- Admin endpoints: 50 requests per minute per admin user

If you exceed the rate limit, you'll receive a `429 Too Many Requests` response.

## Error Handling

Error responses follow the format:
```json
{
  "success": false,
  "message": "Human-readable error message",
  "errors": {
    "field_name": [
      "Error message for the field"
    ]
  }
}
```

Some common error messages:
- `"Unauthenticated."` - Missing or invalid authentication token
- `"This action is unauthorized."` - Insufficient permissions
- `"The given data was invalid."` - Validation errors present
- `"Record not found."` - Requested resource does not exist

## SDK Examples

### JavaScript/Node.js

```javascript
class VidiaSpotAPI {
  constructor(baseURL, token = null) {
    this.baseURL = baseURL;
    this.token = token;
  }

  setToken(token) {
    this.token = token;
  }

  async request(endpoint, method = 'GET', data = null) {
    const config = {
      method,
      headers: {
        'Content-Type': 'application/json',
      }
    };

    if (this.token) {
      config.headers['Authorization'] = `Bearer ${this.token}`;
    }

    if (data) {
      config.body = JSON.stringify(data);
    }

    const response = await fetch(`${this.baseURL}${endpoint}`, config);
    return response.json();
  }

  async login(email, password) {
    const response = await this.request('/login', 'POST', { email, password });
    if (response.success) {
      this.setToken(response.data.token);
    }
    return response;
  }

  async getAds(params = {}) {
    const query = new URLSearchParams(params).toString();
    const endpoint = query ? `/ads?${query}` : '/ads';
    return this.request(endpoint);
  }

  async createAd(adData) {
    return this.request('/ads', 'POST', adData);
  }

  async getMyAds() {
    return this.request('/my-ads');
  }
}

// Usage example
const api = new VidiaSpotAPI('http://localhost:8000/api');

// Login
const loginResult = await api.login('user@example.com', 'password');

// Get ads
const ads = await api.getAds({ 
  category_id: 5, 
  location: 'New York', 
  min_price: 100 
});

// Create ad
const newAd = await api.createAd({
  title: 'Vintage Guitar',
  description: 'Well-maintained vintage guitar for sale',
  price: 250.00,
  category_id: 5,
  condition: 'good',
  location: 'New York, NY'
});
```

### Python

```python
import requests
from urllib.parse import urlencode

class VidiaSpotAPI:
    def __init__(self, base_url, token=None):
        self.base_url = base_url
        self.token = token
        self.session = requests.Session()

    def set_token(self, token):
        self.token = token
        self.session.headers.update({'Authorization': f'Bearer {token}'})

    def request(self, endpoint, method='GET', data=None):
        url = f"{self.base_url}{endpoint}"
        headers = {'Content-Type': 'application/json'}
        
        if self.token:
            headers['Authorization'] = f'Bearer {self.token}'
        
        if method.upper() in ['POST', 'PUT', 'PATCH']:
            response = self.session.request(method, url, json=data, headers=headers)
        else:
            response = self.session.request(method, url, headers=headers)
        
        return response.json()

    def login(self, email, password):
        response = self.request('/login', 'POST', {'email': email, 'password': password})
        if response.get('success'):
            self.set_token(response['data']['token'])
        return response

    def get_ads(self, **params):
        query_string = urlencode(params) if params else ''
        endpoint = f'/ads?{query_string}' if query_string else '/ads'
        return self.request(endpoint)

    def create_ad(self, ad_data):
        return self.request('/ads', 'POST', ad_data)

# Usage example
api = VidiaSpotAPI('http://localhost:8000/api')

# Login
login_result = api.login('user@example.com', 'password')

# Get ads
ads = api.get_ads(category_id=5, location='New York', min_price=100)

# Create ad
new_ad = api.create_ad({
    'title': 'Vintage Guitar',
    'description': 'Well-maintained vintage guitar for sale',
    'price': 250.00,
    'category_id': 5,
    'condition': 'good',
    'location': 'New York, NY'
})
```

## Best Practices for API Consumption

1. **Handle Authentication Properly**: Store tokens securely and refresh them when needed
2. **Use Pagination**: Most list endpoints support pagination, utilize it for better performance
3. **Implement Error Handling**: Always check response success status and handle errors appropriately
4. **Respect Rate Limits**: Implement rate limiting on your client side to avoid being blocked
5. **Cache Responsibly**: Cache public data but ensure to handle invalidation properly
6. **Validate Data**: Always validate data before sending to the API to prevent validation errors
7. **Handle Timeouts**: Implement appropriate timeouts for API requests
8. **Secure Sensitive Data**: Don't expose tokens or sensitive data in client-side code

## Troubleshooting

### Common Issues

1. **401 Unauthorized Error**
   - Ensure you're sending the Authorization header with a valid token
   - Check that your token hasn't expired

2. **403 Forbidden Error**
   - Verify that your user has the required permissions for the endpoint
   - Check if you're trying to access admin endpoints without admin privileges

3. **422 Validation Errors**
   - Ensure all required fields are present and correctly formatted
   - Check field validation rules in the endpoint documentation

4. **Rate Limiting**
   - Check response headers for rate limit information
   - Implement exponential backoff for automated requests

### Debugging Tips

1. **Check Request Headers**: Ensure proper Content-Type and Authorization headers
2. **Validate JSON**: Use JSON validators to ensure your request body is properly formatted
3. **Check Server Logs**: Review Laravel logs at `storage/logs/laravel.log` for server-side errors
4. **Use API Testing Tools**: Tools like Postman or Insomnia can help debug API requests

## Support

If you encounter issues with the API:
1. Check the error messages in the response
2. Review this documentation
3. Check server logs if you have access
4. Contact support if the issue persists