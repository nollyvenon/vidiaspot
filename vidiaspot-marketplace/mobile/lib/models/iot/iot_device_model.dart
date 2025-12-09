// lib/models/iot/iot_device_model.dart
class IoTDevice {
  final int id;
  final String name;
  final String deviceId;
  final int userId;
  final int? adId;
  final String deviceType;
  final String brand;
  final String model;
  final String status;
  final String connectionStatus;
  final DateTime? lastSeen;
  final String? location;
  final Map<String, dynamic>? specs;
  final List<String>? supportedProtocols;
  final String? firmwareVersion;
  final bool isConnected;
  final bool isRegistered;
  final DateTime? registrationDate;
  final Map<String, dynamic>? metadata;

  IoTDevice({
    required this.id,
    required this.name,
    required this.deviceId,
    required this.userId,
    this.adId,
    required this.deviceType,
    required this.brand,
    required this.model,
    required this.status,
    required this.connectionStatus,
    this.lastSeen,
    this.location,
    this.specs,
    this.supportedProtocols,
    this.firmwareVersion,
    required this.isConnected,
    required this.isRegistered,
    this.registrationDate,
    this.metadata,
  });

  factory IoTDevice.fromJson(Map<String, dynamic> json) {
    return IoTDevice(
      id: json['id'] ?? 0,
      name: json['name'] ?? '',
      deviceId: json['device_id'] ?? '',
      userId: json['user_id'] ?? 0,
      adId: json['ad_id'],
      deviceType: json['device_type'] ?? '',
      brand: json['brand'] ?? '',
      model: json['model'] ?? '',
      status: json['status'] ?? 'active',
      connectionStatus: json['connection_status'] ?? 'disconnected',
      lastSeen: json['last_seen'] != null ? DateTime.parse(json['last_seen']) : null,
      location: json['location'],
      specs: json['specs'] ?? {},
      supportedProtocols: List<String>.from(json['supported_protocols'] ?? []),
      firmwareVersion: json['firmware_version'],
      isConnected: json['is_connected'] ?? false,
      isRegistered: json['is_registered'] ?? false,
      registrationDate: json['registration_date'] != null ? DateTime.parse(json['registration_date']) : null,
      metadata: json['metadata'] ?? {},
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'device_id': deviceId,
      'user_id': userId,
      'ad_id': adId,
      'device_type': deviceType,
      'brand': brand,
      'model': model,
      'status': status,
      'connection_status': connectionStatus,
      'last_seen': lastSeen?.toIso8601String(),
      'location': location,
      'specs': specs,
      'supported_protocols': supportedProtocols,
      'firmware_version': firmwareVersion,
      'is_connected': isConnected,
      'is_registered': isRegistered,
      'registration_date': registrationDate?.toIso8601String(),
      'metadata': metadata,
    };
  }
}