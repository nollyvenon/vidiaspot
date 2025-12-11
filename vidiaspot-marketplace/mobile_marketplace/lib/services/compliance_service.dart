// lib/services/compliance_service.dart
import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';

class ComplianceService {
  final String baseUrl = 'http://10.0.2.2:8000/api'; // Update to match your backend
  String? _authToken;

  ComplianceService() {
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

  // Automatic tax reporting (1099, etc.)
  Future<TaxReport> generateTaxReport({
    int year = 2023,
    String? userId,
  }) async {
    String url = '$baseUrl/compliance/tax-report';
    if (userId != null) {
      url += '?user_id=$userId&year=$year';
    } else {
      url += '?year=$year';
    }

    final response = await http.get(
      Uri.parse(url),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] ?? false) {
        return TaxReport.fromJson(data['data']);
      } else {
        throw Exception(data['message'] ?? 'Failed to generate tax report');
      }
    } else {
      throw Exception('Failed to generate tax report: ${response.statusCode}');
    }
  }

  // Transaction monitoring for compliance
  Future<ComplianceStatus> checkTransactionCompliance({
    required String transactionId,
    required double amount,
    required String cryptoSymbol,
    required String fromAddress,
    required String toAddress,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/compliance/transaction-monitoring'),
      headers: getHeaders(),
      body: jsonEncode({
        'transaction_id': transactionId,
        'amount': amount,
        'crypto_symbol': cryptoSymbol,
        'from_address': fromAddress,
        'to_address': toAddress,
      }),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return ComplianceStatus.fromJson(data['data']);
      } else {
        throw Exception(data['message'] ?? 'Transaction compliance check failed');
      }
    } else {
      throw Exception('Transaction compliance check failed: ${response.statusCode}');
    }
  }

  // Suspicious activity reporting (SAR)
  Future<SarResult> submitSuspiciousActivityReport({
    required String activityDescription,
    required List<String> involvedAddresses,
    required double thresholdAmount,
    String? additionalInfo,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/compliance/sar'),
      headers: getHeaders(),
      body: jsonEncode({
        'activity_description': activityDescription,
        'involved_addresses': involvedAddresses,
        'threshold_amount': thresholdAmount,
        'additional_info': additionalInfo,
      }),
    );

    if (response.statusCode == 201) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return SarResult.fromJson(data['data']);
      } else {
        throw Exception(data['message'] ?? 'Failed to submit SAR');
      }
    } else {
      throw Exception('Failed to submit SAR: ${response.statusCode}');
    }
  }

  // Geographic restriction enforcement
  Future<bool> isLocationAllowed(String countryCode) async {
    final response = await http.get(
      Uri.parse('$baseUrl/compliance/geo-restrictions?country_code=$countryCode'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      return data['is_allowed'] ?? false;
    } else {
      throw Exception('Failed to check geographic restrictions: ${response.statusCode}');
    }
  }

  // Age verification system
  Future<VerificationResult> verifyAge({
    required int age,
    required String documentType,
    required String documentNumber,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/compliance/age-verification'),
      headers: getHeaders(),
      body: jsonEncode({
        'age': age,
        'document_type': documentType,
        'document_number': documentNumber,
      }),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return VerificationResult.fromJson(data['data']);
      } else {
        throw Exception(data['message'] ?? 'Age verification failed');
      }
    } else {
      throw Exception('Age verification failed: ${response.statusCode}');
    }
  }

  // Transaction limits based on verification
  Future<TransactionLimits> getTransactionLimits({
    required String userId,
    required String verificationLevel,
  }) async {
    final response = await http.get(
      Uri.parse('$baseUrl/compliance/transaction-limits?user_id=$userId&verification_level=$verificationLevel'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] ?? false) {
        return TransactionLimits.fromJson(data['data']);
      } else {
        throw Exception(data['message'] ?? 'Failed to get transaction limits');
      }
    } else {
      throw Exception('Failed to get transaction limits: ${response.statusCode}');
    }
  }

  // Anti-money laundering (AML) tools
  Future<AmlCheckResult> performAmlCheck({
    required String address,
    required String cryptoSymbol,
    required double amount,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/compliance/aml-check'),
      headers: getHeaders(),
      body: jsonEncode({
        'address': address,
        'crypto_symbol': cryptoSymbol,
        'amount': amount,
      }),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return AmlCheckResult.fromJson(data['data']);
      } else {
        throw Exception(data['message'] ?? 'AML check failed');
      }
    } else {
      throw Exception('AML check failed: ${response.statusCode}');
    }
  }

  // Regulatory reporting automation
  Future<RegulatoryReport> generateRegulatoryReport({
    required String reportType,
    required DateTime startDate,
    required DateTime endDate,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/compliance/regulatory-report'),
      headers: getHeaders(),
      body: jsonEncode({
        'report_type': reportType,
        'start_date': startDate.toIso8601String(),
        'end_date': endDate.toIso8601String(),
      }),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return RegulatoryReport.fromJson(data['data']);
      } else {
        throw Exception(data['message'] ?? 'Failed to generate regulatory report');
      }
    } else {
      throw Exception('Failed to generate regulatory report: ${response.statusCode}');
    }
  }

  // Get compliance status for user
  Future<UserComplianceStatus> getUserComplianceStatus(String userId) async {
    final response = await http.get(
      Uri.parse('$baseUrl/compliance/user-status/$userId'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] ?? false) {
        return UserComplianceStatus.fromJson(data['data']);
      } else {
        throw Exception(data['message'] ?? 'Failed to get user compliance status');
      }
    } else {
      throw Exception('Failed to get user compliance status: ${response.statusCode}');
    }
  }

  // Submit compliance documentation
  Future<SubmissionResult> submitComplianceDocs({
    required String docType,
    required String docId,
    required List<String> fileIds,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/compliance/submit-docs'),
      headers: getHeaders(),
      body: jsonEncode({
        'doc_type': docType,
        'doc_id': docId,
        'file_ids': fileIds,
      }),
    );

    if (response.statusCode == 201) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return SubmissionResult.fromJson(data['data']);
      } else {
        throw Exception(data['message'] ?? 'Failed to submit compliance docs');
      }
    } else {
      throw Exception('Failed to submit compliance docs: ${response.statusCode}');
    }
  }
}

// Data models for compliance operations
class TaxReport {
  final String reportId;
  final int year;
  final String userId;
  final double totalCapitalGains;
  final double totalCapitalLosses;
  final double netCapitalGains;
  final double totalFiatReceived;
  final double totalFiatSpent;
  final Map<String, dynamic> transactions;
  final String format; // 'PDF', 'CSV', 'JSON'
  final DateTime generatedAt;
  final String status; // 'generated', 'pending', 'failed'
  final String documentUrl;

  TaxReport({
    required this.reportId,
    required this.year,
    required this.userId,
    required this.totalCapitalGains,
    required this.totalCapitalLosses,
    required this.netCapitalGains,
    required this.totalFiatReceived,
    required this.totalFiatSpent,
    required this.transactions,
    required this.format,
    required this.generatedAt,
    required this.status,
    required this.documentUrl,
  });

  factory TaxReport.fromJson(Map<String, dynamic> json) {
    return TaxReport(
      reportId: json['report_id'] ?? '',
      year: json['year'] ?? 2023,
      userId: json['user_id'] ?? '',
      totalCapitalGains: (json['total_capital_gains'] is int) 
          ? (json['total_capital_gains'] as int).toDouble() 
          : json['total_capital_gains']?.toDouble() ?? 0.0,
      totalCapitalLosses: (json['total_capital_losses'] is int) 
          ? (json['total_capital_losses'] as int).toDouble() 
          : json['total_capital_losses']?.toDouble() ?? 0.0,
      netCapitalGains: (json['net_capital_gains'] is int) 
          ? (json['net_capital_gains'] as int).toDouble() 
          : json['net_capital_gains']?.toDouble() ?? 0.0,
      totalFiatReceived: (json['total_fiat_received'] is int) 
          ? (json['total_fiat_received'] as int).toDouble() 
          : json['total_fiat_received']?.toDouble() ?? 0.0,
      totalFiatSpent: (json['total_fiat_spent'] is int) 
          ? (json['total_fiat_spent'] as int).toDouble() 
          : json['total_fiat_spent']?.toDouble() ?? 0.0,
      transactions: json['transactions'] ?? {},
      format: json['format'] ?? 'PDF',
      generatedAt: DateTime.parse(json['generated_at'] ?? DateTime.now().toIso8601String()),
      status: json['status'] ?? 'generated',
      documentUrl: json['document_url'] ?? '',
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'report_id': reportId,
      'year': year,
      'user_id': userId,
      'total_capital_gains': totalCapitalGains,
      'total_capital_losses': totalCapitalLosses,
      'net_capital_gains': netCapitalGains,
      'total_fiat_received': totalFiatReceived,
      'total_fiat_spent': totalFiatSpent,
      'transactions': transactions,
      'format': format,
      'generated_at': generatedAt.toIso8601String(),
      'status': status,
      'document_url': documentUrl,
    };
  }
}

class ComplianceStatus {
  final String transactionId;
  final bool isCompliant;
  final List<String> violations;
  final String riskLevel; // 'low', 'medium', 'high', 'critical'
  final double riskScore;
  final DateTime checkedAt;
  final Map<String, dynamic> details;

  ComplianceStatus({
    required this.transactionId,
    required this.isCompliant,
    required this.violations,
    required this.riskLevel,
    required this.riskScore,
    required this.checkedAt,
    required this.details,
  });

  factory ComplianceStatus.fromJson(Map<String, dynamic> json) {
    return ComplianceStatus(
      transactionId: json['transaction_id'] ?? '',
      isCompliant: json['is_compliant'] ?? false,
      violations: List<String>.from(json['violations'] ?? []),
      riskLevel: json['risk_level'] ?? 'unknown',
      riskScore: (json['risk_score'] is int) 
          ? (json['risk_score'] as int).toDouble() 
          : json['risk_score']?.toDouble() ?? 0.0,
      checkedAt: DateTime.parse(json['checked_at'] ?? DateTime.now().toIso8601String()),
      details: json['details'] ?? {},
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'transaction_id': transactionId,
      'is_compliant': isCompliant,
      'violations': violations,
      'risk_level': riskLevel,
      'risk_score': riskScore,
      'checked_at': checkedAt.toIso8601String(),
      'details': details,
    };
  }
}

class SarResult {
  final String sarId;
  final String status;
  final String referenceNumber;
  final String description;
  final List<String> addresses;
  final double thresholdAmount;
  final String submittedBy;
  final DateTime submittedAt;
  final DateTime reviewedAt;
  final String reviewStatus; // 'pending', 'reviewed', 'escalated'

  SarResult({
    required this.sarId,
    required this.status,
    required this.referenceNumber,
    required this.description,
    required this.addresses,
    required this.thresholdAmount,
    required this.submittedBy,
    required this.submittedAt,
    required this.reviewedAt,
    required this.reviewStatus,
  });

  factory SarResult.fromJson(Map<String, dynamic> json) {
    return SarResult(
      sarId: json['sar_id'] ?? '',
      status: json['status'] ?? 'submitted',
      referenceNumber: json['reference_number'] ?? '',
      description: json['description'] ?? '',
      addresses: List<String>.from(json['addresses'] ?? []),
      thresholdAmount: (json['threshold_amount'] is int) 
          ? (json['threshold_amount'] as int).toDouble() 
          : json['threshold_amount']?.toDouble() ?? 0.0,
      submittedBy: json['submitted_by'] ?? '',
      submittedAt: DateTime.parse(json['submitted_at'] ?? DateTime.now().toIso8601String()),
      reviewedAt: json['reviewed_at'] != null 
          ? DateTime.parse(json['reviewed_at']) 
          : DateTime.now(),
      reviewStatus: json['review_status'] ?? 'pending',
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'sar_id': sarId,
      'status': status,
      'reference_number': referenceNumber,
      'description': description,
      'addresses': addresses,
      'threshold_amount': thresholdAmount,
      'submitted_by': submittedBy,
      'submitted_at': submittedAt.toIso8601String(),
      'reviewed_at': reviewedAt.toIso8601String(),
      'review_status': reviewStatus,
    };
  }
}

class VerificationResult {
  final String verificationId;
  final String status;
  final bool isVerified;
  final String verificationType;
  final String documentType;
  final String documentNumber;
  final DateTime verifiedAt;
  final String verificationMethod;
  final Map<String, dynamic> verificationDetails;

  VerificationResult({
    required this.verificationId,
    required this.status,
    required this.isVerified,
    required this.verificationType,
    required this.documentType,
    required this.documentNumber,
    required this.verifiedAt,
    required this.verificationMethod,
    required this.verificationDetails,
  });

  factory VerificationResult.fromJson(Map<String, dynamic> json) {
    return VerificationResult(
      verificationId: json['verification_id'] ?? '',
      status: json['status'] ?? 'completed',
      isVerified: json['is_verified'] ?? false,
      verificationType: json['verification_type'] ?? '',
      documentType: json['document_type'] ?? '',
      documentNumber: json['document_number'] ?? '',
      verifiedAt: DateTime.parse(json['verified_at'] ?? DateTime.now().toIso8601String()),
      verificationMethod: json['verification_method'] ?? '',
      verificationDetails: json['verification_details'] ?? {},
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'verification_id': verificationId,
      'status': status,
      'is_verified': isVerified,
      'verification_type': verificationType,
      'document_type': documentType,
      'document_number': documentNumber,
      'verified_at': verifiedAt.toIso8601String(),
      'verification_method': verificationMethod,
      'verification_details': verificationDetails,
    };
  }
}

class TransactionLimits {
  final String userId;
  final String verificationLevel; // 'unverified', 'basic', 'verified', 'pro'
  final double dailyLimit;
  final double monthlyLimit;
  final double annualLimit;
  final double perTransactionLimit;
  final Map<String, dynamic> cryptoLimits;
  final DateTime updatedAt;

  TransactionLimits({
    required this.userId,
    required this.verificationLevel,
    required this.dailyLimit,
    required this.monthlyLimit,
    required this.annualLimit,
    required this.perTransactionLimit,
    required this.cryptoLimits,
    required this.updatedAt,
  });

  factory TransactionLimits.fromJson(Map<String, dynamic> json) {
    return TransactionLimits(
      userId: json['user_id'] ?? '',
      verificationLevel: json['verification_level'] ?? 'unverified',
      dailyLimit: (json['daily_limit'] is int) 
          ? (json['daily_limit'] as int).toDouble() 
          : json['daily_limit']?.toDouble() ?? 0.0,
      monthlyLimit: (json['monthly_limit'] is int) 
          ? (json['monthly_limit'] as int).toDouble() 
          : json['monthly_limit']?.toDouble() ?? 0.0,
      annualLimit: (json['annual_limit'] is int) 
          ? (json['annual_limit'] as int).toDouble() 
          : json['annual_limit']?.toDouble() ?? 0.0,
      perTransactionLimit: (json['per_transaction_limit'] is int) 
          ? (json['per_transaction_limit'] as int).toDouble() 
          : json['per_transaction_limit']?.toDouble() ?? 0.0,
      cryptoLimits: json['crypto_limits'] ?? {},
      updatedAt: DateTime.parse(json['updated_at'] ?? DateTime.now().toIso8601String()),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'user_id': userId,
      'verification_level': verificationLevel,
      'daily_limit': dailyLimit,
      'monthly_limit': monthlyLimit,
      'annual_limit': annualLimit,
      'per_transaction_limit': perTransactionLimit,
      'crypto_limits': cryptoLimits,
      'updated_at': updatedAt.toIso8601String(),
    };
  }
}

class AmlCheckResult {
  final String address;
  final String cryptoSymbol;
  final double amount;
  final bool isClean;
  final double riskScore;
  final String riskLevel; // 'low', 'medium', 'high', 'critical'
  final List<String> riskFactors;
  final String source; // 'blocklist', 'watchlist', 'history', etc.
  final DateTime checkedAt;
  final Map<String, dynamic> details;

  AmlCheckResult({
    required this.address,
    required this.cryptoSymbol,
    required this.amount,
    required this.isClean,
    required this.riskScore,
    required this.riskLevel,
    required this.riskFactors,
    required this.source,
    required this.checkedAt,
    required this.details,
  });

  factory AmlCheckResult.fromJson(Map<String, dynamic> json) {
    return AmlCheckResult(
      address: json['address'] ?? '',
      cryptoSymbol: json['crypto_symbol'] ?? '',
      amount: (json['amount'] is int) 
          ? (json['amount'] as int).toDouble() 
          : json['amount']?.toDouble() ?? 0.0,
      isClean: json['is_clean'] ?? true,
      riskScore: (json['risk_score'] is int) 
          ? (json['risk_score'] as int).toDouble() 
          : json['risk_score']?.toDouble() ?? 0.0,
      riskLevel: json['risk_level'] ?? 'low',
      riskFactors: List<String>.from(json['risk_factors'] ?? []),
      source: json['source'] ?? 'unknown',
      checkedAt: DateTime.parse(json['checked_at'] ?? DateTime.now().toIso8601String()),
      details: json['details'] ?? {},
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'address': address,
      'crypto_symbol': cryptoSymbol,
      'amount': amount,
      'is_clean': isClean,
      'risk_score': riskScore,
      'risk_level': riskLevel,
      'risk_factors': riskFactors,
      'source': source,
      'checked_at': checkedAt.toIso8601String(),
      'details': details,
    };
  }
}

class RegulatoryReport {
  final String reportId;
  final String reportType; // 'daily', 'weekly', 'monthly', 'quarterly', 'annual'
  final String jurisdiction;
  final DateTime startDate;
  final DateTime endDate;
  final int totalTransactions;
  final double totalVolume;
  final int suspiciousActivities;
  final int blockedTransactions;
  final String format; // 'PDF', 'XML', 'JSON'
  final String status; // 'generated', 'pending', 'failed', 'submitted'
  final String documentUrl;
  final DateTime generatedAt;
  final DateTime submittedAt;

  RegulatoryReport({
    required this.reportId,
    required this.reportType,
    required this.jurisdiction,
    required this.startDate,
    required this.endDate,
    required this.totalTransactions,
    required this.totalVolume,
    required this.suspiciousActivities,
    required this.blockedTransactions,
    required this.format,
    required this.status,
    required this.documentUrl,
    required this.generatedAt,
    required this.submittedAt,
  });

  factory RegulatoryReport.fromJson(Map<String, dynamic> json) {
    return RegulatoryReport(
      reportId: json['report_id'] ?? '',
      reportType: json['report_type'] ?? '',
      jurisdiction: json['jurisdiction'] ?? '',
      startDate: DateTime.parse(json['start_date'] ?? DateTime.now().toIso8601String()),
      endDate: DateTime.parse(json['end_date'] ?? DateTime.now().toIso8601String()),
      totalTransactions: json['total_transactions'] ?? 0,
      totalVolume: (json['total_volume'] is int) 
          ? (json['total_volume'] as int).toDouble() 
          : json['total_volume']?.toDouble() ?? 0.0,
      suspiciousActivities: json['suspicious_activities'] ?? 0,
      blockedTransactions: json['blocked_transactions'] ?? 0,
      format: json['format'] ?? 'PDF',
      status: json['status'] ?? 'pending',
      documentUrl: json['document_url'] ?? '',
      generatedAt: DateTime.parse(json['generated_at'] ?? DateTime.now().toIso8601String()),
      submittedAt: json['submitted_at'] != null 
          ? DateTime.parse(json['submitted_at']) 
          : DateTime.now(),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'report_id': reportId,
      'report_type': reportType,
      'jurisdiction': jurisdiction,
      'start_date': startDate.toIso8601String(),
      'end_date': endDate.toIso8601String(),
      'total_transactions': totalTransactions,
      'total_volume': totalVolume,
      'suspicious_activities': suspiciousActivities,
      'blocked_transactions': blockedTransactions,
      'format': format,
      'status': status,
      'document_url': documentUrl,
      'generated_at': generatedAt.toIso8601String(),
      'submitted_at': submittedAt.toIso8601String(),
    };
  }
}

class UserComplianceStatus {
  final String userId;
  final String verificationLevel;
  final bool isKycVerified;
  final bool isAmlCompliant;
  final bool isTaxReportingCompliant;
  final bool isGeoCompliant;
  final DateTime lastComplianceCheck;
  final String overallStatus; // 'compliant', 'non_compliant', 'pending'
  final List<String> requiredActions;
  final Map<String, dynamic> details;

  UserComplianceStatus({
    required this.userId,
    required this.verificationLevel,
    required this.isKycVerified,
    required this.isAmlCompliant,
    required this.isTaxReportingCompliant,
    required this.isGeoCompliant,
    required this.lastComplianceCheck,
    required this.overallStatus,
    required this.requiredActions,
    required this.details,
  });

  factory UserComplianceStatus.fromJson(Map<String, dynamic> json) {
    return UserComplianceStatus(
      userId: json['user_id'] ?? '',
      verificationLevel: json['verification_level'] ?? 'unverified',
      isKycVerified: json['is_kyc_verified'] ?? false,
      isAmlCompliant: json['is_aml_compliant'] ?? false,
      isTaxReportingCompliant: json['is_tax_reporting_compliant'] ?? false,
      isGeoCompliant: json['is_geo_compliant'] ?? false,
      lastComplianceCheck: DateTime.parse(json['last_compliance_check'] ?? DateTime.now().toIso8601String()),
      overallStatus: json['overall_status'] ?? 'pending',
      requiredActions: List<String>.from(json['required_actions'] ?? []),
      details: json['details'] ?? {},
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'user_id': userId,
      'verification_level': verificationLevel,
      'is_kyc_verified': isKycVerified,
      'is_aml_compliant': isAmlCompliant,
      'is_tax_reporting_compliant': isTaxReportingCompliant,
      'is_geo_compliant': isGeoCompliant,
      'last_compliance_check': lastComplianceCheck.toIso8601String(),
      'overall_status': overallStatus,
      'required_actions': requiredActions,
      'details': details,
    };
  }
}

class SubmissionResult {
  final String submissionId;
  final String docType;
  final String docId;
  final List<String> fileIds;
  final String status;
  final String message;
  final DateTime submittedAt;
  final DateTime processedAt;
  final Map<String, dynamic> processingResult;

  SubmissionResult({
    required this.submissionId,
    required this.docType,
    required this.docId,
    required this.fileIds,
    required this.status,
    required this.message,
    required this.submittedAt,
    required this.processedAt,
    required this.processingResult,
  });

  factory SubmissionResult.fromJson(Map<String, dynamic> json) {
    return SubmissionResult(
      submissionId: json['submission_id'] ?? '',
      docType: json['doc_type'] ?? '',
      docId: json['doc_id'] ?? '',
      fileIds: List<String>.from(json['file_ids'] ?? []),
      status: json['status'] ?? 'pending',
      message: json['message'] ?? '',
      submittedAt: DateTime.parse(json['submitted_at'] ?? DateTime.now().toIso8601String()),
      processedAt: json['processed_at'] != null 
          ? DateTime.parse(json['processed_at']) 
          : DateTime.now(),
      processingResult: json['processing_result'] ?? {},
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'submission_id': submissionId,
      'doc_type': docType,
      'doc_id': docId,
      'file_ids': fileIds,
      'status': status,
      'message': message,
      'submitted_at': submittedAt.toIso8601String(),
      'processed_at': processedAt.toIso8601String(),
      'processing_result': processingResult,
    };
  }
}