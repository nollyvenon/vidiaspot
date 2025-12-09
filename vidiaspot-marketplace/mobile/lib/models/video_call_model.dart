// lib/models/video_call_model.dart
class VideoCall {
  final int id;
  final int? adId;
  final int initiatorUserId;
  final int recipientUserId;
  final String roomId;
  final String callStatus;
  final String callType;
  final DateTime? scheduledAt;
  final DateTime? startedAt;
  final DateTime? endedAt;
  final int? duration;
  final List<dynamic>? participants;
  final Map<String, dynamic>? settings;

  VideoCall({
    required this.id,
    this.adId,
    required this.initiatorUserId,
    required this.recipientUserId,
    required this.roomId,
    this.callStatus = 'pending',
    this.callType = 'video',
    this.scheduledAt,
    this.startedAt,
    this.endedAt,
    this.duration,
    this.participants,
    this.settings,
  });

  factory VideoCall.fromJson(Map<String, dynamic> json) {
    return VideoCall(
      id: json['id'] ?? 0,
      adId: json['ad_id'] ?? json['adId'],
      initiatorUserId: json['initiator_user_id'] ?? json['initiatorUserId'] ?? 0,
      recipientUserId: json['recipient_user_id'] ?? json['recipientUserId'] ?? 0,
      roomId: json['room_id'] ?? json['roomId'] ?? '',
      callStatus: json['call_status'] ?? json['callStatus'] ?? 'pending',
      callType: json['call_type'] ?? json['callType'] ?? 'video',
      scheduledAt: json['scheduled_at'] != null ? DateTime.parse(json['scheduled_at']) : null,
      startedAt: json['started_at'] != null ? DateTime.parse(json['started_at']) : null,
      endedAt: json['ended_at'] != null ? DateTime.parse(json['ended_at']) : null,
      duration: json['duration'],
      participants: json['participants'],
      settings: json['settings'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'ad_id': adId,
      'initiator_user_id': initiatorUserId,
      'recipient_user_id': recipientUserId,
      'room_id': roomId,
      'call_status': callStatus,
      'call_type': callType,
      'scheduled_at': scheduledAt?.toIso8601String(),
      'started_at': startedAt?.toIso8601String(),
      'ended_at': endedAt?.toIso8601String(),
      'duration': duration,
      'participants': participants,
      'settings': settings,
    };
  }
}