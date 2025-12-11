# VidiaSpot Shop Owner App

This is a dedicated mobile application for e-commerce shop owners to manage their stores on the VidiaSpot marketplace platform.

## Features

- **Dashboard**: Overview of store performance with key metrics
- **Order Management**: View, track, and manage customer orders
- **Product Management**: Add, edit, and manage products
- **Inventory Management**: Track stock levels and manage inventory
- **Analytics**: Sales metrics and performance tracking
- **Customer Management**: View and manage customer information
- **Store Settings**: Configure store details and preferences

## Architecture

This Flutter application connects to the same backend as the main VidiaSpot marketplace but provides a specialized interface for shop owners to manage their individual stores.

## Directory Structure

```
lib/
├── core/
│   └── services/
├── models/
├── ui/
│   ├── providers/
│   └── screens/
└── utils/
```

## Setup

1. Clone the repository
2. Run `flutter pub get` to install dependencies
3. Configure your backend API endpoints
4. Run the app with `flutter run`

## Dependencies

- Flutter SDK
- Provider (state management)
- http (API calls)
- shared_preferences (local storage)
- syncfusion_flutter_charts (analytics visualization)
- firebase_core (authentication/notifications)

## Notes

This app is designed to work with the VidiaSpot marketplace backend system to provide shop owners with the tools necessary to manage their stores effectively.