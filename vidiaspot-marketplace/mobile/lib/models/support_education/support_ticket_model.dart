// lib/models/support_education/support_ticket_model.dart
class SupportTicket {
  final int id;
  final String userId;
  final String subject;
  final String description;
  final String category; // 'technical', 'billing', 'trading', 'account', 'other'
  final String priority; // 'low', 'medium', 'high', 'urgent'
  final String status; // 'open', 'in_progress', 'resolved', 'closed'
  final String assignedTo; // support agent ID
  final int departmentId;
  final List<SupportMessage> messages;
  final DateTime createdAt;
  final DateTime updatedAt;
  final DateTime? resolvedAt;
  final String? resolutionNote;
  final int satisfactionRating; // 1-5 stars
  final String? satisfactionComment;
  final bool isEscalated;
  final DateTime? escalatedAt;
  final String? escalationReason;

  SupportTicket({
    required this.id,
    required this.userId,
    required this.subject,
    required this.description,
    required this.category,
    required this.priority,
    required this.status,
    required this.assignedTo,
    required this.departmentId,
    required this.messages,
    required this.createdAt,
    required this.updatedAt,
    this.resolvedAt,
    this.resolutionNote,
    required this.satisfactionRating,
    this.satisfactionComment,
    required this.isEscalated,
    this.escalatedAt,
    this.escalationReason,
  });

  factory SupportTicket.fromJson(Map<String, dynamic> json) {
    return SupportTicket(
      id: json['id'] ?? 0,
      userId: json['user_id'] ?? '',
      subject: json['subject'] ?? '',
      description: json['description'] ?? '',
      category: json['category'] ?? 'other',
      priority: json['priority'] ?? 'medium',
      status: json['status'] ?? 'open',
      assignedTo: json['assigned_to'] ?? '',
      departmentId: json['department_id'] ?? 0,
      messages: (json['messages'] as List?)
          ?.map((m) => SupportMessage.fromJson(m))
          .toList() ?? [],
      createdAt: DateTime.parse(json['created_at'] ?? DateTime.now().toIso8601String()),
      updatedAt: DateTime.parse(json['updated_at'] ?? DateTime.now().toIso8601String()),
      resolvedAt: json['resolved_at'] != null ? DateTime.parse(json['resolved_at']) : null,
      resolutionNote: json['resolution_note'],
      satisfactionRating: json['satisfaction_rating'] ?? 0,
      satisfactionComment: json['satisfaction_comment'],
      isEscalated: json['is_escalated'] ?? false,
      escalatedAt: json['escalated_at'] != null ? DateTime.parse(json['escalated_at']) : null,
      escalationReason: json['escalation_reason'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'user_id': userId,
      'subject': subject,
      'description': description,
      'category': category,
      'priority': priority,
      'status': status,
      'assigned_to': assignedTo,
      'department_id': departmentId,
      'messages': messages.map((m) => m.toJson()).toList(),
      'created_at': createdAt.toIso8601String(),
      'updated_at': updatedAt.toIso8601String(),
      'resolved_at': resolvedAt?.toIso8601String(),
      'resolution_note': resolutionNote,
      'satisfaction_rating': satisfactionRating,
      'satisfaction_comment': satisfactionComment,
      'is_escalated': isEscalated,
      'escalated_at': escalatedAt?.toIso8601String(),
      'escalation_reason': escalationReason,
    };
  }
}

class SupportMessage {
  final int id;
  final int ticketId;
  final String senderId;
  final String senderType; // 'user', 'agent', 'system'
  final String message;
  final List<String> attachments; // URLs to attached files
  final DateTime createdAt;
  final bool isRead;
  final String? language; // for multi-language support
  final String? translatedMessage; // if message was translated

  SupportMessage({
    required this.id,
    required this.ticketId,
    required this.senderId,
    required this.senderType,
    required this.message,
    required this.attachments,
    required this.createdAt,
    required this.isRead,
    this.language,
    this.translatedMessage,
  });

  factory SupportMessage.fromJson(Map<String, dynamic> json) {
    return SupportMessage(
      id: json['id'] ?? 0,
      ticketId: json['ticket_id'] ?? 0,
      senderId: json['sender_id'] ?? '',
      senderType: json['sender_type'] ?? 'user',
      message: json['message'] ?? '',
      attachments: List<String>.from(json['attachments'] ?? []),
      createdAt: DateTime.parse(json['created_at'] ?? DateTime.now().toIso8601String()),
      isRead: json['is_read'] ?? false,
      language: json['language'],
      translatedMessage: json['translated_message'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'ticket_id': ticketId,
      'sender_id': senderId,
      'sender_type': senderType,
      'message': message,
      'attachments': attachments,
      'created_at': createdAt.toIso8601String(),
      'is_read': isRead,
      'language': language,
      'translated_message': translatedMessage,
    };
  }
}

class SupportAgent {
  final int id;
  final String name;
  final String email;
  final String department;
  final String language;
  final int rating;
  final int totalTickets;
  final int resolvedTickets;
  final String status; // 'available', 'busy', 'offline'
  final DateTime lastActive;
  final List<String> specializations; // areas of expertise
  final bool isOnline;

  SupportAgent({
    required this.id,
    required this.name,
    required this.email,
    required this.department,
    required this.language,
    required this.rating,
    required this.totalTickets,
    required this.resolvedTickets,
    required this.status,
    required this.lastActive,
    required this.specializations,
    required this.isOnline,
  });

  factory SupportAgent.fromJson(Map<String, dynamic> json) {
    return SupportAgent(
      id: json['id'] ?? 0,
      name: json['name'] ?? '',
      email: json['email'] ?? '',
      department: json['department'] ?? '',
      language: json['language'] ?? 'English',
      rating: json['rating'] ?? 0,
      totalTickets: json['total_tickets'] ?? 0,
      resolvedTickets: json['resolved_tickets'] ?? 0,
      status: json['status'] ?? 'offline',
      lastActive: DateTime.parse(json['last_active'] ?? DateTime.now().toIso8601String()),
      specializations: List<String>.from(json['specializations'] ?? []),
      isOnline: json['is_online'] ?? false,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'email': email,
      'department': department,
      'language': language,
      'rating': rating,
      'total_tickets': totalTickets,
      'resolved_tickets': resolvedTickets,
      'status': status,
      'last_active': lastActive.toIso8601String(),
      'specializations': specializations,
      'is_online': isOnline,
    };
  }
}

class KnowledgeBaseArticle {
  final int id;
  final String title;
  final String content;
  final String category;
  final List<String> tags;
  final String author;
  final DateTime createdAt;
  final DateTime updatedAt;
  final int viewCount;
  final int likeCount;
  final int dislikeCount;
  final int helpfulCount; // number of users who found article helpful
  final bool isPublished;
  final String language;

  KnowledgeBaseArticle({
    required this.id,
    required this.title,
    required this.content,
    required this.category,
    required this.tags,
    required this.author,
    required this.createdAt,
    required this.updatedAt,
    required this.viewCount,
    required this.likeCount,
    required this.dislikeCount,
    required this.helpfulCount,
    required this.isPublished,
    required this.language,
  });

  factory KnowledgeBaseArticle.fromJson(Map<String, dynamic> json) {
    return KnowledgeBaseArticle(
      id: json['id'] ?? 0,
      title: json['title'] ?? '',
      content: json['content'] ?? '',
      category: json['category'] ?? '',
      tags: List<String>.from(json['tags'] ?? []),
      author: json['author'] ?? 'System',
      createdAt: DateTime.parse(json['created_at'] ?? DateTime.now().toIso8601String()),
      updatedAt: DateTime.parse(json['updated_at'] ?? DateTime.now().toIso8601String()),
      viewCount: json['view_count'] ?? 0,
      likeCount: json['like_count'] ?? 0,
      dislikeCount: json['dislike_count'] ?? 0,
      helpfulCount: json['helpful_count'] ?? 0,
      isPublished: json['is_published'] ?? true,
      language: json['language'] ?? 'English',
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'title': title,
      'content': content,
      'category': category,
      'tags': tags,
      'author': author,
      'created_at': createdAt.toIso8601String(),
      'updated_at': updatedAt.toIso8601String(),
      'view_count': viewCount,
      'like_count': likeCount,
      'dislike_count': dislikeCount,
      'helpful_count': helpfulCount,
      'is_published': isPublished,
      'language': language,
    };
  }
}

class ChatSession {
  final int id;
  final String userId;
  final String agentId;
  final String status; // 'active', 'closed', 'transferred'
  final String language;
  final DateTime startedAt;
  final DateTime? endedAt;
  final List<ChatMessage> messages;
  final String? satisfactionRating; // 'very_satisfied', 'satisfied', 'neutral', 'dissatisfied', 'very_dissatisfied'
  final String? resolutionSummary;

  ChatSession({
    required this.id,
    required this.userId,
    required this.agentId,
    required this.status,
    required this.language,
    required this.startedAt,
    this.endedAt,
    required this.messages,
    this.satisfactionRating,
    this.resolutionSummary,
  });

  factory ChatSession.fromJson(Map<String, dynamic> json) {
    return ChatSession(
      id: json['id'] ?? 0,
      userId: json['user_id'] ?? '',
      agentId: json['agent_id'] ?? '',
      status: json['status'] ?? 'active',
      language: json['language'] ?? 'English',
      startedAt: DateTime.parse(json['started_at'] ?? DateTime.now().toIso8601String()),
      endedAt: json['ended_at'] != null ? DateTime.parse(json['ended_at']) : null,
      messages: (json['messages'] as List?)
          ?.map((m) => ChatMessage.fromJson(m))
          .toList() ?? [],
      satisfactionRating: json['satisfaction_rating'],
      resolutionSummary: json['resolution_summary'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'user_id': userId,
      'agent_id': agentId,
      'status': status,
      'language': language,
      'started_at': startedAt.toIso8601String(),
      'ended_at': endedAt?.toIso8601String(),
      'messages': messages.map((m) => m.toJson()).toList(),
      'satisfaction_rating': satisfactionRating,
      'resolution_summary': resolutionSummary,
    };
  }
}

class ChatMessage {
  final int id;
  final int sessionId;
  final String senderId;
  final String senderType; // 'user', 'agent'
  final String message;
  final DateTime timestamp;
  final bool isRead;
  final String? language; // original language before translation
  final String? translatedMessage; // translated message if applicable

  ChatMessage({
    required this.id,
    required this.sessionId,
    required this.senderId,
    required this.senderType,
    required this.message,
    required this.timestamp,
    required this.isRead,
    this.language,
    this.translatedMessage,
  });

  factory ChatMessage.fromJson(Map<String, dynamic> json) {
    return ChatMessage(
      id: json['id'] ?? 0,
      sessionId: json['session_id'] ?? 0,
      senderId: json['sender_id'] ?? '',
      senderType: json['sender_type'] ?? 'user',
      message: json['message'] ?? '',
      timestamp: DateTime.parse(json['timestamp'] ?? DateTime.now().toIso8601String()),
      isRead: json['is_read'] ?? false,
      language: json['language'],
      translatedMessage: json['translated_message'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'session_id': sessionId,
      'sender_id': senderId,
      'sender_type': senderType,
      'message': message,
      'timestamp': timestamp.toIso8601String(),
      'is_read': isRead,
      'language': language,
      'translated_message': translatedMessage,
    };
  }
}