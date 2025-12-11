// lib/models/conversation_model.dart
class Conversation {
  final int id;
  final int? adId;
  final int user1Id;
  final int user2Id;
  final String? title;
  final String? description;
  final bool isActive;
  final DateTime? lastMessageAt;
  final Map<String, dynamic>? participantsInfo;
  final int unreadCount;

  Conversation({
    required this.id,
    this.adId,
    required this.user1Id,
    required this.user2Id,
    this.title,
    this.description,
    this.isActive = true,
    this.lastMessageAt,
    this.participantsInfo,
    this.unreadCount = 0,
  });

  factory Conversation.fromJson(Map<String, dynamic> json) {
    return Conversation(
      id: json['id'] ?? 0,
      adId: json['ad_id'] ?? json['adId'],
      user1Id: json['user1_id'] ?? json['user1Id'] ?? 0,
      user2Id: json['user2_id'] ?? json['user2Id'] ?? 0,
      title: json['title'],
      description: json['description'],
      isActive: json['is_active'] ?? json['isActive'] ?? true,
      lastMessageAt: json['last_message_at'] != null 
          ? DateTime.parse(json['last_message_at']) 
          : null,
      participantsInfo: json['participants_info'] ?? json['participantsInfo'],
      unreadCount: json['unread_count'] ?? 0,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'ad_id': adId,
      'user1_id': user1Id,
      'user2_id': user2Id,
      'title': title,
      'description': description,
      'is_active': isActive,
      'last_message_at': lastMessageAt?.toIso8601String(),
      'participants_info': participantsInfo,
      'unread_count': unreadCount,
    };
  }
}