# Vidiaspot Marketplace - Setup Guide

## Prerequisites

Before setting up Vidiaspot Marketplace, ensure your system meets the following requirements:

### System Requirements
- **Operating System**: Windows, macOS, or Linux
- **PHP**: Version 8.2 or higher
- **Composer**: Latest version
- **Node.js**: Version 18 or higher
- **npm**: Latest version (comes with Node.js)
- **Docker**: Latest version (recommended for consistent environment)
- **Memory**: Minimum 4GB RAM recommended

### PHP Extensions
The following PHP extensions must be enabled:
- `openssl`
- `pdo`
- `mbstring`
- `tokenizer`
- `xml`
- `curl`
- `zip`
- `gd` or `imagick` (for image processing)

## Quick Setup (Development)

### 1. Clone the Repository
```bash
git clone <repository-url>
cd vidiaspot-marketplace
```

### 2. Install PHP Dependencies
```bash
composer install
```

### 3. Install Frontend Dependencies
```bash
npm install
```

### 4. Set Up Environment Configuration
```bash
cp .env.example .env
php artisan key:generate
```

### 5. Configure Database (SQLite Default with MySQL Option)
The application uses SQLite as the default primary database for easy local development. For production, MySQL can be configured as the primary database with SQLite used as a cache layer:

**For Local Development (Default - SQLite):**
```bash
# SQLite is already configured as default
DB_CONNECTION=sqlite
# No additional configuration needed for SQLite
```

**For Production (MySQL with SQLite Cache Option):**
```bash
# Update .env file to use MySQL as primary database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_production_db
DB_USERNAME=your_username
DB_PASSWORD=your_password

# SQLite cache for improved performance
SQLITE_CACHE_DATABASE=database/cache.sqlite
```

**Cache Setup (Optional for Performance):**
For improved performance, the application can use additional caching:
```bash
# Create SQLite cache file if using as cache layer
touch database/cache.sqlite
```

### 6. Run Database Migrations
```bash
php artisan migrate
```

### 7. Build Frontend Assets
```bash
npm run dev  # For development with hot-reload
# or
npm run build  # For production build
```

### 8. Start the Development Server
```bash
php artisan serve
```

The application will be available at `http://127.0.0.1:8000`

## Production Setup

### Database Configuration (MySQL)

For production, it's recommended to use MySQL. Update your `.env` file:

```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password
```

#### Optional: Read Replica Configuration
For high-traffic applications, you can configure read replicas:

```bash
# Primary (write) database
DB_HOST_WRITE=primary-host.example.com
DB_PORT_WRITE=3306
DB_USERNAME_WRITE=primary_user
DB_PASSWORD_WRITE=primary_password
DB_DATABASE=your_database_name

# Replica (read) databases
DB_HOST_READ=replica1.example.com,replica2.example.com
DB_PORT_READ=3306,3306
DB_USERNAME_READ=replica_user
DB_PASSWORD_READ=replica_password
```

### Install Dependencies
```bash
composer install --optimize-autoloader --no-dev
npm install --production
```

### Build Frontend Assets
```bash
npm run build
```

### Run Migrations
```bash
php artisan migrate --force
```

### Set Up Web Server
Configure your web server (Apache/Nginx) to point to the `public/` directory.

## Docker Setup (Recommended)

Vidiaspot Marketplace includes Docker configuration using Laravel Sail.

### Prerequisites
- Docker Desktop installed
- Docker Compose installed

### Setup with Docker
```bash
# Install Sail
composer require laravel/sail --dev

# Publish the Sail Docker files
php artisan sail:install

# Build and start containers
./vendor/bin/sail up -d

# Install dependencies in the container
./vendor/bin/sail artisan key:generate
./vendor/bin/sail npm install
./vendor/bin/sail npm run build
./vendor/bin/sail artisan migrate
```

The application will be available at `http://localhost`

## Environment Configuration

### Application Settings
```bash
APP_NAME="Vidiaspot Marketplace"
APP_ENV=local          # local, staging, production
APP_KEY=              # Auto-generated with artisan key:generate
APP_DEBUG=true        # Set to false in production
APP_URL=http://localhost
```

### Database Settings
```bash
# SQLite (Default for local)
DB_CONNECTION=sqlite

# MySQL (Production)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=
```

### Cache and Session Settings
```bash
CACHE_STORE=database  # redis, database, file, memcached
SESSION_DRIVER=database
QUEUE_CONNECTION=database  # or redis, database, sync
```

### Payment Gateway Keys
```bash
# Paystack
PAYSTACK_SECRET_KEY=ssss
PAYSTACK_PUBLIC_KEY=sss

# Flutterwave
FLUTTERWAVE_SECRET_KEY=FLWSECK-xxxxxxxxxxxxxxxxx-X
FLUTTERWAVE_PUBLIC_KEY=FLWPUBK-xxxxxxxxxxxxxxxxx-X
FLUTTERWAVE_ENCRYPTION_KEY=xxx
FLUTTERWAVE_SECRET_HASH=your_secret_hash
```

## Payment Gateway Setup

### Paystack Configuration
1. Create an account at [Paystack](https://paystack.com)
2. Get your API keys from the dashboard
3. Set the environment variables:
   - `PAYSTACK_SECRET_KEY`
   - `PAYSTACK_PUBLIC_KEY`

### Flutterwave Configuration
1. Create an account at [Flutterwave](https://flutterwave.com)
2. Get your API keys from the dashboard
3. Set the environment variables:
   - `FLUTTERWAVE_SECRET_KEY`
   - `FLUTTERWAVE_PUBLIC_KEY`
   - `FLUTTERWAVE_ENCRYPTION_KEY`
   - `FLUTTERWAVE_SECRET_HASH` (for webhook verification)

## Redis Configuration (Optional but Recommended)

For caching and queues:
```bash
REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

## Email Configuration

Set up your email service:
```bash
MAIL_MAILER=smtp
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=your-email-username
MAIL_PASSWORD=your-email-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=hello@example.com
MAIL_FROM_NAME="${APP_NAME}"
```

## File Storage Configuration

### Local Storage (Default)
```bash
FILESYSTEM_DISK=local
```

### AWS S3 Configuration
```bash
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=your-access-key
AWS_SECRET_ACCESS_KEY=your-secret-key
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your-bucket-name
AWS_ENDPOINT=https://s3.amazonaws.com
```

## Running the Application

### Development Mode
```bash
# Start the development server
php artisan serve

# Watch and compile frontend assets
npm run dev

# With Laravel Sail
./vendor/bin/sail up
```

### Production Mode
```bash
# Start with built-in server (not recommended for production)
php artisan serve --host=0.0.0.0 --port=80

# Or configure with Apache/Nginx
```

## Maintenance Tasks

### Regular Maintenance
```bash
# Clear application cache
php artisan cache:clear

# Clear configuration cache
php artisan config:clear

# Clear route cache
php artisan route:clear

# Clear view cache
php artisan view:clear

# Run database migrations
php artisan migrate

# Seed the database (if needed)
php artisan db:seed
```

### Queue Worker (if using queues)
```bash
# Start queue worker
php artisan queue:work

# Process a single job
php artisan queue:work --once
```

## Testing Setup

### Run Tests
```bash
# Run all tests
php artisan test

# Run unit tests
php artisan test --testsuite=Unit

# Run feature tests
php artisan test --testsuite=Feature
```

## Performance Optimization

### Production Optimization
```bash
# Install dependencies without dev dependencies
composer install --optimize-autoloader --no-dev

# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer dump-autoload --optimize
```

## Troubleshooting

### Common Issues

#### Issue: Cannot create directory 'database'
**Solution**: Create the database directory manually
```bash
mkdir database
touch database/database.sqlite
```

#### Issue: SQLSTATE[HY000] [2002] Connection refused
**Solution**: Check if MySQL server is running, or switch to SQLite for local development:
```bash
DB_CONNECTION=sqlite
```

#### Issue: Class 'App\Models\Model' not found
**Solution**: Run autoloader dump
```bash
composer dump-autoload
```

#### Issue: Maximum execution time exceeded
**Solution**: Increase execution time in php.ini or add at beginning of file:
```php
ini_set('max_execution_time', 300); // 5 minutes
```

## Deployment Checklist

- [ ] Environment variables configured
- [ ] Database connection tested
- [ ] Payment gateways configured
- [ ] Email service configured
- [ ] File storage configured
- [ ] Caching configured
- [ ] Queue system configured (if applicable)
- [ ] SSL certificate installed
- [ ] Security headers configured
- [ ] Performance optimizations applied
- [ ] Backup configured
- [ ] Monitoring configured

## Next Steps

1. **Customize the application** with your branding
2. **Configure payment gateways** with your actual keys
3. **Set up your domain** and SSL certificate
4. **Configure email** for notifications
5. **Set up monitoring** and analytics
6. **Test all functionality** in your environment

---

*This setup guide is updated with each release. Please ensure you're using the latest version of this document with your application version.*