# VidiaSpot Marketplace Mobile App

A Flutter mobile application for the VidiaSpot marketplace platform.

## Features

- Browse ads
- Search for items
- Post new ads
- Communicate with sellers
- Manage user profile
- View transaction history

## Prerequisites

- Flutter SDK
- Dart SDK
- Android Studio / VS Code with Flutter extension

## Setup Instructions

1. Clone the repository
2. Navigate to the `mobile` directory
3. Run `flutter pub get` to install dependencies
4. Run `flutter run` to launch the app

## Project Structure

```
lib/
├── main.dart              # App entry point
├── models/                # Data models
├── screens/               # UI screens
├── widgets/               # Reusable UI components
├── services/              # API and utility services
└── utils/                 # Utility functions
```

## API Integration

The app connects to the backend API located at `http://localhost:8000/api` (development) or your production endpoint.

## Dependencies

- http: For API requests
- shared_preferences: For local storage
- image_picker: For photo selection
- cached_network_image: For efficient image loading