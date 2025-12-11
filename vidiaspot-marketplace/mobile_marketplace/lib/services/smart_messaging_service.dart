// lib/services/smart_messaging_service.dart
import 'dart:convert';
import 'package:http/http.dart' as http;
import '../models/message_model.dart';
import '../models/conversation_model.dart';
import '../models/video_call_model.dart';
import '../models/scheduling_model.dart';
import '../models/escrow_model.dart';

class SmartMessagingService {
  final String baseUrl = 'http://10.0.2.2:8000'; // For Android emulator, adjust as needed
  final String token = ''; // This would be replaced with actual auth token
  
  // Headers for API requests
  Map<String, String> getHeaders() {
    return {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'Authorization': 'Bearer $token', // Replace with actual token management
    };
  }

  // Get smart replies suggestions
  Future<List<String>> getSmartReplies(String message, {Map<String, dynamic>? context}) async {
    final response = await http.post(
      Uri.parse('$baseUrl/messaging/smart-replies'),
      headers: getHeaders(),
      body: jsonEncode({
        'message': message,
        'context': context ?? {},
      }),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      return List<String>.from(data['replies']);
    } else {
      throw Exception('Failed to load smart replies');
    }
  }

  // Translate message
  Future<Map<String, dynamic>> translateMessage(String text, String from, String to) async {
    final response = await http.post(
      Uri.parse('$baseUrl/messaging/translate'),
      headers: getHeaders(),
      body: jsonEncode({
        'text': text,
        'from': from,
        'to': to,
      }),
    );

    if (response.statusCode == 200) {
      return jsonDecode(response.body);
    } else {
      throw Exception('Failed to translate message');
    }
  }

  // Get user conversations
  Future<List<Conversation>> getUserConversations() async {
    final response = await http.get(
      Uri.parse('$baseUrl/messaging/conversations'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      return (data['conversations'] as List)
          .map((json) => Conversation.fromJson(json))
          .toList();
    } else {
      throw Exception('Failed to load conversations');
    }
  }

  // Get conversation history
  Future<List<Message>> getConversationHistory(int conversationId) async {
    final response = await http.get(
      Uri.parse('$baseUrl/messaging/conversations/$conversationId'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      return (data['messages'] as List)
          .map((json) => Message.fromJson(json))
          .toList();
    } else {
      throw Exception('Failed to load conversation history');
    }
  }

  // Send a message
  Future<Message> sendMessage(int conversationId, String content, {String messageType = 'text'}) async {
    final response = await http.post(
      Uri.parse('$baseUrl/messaging/conversations/$conversationId/messages'),
      headers: getHeaders(),
      body: jsonEncode({
        'conversation_id': conversationId,
        'content': content,
        'message_type': messageType,
      }),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      return Message.fromJson(data['message']);
    } else {
      throw Exception('Failed to send message');
    }
  }

  // Start a conversation
  Future<Conversation> startConversation(int userId, {int? adId}) async {
    final response = await http.post(
      Uri.parse('$baseUrl/messaging/conversations'),
      headers: getHeaders(),
      body: jsonEncode({
        'user_id': userId,
        'ad_id': adId,
      }),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      return Conversation.fromJson(data['conversation']);
    } else {
      throw Exception('Failed to start conversation');
    }
  }

  // Schedule a meeting/pickup
  Future<Scheduling> scheduleMeeting({
    required int adId,
    required int recipientUserId,
    required String title,
    required DateTime scheduledDateTime,
    required String location,
    String? description,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/messaging/schedule'),
      headers: getHeaders(),
      body: jsonEncode({
        'ad_id': adId,
        'recipient_user_id': recipientUserId,
        'title': title,
        'scheduled_datetime': scheduledDateTime.toIso8601String(),
        'location': location,
        'description': description ?? '',
      }),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      return Scheduling.fromJson(data['schedule']);
    } else {
      throw Exception('Failed to schedule meeting');
    }
  }

  // Create a video call
  Future<VideoCall> createVideoCall({
    required int recipientUserId,
    int? adId,
    String callType = 'video',
    DateTime? scheduledAt,
  }) async {
    final Map<String, dynamic> body = {
      'recipient_user_id': recipientUserId,
      'call_type': callType,
    };

    if (adId != null) body['ad_id'] = adId;
    if (scheduledAt != null) body['scheduled_at'] = scheduledAt.toIso8601String();

    final response = await http.post(
      Uri.parse('$baseUrl/messaging/video-call'),
      headers: getHeaders(),
      body: jsonEncode(body),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      return VideoCall.fromJson(data['call']);
    } else {
      throw Exception('Failed to create video call');
    }
  }

  // Create an escrow
  Future<Escrow> createEscrow({
    required int transactionId,
    required int adId,
    required int sellerUserId,
    required double amount,
    String currency = 'NGN',
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/messaging/escrow'),
      headers: getHeaders(),
      body: jsonEncode({
        'transaction_id': transactionId,
        'ad_id': adId,
        'seller_user_id': sellerUserId,
        'amount': amount,
        'currency': currency,
      }),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      return Escrow.fromJson(data['escrow']);
    } else {
      throw Exception('Failed to create escrow');
    }
  }

  // Resolve escrow dispute
  Future<Map<String, dynamic>> resolveEscrowDispute(int escrowId, Map<String, dynamic> disputeDetails) async {
    final response = await http.post(
      Uri.parse('$baseUrl/messaging/escrow/$escrowId/resolve'),
      headers: getHeaders(),
      body: jsonEncode({'dispute_details': disputeDetails}),
    );

    if (response.statusCode == 200) {
      return jsonDecode(response.body);
    } else {
      throw Exception('Failed to resolve escrow dispute');
    }
  }

  // Release escrow funds
  Future<Map<String, dynamic>> releaseEscrow(int escrowId) async {
    final response = await http.post(
      Uri.parse('$baseUrl/messaging/escrow/$escrowId/release'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      return jsonDecode(response.body);
    } else {
      throw Exception('Failed to release escrow');
    }
  }

  // Verify escrow on blockchain
  Future<Map<String, dynamic>> verifyEscrowOnBlockchain(int escrowId) async {
    final response = await http.get(
      Uri.parse('$baseUrl/messaging/escrow/$escrowId/verify'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      return jsonDecode(response.body);
    } else {
      throw Exception('Failed to verify escrow on blockchain');
    }
  }

  // Get user notifications
  Future<List<Map<String, dynamic>>> getNotifications() async {
    final response = await http.get(
      Uri.parse('$baseUrl/messaging/notifications'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      return (data['notifications'] as List).cast<Map<String, dynamic>>();
    } else {
      throw Exception('Failed to load notifications');
    }
  }
}