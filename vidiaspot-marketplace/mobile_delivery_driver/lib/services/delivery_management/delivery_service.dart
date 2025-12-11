import 'package:flutter/foundation.dart';
import 'package:geolocator/geolocator.dart';

class DeliveryManagementService extends ChangeNotifier {
  List<DeliveryPackage> _packages = [];
  List<DeliveryEvent> _deliveryEvents = [];
  
  List<DeliveryPackage> get packages => _packages;
  List<DeliveryEvent> get deliveryEvents => _deliveryEvents;
  
  // Add a package to the delivery list
  void addPackage(DeliveryPackage deliveryPackage) {
    _packages.add(deliveryPackage);
    notifyListeners();
  }
  
  // Update package status
  void updatePackageStatus(String packageId, DeliveryStatus status) {
    int index = _packages.indexWhere((pkg) => pkg.id == packageId);
    if (index != -1) {
      _packages[index] = _packages[index].copyWith(status: status);
      
      // Add delivery event
      _deliveryEvents.add(DeliveryEvent(
        id: DateTime.now().millisecondsSinceEpoch.toString(),
        packageId: packageId,
        status: status,
        timestamp: DateTime.now(),
        location: geolocatorPosition,
      ));
      
      notifyListeners();
    }
  }
  
  // Mark package as delivered with proof
  void markPackageAsDelivered(String packageId, {String? signature, String? photo, String? notes}) {
    int index = _packages.indexWhere((pkg) => pkg.id == packageId);
    if (index != -1) {
      _packages[index] = _packages[index].copyWith(
        status: DeliveryStatus.delivered,
        deliveredAt: DateTime.now(),
        signature: signature,
        photo: photo,
        notes: notes,
      );
      
      // Add delivery event
      _deliveryEvents.add(DeliveryEvent(
        id: DateTime.now().millisecondsSinceEpoch.toString(),
        packageId: packageId,
        status: DeliveryStatus.delivered,
        timestamp: DateTime.now(),
        location: geolocatorPosition,
        signature: signature,
        photo: photo,
        notes: notes,
      ));
      
      notifyListeners();
    }
  }
  
  // Get packages by status
  List<DeliveryPackage> getPackagesByStatus(DeliveryStatus status) {
    return _packages.where((pkg) => pkg.status == status).toList();
  }
  
  // Get packages by priority
  List<DeliveryPackage> getPackagesByPriority(int priority) {
    return _packages.where((pkg) => pkg.priority == priority).toList();
  }
  
  // Get ETA for a package
  DateTime? getEtaForPackage(String packageId) {
    int index = _packages.indexWhere((pkg) => pkg.id == packageId);
    if (index != -1) {
      return _packages[index].eta;
    }
    return null;
  }
  
  // Update ETA for a package
  void updateEtaForPackage(String packageId, DateTime newEta) {
    int index = _packages.indexWhere((pkg) => pkg.id == packageId);
    if (index != -1) {
      _packages[index] = _packages[index].copyWith(eta: newEta);
      notifyListeners();
    }
  }
}

// Placeholder for geolocator position
Position geolocatorPosition = Position(
  latitude: 0.0,
  longitude: 0.0,
  timestamp: DateTime.now(),
  accuracy: 0.0,
  altitude: 0.0,
  heading: 0.0,
  speed: 0.0,
  speedAccuracy: 0.0,
);

enum DeliveryStatus {
  pending,
  inTransit,
  outForDelivery,
  delivered,
  failed,
  returned,
}

class DeliveryPackage {
  final String id;
  final String trackingNumber;
  final String recipientName;
  final String recipientPhone;
  final String deliveryAddress;
  final double latitude;
  final double longitude;
  final String packageDetails;
  final double weight; // in kg
  final double volume; // in cubic meters
  final bool requiresSignature;
  final bool requiresPhoto;
  final bool requiresIdCheck;
  final int priority; // 1 (highest) to 5 (lowest)
  final DateTime? scheduledDeliveryTime;
  final DateTime? deliveredAt;
  final DateTime? eta;
  final DeliveryStatus status;
  final String? signature;
  final String? photo;
  final String? notes; // delivery notes or exceptions
  
  DeliveryPackage({
    required this.id,
    required this.trackingNumber,
    required this.recipientName,
    required this.recipientPhone,
    required this.deliveryAddress,
    required this.latitude,
    required this.longitude,
    required this.packageDetails,
    this.weight = 0.0,
    this.volume = 0.0,
    this.requiresSignature = true,
    this.requiresPhoto = true,
    this.requiresIdCheck = false,
    this.priority = 3,
    this.scheduledDeliveryTime,
    this.deliveredAt,
    this.eta,
    this.status = DeliveryStatus.pending,
    this.signature,
    this.photo,
    this.notes,
  });
  
  DeliveryPackage copyWith({
    String? id,
    String? trackingNumber,
    String? recipientName,
    String? recipientPhone,
    String? deliveryAddress,
    double? latitude,
    double? longitude,
    String? packageDetails,
    double? weight,
    double? volume,
    bool? requiresSignature,
    bool? requiresPhoto,
    bool? requiresIdCheck,
    int? priority,
    DateTime? scheduledDeliveryTime,
    DateTime? deliveredAt,
    DateTime? eta,
    DeliveryStatus? status,
    String? signature,
    String? photo,
    String? notes,
  }) {
    return DeliveryPackage(
      id: id ?? this.id,
      trackingNumber: trackingNumber ?? this.trackingNumber,
      recipientName: recipientName ?? this.recipientName,
      recipientPhone: recipientPhone ?? this.recipientPhone,
      deliveryAddress: deliveryAddress ?? this.deliveryAddress,
      latitude: latitude ?? this.latitude,
      longitude: longitude ?? this.longitude,
      packageDetails: packageDetails ?? this.packageDetails,
      weight: weight ?? this.weight,
      volume: volume ?? this.volume,
      requiresSignature: requiresSignature ?? this.requiresSignature,
      requiresPhoto: requiresPhoto ?? this.requiresPhoto,
      requiresIdCheck: requiresIdCheck ?? this.requiresIdCheck,
      priority: priority ?? this.priority,
      scheduledDeliveryTime: scheduledDeliveryTime ?? this.scheduledDeliveryTime,
      deliveredAt: deliveredAt ?? this.deliveredAt,
      eta: eta ?? this.eta,
      status: status ?? this.status,
      signature: signature ?? this.signature,
      photo: photo ?? this.photo,
      notes: notes ?? this.notes,
    );
  }
}

class DeliveryEvent {
  final String id;
  final String packageId;
  final DeliveryStatus status;
  final DateTime timestamp;
  final Position location;
  final String? signature;
  final String? photo;
  final String? notes;
  
  DeliveryEvent({
    required this.id,
    required this.packageId,
    required this.status,
    required this.timestamp,
    required this.location,
    this.signature,
    this.photo,
    this.notes,
  });
}