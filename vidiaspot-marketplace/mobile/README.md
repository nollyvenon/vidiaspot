# VidiaSpot Marketplace Mobile App

A comprehensive marketplace application built with Flutter for buying and selling items locally.

## Features

### Real-time Translation
- **Language Support**: English, French, Portuguese, Arabic, Spanish, German, Chinese, Yoruba, Igbo, Hausa
- **Auto-detection**: Automatic language detection for content
- **Local Dialect Support**: Specialized support for Nigerian languages (Yoruba, Igbo, Hausa)
- **Seamless Translation**: In-app translation of content and UI elements

### Database Optimization
- **Caching Layer**: Implemented with Redis for high-performance caching
- **Database Indexing**: Strategic indexing for improved query performance
- **Connection Pooling**: Optimized database connection management

### Performance Technologies
- **Offline-First Architecture**: Using Hive for local data storage
- **Adaptive Loading**: Network condition-based content loading
- **Image Optimization**: Lazy loading and caching with WebP format support
- **Code Splitting**: Modular code architecture for faster loading

### API Integration
- **RESTful APIs**: Standard REST API integration
- **GraphQL Support**: Complex queries with GraphQL
- **API Versioning**: Backward compatibility with versioned APIs

### Push Notifications
- **Firebase Integration**: Real-time push notifications
- **Custom Messaging**: Personalized notification system

### Additional Features
- **User Authentication**: Secure login and registration
- **Ad Management**: Post, edit, and manage marketplace ads
- **Search Functionality**: Advanced search with filtering
- **Messaging System**: In-app chat between users
- **Location Services**: GPS integration for location-based services

## Architecture

### Mobile App Structure
```
lib/
├── main.dart               # App entry point
├── models/                 # Data models
│   ├── ad_model.dart
│   └── user_model.dart
├── services/               # Business logic
│   ├── translation_service.dart
│   ├── settings_service.dart
│   ├── cache_service.dart
│   └── connectivity_service.dart
├── screens/                # UI screens
│   ├── home_screen.dart
│   ├── search_screen.dart
│   ├── profile_screen.dart
│   ├── post_ad_screen.dart
│   └── messages_screen.dart
└── widgets/                # Reusable UI components
    └── translation_widget.dart
```

### API Architecture
- Laravel backend with RESTful endpoints
- API versioning for backward compatibility
- GraphQL for complex queries
- JWT authentication
- Rate limiting and security measures

### Backend Technologies
- **Laravel**: Full-featured backend framework
- **Redis**: Caching and session management
- **Elasticsearch**: Advanced search functionality
- **RabbitMQ/AWS SQS**: Queue management
- **Firebase**: Push notifications
- **MeiliSearch**: Fast search engine

### Monitoring & Analytics
- **Laravel Telescope**: Debugging and monitoring
- **Google Analytics 4**: User behavior tracking
- **Sentry**: Error tracking and monitoring
- **Performance Monitoring**: Comprehensive app performance tracking

## Mobile App Screens

1. **Home Screen**: Featured ads, categories, search
2. **Search Screen**: Advanced search with filters
3. **Post Ad Screen**: Create new marketplace listings
4. **Messages Screen**: In-app messaging system
5. **Profile Screen**: User account management

## Installation

### Prerequisites
- Flutter SDK 3.0+
- Android Studio / Xcode
- Git

### Setup
1. Clone the repository:
   ```bash
   git clone <repository-url>
   cd vidiaspot-marketplace
   cd mobile
   ```

2. Install dependencies:
   ```bash
   flutter pub get
   ```

3. Run the app:
   ```bash
   flutter run
   ```

## API Endpoints

The mobile app connects to a Laravel backend with the following key endpoints:

### Public Endpoints
- `GET /api/ads` - Get all ads with filtering options
- `GET /api/ads/{id}` - Get specific ad
- `GET /api/categories` - Get all categories
- `GET /api/pages/{slug}` - Get content pages

### Authenticated Endpoints
- `POST /api/ads` - Create new ad
- `PUT /api/ads/{id}` - Update ad
- `DELETE /api/ads/{id}` - Delete ad
- `GET /api/my-ads` - Get user's ads
- `POST /api/messages` - Send message
- `GET /api/messages` - Get messages

### Admin Endpoints
- `GET /api/admin/dashboard` - Admin dashboard
- `GET /api/admin/ads` - Manage ads
- `GET /api/admin/users` - Manage users
- `GET /api/admin/categories` - Manage categories

## Performance Optimizations

### Mobile App
- Image caching with `cached_network_image`
- Lazy loading for content
- Shimmer effects for loading states
- Connectivity-aware operations
- Offline-first data storage

### Backend
- Database indexing strategies
- Redis caching layer
- Queue management for background tasks
- API response caching
- Database connection pooling

## Security Features

- JWT-based authentication
- Input validation and sanitization
- Rate limiting
- Secure file uploads
- SQL injection prevention
- XSS protection

## Development

### Code Structure
- MVVM architecture pattern
- Service layer for business logic
- Repository pattern for data management
- Provider for state management

### Testing
- Unit tests for business logic
- Integration tests for API calls
- Widget tests for UI components

## Deployment

### Mobile App
- Android: Generate signed APK/Bundle
- iOS: Archive and upload to App Store Connect

### Backend
- Server: Nginx/Apache with PHP-FPM
- Database: MySQL/MariaDB optimized
- Cache: Redis server
- Queue: Redis/RabbitMQ
- Search: Elasticsearch/MeiliSearch

## Technologies Used

### Mobile App
- Flutter (Dart)
- Hive for local storage
- Cached Network Image
- Connectivity Plus
- Provider for state management

### Backend
- Laravel PHP Framework
- MySQL/MariaDB
- Redis
- Elasticsearch/MeiliSearch
- RabbitMQ
- Firebase

### Monitoring
- Laravel Telescope
- Google Analytics 4
- Sentry
- Performance monitoring tools

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests if applicable
5. Submit a pull request

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Support

For support and questions, please open an issue in the repository.