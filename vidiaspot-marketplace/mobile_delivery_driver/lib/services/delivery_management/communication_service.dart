import 'package:flutter/foundation.dart';

class CommunicationService extends ChangeNotifier {
  List<Conversation> _conversations = [];
  
  List<Conversation> get conversations => _conversations;
  
  // Create a new conversation with a customer
  void createConversation(String customerId, String customerName, String initialMessage) {
    final conversation = Conversation(
      id: DateTime.now().millisecondsSinceEpoch.toString(),
      customerId: customerId,
      customerName: customerName,
      messages: [
        Message(
          id: DateTime.now().millisecondsSinceEpoch.toString(),
          sender: 'driver',
          timestamp: DateTime.now(),
          content: initialMessage,
        ),
      ],
      lastMessageAt: DateTime.now(),
      unreadCount: 0,
    );
    
    _conversations.add(conversation);
    notifyListeners();
  }
  
  // Send a message in a conversation
  void sendMessage(String conversationId, String content) {
    int index = _conversations.indexWhere((conv) => conv.id == conversationId);
    if (index != -1) {
      _conversations[index].messages.add(
        Message(
          id: DateTime.now().millisecondsSinceEpoch.toString(),
          sender: 'driver',
          timestamp: DateTime.now(),
          content: content,
        ),
      );
      
      _conversations[index] = _conversations[index].copyWith(
        lastMessageAt: DateTime.now(),
        unreadCount: 0, // Reset unread count when driver sends a message
      );
      
      notifyListeners();
    }
  }
  
  // Receive a message from customer
  void receiveMessage(String conversationId, String content) {
    int index = _conversations.indexWhere((conv) => conv.id == conversationId);
    if (index != -1) {
      _conversations[index].messages.add(
        Message(
          id: DateTime.now().millisecondsSinceEpoch.toString(),
          sender: 'customer',
          timestamp: DateTime.now(),
          content: content,
        ),
      );
      
      _conversations[index] = _conversations[index].copyWith(
        lastMessageAt: DateTime.now(),
        unreadCount: _conversations[index].unreadCount + 1,
      );
      
      notifyListeners();
    }
  }
  
  // Get conversation by ID
  Conversation? getConversation(String conversationId) {
    return _conversations.firstWhere((conv) => conv.id == conversationId);
  }
  
  // Mark conversation as read
  void markAsRead(String conversationId) {
    int index = _conversations.indexWhere((conv) => conv.id == conversationId);
    if (index != -1) {
      _conversations[index] = _conversations[index].copyWith(unreadCount: 0);
      notifyListeners();
    }
  }
  
  // Get unread message count
  int getUnreadCount() {
    return _conversations.fold(0, (sum, conv) => sum + conv.unreadCount);
  }
}

class Conversation {
  final String id;
  final String customerId;
  final String customerName;
  List<Message> messages;
  DateTime lastMessageAt;
  int unreadCount;
  
  Conversation({
    required this.id,
    required this.customerId,
    required this.customerName,
    required this.messages,
    required this.lastMessageAt,
    required this.unreadCount,
  });
  
  Conversation copyWith({
    String? id,
    String? customerId,
    String? customerName,
    List<Message>? messages,
    DateTime? lastMessageAt,
    int? unreadCount,
  }) {
    return Conversation(
      id: id ?? this.id,
      customerId: customerId ?? this.customerId,
      customerName: customerName ?? this.customerName,
      messages: messages ?? this.messages,
      lastMessageAt: lastMessageAt ?? this.lastMessageAt,
      unreadCount: unreadCount ?? this.unreadCount,
    );
  }
}

class Message {
  final String id;
  final String sender; // 'driver' or 'customer'
  final DateTime timestamp;
  final String content;
  
  Message({
    required this.id,
    required this.sender,
    required this.timestamp,
    required this.content,
  });
}