# VidiaSpot Marketplace - Documentation

## Table of Contents
1. [Features Overview](#features-overview)
2. [Setup Guide](#setup-guide)
3. [Developer Guide](#developer-guide)
4. [API Developer Guide](#api-developer-guide)

---

## Features Overview

VidiaSpot Marketplace is a comprehensive full-stack marketplace application built with Laravel, React, and Flutter. It provides a complete solution for buying and selling goods locally with advanced features including AI integration, payment processing, and cross-platform support.

### Core Features

#### 1. Ad/Listing Management
- **Create Listings**: Users can create detailed ad listings with multiple images, pricing, location, and description
- **Manage Listings**: Edit, update, and delete personal listings
- **Search & Filter**: Advanced search functionality with filters for category, location, price, and condition
- **Image Management**: Upload up to 10 images per listing with primary image selection
- **Listing Status**: Set listings as active, inactive, sold, or pending

#### 2. Categories & Organization
- **Hierarchical Categories**: Organized product categories for better listing organization
- **Category Management**: Admin can manage, enable/disable categories
- **Category Tree View**: Display of nested category structure

#### 3. User Management
- **User Registration & Authentication**: Secure user registration and authentication system
- **Social Login**: Integration with Google, Facebook, and Twitter for easy sign-up
- **User Profiles**: Complete user profile management
- **Role Management**: Different access levels (admin, vendor, regular user)

#### 4. Location Management
- **Multi-level Location System**: Countries, states, and cities management
- **Location-based Listings**: Filter ads by specific locations
- **Admin Location Management**: Dynamic management of location data

#### 5. Messaging & Communication
- **User-to-User Messaging**: Direct communication between buyers and sellers
- **Real-time Chat**: Real-time messaging system for immediate communication
- **Chat History**: Maintain conversation history between users
- **Unread Message Count**: Track unread messages

#### 6. Payment Processing
- **Multi-Gateway Support**: Integration with Paystack, Flutterwave, Stripe, and PayPal
- **Payment Management**: Admin can manage payment settings and view transaction history
- **Subscription Management**: Premium subscriptions for enhanced features
- **Transaction Reporting**: Detailed transaction reports and analytics

#### 7. AI-Powered Features
- **AI Recommendations**: Intelligent recommendation system for relevant listings
- **Chatbot Integration**: Automated chatbot for customer support
- **Image Recognition**: AI-powered image recognition capabilities
- **Pricing Insights**: AI-driven pricing suggestions for sellers
- **Fraud Detection**: Automated fraud detection and moderation
- **Voice Search**: Voice-enabled search functionality
- **Translation Services**: Real-time content translation using AI

#### 8. Content Management
- **Blogs & Articles**: Content management for blogs and informational articles
- **Static Pages**: Management of about, contact, services, privacy, and terms pages
- **FAQ Management**: FAQ section with admin management
- **Testimonials**: Testimonials management with featured status

#### 9. Premium Features
- **Featured Ads**: Boost listings with featured ad placement
- **Premium Ads**: Enhanced ad visibility options
- **Ad Placements**: Various ad placement options for vendors
- **Subscription Plans**: Tiered subscription models

#### 10. Vendor Management
- **Vendor Verification**: Vendor verification system
- **Vendor Profiles**: Enhanced vendor profiles and management
- **Vendor Approval**: Admin approval workflow for vendors
- **Featured Vendors**: Featured vendor status

#### 11. Admin Dashboard
- **Comprehensive Admin Panel**: Full-featured admin panel with multiple management sections
- **Analytics & Reporting**: Detailed analytics and reporting system
- **Content Moderation**: Content and user moderation tools
- **User Management**: Admin tools for user account management
- **System Configuration**: Site-wide configuration management

#### 12. Multi-Currency Support
- **Multiple Currencies**: Support for multiple currencies with automatic conversion
- **Exchange Rates**: Real-time exchange rate integration
- **Currency Formatting**: Proper currency formatting and display

#### 13. Mobile Application
- **Cross-Platform Mobile App**: Flutter-based mobile application supporting iOS and Android
- **Native Mobile Experience**: Full mobile-optimized experience
- **Native Features**: Access to device camera, storage, and other native features

#### 14. Search & Discovery
- **Advanced Search**: Full-text search across listings
- **Recommendation Engine**: Personalized recommendations based on user behavior
- **Similar Ads**: "Similar ads" suggestions
- **Trending Ads**: Trending listings section

#### 15. Notifications
- **Push Notifications**: Real-time push notifications
- **In-App Notifications**: System for in-app notifications
- **Activity Tracking**: User activity and notification management

#### 16. SEO & Marketing
- **Content Pages**: SEO-optimized static content pages
- **Social Sharing**: Social media integration and sharing
- **Marketing Tools**: Admin tools for marketing and promotions

---

## Setup Guide

### Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js and npm (or yarn)
- MySQL or MariaDB
- Redis (for caching and queues)
- MeiliSearch (for search functionality)
- Git

### Environment Setup

1. **Clone the repository**:
   ```bash
   git clone <repository-url>
   cd vidiaspot-marketplace
   ```

2. **Install PHP dependencies**:
   ```bash
   composer install
   ```

3. **Install frontend dependencies**:
   ```bash
   cd frontend
   npm install
   cd ..
   ```

4. **Install mobile app dependencies**:
   ```bash
   cd mobile
   flutter pub get
   cd ..
   ```

5. **Environment configuration**:
   - Copy `.env.example` to `.env`:
     ```bash
     cp .env.example .env
     ```
   - Generate application key:
     ```bash
     php artisan key:generate
     ```

6. **Configure environment variables**:
   Edit the `.env` file to set up:
   - Database connection settings
   - Payment gateway credentials
   - Social login credentials
   - AI service API keys
   - Storage settings

### Database Setup

1. **Create database**:
   ```bash
   # Create database in MySQL/MariaDB
   CREATE DATABASE vidiaspot_marketplace;
   ```

2. **Run migrations**:
   ```bash
   php artisan migrate --seed
   ```

### Service Configuration

1. **Set up Redis**:
   - Ensure Redis server is running
   - Configure in `.env`:
     ```
     REDIS_HOST=127.0.0.1
     REDIS_PASSWORD=null
     REDIS_PORT=6379
     ```

2. **Set up MeiliSearch**:
   - Ensure MeiliSearch server is running on port 7700
   - Configure in `.env`:
     ```
     MEILISEARCH_HOST=http://127.0.0.1:7700
     ```

3. **Set up Queue System**:
   - Configure queue driver in `.env` (database, redis, or other)
   - Run queue worker:
     ```bash
     php artisan queue:work
     ```

### Frontend Setup

1. **Build frontend assets**:
   ```bash
   cd frontend
   npm run build
   ```

2. **Serve frontend during development**:
   ```bash
   npm run dev
   ```

### Mobile App Setup

1. **Run mobile app**:
   ```bash
   cd mobile
   flutter run
   ```

### Running the Application

1. **Start the Laravel development server**:
   ```bash
   php artisan serve
   ```

2. **Alternatively, use the development script**:
   ```bash
   composer run dev
   ```

3. **Access the application**:
   - API: http://localhost:8000
   - Frontend: http://localhost:3000 (during development)

### Post-Installation Steps

1. **Set up scheduled tasks**:
   - Configure cron job for Laravel's scheduler:
     ```
     * * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
     ```

2. **Configure storage links**:
   ```bash
   php artisan storage:link
   ```

---

## Developer Guide

### Project Structure

The VidiaSpot Marketplace project follows the standard Laravel directory structure with additional frontend and mobile components:

```
├── app/                    # Application logic
│   ├── Http/              # HTTP controllers, middleware, requests
│   │   ├── Controllers/
│   │   ├── Middleware/
│   │   └── Requests/
│   ├── Models/            # Eloquent models
│   ├── Services/          # Business logic services
│   └── Providers/         # Service providers
├── config/                # Configuration files
├── database/              # Migrations, seeds, factories
├── frontend/              # React frontend application
├── mobile/                # Flutter mobile application
├── routes/                # Route definitions
├── resources/             # Views, assets, language files
├── storage/               # Storage directory
├── tests/                 # Test files
└── vendor/                # Composer dependencies
```

### Coding Standards

1. **PHP Code**:
   - Follow PSR-12 coding standards
   - Use Laravel's built-in coding conventions
   - Maintain consistent naming conventions
   - Write descriptive docblocks for all public methods

2. **JavaScript/React Code**:
   - Follow Airbnb JavaScript style guide
   - Use functional components with hooks when possible
   - Maintain consistent component structure
   - Use TypeScript for type safety (where applicable)

3. **Database**:
   - Use proper migration files for schema changes
   - Follow Laravel's naming conventions for tables and columns
   - Utilize Eloquent relationships properly
   - Include proper indexes for performance

### API Design

1. **RESTful Endpoints**:
   - Follow RESTful conventions for endpoint design
   - Use consistent URL structure
   - Implement proper HTTP status codes
   - Return consistent JSON response format

2. **Authentication**:
   - Use Laravel Sanctum for API authentication
   - Include authentication middleware for protected routes
   - Implement CSRF protection where necessary

3. **Validation**:
   - Use Form Request classes for validation
   - Validate all user inputs
   - Return appropriate error responses

### Testing

1. **Unit Tests**:
   - Write unit tests for all business logic
   - Use PHPUnit for PHP testing
   - Maintain high test coverage for critical functionality

2. **Feature Tests**:
   - Test API endpoints
   - Verify authentication and authorization
   - Test all major user workflows

3. **Running Tests**:
   ```bash
   composer test
   # or
   php artisan test
   ```

### Security Practices

1. **Input Validation**:
   - Always validate and sanitize user input
   - Use Laravel's validation features
   - Implement proper file upload validation

2. **SQL Injection Prevention**:
   - Use Eloquent ORM or query builder
   - Avoid raw SQL queries when possible
   - Use parameterized queries when necessary

3. **XSS Prevention**:
   - Use Blade's automatic escaping
   - Sanitize output when displaying user-generated content

### Performance Optimization

1. **Caching**:
   - Use Redis for caching frequently accessed data
   - Implement model caching where appropriate
   - Use HTTP caching headers

2. **Database Optimization**:
   - Add proper database indexes
   - Use eager loading to prevent N+1 queries
   - Optimize complex queries

3. **Queue System**:
   - Use queues for time-intensive tasks
   - Implement proper queue monitoring
   - Handle failed jobs appropriately

### Deployment

1. **Environment Configuration**:
   - Use environment-specific configuration
   - Never commit sensitive credentials to version control
   - Use environment variables for sensitive data

2. **Asset Optimization**:
   - Minify and combine CSS/JS files in production
   - Use Laravel's asset versioning
   - Optimize images

3. **Database Migrations**:
   - Always backup database before migrations
   - Test migrations in staging environment first
   - Use proper deployment procedures

---

## API Developer Guide

### Authentication

The API uses Laravel Sanctum for authentication. All protected endpoints require a valid API token in the Authorization header.

#### Getting an API Token

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

### API Endpoints

#### Public Endpoints (No Authentication Required)

##### Ads
- **GET /api/ads**: Get all ads with filtering options
  - Query parameters:
    - `category_id`: Filter by category
    - `location`: Filter by location
    - `min_price`/`max_price`: Filter by price range
    - `condition`: Filter by condition (new, like_new, good, fair, poor)
    - `search`: Search in title and description
    - `order_by`: Sort field (default: created_at)
    - `order_direction`: Sort direction (asc/desc, default: desc)
    - `per_page`: Results per page (default: 15)

- **GET /api/ads/{id}**: Get a specific ad
  - Includes ad details, user information, and images
  - Increments view count when accessed

##### Categories
- **GET /api/categories**: Get all available categories
- **GET /api/categories/{id}**: Get a specific category

##### Content Pages
- **GET /api/pages**: Get all content pages
- **GET /api/pages/{slug}**: Get a specific content page
- **GET /api/about**: Get about page
- **GET /api/contact**: Get contact page
- **GET /api/services**: Get services page
- **GET /api/privacy**: Get privacy policy
- **GET /api/terms**: Get terms of service

##### Recommendations
- **GET /api/recommendations/trending**: Get trending ads
- **GET /api/ads/{id}/similar**: Get similar ads to the specified ad

##### Social Authentication
- **GET /api/auth/{provider}**: Redirect to social provider for authentication
- **GET /api/auth/{provider}/callback**: Handle social provider callback
- **GET /api/auth/providers**: Get list of available social providers

#### Protected Endpoints (Authentication Required)

##### User Ads
- **POST /api/ads**: Create a new ad
  - Required fields:
    - `title` (string, max 255)
    - `description` (string)
    - `price` (numeric, min 0)
    - `category_id` (exists in categories table)
    - `condition` (in: new, like_new, good, fair, poor)
    - `location` (string, max 255)
  - Optional fields:
    - `currency_code` (default: NGN)
    - `negotiable` (boolean)
    - `contact_phone` (string, max 20)
    - `images` (array of up to 10 images)

- **PUT /api/ads/{id}**: Update an existing ad
  - Same validation rules as POST but all fields optional

- **DELETE /api/ads/{id}**: Delete an ad

- **GET /api/my-ads**: Get ads created by the authenticated user

- **POST /api/ads/{id}/images**: Add images to an existing ad

##### Messages
- **GET /api/messages**: Get messages
- **POST /api/messages**: Send a new message
- **GET /api/messages/conversations**: Get conversation list
- **PUT /api/messages/{id}/mark-as-read**: Mark message as read

##### Categories (User)
- **GET /api/categories/tree**: Get hierarchical category tree

##### Recommendations (Personalized)
- **GET /api/recommendations**: Get personalized recommendations for the authenticated user

#### Admin Endpoints (Admin Role Required)

All admin endpoints are prefixed with `/api/admin/`

##### Dashboard & Analytics
- **GET /api/admin/dashboard**: Admin dashboard data
- **GET /api/admin/analytics**: Analytics and statistics

##### Ads Management
- **GET /api/admin/ads**: List all ads (with pagination)
- **GET /api/admin/ads/{id}**: Get specific ad details
- **POST /api/admin/ads**: Create ad (as admin)
- **PUT /api/admin/ads/{id}**: Update ad (as admin)
- **DELETE /api/admin/ads/{id}**: Delete ad (as admin)
- **PUT /api/admin/ads/{id}/status**: Update ad status
- **GET /api/admin/ads/pending**: Get pending ads for review

##### Users Management
- **GET /api/admin/users**: List users
- **GET /api/admin/users/{id}**: Get user details
- **PUT /api/admin/users/{id}**: Update user
- **DELETE /api/admin/users/{id}**: Delete user
- **PATCH /api/admin/users/{id}/role**: Assign user role
- **GET /api/admin/users/stats**: User statistics

##### Categories Management
- **GET /api/admin/categories**: List categories
- **POST /api/admin/categories**: Create category
- **GET /api/admin/categories/{id}**: Get category details
- **PUT /api/admin/categories/{id}**: Update category
- **DELETE /api/admin/categories/{id}**: Delete category
- **PATCH /api/admin/categories/{id}/toggle-status**: Toggle category status

##### Payments Management
- **GET /api/admin/payments**: List payments
- **GET /api/admin/payments/{id}**: Get payment details
- **PUT /api/admin/payments/{id}/status**: Update payment status
- **GET /api/admin/payments/stats**: Payment statistics

##### Subscriptions Management
- **GET /api/admin/subscriptions**: List subscriptions
- **POST /api/admin/subscriptions**: Create subscription
- **GET /api/admin/subscriptions/{id}**: Get subscription details
- **PUT /api/admin/subscriptions/{id}**: Update subscription
- **DELETE /api/admin/subscriptions/{id}**: Delete subscription
- **PATCH /api/admin/subscriptions/{id}/toggle-status**: Toggle subscription status
- **GET /api/admin/subscriptions/stats**: Subscription statistics

##### Vendors Management
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

##### Locations Management
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

##### Featured Ads Management
- **GET /api/admin/featured-ads**: List featured ads
- **POST /api/admin/featured-ads**: Create featured ad
- **GET /api/admin/featured-ads/{id}**: Get featured ad details
- **PUT /api/admin/featured-ads/{id}**: Update featured ad
- **DELETE /api/admin/featured-ads/{id}**: Delete featured ad
- **PATCH /api/admin/featured-ads/{id}/cancel**: Cancel featured ad
- **PATCH /api/admin/featured-ads/{id}/extend**: Extend featured ad duration
- **GET /api/admin/featured-ads/stats**: Featured ads statistics

##### Blogs Management
- **GET /api/admin/blogs**: List blogs
- **POST /api/admin/blogs**: Create blog
- **GET /api/admin/blogs/{id}**: Get blog details
- **PUT /api/admin/blogs/{id}**: Update blog
- **DELETE /api/admin/blogs/{id}**: Delete blog
- **PATCH /api/admin/blogs/{id}/publish**: Publish blog
- **PATCH /api/admin/blogs/{id}/unpublish**: Unpublish blog
- **PATCH /api/admin/blogs/{id}/toggle-featured**: Toggle featured status
- **GET /api/admin/blogs/stats**: Blog statistics

##### Payment Gateways Management
- **GET /api/admin/payments/gateways**: List payment gateways
- **PUT /api/admin/payments/gateways/{gateway}**: Update payment gateway settings
- **PATCH /api/admin/payments/gateways/{gateway}/toggle-status**: Toggle payment gateway status
- **GET /api/admin/payments/gateways/support**: Get supported payment gateways

##### Testimonials Management
- **GET /api/admin/testimonials**: List testimonials
- **POST /api/admin/testimonials**: Create testimonial
- **GET /api/admin/testimonials/{id}**: Get testimonial details
- **PUT /api/admin/testimonials/{id}**: Update testimonial
- **DELETE /api/admin/testimonials/{id}**: Delete testimonial
- **PATCH /api/admin/testimonials/{id}/toggle-status**: Toggle testimonial status
- **PATCH /api/admin/testimonials/{id}/toggle-featured**: Toggle featured status

##### Careers Management
- **GET /api/admin/careers**: List career opportunities
- **POST /api/admin/careers**: Create career opportunity
- **GET /api/admin/careers/{id}**: Get career details
- **PUT /api/admin/careers/{id}**: Update career
- **DELETE /api/admin/careers/{id}**: Delete career
- **PATCH /api/admin/careers/{id}/publish**: Publish career
- **PATCH /api/admin/careers/{id}/unpublish**: Unpublish career

##### Ad Placements Management
- **GET /api/admin/ad-placements**: List ad placements
- **POST /api/admin/ad-placements**: Create ad placement
- **GET /api/admin/ad-placements/{id}**: Get ad placement details
- **PUT /api/admin/ad-placements/{id}**: Update ad placement
- **DELETE /api/admin/ad-placements/{id}**: Delete ad placement
- **PATCH /api/admin/ad-placements/{id}/toggle-status**: Toggle ad placement status
- **GET /api/admin/ad-placements/stats**: Ad placement statistics

##### Premium Ads Management
- **GET /api/admin/premium-ads**: List premium ads
- **POST /api/admin/premium-ads**: Create premium ad
- **GET /api/admin/premium-ads/{id}**: Get premium ad details
- **PUT /api/admin/premium-ads/{id}**: Update premium ad
- **DELETE /api/admin/premium-ads/{id}**: Delete premium ad
- **PATCH /api/admin/premium-ads/{id}/activate**: Activate premium ad
- **PATCH /api/admin/premium-ads/{id}/pause**: Pause premium ad
- **PATCH /api/admin/premium-ads/{id}/cancel**: Cancel premium ad
- **GET /api/admin/premium-ads/stats**: Premium ads statistics

### Response Format

All API responses follow a consistent format:

```json
{
  "success": true,
  "data": {
    // Response data
  }
}
```

For errors:
```json
{
  "success": false,
  "message": "Error message",
  "errors": {
    // Validation errors if applicable
  }
}
```

### Error Handling

Common HTTP status codes:
- `200`: Success
- `201`: Created
- `400`: Bad Request (validation errors)
- `401`: Unauthorized
- `403`: Forbidden
- `404`: Not Found
- `422`: Unprocessable Entity (validation errors)
- `500`: Internal Server Error

### Rate Limiting

The API implements rate limiting to prevent abuse:
- Public endpoints: 60 requests per minute per IP
- Authenticated endpoints: 100 requests per minute per user
- Admin endpoints: 50 requests per minute per admin user

### Best Practices for API Consumption

1. **Handle errors gracefully**: Always check the `success` field in responses
2. **Use pagination**: Most list endpoints support pagination with `per_page` and page parameters
3. **Cache responses**: Cache non-sensitive data to reduce API calls
4. **Implement retry logic**: For failed requests, implement exponential backoff
5. **Secure tokens**: Store API tokens securely and refresh them as needed
6. **Validate responses**: Validate API responses even if they seem correct

### Example API Usage

#### Creating an Ad

```javascript
// JavaScript example
const response = await fetch('/api/ads', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Authorization': 'Bearer YOUR_API_TOKEN'
  },
  body: JSON.stringify({
    title: 'Vintage Guitar',
    description: 'Well-maintained vintage guitar for sale',
    price: 250.00,
    category_id: 5,
    condition: 'good',
    location: 'New York, NY',
    negotiable: true,
    contact_phone: '+1234567890'
  })
});

const result = await response.json();
console.log(result);
```

#### Searching Ads

```javascript
// Search for guitars with price between $100 and $500 in New York
const response = await fetch('/api/ads?search=guitar&min_price=100&max_price=500&location=New%20York', {
  headers: {
    'Authorization': 'Bearer YOUR_API_TOKEN'
  }
});

const result = await response.json();
console.log(result.data);
```