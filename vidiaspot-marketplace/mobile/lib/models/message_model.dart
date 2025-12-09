// lib/models/message_model.dart
class Message {
  final int id;
  final int conversationId;
  final int senderId;
  final int receiverId;
  final String content;
  final String messageType;
  final String language;
  final String? translatedContent;
  final String status;
  final bool isRead;
  final DateTime? readAt;
  final DateTime createdAt;
  final Map<String, dynamic>? metadata;

  Message({
    required this.id,
    required this.conversationId,
    required this.senderId,
    required this.receiverId,
    required this.content,
    this.messageType = 'text',
    this.language = 'en',
    this.translatedContent,
    this.status = 'sent',
    this.isRead = false,
    this.readAt,
    required this.createdAt,
    this.metadata,
  });

  factory Message.fromJson(Map<String, dynamic> json) {
    return Message(
      id: json['id'] ?? 0,
      conversationId: json['conversation_id'] ?? json['conversationId'] ?? 0,
      senderId: json['sender_id'] ?? json['senderId'] ?? 0,
      receiverId: json['receiver_id'] ?? json['receiverId'] ?? 0,
      content: json['content'] ?? '',
      messageType: json['message_type'] ?? json['messageType'] ?? 'text',
      language: json['language'] ?? 'en',
      translatedContent: json['translated_content'] ?? json['translatedContent'],
      status: json['status'] ?? 'sent',
      isRead: json['is_read'] ?? json['isRead'] ?? false,
      readAt: json['read_at'] != null ? DateTime.parse(json['read_at']) : null,
      createdAt: DateTime.parse(json['created_at'] ?? DateTime.now().toIso8601String()),
      metadata: json['metadata'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'conversation_id': conversationId,
      'sender_id': senderId,
      'receiver_id': receiverId,
      'content': content,
      'message_type': messageType,
      'language': language,
      'translated_content': translatedContent,
      'status': status,
      'is_read': isRead,
      'read_at': readAt?.toIso8601String(),
      'created_at': createdAt.toIso8601String(),
      'metadata': metadata,
    };
  }
}