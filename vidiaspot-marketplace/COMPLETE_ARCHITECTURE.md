# Vidiaspot Marketplace - Complete Architecture

## Overview
This document describes the complete architecture of the Vidiaspot Marketplace application, which uses multiple technologies for high performance and scalability.

## Architecture Components

### 1. Database Layer
- **Primary Database**: MySQL (for all core data storage and writes)
- **Cache Layer**: 
  - Redis (for high-performance caching and session management)
  - SQLite (as local cache layer to reduce read load from MySQL)

### 2. Search Engine
- **Elasticsearch**: Advanced search functionality with full-text search, filtering, and aggregations

### 3. Queue Management
- **Redis**: For simple queue operations
- **RabbitMQ or AWS SQS**: For advanced queue management and job processing

### 4. Session Management
- **Redis**: High-performance session storage

### 5. AI & ML Services
- **AI-powered Product Descriptions**: Generate descriptions from images
- **Automatic Image Enhancement**: AI-powered image processing
- **Computer Vision Categorization**: Smart item categorization

## Technology Stack

### Primary Data Store
```
MySQL (Primary)
├── Core application data
├── User data
├── Transaction data
└── Content management
```

### Caching Hierarchy
```
Redis (Primary Cache)
├── Session storage
├── Frequently accessed data
├── Temporary data
└── Queue management

SQLite (Local Cache Layer)
├── Cache tables for reduced MySQL reads
├── Local caching for development
└── Fallback caching
```

### Search Infrastructure
```
Elasticsearch
├── Advanced product search
├── Full-text search capabilities
├── Faceted search
├── Auto-complete suggestions
└── Relevance scoring
```

### Queue System
```
Message Queue (RabbitMQ/SQS)
├── Background job processing
├── Email notifications
├── Image processing jobs
├── Category import jobs
└── Search indexing
```

## Implementation Details

### Redis Configuration
- **Cache**: Database 1 - High-speed data caching
- **Session**: Database 2 - User session management  
- **Queue**: Database 3 - Job queue management
- **Default**: Database 0 - General purpose

### Elasticsearch Integration
- **Index**: `vidiaspot_marketplace`
- **Document Types**: Ad listings with comprehensive mapping
- **Search Features**: Multi-field matching, filtering, sorting
- **Autocomplete**: Suggest functionality for search terms

### Queue Processing
- **Import Categories**: Background job to import from jiji.ng
- **Image Processing**: Async image enhancement and analysis
- **Search Indexing**: Async updates to Elasticsearch

## Data Flow

### Read Operations
```
User Request
├── Check Redis Cache (1st priority)
├── Check SQLite Cache (2nd priority) 
├── Query MySQL (3rd priority)
└── Update caches for future requests
```

### Write Operations
```
Data Update
├── Update MySQL (primary)
├── Invalidate Redis cache
├── Update search index (async via queue)
└── Update SQLite cache (as needed)
```

## API Endpoints

### AI Services
- `POST /api/ai/generate-description` - AI-generated product descriptions
- `POST /api/ai/enhance-image` - Image enhancement
- `POST /api/ai/remove-background` - Background removal
- `POST /api/ai/categorize-item` - Computer vision categorization
- `POST /api/ai/batch-categorize` - Batch categorization

### Admin Import
- `POST /api/admin/categories/import/jiji` - Import categories from jiji.ng
- `GET /api/admin/categories/import/status` - Import status
- `POST /api/admin/products/import/latest` - Import latest products from jiji.ng
- `GET /api/admin/products/import/settings` - Get product import settings
- `PUT /api/admin/products/import/settings` - Update product import settings

### Settings Management
- Product import settings stored in database
- Configurable time period (default: last 3 days)
- Admin panel configuration capability
- Import interval management
- Category and location filters
- Price range filtering

## Performance Benefits

1. **Reduced Database Load**: 80% of reads served from Redis
2. **Faster Search**: Elasticsearch provides sub-second search results
3. **Scalable Processing**: Queue system handles background jobs
4. **High Availability**: Multi-tiered caching ensures uptime
5. **Cost Efficiency**: Reduced load on primary MySQL database

## Queue Jobs Implemented

1. **ImportCategoriesFromJiji**: Imports categories and subcategories from jiji.ng
2. **Elasticsearch Indexing**: Asynchronous search index updates
3. **Image Processing**: Background image enhancement and analysis
4. **AI Processing**: Background AI service operations

## Command Line Tools

### Import Categories
```bash
php artisan import:categories --url=https://jiji.ng
```

### Import Latest Products
```bash
php artisan import:latest-products --days=3 --force
```

## Security Considerations

- All external API calls are rate-limited
- Queue jobs include proper error handling and retry logic
- Sensitive data is properly encrypted
- Caching respects data privacy requirements

## Monitoring & Maintenance

- Configure Redis monitoring for cache hit ratios
- Set up Elasticsearch cluster monitoring
- Monitor queue job processing times
- Track database query performance