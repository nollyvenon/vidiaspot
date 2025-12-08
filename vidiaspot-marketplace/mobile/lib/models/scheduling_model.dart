// lib/models/scheduling_model.dart
class Scheduling {
  final int id;
  final int? adId;
  final int initiatorUserId;
  final int recipientUserId;
  final String title;
  final String? description;
  final DateTime scheduledDatetime;
  final String location;
  final String status;
  final String type;
  final List<dynamic>? participants;
  final Map<String, dynamic>? preferences;
  final DateTime? confirmedAt;
  final DateTime? completedAt;
  final String? notes;

  Scheduling({
    required this.id,
    this.adId,
    required this.initiatorUserId,
    required this.recipientUserId,
    required this.title,
    this.description,
    required this.scheduledDatetime,
    required this.location,
    this.status = 'pending',
    this.type = 'pickup',
    this.participants,
    this.preferences,
    this.confirmedAt,
    this.completedAt,
    this.notes,
  });

  factory Scheduling.fromJson(Map<String, dynamic> json) {
    return Scheduling(
      id: json['id'] ?? 0,
      adId: json['ad_id'] ?? json['adId'],
      initiatorUserId: json['initiator_user_id'] ?? json['initiatorUserId'] ?? 0,
      recipientUserId: json['recipient_user_id'] ?? json['recipientUserId'] ?? 0,
      title: json['title'] ?? '',
      description: json['description'],
      scheduledDatetime: DateTime.parse(json['scheduled_datetime'] ?? DateTime.now().toIso8601String()),
      location: json['location'] ?? '',
      status: json['status'] ?? 'pending',
      type: json['type'] ?? 'pickup',
      participants: json['participants'],
      preferences: json['preferences'],
      confirmedAt: json['confirmed_at'] != null ? DateTime.parse(json['confirmed_at']) : null,
      completedAt: json['completed_at'] != null ? DateTime.parse(json['completed_at']) : null,
      notes: json['notes'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'ad_id': adId,
      'initiator_user_id': initiatorUserId,
      'recipient_user_id': recipientUserId,
      'title': title,
      'description': description,
      'scheduled_datetime': scheduledDatetime.toIso8601String(),
      'location': location,
      'status': status,
      'type': type,
      'participants': participants,
      'preferences': preferences,
      'confirmed_at': confirmedAt?.toIso8601String(),
      'completed_at': completedAt?.toIso8601String(),
      'notes': notes,
    };
  }
}