# VidiaSpot Marketplace - Technical Documentation

## Table of Contents
1. [Overview](#overview)
2. [Architecture](#architecture)
3. [Frontend Structure](#frontend-structure)
4. [Backend Structure](#backend-structure)
5. [Database Schema](#database-schema)
6. [API Documentation](#api-documentation)
7. [Features Overview](#features-overview)
8. [Deployment Guide](#deployment-guide)

## Overview
VidiaSpot Marketplace is a comprehensive SaaS platform that combines e-commerce capabilities (Shopify-like) with food ordering functionality (Chowdeck-like). The platform supports multi-tenant stores, affiliate programs, MLM features, and integration with external platforms.

## Architecture
### Tech Stack
- **Frontend**: React.js with Tailwind CSS
- **Backend**: Laravel PHP with MySQL
- **Mobile**: Flutter (future implementation)
- **Payment**: Paystack, Flutterwave, PayPal, Stripe
- **Authentication**: Laravel Sanctum/JWT
- **File Storage**: AWS S3 / Local Storage
- **Caching**: Redis
- **Queue**: Laravel Queues with Redis
- **Search**: Elasticsearch (future implementation)

### Architecture Diagram
```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Frontend      │    │   Backend       │    │    Database     │
│   (React)       │◄──►│   (Laravel)     │◄──►│    (MySQL)      │
│                 │    │                 │    │                 │
│  - Admin Panel  │    │  - API Layer    │    │  - Users        │
│  - Store Front │    │  - Auth System  │    │  - Products     │
│  - User Portal  │    │  - Payment Int. │    │  - Orders       │
│  - Mobile UI    │    │  - Reporting   │    │  - Affiliates   │
└─────────────────┘    └─────────────────┘    └─────────────────┘
```

## Frontend Structure
```
frontend/
├── public/
├── src/
│   ├── components/
│   │   ├── Admin/
│   │   ├── Ecommerce/
│   │   ├── FoodOrdering/
│   │   ├── Affiliate/
│   │   └── Shared/
│   ├── services/
│   │   ├── api.js
│   │   ├── productService.js
│   │   ├── orderService.js
│   │   ├── affiliateService.js
│   │   └── storeService.js
│   ├── hooks/
│   ├── utils/
│   ├── contexts/
│   ├── assets/
│   └── styles/
├── package.json
└── vite.config.js
```

## Backend Structure
```
backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   ├── Middleware/
│   │   └── Requests/
│   ├── Models/
│   ├── Services/
│   ├── Jobs/
│   └── Events/
├── routes/
│   ├── api.php
│   ├── web.php
│   └── channels.php
├── resources/
│   ├── views/
│   └── lang/
├── database/
│   ├── migrations/
│   ├── seeds/
│   └── factories/
├── config/
└── storage/
```

## Database Schema

### Core Tables
```
users
├── id (PK)
├── name
├── email
├── password
├── role (admin, seller, customer, affiliate)
├── created_at
└── updated_at

stores
├── id (PK)
├── user_id (FK)
├── name
├── slug
├── description
├── logo
├── domain
├── is_active
├── created_at
└── updated_at

products
├── id (PK)
├── store_id (FK)
├── name
├── description
├── price
├── inventory
├── category_id (FK)
├── is_active
├── created_at
└── updated_at

orders
├── id (PK)
├── user_id (FK)
├── store_id (FK)
├── items (JSON)
├── total_amount
├── status
├── created_at
└── updated_at

affiliates
├── id (PK)
├── user_id (FK)
├── referral_code
├── commission_rate
├── total_earnings
├── total_paid
├── is_approved
├── created_at
└── updated_at

affiliate_downlines
├── id (PK)
├── affiliate_id (FK)
├── referred_user_id (FK)
├── level (1-20)
├── commission_earned
└── created_at

affiliate_commissions
├── id (PK)
├── affiliate_id (FK)
├── order_id (FK)
├── amount
├── level
├── type (direct, upline)
└── created_at

food_vendors
├── id (PK)
├── user_id (FK)
├── name
├── description
├── address
├── phone
├── is_active
└── created_at

food_menu_items
├── id (PK)
├── vendor_id (FK)
├── name
├── description
├── price
├── category
├── is_available
└── created_at

import_export_operations
├── id (PK)
├── user_id (FK)
├── platform (shopify, woocommerce, etc.)
├── type (import, export)
├── status
├── data_summary
└── created_at
```

## API Documentation

### Authentication
All authenticated endpoints require a Bearer token in the Authorization header.

```
Authorization: Bearer {jwt_token}
```

### Core Endpoints

#### Authentication
```
POST /api/login
POST /api/register
POST /api/logout
POST /api/forgot-password
POST /api/reset-password
```

#### User Management
```
GET /api/user - Get authenticated user
PUT /api/user - Update user profile
DELETE /api/user - Delete user account
```

#### Store Management
```
GET /api/stores - Get all stores
GET /api/stores/{id} - Get specific store
POST /api/stores - Create new store
PUT /api/stores/{id} - Update store
DELETE /api/stores/{id} - Delete store

GET /api/stores/{storeId}/products - Get store products
POST /api/stores/{storeId}/products - Add product to store
```

#### Product Management
```
GET /api/products - Get all products
GET /api/products/{id} - Get specific product
POST /api/products - Create new product
PUT /api/products/{id} - Update product
DELETE /api/products/{id} - Delete product
```

#### Food Ordering
```
GET /api/food/vendors - Get all food vendors
GET /api/food/vendors/{id} - Get specific vendor
GET /api/food/vendors/{id}/menu - Get vendor menu
POST /api/food/orders - Create food order
GET /api/food/orders - Get user orders
```

#### Affiliate System
```
GET /api/affiliate/settings - Get affiliate program settings
GET /api/affiliate/current-user - Get current user affiliate status
POST /api/affiliate/apply - Apply for affiliate program
GET /api/affiliate/{id}/referrals - Get user referrals
GET /api/affiliate/{id}/commissions - Get user commissions
GET /api/affiliate/{id}/payouts - Get user payouts
POST /api/affiliate/payouts - Request payout
GET /api/affiliate/{id}/downline - Get downline (up to 20 levels)
GET /api/affiliate/commission-structure - Get MLM commission structure
```

#### Import/Export
```
GET /api/import-export/supported-platforms - Get supported platforms
POST /api/import-export/woocommerce - Import from WooCommerce
POST /api/import-export/shopify - Import from Shopify
POST /api/import-export/bigcommerce - Import from BigCommerce
POST /api/import-export/magento - Import from Magento
POST /api/import-export/csv - Import from CSV
POST /api/import-export/export-csv - Export to CSV
POST /api/import-export/export-shopify - Export to Shopify
POST /api/import-export/export-woocommerce - Export to WooCommerce
GET /api/import-export/history - Get import/export history
POST /api/import-export/{id}/cancel - Cancel operation
POST /api/import-export/validate - Validate import file
```

#### E-commerce Analytics
```
GET /api/analytics/fleet-utilization - Fleet utilization rates
GET /api/analytics/driver-productivity - Driver productivity
GET /api/analytics/peak-demand - Peak demand analysis
GET /api/analytics/geographic-heatmaps - Geographic heat maps
GET /api/analytics/exception-handling - Exception handling reports
GET /api/analytics/resource-allocation - Resource allocation optimization

GET /api/accounting/general-ledger-reconciliation - General ledger reconciliation
GET /api/accounting/accounts-receivable-aging - Accounts receivable aging
GET /api/accounting/accounts-payable-aging - Accounts payable aging
GET /api/accounting/tax-reporting-compliance - Tax reporting compliance
GET /api/accounting/audit-trails - Audit trails
GET /api/accounting/cost-center-analysis - Cost center analysis
GET /api/accounting/financial-statements - Financial statements
```

## Features Overview

### E-commerce (Shopify-like)
- Multi-tenant store creation
- Custom domain linking
- Product management with variants
- Inventory tracking
- Order management
- Payment processing
- Shipping integration
- Customer management
- SEO tools
- Theme customization
- Analytics and reporting

### Food Ordering (Chowdeck-like)
- Restaurant discovery
- Menu browsing
- Real-time order tracking
- Delivery management
- Vendor management
- Rating and review system
- Special instruction handling
- Dietary restriction filtering

### Affiliate/MLM System
- Referral code generation
- Commission tracking
- Up to 20-level downline tracking
- Direct referral commissions
- Upline commission system
- Payout management
- MLM settings control (toggle on/off)

### Import/Export
- WooCommerce import/export
- Shopify import/export
- BigCommerce import/export
- Magento import/export
- CSV import/export
- Data validation and mapping
- Bulk operations support

## Deployment Guide

### Prerequisites
- PHP 8.1+
- MySQL 8.0+
- Node.js 18+
- Composer
- NPM/Yarn

### Backend Setup
```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
# Configure database settings in .env
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

### Frontend Setup
```bash
cd frontend
npm install
cp .env.example .env
# Configure API URL in .env
npm run dev
```

### Mobile Setup (Future)
```bash
cd mobile
flutter pub get
flutter run
```

### Environment Variables
```
APP_NAME="VidiaSpot Marketplace"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=vidiaspot_marketplace
DB_USERNAME=root
DB_PASSWORD=

PAYSTACK_SECRET_KEY=sk_test_...
PAYSTACK_PUBLIC_KEY=pk_test_...
FLUTTERWAVE_SECRET_KEY=FLWSECRE...
STRIPE_SECRET_KEY=sk_test_...
PAYPAL_CLIENT_ID=...
PAYPAL_CLIENT_SECRET=...

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="hello@example.com"
```

## Security Considerations
- All API endpoints are secured with JWT authentication
- Passwords are hashed using bcrypt
- Input validation and sanitization on all endpoints
- CSRF protection for web forms
- SQL injection prevention with prepared statements
- XSS prevention with output encoding
- Rate limiting on sensitive endpoints
- Secure file upload validation
- SSL enforced in production

## Scalability Features
- Redis for caching and session management
- Queue system for background jobs
- Database indexing on frequently queried columns
- CDN readiness for static assets
- Microservice architecture for high-traffic components
- Horizontal scaling with load balancers
- Database read replicas for performance