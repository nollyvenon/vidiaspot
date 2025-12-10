// lib/services/support_education/educational_resources_service.dart
import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import '../../models/support_education/educational_resource_model.dart';

class EducationalResourcesService {
  final String baseUrl = 'http://10.0.2.2:8000/api';
  String? _authToken;

  EducationalResourcesService() {
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

  // Get all educational resources
  Future<List<EducationalResource>> getEducationalResources({
    String? category,
    String? level,
    String? type,
    int page = 1,
    int perPage = 20,
  }) async {
    String url = '$baseUrl/education/resources?page=$page&per_page=$perPage';
    if (category != null) url += '&category=$category';
    if (level != null) url += '&level=$level';
    if (type != null) url += '&type=$type';

    final response = await http.get(Uri.parse(url), headers: getHeaders());

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] ?? false) {
        List<dynamic> resources = data['data'];
        return resources.map((json) => EducationalResource.fromJson(json)).toList();
      } else {
        throw Exception(data['message'] ?? 'Failed to load educational resources');
      }
    } else {
      throw Exception('Failed to load educational resources: ${response.statusCode}');
    }
  }

  // Get educational resource by ID
  Future<EducationalResource> getEducationalResource(int id) async {
    final response = await http.get(
      Uri.parse('$baseUrl/education/resources/$id'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return EducationalResource.fromJson(data['data']);
      } else {
        throw Exception(data['message'] ?? 'Failed to load educational resource');
      }
    } else {
      throw Exception('Failed to load educational resource: ${response.statusCode}');
    }
  }

  // Get education categories
  Future<List<EducationCategory>> getEducationCategories() async {
    final response = await http.get(
      Uri.parse('$baseUrl/education/categories'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] ?? false) {
        List<dynamic> categories = data['data'];
        return categories.map((json) => EducationCategory.fromJson(json)).toList();
      } else {
        throw Exception(data['message'] ?? 'Failed to load education categories');
      }
    } else {
      throw Exception('Failed to load education categories: ${response.statusCode}');
    }
  }

  // Get user's learning progress
  Future<List<LearningProgress>> getUserLearningProgress() async {
    final response = await http.get(
      Uri.parse('$baseUrl/education/progress'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] ?? false) {
        List<dynamic> progress = data['data'];
        return progress.map((json) => LearningProgress.fromJson(json)).toList();
      } else {
        throw Exception(data['message'] ?? 'Failed to load learning progress');
      }
    } else {
      throw Exception('Failed to load learning progress: ${response.statusCode}');
    }
  }

  // Update learning progress
  Future<LearningProgress> updateLearningProgress({
    required int educationResourceId,
    required double completionPercentage,
    required int currentLesson,
    required double score,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/education/progress'),
      headers: getHeaders(),
      body: jsonEncode({
        'education_resource_id': educationResourceId,
        'completion_percentage': completionPercentage,
        'current_lesson': currentLesson,
        'score': score,
      }),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return LearningProgress.fromJson(data['data']);
      } else {
        throw Exception(data['message'] ?? 'Failed to update learning progress');
      }
    } else {
      throw Exception('Failed to update learning progress: ${response.statusCode}');
    }
  }

  // Get quiz by education resource ID
  Future<Quiz> getQuiz(int educationResourceId) async {
    final response = await http.get(
      Uri.parse('$baseUrl/education/resources/$educationResourceId/quiz'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return Quiz.fromJson(data['data']);
      } else {
        throw Exception(data['message'] ?? 'Failed to load quiz');
      }
    } else {
      throw Exception('Failed to load quiz: ${response.statusCode}');
    }
  }

  // Submit quiz answers
  Future<Map<String, dynamic>> submitQuiz({
    required int quizId,
    required Map<int, dynamic> answers, // questionId: answer
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/education/quizzes/$quizId/submit'),
      headers: getHeaders(),
      body: jsonEncode({
        'answers': answers,
      }),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return data;
      } else {
        throw Exception(data['message'] ?? 'Failed to submit quiz');
      }
    } else {
      throw Exception('Failed to submit quiz: ${response.statusCode}');
    }
  }

  // Get user's quiz attempts
  Future<List<Map<String, dynamic>>> getQuizAttempts(int quizId) async {
    final response = await http.get(
      Uri.parse('$baseUrl/education/quizzes/$quizId/attempts'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] ?? false) {
        return List<Map<String, dynamic>>.from(data['data']);
      } else {
        throw Exception(data['message'] ?? 'Failed to load quiz attempts');
      }
    } else {
      throw Exception('Failed to load quiz attempts: ${response.statusCode}');
    }
  }

  // Get cryptocurrency education center resources
  Future<List<EducationalResource>> getCryptoEducationCenter() async {
    return await getEducationalResources(category: 'cryptocurrency');
  }

  // Get risk management guides
  Future<List<EducationalResource>> getRiskManagementGuides() async {
    return await getEducationalResources(category: 'risk_management');
  }

  // Get market analysis tutorials
  Future<List<EducationalResource>> getMarketAnalysisTutorials() async {
    return await getEducationalResources(category: 'market_analysis');
  }

  // Mark resource as favorite
  Future<bool> toggleFavoriteResource(int resourceId) async {
    final response = await http.post(
      Uri.parse('$baseUrl/education/resources/$resourceId/toggle-favorite'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      return data['success'] ?? false;
    } else {
      throw Exception('Failed to toggle favorite: ${response.statusCode}');
    }
  }

  // Get user's favorite resources
  Future<List<EducationalResource>> getFavoriteResources() async {
    final response = await http.get(
      Uri.parse('$baseUrl/education/favorites'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] ?? false) {
        List<dynamic> resources = data['data'];
        return resources.map((json) => EducationalResource.fromJson(json)).toList();
      } else {
        throw Exception(data['message'] ?? 'Failed to load favorite resources');
      }
    } else {
      throw Exception('Failed to load favorite resources: ${response.statusCode}');
    }
  }

  // Search educational resources
  Future<List<EducationalResource>> searchResources(String query) async {
    final response = await http.get(
      Uri.parse('$baseUrl/education/search?q=$query'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] ?? false) {
        List<dynamic> resources = data['data'];
        return resources.map((json) => EducationalResource.fromJson(json)).toList();
      } else {
        throw Exception(data['message'] ?? 'Failed to search resources');
      }
    } else {
      throw Exception('Failed to search resources: ${response.statusCode}');
    }
  }
}