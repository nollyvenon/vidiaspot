# Managing Different Ad Types in Various Page Sections

## Overview

The Vidiaspot Marketplace includes a comprehensive ad placement system that allows administrators to upload and manage different types of advertisements in various sections of web pages including top, side, bottom, and between content areas.

## Ad Placement Positions

The system supports multiple ad positions across the platform:

### 1. Top Banner (Header)
- **Position Code**: `top` or `header`
- **Description**: Ads displayed at the top of pages
- **Ideal For**: High-visibility campaigns
- **Dimensions**: Typically 728x90, 970x250, or full-width banners
- **Use Cases**: Brand awareness, promotional campaigns, featured sponsors

### 2. Side Column (Sidebar)  
- **Position Code**: `side` or `sidebar`
- **Description**: Ads in the side navigation column
- **Ideal For**: Category-specific or targeted ads
- **Dimensions**: Typically 300x250, 300x600, or 160x600
- **Use Cases**: Contextual targeting, local advertisements

### 3. Bottom Banner (Footer)
- **Position Code**: `bottom` or `footer`
- **Description**: Ads displayed at the bottom of pages
- **Ideal For**: Retention-focused campaigns
- **Dimensions**: Similar to top banners (728x90, 970x250)
- **Use Cases**: Remarketing, secondary offers

### 4. Between Content Sections
- **Position Code**: `between` or `content`
- **Description**: Ads inserted between content sections
- **Ideal For**: Native or integrated advertising
- **Dimensions**: Variable based on content width
- **Use Cases**: Content-relevant promotions, sponsored content

### 5. Inline Within Content
- **Position Code**: `inline`
- **Description**: Ads placed within content flow
- **Ideal For**: Native advertising that blends with content
- **Dimensions**: Matches content width and height
- **Use Cases**: Sponsored articles, recommended content

### 6. Popup/Interstitial
- **Position Code**: `popup` or `interstitial`
- **Description**: Full-screen ads that appear over content
- **Ideal For**: High-impact promotional messages
- **Dimensions**: Full viewport or modal
- **Use Cases**: Time-sensitive announcements, lead generation

## Ad Types Supported

### 1. Banner Ads
- **Type Code**: `banner`
- **Content**: Static or animated images
- **Format**: JPEG, PNG, GIF, or HTML5
- **Use Case**: Traditional display advertising

### 2. Text Ads
- **Type Code**: `text`
- **Content**: Title, description, and link
- **Format**: Simple HTML or plain text
- **Use Case**: Cost-effective advertising with high CTR

### 3. Image Ads
- **Type Code**: `image`
- **Content**: Single promotional image
- **Format**: High-resolution JPEG or PNG
- **Use Case**: Visual product showcase

### 4. Video Ads
- **Type Code**: `video`
- **Content**: Video promotional content
- **Format**: MP4, WebM, or streaming video
- **Use Case**: Rich media storytelling

### 5. Native Ads
- **Type Code**: `native`
- **Content**: Blends with page content
- **Format**: Matches site's appearance
- **Use Case**: Non-disruptive advertising

### 6. HTML Ads
- **Type Code**: `html`
- **Content**: Custom HTML/CSS/JavaScript code
- **Format**: Rich interactive content
- **Use Case**: Custom experiences and animations

## Admin Management Interface

### Creating New Ad Placements

Administrators can create ad placements through the admin panel with the following parameters:

```json
{
  "name": "Homepage Top Banner",
  "location": "top",  // Position: top, side, bottom, between, etc.
  "type": "banner",   // Type: banner, text, image, video, native, html
  "size": "728x90",
  "priority": 1,      // Higher priority ads appear first
  "is_active": true,
  "content": {
    "image_url": "https://example.com/banner.jpg",
    "link_url": "https://example.com",
    "alt_text": "Example Product Banner"
  },
  "starts_at": "2025-01-01T00:00:00Z",
  "expires_at": "2025-12-31T23:59:59Z",
  "target_pages": "homepage",  // homepage, category, ad-detail, all
  "targeting_rules": {
    "categories": ["electronics", "mobile-phones"],
    "locations": ["Lagos", "Abuja"],
    "user_types": ["buyer", "seller"]
  }
}
```

### Managing Existing Ad Placements

Administrators can:

1. **View All Placements**: Browse all active and inactive ad placements
2. **Update Content**: Modify ad content, timing, and targeting
3. **Toggle Active Status**: Enable/disable placements without deleting
4. **Adjust Priority**: Control display order for competing ads
5. **Modify Targeting**: Update audience and page targeting rules
6. **Schedule Campaigns**: Set start/end dates for promotional periods

### Targeting Options

#### Page-Level Targeting
- **Homepage**: Ads appearing on the main landing page
- **Category Pages**: Ads specific to product categories
- **Ad Detail Pages**: Ads appearing on individual ad pages
- **Search Results**: Ads in search result pages
- **User Dashboard**: Ads in user-specific areas

#### Demographic Targeting
- **Categories**: Target specific product categories
- **Locations**: Geographic targeting by city or region
- **User Types**: Buyer vs. seller targeting
- **Device Types**: Mobile vs. desktop optimization

## Implementation Details

### Caching Strategy
- Ad placements are cached using SQLite to reduce MySQL reads
- Cache automatically refreshes after 1 hour or when admin updates placements
- Invalidated immediately when ads are activated/deactivated

### Performance Optimization
- Lazy loading for images and videos
- Asynchronous loading to prevent blocking page content
- CDN delivery for ad assets

### Security Measures
- Content filtering for HTML ads
- Link validation to prevent malicious redirects
- Admin approval workflow for new ad content
- Rate limiting on ad impression counting

## API Endpoints

### For Admins:
- `GET /api/admin/ad-placements` - List all ad placements
- `POST /api/admin/ad-placements` - Create new ad placement
- `PUT /api/admin/ad-placements/{id}` - Update ad placement
- `PATCH /api/admin/ad-placements/{id}/status` - Toggle status
- `DELETE /api/admin/ad-placements/{id}` - Delete ad placement
- `GET /api/admin/ad-placements/stats` - View statistics

### For Application:
- `GET /api/ad-placements/active` - Retrieve active placements by position
- `POST /api/ad-placements/{id}/impression` - Log ad impression
- `POST /api/ad-placements/{id}/click` - Track ad clicks

## Best Practices

### For Administrators:
1. **Balance Revenue & UX**: Avoid excessive ad density
2. **Rotate Fresh Content**: Regularly update ad content
3. **Monitor Performance**: Track CTR, conversion, and user feedback
4. **Optimize Timing**: Schedule campaigns during peak traffic
5. **A/B Testing**: Test different ad formats and placements

### For Advertisers:
1. **High-Quality Assets**: Use high-resolution, engaging visuals
2. **Clear CTAs**: Include compelling call-to-action buttons
3. **Relevant Targeting**: Match ads to appropriate categories/audiences
4. **Compliance**: Follow platform advertising guidelines
5. **Performance Monitoring**: Track campaign effectiveness

## Analytics & Reporting

### Available Metrics:
- Impressions: Total ad views
- Clicks: User interactions
- CTR: Click-through rate
- Conversions: Goal completions
- Revenue: Earnings by placement/type

### Reports Include:
- Daily performance summaries
- Weekly trend analysis
- Monthly revenue reports
- Ad type effectiveness comparison
- Placement optimization recommendations