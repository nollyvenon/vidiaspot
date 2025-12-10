# VidiaSpot Marketplace - User Guide

## Table of Contents
1. [Getting Started](#getting-started)
2. [Creating Your Store](#creating-your-store)
3. [Food Ordering](#food-ordering)
4. [Affiliate & MLM Program](#affiliate--mlm-program)
5. [Import/Export Data](#importexport-data)
6. [Managing Your Downline](#managing-your-downline)
7. [Earning Commissions](#earning-commissions)
8. [Store Administration](#store-administration)
9. [Security & Compliance](#security--compliance)

## Getting Started

### Registration and Account Setup
1. Visit the VidiaSpot Marketplace website
2. Click "Sign Up" in the top navigation bar
3. Fill in your details:
   - Full name
   - Email address
   - Password (minimum 8 characters)
   - Confirm password
   - Select your role (Customer, Seller, Store Owner)

4. Verify your email address by clicking the link in the confirmation email
5. Once verified, you can:
   - Browse food vendors and products
   - Create your own store (if registered as Seller/Store Owner)
   - Join the affiliate program
   - Start earning commissions

### Dashboard Overview
After logging in, you'll have access to your personalized dashboard:
- **Quick Stats** showing your current activity
- **Navigation** to different sections of the platform
- **Recent activity** updates
- **Notifications** and alerts

## Creating Your Store

### Setting Up Your Store
1. Navigate to "My Stores" → "Create New Store"
2. Fill out your store details:
   - Store name (will be used in the URL)
   - Store description
   - Contact information
   - Business type
   - Logo and cover image

3. Choose a unique store slug which will become your subdomain: `yourslug.vidiaspot.com`

4. Select a payment gateway:
   - Paystack (for African markets)
   - Flutterwave (for African markets)
   - PayPal
   - Stripe
   - Or add multiple gateways

5. Configure store settings:
   - Shipping options
   - Tax settings
   - Inventory tracking
   - Theme customization

### Linking Custom Domain
1. Go to "Store Settings" → "Domain Management"
2. Enter your custom domain (e.g., mystore.com)
3. Update your DNS settings:
   - Create an A record pointing to our server IP: `45.12.34.56`
   - Or create a CNAME record: `shop.yourdomain.com → yourstore.vidiaspot.com`
4. Enable SSL certificate (automatically provisioned)
5. Wait for DNS propagation (typically 24-48 hours)

### Adding Products
1. Navigate to "My Products" → "Add Product"
2. Fill in product details:
   - Product name
   - Description
   - Price and currency
   - Inventory count
   - Category and tags
   - Images (up to 10 per product)
   - Variants (size, color, etc.)

3. Set inventory alerts for low stock
4. Add SEO-friendly titles and descriptions

### Customizing Your Store
1. Go to "Store Themes"
2. Choose from available templates
3. Customize colors, fonts, and layout
4. Add your brand elements
5. Preview changes before publishing

## Food Ordering

### Browsing Food Vendors
1. Click on the "Food" section in the main navigation
2. Browse vendors by:
   - Cuisine type (Italian, Mexican, Chinese, etc.)
   - Location/Distance
   - Rating
   - Dietary restrictions
   - Delivery time

3. Filter vendors by:
   - Open now
   - Free delivery
   - Top rated
   - New arrivals
   - Special offers

### Placing a Food Order
1. Select a vendor from the list
2. Browse their menu
3. Add items to your cart with customizations
4. Proceed to checkout
5. Enter delivery address
6. Select delivery time (ASAP or scheduled)
7. Choose payment method (card, wallet, cash on delivery)
8. Add tip and special instructions
9. Review and confirm your order

### Tracking Your Order
1. Go to "My Orders" → "Food Orders"
2. View the status of your order:
   - Preparing
   - Out for delivery
   - Delivered
3. Contact the driver directly if needed
4. Rate and review your experience

## Affiliate & MLM Program

### Joining the Affiliate Program
1. Go to "Affiliate Program" in your dashboard
2. Click "Apply for Affiliate Program"
3. Fill out the application form:
   - Preferred marketing channels
   - Expected monthly traffic
   - Social media profiles
   - Business type

4. Wait for admin approval (typically 1-2 business days)
5. Once approved, you'll receive:
   - Unique referral code
   - Marketing materials
   - Commission rates

### Understanding the MLM Structure (Up to 20 Levels)
Our MLM program allows you to earn commissions from your direct referrals and your up to 20-level deep downline:

#### Level Structure:
- **Level 1**: Direct referrals (people you personally refer)
- **Level 2**: Referrals made by your Level 1 referrals
- **Level 3**: Referrals made by your Level 2 referrals
- ...
- **Level 20**: Up to 20 levels deep

#### Commission Rates (Example - Actual rates may vary):
- Level 1: 10% of referred user's purchase
- Level 2: 5% of referred user's purchase
- Level 3-5: 3% of referred user's purchase
- Level 6-10: 2% of referred user's purchase
- Level 11-15: 1.5% of referred user's purchase
- Level 16-20: 1% of referred user's purchase

### Getting Your Referral Code
1. Visit "Affiliate Dashboard"
2. Your unique referral code is displayed prominently
3. Copy the referral link: `https://vidiaspot.com/signup?ref=YOUR_CODE`
4. Share this link anywhere:
   - Social media posts
   - Blog articles
   - YouTube videos
   - Email signatures
   - Word of mouth

### Earning Commissions
You earn commissions when:
1. Someone signs up using your referral code
2. That person makes purchases on the platform
3. Your downline grows and they make purchases

### Commission Tracking
1. Go to "Affiliate Dashboard" → "Commissions"
2. View:
   - Real-time commission tracking
   - Commission by level
   - Earnings history
   - Pending payments
   - Paid commissions

### Requesting Payouts
1. Navigate to "Affiliate Dashboard" → "Payouts"
2. Check minimum threshold (typically $50)
3. Select payout method:
   - PayPal
   - Bank transfer
   - Paystack (for African users)
   - Flutterwave (for African users)
4. Enter payout details
5. Submit request
6. Payouts processed within 5-7 business days

## Import/Export Data

### Importing from Other Platforms
1. Go to "Tools" → "Import/Export"
2. Select "Import" tab
3. Choose platform:
   - Shopify
   - WooCommerce
   - BigCommerce
   - Magento
   - Custom CSV

4. Enter API credentials or upload CSV file
5. Map fields appropriately
6. Select which data to import:
   - Products
   - Customers
   - Categories
   - Inventory
   - Orders (historical)

7. Start import process
8. Monitor progress in "Import History"

### Importing from CSV
1. Prepare your CSV file with columns matching our schema
2. Download sample CSV template from the import section
3. Upload your CSV file
4. Match columns to our system fields
5. Choose import options:
   - Skip duplicates
   - Update existing items
   - Create new items only
6. Start the import

### Exporting Your Data
1. Go to "Tools" → "Import/Export"
2. Select "Export" tab
3. Choose data type:
   - Products
   - Customers
   - Orders
   - Categories
   - Inventory

4. Set filters (date range, specific store, etc.)
5. Select format (Excel, CSV, JSON)
6. Start export process
7. Download file when complete

## Managing Your Downline

### Viewing Your Downline Structure
1. Visit "Affiliate Dashboard" → "My Downline"
2. See up to 20 levels of your referral network
3. View:
   - Number of referrals at each level
   - Total commissions earned per level
   - Individual referral details

### Downline Performance Analytics
1. Go to "Affiliate Dashboard" → "Analytics"
2. See visual representation of your network
3. Track:
   - Growth rate of your network
   - Commission trends
   - Most active downline members
   - Conversion rates by level

### Supporting Your Downline
Provide resources to help your downline succeed:
- Training materials
- Marketing tips
- Best practices
- Support contact information

## Earning Commissions

### How Commissions Work
1. Commission earned when someone:
   - Signs up using your referral code
   - Makes purchases on the platform
   - Your commission is calculated based on the product/service and your level in the MLM tree

2. Commission rates vary by:
   - Product category
   - Subscription level of referred user
   - Your affiliate tier (based on performance)

### Commission Calculation
For each purchase made by your referrals:

**Direct Referral (Level 1):**
- If John signs up using your code and spends $100
- You earn: $100 × your Level 1 rate

**Downline Referral (Level 2-20):**
- If John refers Mary, who spends $100
- You earn: $100 × your level-specific rate

### Commission Payouts
- Commissions accrue in your account
- Minimum payout amount: $50
- Payouts processed monthly on the 1st
- Manual payouts available when threshold met
- Processing takes 5-7 business days

## Store Administration

### Managing Orders
1. Go to "My Store" → "Orders"
2. View and manage:
   - Pending orders
   - Processing orders
   - Shipped orders
   - Delivered orders
   - Cancelled orders

3. Update order statuses
4. Print shipping labels
5. Send order updates to customers

### Inventory Management
1. Navigate to "My Store" → "Inventory"
2. Track:
   - Current stock levels
   - Low stock alerts
   - Out of stock items
   - Sales velocity

3. Set automatic reordering
4. Import/export inventory data

### Customer Management
1. Go to "My Store" → "Customers"
2. View customer details
3. Track purchase history
4. Manage customer communications

### Product Management
1. Visit "My Store" → "Products"
2. Create, edit, or delete products
3. Manage product variants and pricing
4. Upload and manage product images

## Security & Compliance

### Account Security
- Use strong passwords (12+ characters with mixed case, numbers, symbols)
- Enable two-factor authentication
- Regularly review connected devices
- Log out of unused sessions

### Payment Security
- All payment data encrypted
- PCI DSS compliant payment processing
- Fraud detection systems active
- Secure payment gateway integrations

### Data Protection
- GDPR compliant privacy controls
- California Consumer Privacy Act (CCPA) compliance
- Right to data portability
- Right to erasure

### Reporting Issues
- Contact support for technical issues
- Report security vulnerabilities immediately
- Use the help center for common questions
- Community forum for peer support

## Troubleshooting

### Common Issues
- **Can't find my referral code:** Check "Affiliate Dashboard"
- **Import failed:** Verify API credentials and file format
- **Commission not credited:** May take 24-48 hours to process
- **Domain not working:** Check DNS settings and allow up to 48 hours

### Getting Help
- Visit the Help Center: `https://vidiaspot.com/help`
- Email support: `support@vidiaspot.com`
- Live chat: Available 24/7 in the app
- Community forum: `community.vidiaspot.com`

## Best Practices

### For Sellers
- Maintain accurate inventory levels
- Respond to customer inquiries promptly
- Use high-quality product images
- Optimize product descriptions for SEO
- Monitor competitor pricing

### For Affiliates
- Target relevant audiences
- Create quality content around our platform
- Use multiple marketing channels
- Engage with your community
- Track your campaign performance

### For Customers
- Leave honest reviews
- Update your preferences regularly
- Take advantage of loyalty programs
- Refer friends to earn rewards
- Use secure payment methods

This guide will be updated regularly to reflect new features and best practices. For the most up-to-date information, visit the Help Center or contact our support team.