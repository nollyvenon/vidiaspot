// lib/models/support_education/educational_resource_model.dart
class EducationalResource {
  final int id;
  final String title;
  final String description;
  final String type; // 'course', 'tutorial', 'guide', 'webinar', 'video'
  final String contentUrl;
  final String thumbnailUrl;
  final int duration; // in minutes
  final String level; // 'beginner', 'intermediate', 'advanced'
  final List<String> tags;
  final bool isFree;
  final double rating;
  final int ratingCount;
  final int viewCount;
  final DateTime createdAt;
  final DateTime updatedAt;
  final int categoryId;
  final String author;
  final int estimatedReadingTime; // for text content

  EducationalResource({
    required this.id,
    required this.title,
    required this.description,
    required this.type,
    required this.contentUrl,
    required this.thumbnailUrl,
    required this.duration,
    required this.level,
    required this.tags,
    required this.isFree,
    required this.rating,
    required this.ratingCount,
    required this.viewCount,
    required this.createdAt,
    required this.updatedAt,
    required this.categoryId,
    required this.author,
    required this.estimatedReadingTime,
  });

  factory EducationalResource.fromJson(Map<String, dynamic> json) {
    return EducationalResource(
      id: json['id'] ?? 0,
      title: json['title'] ?? '',
      description: json['description'] ?? '',
      type: json['type'] ?? 'tutorial',
      contentUrl: json['content_url'] ?? '',
      thumbnailUrl: json['thumbnail_url'] ?? '',
      duration: json['duration'] ?? 0,
      level: json['level'] ?? 'beginner',
      tags: List<String>.from(json['tags'] ?? []),
      isFree: json['is_free'] ?? true,
      rating: (json['rating'] is int)
          ? (json['rating'] as int).toDouble()
          : json['rating']?.toDouble() ?? 0.0,
      ratingCount: json['rating_count'] ?? 0,
      viewCount: json['view_count'] ?? 0,
      createdAt: DateTime.parse(json['created_at'] ?? DateTime.now().toIso8601String()),
      updatedAt: DateTime.parse(json['updated_at'] ?? DateTime.now().toIso8601String()),
      categoryId: json['category_id'] ?? 0,
      author: json['author'] ?? 'VidiaSpot Team',
      estimatedReadingTime: json['estimated_reading_time'] ?? 0,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'title': title,
      'description': description,
      'type': type,
      'content_url': contentUrl,
      'thumbnail_url': thumbnailUrl,
      'duration': duration,
      'level': level,
      'tags': tags,
      'is_free': isFree,
      'rating': rating,
      'rating_count': ratingCount,
      'view_count': viewCount,
      'created_at': createdAt.toIso8601String(),
      'updated_at': updatedAt.toIso8601String(),
      'category_id': categoryId,
      'author': author,
      'estimated_reading_time': estimatedReadingTime,
    };
  }
}

class EducationCategory {
  final int id;
  final String name;
  final String description;
  final String icon;
  final int resourceCount;
  final bool isFeatured;
  final int sortOrder;

  EducationCategory({
    required this.id,
    required this.name,
    required this.description,
    required this.icon,
    required this.resourceCount,
    required this.isFeatured,
    required this.sortOrder,
  });

  factory EducationCategory.fromJson(Map<String, dynamic> json) {
    return EducationCategory(
      id: json['id'] ?? 0,
      name: json['name'] ?? '',
      description: json['description'] ?? '',
      icon: json['icon'] ?? 'book',
      resourceCount: json['resource_count'] ?? 0,
      isFeatured: json['is_featured'] ?? false,
      sortOrder: json['sort_order'] ?? 0,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'description': description,
      'icon': icon,
      'resource_count': resourceCount,
      'is_featured': isFeatured,
      'sort_order': sortOrder,
    };
  }
}

class Quiz {
  final int id;
  final String title;
  final String description;
  final int educationResourceId;
  final List<QuizQuestion> questions;
  final int timeLimit; // in minutes
  final double passingScore; // percentage
  final bool randomizeQuestions;

  Quiz({
    required this.id,
    required this.title,
    required this.description,
    required this.educationResourceId,
    required this.questions,
    required this.timeLimit,
    required this.passingScore,
    required this.randomizeQuestions,
  });

  factory Quiz.fromJson(Map<String, dynamic> json) {
    return Quiz(
      id: json['id'] ?? 0,
      title: json['title'] ?? '',
      description: json['description'] ?? '',
      educationResourceId: json['education_resource_id'] ?? 0,
      questions: (json['questions'] as List?)
          ?.map((q) => QuizQuestion.fromJson(q))
          .toList() ?? [],
      timeLimit: json['time_limit'] ?? 30,
      passingScore: (json['passing_score'] is int)
          ? (json['passing_score'] as int).toDouble()
          : json['passing_score']?.toDouble() ?? 70.0,
      randomizeQuestions: json['randomize_questions'] ?? false,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'title': title,
      'description': description,
      'education_resource_id': educationResourceId,
      'questions': questions.map((q) => q.toJson()).toList(),
      'time_limit': timeLimit,
      'passing_score': passingScore,
      'randomize_questions': randomizeQuestions,
    };
  }
}

class QuizQuestion {
  final int id;
  final String question;
  final String questionType; // 'multiple_choice', 'true_false', 'short_answer'
  final List<QuizOption> options;
  final String correctAnswer;
  final int points;
  final String explanation;

  QuizQuestion({
    required this.id,
    required this.question,
    required this.questionType,
    required this.options,
    required this.correctAnswer,
    required this.points,
    required this.explanation,
  });

  factory QuizQuestion.fromJson(Map<String, dynamic> json) {
    return QuizQuestion(
      id: json['id'] ?? 0,
      question: json['question'] ?? '',
      questionType: json['question_type'] ?? 'multiple_choice',
      options: (json['options'] as List?)
          ?.map((o) => QuizOption.fromJson(o))
          .toList() ?? [],
      correctAnswer: json['correct_answer'] ?? '',
      points: json['points'] ?? 1,
      explanation: json['explanation'] ?? '',
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'question': question,
      'question_type': questionType,
      'options': options.map((o) => o.toJson()).toList(),
      'correct_answer': correctAnswer,
      'points': points,
      'explanation': explanation,
    };
  }
}

class QuizOption {
  final int id;
  final String text;
  final bool isCorrect;

  QuizOption({
    required this.id,
    required this.text,
    required this.isCorrect,
  });

  factory QuizOption.fromJson(Map<String, dynamic> json) {
    return QuizOption(
      id: json['id'] ?? 0,
      text: json['text'] ?? '',
      isCorrect: json['is_correct'] ?? false,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'text': text,
      'is_correct': isCorrect,
    };
  }
}

class LearningProgress {
  final int userId;
  final int educationResourceId;
  final double completionPercentage;
  final bool isCompleted;
  final int currentLesson;
  final DateTime startedAt;
  final DateTime? completedAt;
  final double score;
  final Map<String, dynamic> progressData;

  LearningProgress({
    required this.userId,
    required this.educationResourceId,
    required this.completionPercentage,
    required this.isCompleted,
    required this.currentLesson,
    required this.startedAt,
    this.completedAt,
    required this.score,
    required this.progressData,
  });

  factory LearningProgress.fromJson(Map<String, dynamic> json) {
    return LearningProgress(
      userId: json['user_id'] ?? 0,
      educationResourceId: json['education_resource_id'] ?? 0,
      completionPercentage: (json['completion_percentage'] is int)
          ? (json['completion_percentage'] as int).toDouble()
          : json['completion_percentage']?.toDouble() ?? 0.0,
      isCompleted: json['is_completed'] ?? false,
      currentLesson: json['current_lesson'] ?? 0,
      startedAt: DateTime.parse(json['started_at'] ?? DateTime.now().toIso8601String()),
      completedAt: json['completed_at'] != null ? DateTime.parse(json['completed_at']) : null,
      score: (json['score'] is int)
          ? (json['score'] as int).toDouble()
          : json['score']?.toDouble() ?? 0.0,
      progressData: json['progress_data'] ?? {},
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'user_id': userId,
      'education_resource_id': educationResourceId,
      'completion_percentage': completionPercentage,
      'is_completed': isCompleted,
      'current_lesson': currentLesson,
      'started_at': startedAt.toIso8601String(),
      'completed_at': completedAt?.toIso8601String(),
      'score': score,
      'progress_data': progressData,
    };
  }
}