// lib/services/support_education/support_service.dart
import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import '../../models/support_education/support_ticket_model.dart';

class SupportService {
  final String baseUrl = 'http://10.0.2.2:8000/api';
  String? _authToken;

  SupportService() {
    _loadAuthToken();
  }

  Future<void> _loadAuthToken() async {
    SharedPreferences prefs = await SharedPreferences.getInstance();
    _authToken = prefs.getString('auth_token');
  }

  Map<String, String> getHeaders() {
    Map<String, String> headers = {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    };

    if (_authToken != null) {
      headers['Authorization'] = 'Bearer $_authToken';
    }

    return headers;
  }

  // Create a new support ticket
  Future<SupportTicket> createSupportTicket({
    required String subject,
    required String description,
    required String category,
    String priority = 'medium',
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/support/tickets'),
      headers: getHeaders(),
      body: jsonEncode({
        'subject': subject,
        'description': description,
        'category': category,
        'priority': priority,
      }),
    );

    if (response.statusCode == 201) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return SupportTicket.fromJson(data['data']);
      } else {
        throw Exception(data['message'] ?? 'Failed to create support ticket');
      }
    } else {
      throw Exception('Failed to create support ticket: ${response.statusCode}');
    }
  }

  // Get user's support tickets
  Future<List<SupportTicket>> getUserTickets({
    String? status,
    String? category,
    String? priority,
    int page = 1,
    int perPage = 20,
  }) async {
    String url = '$baseUrl/support/tickets?page=$page&per_page=$perPage';
    if (status != null) url += '&status=$status';
    if (category != null) url += '&category=$category';
    if (priority != null) url += '&priority=$priority';

    final response = await http.get(Uri.parse(url), headers: getHeaders());

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] ?? false) {
        List<dynamic> tickets = data['data'];
        return tickets.map((json) => SupportTicket.fromJson(json)).toList();
      } else {
        throw Exception(data['message'] ?? 'Failed to load support tickets');
      }
    } else {
      throw Exception('Failed to load support tickets: ${response.statusCode}');
    }
  }

  // Get support ticket by ID
  Future<SupportTicket> getSupportTicket(int ticketId) async {
    final response = await http.get(
      Uri.parse('$baseUrl/support/tickets/$ticketId'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return SupportTicket.fromJson(data['data']);
      } else {
        throw Exception(data['message'] ?? 'Failed to load support ticket');
      }
    } else {
      throw Exception('Failed to load support ticket: ${response.statusCode}');
    }
  }

  // Add message to support ticket
  Future<SupportMessage> addMessageToTicket({
    required int ticketId,
    required String message,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/support/tickets/$ticketId/messages'),
      headers: getHeaders(),
      body: jsonEncode({
        'message': message,
      }),
    );

    if (response.statusCode == 201) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return SupportMessage.fromJson(data['data']);
      } else {
        throw Exception(data['message'] ?? 'Failed to add message to ticket');
      }
    } else {
      throw Exception('Failed to add message to ticket: ${response.statusCode}');
    }
  }

  // Update ticket status
  Future<SupportTicket> updateTicketStatus({
    required int ticketId,
    required String newStatus,
  }) async {
    final response = await http.put(
      Uri.parse('$baseUrl/support/tickets/$ticketId/status'),
      headers: getHeaders(),
      body: jsonEncode({
        'status': newStatus,
      }),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return SupportTicket.fromJson(data['data']);
      } else {
        throw Exception(data['message'] ?? 'Failed to update ticket status');
      }
    } else {
      throw Exception('Failed to update ticket status: ${response.statusCode}');
    }
  }

  // Get available support agents
  Future<List<SupportAgent>> getAvailableSupportAgents() async {
    final response = await http.get(
      Uri.parse('$baseUrl/support/agents/available'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] ?? false) {
        List<dynamic> agents = data['data'];
        return agents.map((json) => SupportAgent.fromJson(json)).toList();
      } else {
        throw Exception(data['message'] ?? 'Failed to load support agents');
      }
    } else {
      throw Exception('Failed to load support agents: ${response.statusCode}');
    }
  }

  // Start a new live chat session
  Future<ChatSession> startLiveChatSession({
    required String language = 'English',
    String? department,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/support/chat/start'),
      headers: getHeaders(),
      body: jsonEncode({
        'language': language,
        'department': department,
      }),
    );

    if (response.statusCode == 201) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return ChatSession.fromJson(data['data']);
      } else {
        throw Exception(data['message'] ?? 'Failed to start chat session');
      }
    } else {
      throw Exception('Failed to start chat session: ${response.statusCode}');
    }
  }

  // Send message in chat session
  Future<ChatMessage> sendChatMessage({
    required int sessionId,
    required String message,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/support/chat/$sessionId/messages'),
      headers: getHeaders(),
      body: jsonEncode({
        'message': message,
      }),
    );

    if (response.statusCode == 201) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return ChatMessage.fromJson(data['data']);
      } else {
        throw Exception(data['message'] ?? 'Failed to send chat message');
      }
    } else {
      throw Exception('Failed to send chat message: ${response.statusCode}');
    }
  }

  // Get chat session messages
  Future<List<ChatMessage>> getChatSessionMessages(int sessionId) async {
    final response = await http.get(
      Uri.parse('$baseUrl/support/chat/$sessionId/messages'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] ?? false) {
        List<dynamic> messages = data['data'];
        return messages.map((json) => ChatMessage.fromJson(json)).toList();
      } else {
        throw Exception(data['message'] ?? 'Failed to load chat messages');
      }
    } else {
      throw Exception('Failed to load chat messages: ${response.statusCode}');
    }
  }

  // End chat session
  Future<bool> endChatSession(int sessionId) async {
    final response = await http.post(
      Uri.parse('$baseUrl/support/chat/$sessionId/end'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      return data['success'] ?? false;
    } else {
      throw Exception('Failed to end chat session: ${response.statusCode}');
    }
  }

  // Get knowledge base articles
  Future<List<KnowledgeBaseArticle>> getKnowledgeBaseArticles({
    String? category,
    String? language = 'English',
    String? query,
    int page = 1,
    int perPage = 20,
  }) async {
    String url = '$baseUrl/support/knowledge-base?page=$page&per_page=$perPage&language=$language';
    if (category != null) url += '&category=$category';
    if (query != null) url += '&q=$query';

    final response = await http.get(Uri.parse(url), headers: getHeaders());

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] ?? false) {
        List<dynamic> articles = data['data'];
        return articles.map((json) => KnowledgeBaseArticle.fromJson(json)).toList();
      } else {
        throw Exception(data['message'] ?? 'Failed to load knowledge base articles');
      }
    } else {
      throw Exception('Failed to load knowledge base articles: ${response.statusCode}');
    }
  }

  // Get knowledge base article by ID
  Future<KnowledgeBaseArticle> getKnowledgeBaseArticle(int articleId) async {
    final response = await http.get(
      Uri.parse('$baseUrl/support/knowledge-base/$articleId'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return KnowledgeBaseArticle.fromJson(data['data']);
      } else {
        throw Exception(data['message'] ?? 'Failed to load knowledge base article');
      }
    } else {
      throw Exception('Failed to load knowledge base article: ${response.statusCode}');
    }
  }

  // Mark article as helpful
  Future<bool> markArticleAsHelpful({
    required int articleId,
    required bool isHelpful,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/support/knowledge-base/$articleId/helpful'),
      headers: getHeaders(),
      body: jsonEncode({
        'is_helpful': isHelpful,
      }),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      return data['success'] ?? false;
    } else {
      throw Exception('Failed to mark article as helpful: ${response.statusCode}');
    }
  }

  // Submit satisfaction rating for ticket
  Future<bool> submitTicketSatisfaction({
    required int ticketId,
    required int rating, // 1-5 stars
    String? comment,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/support/tickets/$ticketId/satisfaction'),
      headers: getHeaders(),
      body: jsonEncode({
        'rating': rating,
        'comment': comment,
      }),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      return data['success'] ?? false;
    } else {
      throw Exception('Failed to submit satisfaction rating: ${response.statusCode}');
    }
  }

  // Get system status
  Future<Map<String, dynamic>> getSystemStatus() async {
    final response = await http.get(
      Uri.parse('$baseUrl/support/system-status'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] ?? false) {
        return data['data'];
      } else {
        throw Exception(data['message'] ?? 'Failed to get system status');
      }
    } else {
      throw Exception('Failed to get system status: ${response.statusCode}');
    }
  }

  // Report a technical issue
  Future<SupportTicket> reportTechnicalIssue({
    required String description,
    String? additionalInfo,
    String priority = 'medium',
  }) async {
    return await createSupportTicket(
      subject: 'Technical Issue Report',
      description: description,
      category: 'technical',
      priority: priority,
    );
  }

  // Get FAQ section
  Future<List<KnowledgeBaseArticle>> getFAQs() async {
    return await getKnowledgeBaseArticles(category: 'faq');
  }

  // Search in knowledge base
  Future<List<KnowledgeBaseArticle>> searchKnowledgeBase(String query) async {
    return await getKnowledgeBaseArticles(query: query);
  }
}