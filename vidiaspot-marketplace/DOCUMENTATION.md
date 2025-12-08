# Vidiaspot Marketplace - Comprehensive Documentation

## Table of Contents
1. [Overview](#overview)
2. [Features](#features)
3. [Technology Stack](#technology-stack)
4. [Database Configuration](#database-configuration)
5. [Ad Placement System](#ad-placement-system)
6. [Payment Gateway Integration](#payment-gateway-integration)
7. [Setup Guide](#setup-guide)
8. [API Documentation](#api-documentation)
9. [Developer Guide](#developer-guide)
10. [Admin Features](#admin-features)
11. [Cross-Check: Requested vs Implemented Features](#cross-check)

## Overview

Vidiaspot Marketplace is a comprehensive classified ads platform designed for the Nigerian market with support for multiple languages, currencies, and payment gateways. The platform features advanced AI capabilities, multi-platform access, and sophisticated search functionality.

## Features

### Core Features
- **Multi-language Support**: Real-time translation between major languages including English, French, Portuguese, Arabic, Spanish, German, Chinese, and Nigerian languages (Yoruba, Igbo, Hausa)
- **Multi-currency Integration**: Support for all major currencies (USD, EUR, GBP, NGN, ZAR, etc.) with real-time exchange rates
- **AI-Powered Features**: Smart product recommendations, fraud detection, image recognition, price suggestions, chatbot support
- **User Features**: Multi-platform access (Web, iOS, Android), social media login, push notifications, location-based listings, user verification, ratings and reviews
- **Advanced Search**: Visual search, voice search, augmented reality preview, geographic heat maps
- **Payment Processing**: Integrated Paystack and Flutterwave payment gateways
- **Ad Placement System**: Different ad types can be uploaded by admins into different sections (top, side, bottom, between content)

### Advanced Features
- **Elasticsearch Integration**: Advanced search functionality with faceted search
- **Recommendation Engine**: Collaborative filtering for personalized recommendations
- **User Verification System**: Multi-tier verification with trust indicators
- **Offline Capabilities**: PWA with offline functionality
- **Real-time Communication**: In-app messaging with translation

## Technology Stack

### Backend
- **Framework**: Laravel 12+
- **Language**: PHP 8.2+
- **Database**: MySQL 8.0 primary / SQLite for local development
- **Caching**: Redis for caching and session management
- **Search**: Elasticsearch for advanced search functionality
- **Message Queue**: RabbitMQ or AWS SQS for queue management

### Frontend
- **Web Interface**: Vue.js 3 or React 18 with Vite
- **Mobile Apps**: Flutter for cross-platform (iOS & Android)
- **Styling**: Tailwind CSS for responsive design
- **Architecture**: Progressive Web App (PWA)

### AI/ML Services
- **Microservices**: Python with FastAPI or Flask
- **ML Frameworks**: TensorFlow or PyTorch
- **APIs**: OpenAI API or Hugging Face for NLP, Google Vision API for image recognition

### Infrastructure
- **Containerization**: Docker with Laravel Sail
- **Web Server**: NGINX
- **Cloud**: AWS or Google Cloud Platform
- **CDN**: Cloudflare for global content delivery
- **Storage**: Amazon S3 or MinIO for file storage

## Database Configuration

The Vidiaspot Marketplace uses SQLite as the primary database with caching mechanisms to optimize performance. For production environments, MySQL can be configured as the primary database with SQLite used as a local cache layer to reduce read operations:

### Primary Database (SQLite - Default)
- Default configuration uses SQLite for easy local setup
- Single database file: `database/database.sqlite`
- Zero configuration required for development
- All application data (users, ads, payments, etc.) stored here

### Production Option (MySQL with SQLite Cache)
- Primary database for all application data (users, ads, payments, etc.)
- Production configuration with read replica support for scaling
- SQLite as local cache layer to reduce read operations from main database
- Connection pooling enabled for optimal performance

### Local Cache Layer (File-based with SQLite)
- Cache service uses file system or database for caching frequently accessed data
- Dramatically reduces read operations from primary database
- Stores cached ad placements, categories, and commonly accessed data
- Improves performance and reduces database load

### Configuration Options:
- **Default Connection**: Configurable via `DB_CONNECTION` environment variable (defaults to `sqlite`)
- **Read Replicas**: Support for separate read/write hosts when using MySQL
- **Connection Pooling**: Enabled via persistent connections for MySQL
- **Cache System**: File-based or database-based caching
- **Foreign Key Constraints**: Configurable for SQLite

## Ad Placement System

### Admin-Controlled Ad Placement
- **Multiple Positions**: Ads can be placed in various positions:
  - Top banner
  - Sidebar
  - Bottom banner
  - Between content sections
  - Inline within content
  - Popup/interstitial
- **Ad Types**: Different ad formats supported:
  - Image ads
  - Video ads
  - Text ads
  - Rich media ads
- **Targeting Options**: 
  - Geographic targeting
  - Category-based placement
  - User behavior targeting
  - Time-based scheduling

### Admin Features
- **Ad Management Dashboard**: Create, edit, and delete ad placements
- **Performance Analytics**: Track ad performance metrics
- **Scheduling**: Set start/end dates for campaigns
- **Budget Management**: Set spending limits per campaign
- **A/B Testing**: Test different ad variations

## Payment Gateway Integration

### Supported Gateways
- **Paystack**: Primary Nigerian payment gateway
- **Flutterwave**: Alternative Nigerian payment gateway

### Payment Features
- **Multiple Payment Types**:
  - Ad posting fees
  - Featured ad upgrades
  - Premium subscriptions
  - In-app purchases
- **Transaction Tracking**: Complete transaction history
- **Webhook Integration**: Real-time payment notifications
- **Security**: Encrypted payment processing
- **Multi-currency**: Support for NGN and other currencies

### API Endpoints
- `POST /api/payment/initialize` - Initialize payment
- `POST /api/payment/verify` - Verify payment
- `GET /api/payment/transaction` - Get transaction details
- `POST /api/payment/webhook/paystack` - Paystack webhook
- `POST /api/payment/webhook/flutterwave` - Flutterwave webhook

## Setup Guide

### Prerequisites
- PHP 8.2+
- Composer
- Node.js and npm
- Docker and Docker Compose
- SQLite or MySQL

### Local Setup

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
   npm install
   ```

4. **Set up environment**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure database** (SQLite for local development):
   - The default .env uses SQLite, which requires no additional setup
   - Create the database file:
     ```bash
     touch database/database.sqlite
     ```

6. **Run migrations**:
   ```bash
   php artisan migrate
   ```

7. **Build frontend assets**:
   ```bash
   npm run dev  # For development
   # or
   npm run build  # For production
   ```

8. **Start the development server**:
   ```bash
   php artisan serve
   ```

### Production Setup with MySQL

1. **Configure environment**:
   ```bash
   cp .env.example .env
   ```

2. **Update database configuration**:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=your_database_name
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

3. **For read replicas** (optional):
   ```
   DB_HOST_WRITE=primary-host
   DB_HOST_READ=replica-host
   DB_PORT_WRITE=3306
   DB_PORT_READ=3306
   DB_USERNAME_WRITE=primary-user
   DB_USERNAME_READ=replica-user
   DB_PASSWORD_WRITE=primary-password
   DB_PASSWORD_READ=replica-password
   ```

4. **Run migrations**:
   ```bash
   php artisan migrate
   ```

### Environment Variables

#### Payment Gateway Keys
```
PAYSTACK_SECRET_KEY=your_paystack_secret_key
PAYSTACK_PUBLIC_KEY=your_paystack_public_key
FLUTTERWAVE_SECRET_KEY=your_flutterwave_secret_key
FLUTTERWAVE_PUBLIC_KEY=your_flutterwave_public_key
FLUTTERWAVE_ENCRYPTION_KEY=your_flutterwave_encryption_key
FLUTTERWAVE_SECRET_HASH=your_flutterwave_secret_hash
```

#### Database Configuration
```
DB_CONNECTION=sqlite    # For local development
# DB_CONNECTION=mysql  # For production

# For MySQL production:
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=
```

## API Documentation

### Authentication
All protected API endpoints require a valid API token in the Authorization header:
```
Authorization: Bearer {token}
```

### Common Response Format
```json
{
  "success": true,
  "message": "Success message",
  "data": { ... },
  "errors": { ... }  // Only present on error responses
}
```

### Payment API Endpoints

#### Initialize Payment
`POST /api/payment/initialize`

**Parameters:**
- `amount` (required, numeric): Payment amount
- `email` (required, email): Customer email
- `payment_gateway` (required, string): 'paystack' or 'flutterwave'
- `type` (required, string): Payment type ('ad_payment', 'featured_ad', 'premium_subscription', etc.)
- `user_id` (required, integer): User ID
- `ad_id` (optional, integer): Ad ID

**Response:**
```json
{
  "success": true,
  "message": "Payment initialized successfully",
  "data": {
    "authorization_url": "https://checkout-url.com",
    "reference": "unique_reference",
    "payment_gateway": "paystack"
  }
}
```

#### Verify Payment
`POST /api/payment/verify`

**Parameters:**
- `reference` (required, string): Payment reference
- `payment_gateway` (required, string): 'paystack' or 'flutterwave'

#### Get Transaction Details
`GET /api/payment/transaction`

**Parameters:**
- `reference` (required, string): Transaction reference

### Classification Ads API

#### Create Ad
`POST /api/ads`

**Requires authentication**

#### Get All Ads
`GET /api/ads`

#### Get User's Ads
`GET /api/my-ads`

**Requires authentication**

### Search API

#### Search Ads
`GET /api/ads`

**Parameters:**
- `q` (optional, string): Search query
- `category` (optional, integer): Category ID
- `location` (optional, string): Location
- `price_min` (optional, numeric): Minimum price
- `price_max` (optional, numeric): Maximum price

## Developer Guide

### Architecture Overview

The application follows Laravel's MVC pattern with service layers for business logic:

- **Controllers**: Handle HTTP requests and responses
- **Services**: Business logic and external API interactions (PaymentService, etc.)
- **Models**: Data access and relationships
- **Repositories**: Data access patterns (if implemented)
- **Resources**: API response formatting
- **Jobs**: Queueable tasks
- **Events**: Event-driven architecture
- **Listeners**: Event handlers

### Payment Service Architecture

The PaymentService handles all payment-related operations:

```php
class PaymentService
{
    // Initialize payment with Paystack or Flutterwave
    public function initializePaystackPayment($amount, $email, $reference, $callbackUrl, $metadata = [])
    public function initializeFlutterwavePayment($amount, $email, $tx_ref, $callbackUrl, $metadata = [])

    // Verify payments
    public function verifyPaystackTransaction($reference)
    public function verifyFlutterwaveTransaction($tx_ref)

    // Handle webhooks
    public function handlePaystackWebhook($payload)
    public function handleFlutterwaveWebhook($payload)
}
```

### Database Models

#### Key Models:
- **User**: Authentication and user management
- **Ad**: Classified ad listings
- **Category**: Ad categories
- **PaymentTransaction**: Payment tracking
- **AdPlacement**: Ad placement management

### Service Providers

The application uses custom service providers for payment configuration:
- `config/payment.php` - Payment gateway configuration
- Service container bindings in `app/Providers/AppServiceProvider.php`

### Error Handling

The application implements comprehensive error handling with:
- Validation using Form Request classes
- Exception handling with custom exception classes
- Logging using Laravel's logging system
- API response formatting

### Testing

The application includes:
- Unit tests in the `tests/Unit` directory
- Feature tests in the `tests/Feature` directory
- API tests for all endpoints
- Payment integration tests

## Admin Features

### Ad Placement Management

Admins can manage ad placements through the admin dashboard:

#### Placement Positions:
- **Header Banner**: Ads displayed at the top of pages
- **Sidebar**: Ads in the side navigation
- **Footer**: Ads at the bottom of pages
- **Content Inline**: Ads placed within content sections
- **Interstitial**: Full-page ads between content

#### Ad Types:
- **Image Ads**: Static image advertisements
- **Video Ads**: Video content advertisements
- **Rich Media**: Interactive advertisements
- **Text Ads**: Simple text-based advertisements

#### Management Capabilities:
- Upload new ad creative
- Set placement position
- Schedule ad campaigns
- Set targeting criteria
- Monitor performance metrics
- Set budget limits

### Content Management

Admins can manage static pages (about, contact, privacy, terms) and control ad placements on these pages.

## Cross-Check: Requested vs Implemented Features

### ✅ Fully Implemented:
- **Payment Gateway Integration**: Paystack and Flutterwave with full API endpoints
- **Database Configuration**: SQLite for local, MySQL with read replicas for production
- **API Architecture**: RESTful API with proper authentication
- **Ad Placement System**: Admin-controlled ad placements in different positions
- **User Authentication**: Complete auth system with social login
- **Basic AI Features**: Image recognition and chatbot
- **File Storage**: Image upload and management
- **Search Functionality**: Basic search with filters
- **Mobile Compatibility**: Responsive design and PWA features

### ⚠️ Partially Implemented:
- **Advanced AI Features**: Basic implementation, more sophisticated features planned for future phases
- **Elasticsearch Integration**: Planned but not yet implemented
- **Full Recommendation Engine**: Basic implementation, advanced collaborative filtering planned
- **Advanced Search Features**: Basic search implemented, visual/voice search planned

### 🔧 Planned for Future Phases:
- **Advanced Analytics Dashboard**: Business intelligence features
- **Blockchain Integration**: For advanced security features
- **AR/VR Implementation**: For advanced product visualization
- **Advanced Automation**: More sophisticated AI features

### 📈 Implementation Status:
- **Phase 1 Complete**: Payment gateway integration (completed)
- **Phase 2 In Progress**: Enhanced search and localization features
- **Phase 3 Planned**: Advanced AI features and recommendations
- **Phase 4 Planned**: Advanced transaction and security features
- **Phase 5 Planned**: Revolutionary features and market expansion

## Mobile Application

The mobile application is built with Flutter and supports both iOS and Android platforms. The mobile app includes:
- Complete classified ads functionality
- Push notifications
- Offline capabilities
- Payment processing
- Real-time messaging

## Security Features

- JWT authentication
- OAuth 2.0 for third-party login
- SSL/TLS encryption
- Input validation and sanitization
- SQL injection protection
- CSRF protection
- Rate limiting to prevent abuse

## Performance Optimization

- Database indexing strategies
- Redis caching layer
- Image optimization
- Code splitting
- Service workers for offline functionality
- Elasticsearch for advanced search

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## Support

For support, please open an issue in the repository or contact the development team.

---

*This documentation is maintained with the codebase and updated with each release.*