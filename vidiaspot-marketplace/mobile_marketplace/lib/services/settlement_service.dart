// lib/services/settlement_service.dart
import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import '../models/crypto_p2p/crypto_trade_model.dart';

class SettlementService {
  final String baseUrl = 'http://10.0.2.2:8000/api'; // Update to match your backend
  String? _authToken;

  SettlementService() {
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

  // Real-time cryptocurrency transfer
  Future<TransferResult> transferCrypto({
    required String fromAddress,
    required String toAddress,
    required String cryptoSymbol,
    required double amount,
    String? memo,
    String? feeLevel = 'normal',
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/settlement/transfer-crypto'),
      headers: getHeaders(),
      body: jsonEncode({
        'from_address': fromAddress,
        'to_address': toAddress,
        'crypto_symbol': cryptoSymbol,
        'amount': amount,
        'memo': memo,
        'fee_level': feeLevel,
      }),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return TransferResult.fromJson(data['data']);
      } else {
        throw Exception(data['message'] ?? 'Transfer failed');
      }
    } else {
      throw Exception('Transfer failed: ${response.statusCode}');
    }
  }

  // Atomic swap functionality
  Future<AtomicSwapResult> initiateAtomicSwap({
    required String fromCrypto,
    required String toCrypto,
    required double fromAmount,
    required double toAmount,
    required String counterpartyAddress,
    int lockTimeSeconds = 14400, // 4 hours
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/settlement/atomic-swap'),
      headers: getHeaders(),
      body: jsonEncode({
        'from_crypto': fromCrypto,
        'to_crypto': toCrypto,
        'from_amount': fromAmount,
        'to_amount': toAmount,
        'counterparty_address': counterpartyAddress,
        'lock_time_seconds': lockTimeSeconds,
      }),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return AtomicSwapResult.fromJson(data['data']);
      } else {
        throw Exception(data['message'] ?? 'Atomic swap failed to initiate');
      }
    } else {
      throw Exception('Atomic swap failed: ${response.statusCode}');
    }
  }

  // Cross-chain bridge functionality
  Future<BridgeResult> initiateCrossChainTransfer({
    required String fromChain,
    required String toChain,
    required String cryptoSymbol,
    required double amount,
    required String destinationAddress,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/settlement/cross-chain'),
      headers: getHeaders(),
      body: jsonEncode({
        'from_chain': fromChain,
        'to_chain': toChain,
        'crypto_symbol': cryptoSymbol,
        'amount': amount,
        'destination_address': destinationAddress,
      }),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return BridgeResult.fromJson(data['data']);
      } else {
        throw Exception(data['message'] ?? 'Cross-chain transfer failed');
      }
    } else {
      throw Exception('Cross-chain transfer failed: ${response.statusCode}');
    }
  }

  // Lightning Network support for Bitcoin
  Future<LightningResult> sendLightningPayment({
    required String invoice,
    required double amount,
    String? description,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/settlement/lightning'),
      headers: getHeaders(),
      body: jsonEncode({
        'invoice': invoice,
        'amount': amount,
        'description': description,
      }),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return LightningResult.fromJson(data['data']);
      } else {
        throw Exception(data['message'] ?? 'Lightning payment failed');
      }
    } else {
      throw Exception('Lightning payment failed: ${response.statusCode}');
    }
  }

  // Layer 2 solution for Ethereum
  Future<Layer2Result> sendLayer2Payment({
    required String toAddress,
    required double amount,
    required String layer2Provider, // 'arbitrum', 'optimism', 'polygon'
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/settlement/layer2'),
      headers: getHeaders(),
      body: jsonEncode({
        'to_address': toAddress,
        'amount': amount,
        'layer2_provider': layer2Provider,
      }),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return Layer2Result.fromJson(data['data']);
      } else {
        throw Exception(data['message'] ?? 'Layer 2 payment failed');
      }
    } else {
      throw Exception('Layer 2 payment failed: ${response.statusCode}');
    }
  }

  // Instant fiat settlement
  Future<FiatSettlementResult> settleFiat({
    required String paymentMethodId,
    required double amount,
    required String currency,
    required String recipientId,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/settlement/fiat'),
      headers: getHeaders(),
      body: jsonEncode({
        'payment_method_id': paymentMethodId,
        'amount': amount,
        'currency': currency,
        'recipient_id': recipientId,
      }),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return FiatSettlementResult.fromJson(data['data']);
      } else {
        throw Exception(data['message'] ?? 'Fiat settlement failed');
      }
    } else {
      throw Exception('Fiat settlement failed: ${response.statusCode}');
    }
  }

  // Multi-currency wallet support
  Future<List<WalletBalance>> getWalletBalances() async {
    final response = await http.get(
      Uri.parse('$baseUrl/settlement/wallet-balances'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] ?? false) {
        List<dynamic> balances = data['data'];
        return balances.map((json) => WalletBalance.fromJson(json)).toList();
      } else {
        throw Exception(data['message'] ?? 'Failed to get wallet balances');
      }
    } else {
      throw Exception('Failed to get wallet balances: ${response.statusCode}');
    }
  }

  // Automatic rebalancing
  Future<RebalanceResult> rebalancePortfolio({
    required Map<String, double> targetAllocation,
    double tolerancePercent = 5.0,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/settlement/rebalance'),
      headers: getHeaders(),
      body: jsonEncode({
        'target_allocation': targetAllocation,
        'tolerance_percent': tolerancePercent,
      }),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return RebalanceResult.fromJson(data['data']);
      } else {
        throw Exception(data['message'] ?? 'Portfolio rebalancing failed');
      }
    } else {
      throw Exception('Portfolio rebalancing failed: ${response.statusCode}');
    }
  }

  // Get settlement history
  Future<List<SettlementRecord>> getSettlementHistory({
    int page = 1,
    int perPage = 20,
    String? type,
    String? status,
  }) async {
    String url = '$baseUrl/settlement/history?page=$page&per_page=$perPage';
    if (type != null) url += '&type=$type';
    if (status != null) url += '&status=$status';

    final response = await http.get(
      Uri.parse(url),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] ?? false) {
        List<dynamic> records = data['data'];
        return records.map((json) => SettlementRecord.fromJson(json)).toList();
      } else {
        throw Exception(data['message'] ?? 'Failed to get settlement history');
      }
    } else {
      throw Exception('Failed to get settlement history: ${response.statusCode}');
    }
  }

  // Get blockchain transaction status
  Future<TransactionStatus> getTransactionStatus(String transactionId) async {
    final response = await http.get(
      Uri.parse('$baseUrl/settlement/status/$transactionId'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return TransactionStatus.fromJson(data['data']);
      } else {
        throw Exception(data['message'] ?? 'Failed to get transaction status');
      }
    } else {
      throw Exception('Failed to get transaction status: ${response.statusCode}');
    }
  }
}

// Data models for settlement operations
class TransferResult {
  final String transactionId;
  final String status;
  final String message;
  final double amount;
  final String cryptoSymbol;
  final String fromAddress;
  final String toAddress;
  final double networkFee;
  final int confirmations;
  final DateTime createdAt;

  TransferResult({
    required this.transactionId,
    required this.status,
    required this.message,
    required this.amount,
    required this.cryptoSymbol,
    required this.fromAddress,
    required this.toAddress,
    required this.networkFee,
    required this.confirmations,
    required this.createdAt,
  });

  factory TransferResult.fromJson(Map<String, dynamic> json) {
    return TransferResult(
      transactionId: json['transaction_id'] ?? '',
      status: json['status'] ?? 'pending',
      message: json['message'] ?? '',
      amount: (json['amount'] is int) 
          ? (json['amount'] as int).toDouble() 
          : json['amount']?.toDouble() ?? 0.0,
      cryptoSymbol: json['crypto_symbol'] ?? '',
      fromAddress: json['from_address'] ?? '',
      toAddress: json['to_address'] ?? '',
      networkFee: (json['network_fee'] is int) 
          ? (json['network_fee'] as int).toDouble() 
          : json['network_fee']?.toDouble() ?? 0.0,
      confirmations: json['confirmations'] ?? 0,
      createdAt: DateTime.parse(json['created_at'] ?? DateTime.now().toIso8601String()),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'transaction_id': transactionId,
      'status': status,
      'message': message,
      'amount': amount,
      'crypto_symbol': cryptoSymbol,
      'from_address': fromAddress,
      'to_address': toAddress,
      'network_fee': networkFee,
      'confirmations': confirmations,
      'created_at': createdAt.toIso8601String(),
    };
  }
}

class AtomicSwapResult {
  final String swapId;
  final String status;
  final String hashlock;
  final String timelock;
  final String initiatorAddress;
  final String participantAddress;
  final double fromAmount;
  final String fromCrypto;
  final double toAmount;
  final String toCrypto;
  final int lockTimeSeconds;
  final String contractAddress;
  final DateTime createdAt;

  AtomicSwapResult({
    required this.swapId,
    required this.status,
    required this.hashlock,
    required this.timelock,
    required this.initiatorAddress,
    required this.participantAddress,
    required this.fromAmount,
    required this.fromCrypto,
    required this.toAmount,
    required this.toCrypto,
    required this.lockTimeSeconds,
    required this.contractAddress,
    required this.createdAt,
  });

  factory AtomicSwapResult.fromJson(Map<String, dynamic> json) {
    return AtomicSwapResult(
      swapId: json['swap_id'] ?? '',
      status: json['status'] ?? 'initiated',
      hashlock: json['hashlock'] ?? '',
      timelock: json['timelock'] ?? '',
      initiatorAddress: json['initiator_address'] ?? '',
      participantAddress: json['participant_address'] ?? '',
      fromAmount: (json['from_amount'] is int) 
          ? (json['from_amount'] as int).toDouble() 
          : json['from_amount']?.toDouble() ?? 0.0,
      fromCrypto: json['from_crypto'] ?? '',
      toAmount: (json['to_amount'] is int) 
          ? (json['to_amount'] as int).toDouble() 
          : json['to_amount']?.toDouble() ?? 0.0,
      toCrypto: json['to_crypto'] ?? '',
      lockTimeSeconds: json['lock_time_seconds'] ?? 14400,
      contractAddress: json['contract_address'] ?? '',
      createdAt: DateTime.parse(json['created_at'] ?? DateTime.now().toIso8601String()),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'swap_id': swapId,
      'status': status,
      'hashlock': hashlock,
      'timelock': timelock,
      'initiator_address': initiatorAddress,
      'participant_address': participantAddress,
      'from_amount': fromAmount,
      'from_crypto': fromCrypto,
      'to_amount': toAmount,
      'to_crypto': toCrypto,
      'lock_time_seconds': lockTimeSeconds,
      'contract_address': contractAddress,
      'created_at': createdAt.toIso8601String(),
    };
  }
}

class BridgeResult {
  final String bridgeId;
  final String status;
  final String fromChain;
  final String toChain;
  final String cryptoSymbol;
  final double amount;
  final String userAddress;
  final String destinationAddress;
  final double estimatedTimeMinutes;
  final double bridgeFee;
  final String transactionHash;
  final DateTime createdAt;

  BridgeResult({
    required this.bridgeId,
    required this.status,
    required this.fromChain,
    required this.toChain,
    required this.cryptoSymbol,
    required this.amount,
    required this.userAddress,
    required this.destinationAddress,
    required this.estimatedTimeMinutes,
    required this.bridgeFee,
    required this.transactionHash,
    required this.createdAt,
  });

  factory BridgeResult.fromJson(Map<String, dynamic> json) {
    return BridgeResult(
      bridgeId: json['bridge_id'] ?? '',
      status: json['status'] ?? 'pending',
      fromChain: json['from_chain'] ?? '',
      toChain: json['to_chain'] ?? '',
      cryptoSymbol: json['crypto_symbol'] ?? '',
      amount: (json['amount'] is int) 
          ? (json['amount'] as int).toDouble() 
          : json['amount']?.toDouble() ?? 0.0,
      userAddress: json['user_address'] ?? '',
      destinationAddress: json['destination_address'] ?? '',
      estimatedTimeMinutes: (json['estimated_time_minutes'] is int) 
          ? (json['estimated_time_minutes'] as int).toDouble() 
          : json['estimated_time_minutes']?.toDouble() ?? 0.0,
      bridgeFee: (json['bridge_fee'] is int) 
          ? (json['bridge_fee'] as int).toDouble() 
          : json['bridge_fee']?.toDouble() ?? 0.0,
      transactionHash: json['transaction_hash'] ?? '',
      createdAt: DateTime.parse(json['created_at'] ?? DateTime.now().toIso8601String()),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'bridge_id': bridgeId,
      'status': status,
      'from_chain': fromChain,
      'to_chain': toChain,
      'crypto_symbol': cryptoSymbol,
      'amount': amount,
      'user_address': userAddress,
      'destination_address': destinationAddress,
      'estimated_time_minutes': estimatedTimeMinutes,
      'bridge_fee': bridgeFee,
      'transaction_hash': transactionHash,
      'created_at': createdAt.toIso8601String(),
    };
  }
}

class LightningResult {
  final String paymentHash;
  final String status;
  final double amount;
  final String currency;
  final String description;
  final String preimage;
  final double fee;
  final int routingTimeMs;
  final DateTime createdAt;

  LightningResult({
    required this.paymentHash,
    required this.status,
    required this.amount,
    required this.currency,
    required this.description,
    required this.preimage,
    required this.fee,
    required this.routingTimeMs,
    required this.createdAt,
  });

  factory LightningResult.fromJson(Map<String, dynamic> json) {
    return LightningResult(
      paymentHash: json['payment_hash'] ?? '',
      status: json['status'] ?? 'complete',
      amount: (json['amount'] is int) 
          ? (json['amount'] as int).toDouble() 
          : json['amount']?.toDouble() ?? 0.0,
      currency: json['currency'] ?? 'BTC',
      description: json['description'] ?? '',
      preimage: json['preimage'] ?? '',
      fee: (json['fee'] is int) 
          ? (json['fee'] as int).toDouble() 
          : json['fee']?.toDouble() ?? 0.0,
      routingTimeMs: json['routing_time_ms'] ?? 0,
      createdAt: DateTime.parse(json['created_at'] ?? DateTime.now().toIso8601String()),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'payment_hash': paymentHash,
      'status': status,
      'amount': amount,
      'currency': currency,
      'description': description,
      'preimage': preimage,
      'fee': fee,
      'routing_time_ms': routingTimeMs,
      'created_at': createdAt.toIso8601String(),
    };
  }
}

class Layer2Result {
  final String transactionId;
  final String status;
  final String layer2Provider;
  final String toAddress;
  final double amount;
  final double fee;
  final int confirmations;
  final String transactionHash;
  final double estimatedTimeSeconds;
  final DateTime createdAt;

  Layer2Result({
    required this.transactionId,
    required this.status,
    required this.layer2Provider,
    required this.toAddress,
    required this.amount,
    required this.fee,
    required this.confirmations,
    required this.transactionHash,
    required this.estimatedTimeSeconds,
    required this.createdAt,
  });

  factory Layer2Result.fromJson(Map<String, dynamic> json) {
    return Layer2Result(
      transactionId: json['transaction_id'] ?? '',
      status: json['status'] ?? 'pending',
      layer2Provider: json['layer2_provider'] ?? '',
      toAddress: json['to_address'] ?? '',
      amount: (json['amount'] is int) 
          ? (json['amount'] as int).toDouble() 
          : json['amount']?.toDouble() ?? 0.0,
      fee: (json['fee'] is int) 
          ? (json['fee'] as int).toDouble() 
          : json['fee']?.toDouble() ?? 0.0,
      confirmations: json['confirmations'] ?? 0,
      transactionHash: json['transaction_hash'] ?? '',
      estimatedTimeSeconds: (json['estimated_time_seconds'] is int) 
          ? (json['estimated_time_seconds'] as int).toDouble() 
          : json['estimated_time_seconds']?.toDouble() ?? 0.0,
      createdAt: DateTime.parse(json['created_at'] ?? DateTime.now().toIso8601String()),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'transaction_id': transactionId,
      'status': status,
      'layer2_provider': layer2Provider,
      'to_address': toAddress,
      'amount': amount,
      'fee': fee,
      'confirmations': confirmations,
      'transaction_hash': transactionHash,
      'estimated_time_seconds': estimatedTimeSeconds,
      'created_at': createdAt.toIso8601String(),
    };
  }
}

class FiatSettlementResult {
  final String transactionId;
  final String status;
  final double amount;
  final String currency;
  final String paymentMethod;
  final String recipientId;
  final double fee;
  final String referenceNumber;
  final DateTime expectedSettlementAt;
  final DateTime createdAt;

  FiatSettlementResult({
    required this.transactionId,
    required this.status,
    required this.amount,
    required this.currency,
    required this.paymentMethod,
    required this.recipientId,
    required this.fee,
    required this.referenceNumber,
    required this.expectedSettlementAt,
    required this.createdAt,
  });

  factory FiatSettlementResult.fromJson(Map<String, dynamic> json) {
    return FiatSettlementResult(
      transactionId: json['transaction_id'] ?? '',
      status: json['status'] ?? 'processing',
      amount: (json['amount'] is int) 
          ? (json['amount'] as int).toDouble() 
          : json['amount']?.toDouble() ?? 0.0,
      currency: json['currency'] ?? 'USD',
      paymentMethod: json['payment_method'] ?? '',
      recipientId: json['recipient_id'] ?? '',
      fee: (json['fee'] is int) 
          ? (json['fee'] as int).toDouble() 
          : json['fee']?.toDouble() ?? 0.0,
      referenceNumber: json['reference_number'] ?? '',
      expectedSettlementAt: DateTime.parse(json['expected_settlement_at'] ?? DateTime.now().toIso8601String()),
      createdAt: DateTime.parse(json['created_at'] ?? DateTime.now().toIso8601String()),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'transaction_id': transactionId,
      'status': status,
      'amount': amount,
      'currency': currency,
      'payment_method': paymentMethod,
      'recipient_id': recipientId,
      'fee': fee,
      'reference_number': referenceNumber,
      'expected_settlement_at': expectedSettlementAt.toIso8601String(),
      'created_at': createdAt.toIso8601String(),
    };
  }
}

class WalletBalance {
  final String cryptoSymbol;
  final double availableBalance;
  final double lockedBalance;
  final double totalBalance;
  final String address;
  final String chain;
  final bool isMainWallet;

  WalletBalance({
    required this.cryptoSymbol,
    required this.availableBalance,
    required this.lockedBalance,
    required this.totalBalance,
    required this.address,
    required this.chain,
    required this.isMainWallet,
  });

  factory WalletBalance.fromJson(Map<String, dynamic> json) {
    return WalletBalance(
      cryptoSymbol: json['crypto_symbol'] ?? '',
      availableBalance: (json['available_balance'] is int) 
          ? (json['available_balance'] as int).toDouble() 
          : json['available_balance']?.toDouble() ?? 0.0,
      lockedBalance: (json['locked_balance'] is int) 
          ? (json['locked_balance'] as int).toDouble() 
          : json['locked_balance']?.toDouble() ?? 0.0,
      totalBalance: (json['total_balance'] is int) 
          ? (json['total_balance'] as int).toDouble() 
          : json['total_balance']?.toDouble() ?? 0.0,
      address: json['address'] ?? '',
      chain: json['chain'] ?? '',
      isMainWallet: json['is_main_wallet'] ?? false,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'crypto_symbol': cryptoSymbol,
      'available_balance': availableBalance,
      'locked_balance': lockedBalance,
      'total_balance': totalBalance,
      'address': address,
      'chain': chain,
      'is_main_wallet': isMainWallet,
    };
  }
}

class RebalanceResult {
  final String rebalanceId;
  final String status;
  final Map<String, double> initialAllocation;
  final Map<String, double> targetAllocation;
  final Map<String, double> finalAllocation;
  final List<RebalanceTransaction> transactions;
  final double totalFee;
  final DateTime completedAt;
  final DateTime createdAt;

  RebalanceResult({
    required this.rebalanceId,
    required this.status,
    required this.initialAllocation,
    required this.targetAllocation,
    required this.finalAllocation,
    required this.transactions,
    required this.totalFee,
    required this.completedAt,
    required this.createdAt,
  });

  factory RebalanceResult.fromJson(Map<String, dynamic> json) {
    return RebalanceResult(
      rebalanceId: json['rebalance_id'] ?? '',
      status: json['status'] ?? 'completed',
      initialAllocation: Map<String, double>.from(json['initial_allocation'] ?? {}),
      targetAllocation: Map<String, double>.from(json['target_allocation'] ?? {}),
      finalAllocation: Map<String, double>.from(json['final_allocation'] ?? {}),
      transactions: (json['transactions'] as List?)
          ?.map((t) => RebalanceTransaction.fromJson(t))
          .toList() ?? [],
      totalFee: (json['total_fee'] is int) 
          ? (json['total_fee'] as int).toDouble() 
          : json['total_fee']?.toDouble() ?? 0.0,
      completedAt: DateTime.parse(json['completed_at'] ?? DateTime.now().toIso8601String()),
      createdAt: DateTime.parse(json['created_at'] ?? DateTime.now().toIso8601String()),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'rebalance_id': rebalanceId,
      'status': status,
      'initial_allocation': initialAllocation,
      'target_allocation': targetAllocation,
      'final_allocation': finalAllocation,
      'transactions': transactions.map((t) => t.toJson()).toList(),
      'total_fee': totalFee,
      'completed_at': completedAt.toIso8601String(),
      'created_at': createdAt.toIso8601String(),
    };
  }
}

class RebalanceTransaction {
  final String fromCrypto;
  final String toCrypto;
  final double fromAmount;
  final double toAmount;
  final String transactionId;
  final String status;

  RebalanceTransaction({
    required this.fromCrypto,
    required this.toCrypto,
    required this.fromAmount,
    required this.toAmount,
    required this.transactionId,
    required this.status,
  });

  factory RebalanceTransaction.fromJson(Map<String, dynamic> json) {
    return RebalanceTransaction(
      fromCrypto: json['from_crypto'] ?? '',
      toCrypto: json['to_crypto'] ?? '',
      fromAmount: (json['from_amount'] is int) 
          ? (json['from_amount'] as int).toDouble() 
          : json['from_amount']?.toDouble() ?? 0.0,
      toAmount: (json['to_amount'] is int) 
          ? (json['to_amount'] as int).toDouble() 
          : json['to_amount']?.toDouble() ?? 0.0,
      transactionId: json['transaction_id'] ?? '',
      status: json['status'] ?? 'completed',
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'from_crypto': fromCrypto,
      'to_crypto': toCrypto,
      'from_amount': fromAmount,
      'to_amount': toAmount,
      'transaction_id': transactionId,
      'status': status,
    };
  }
}

class SettlementRecord {
  final String recordId;
  final String type; // 'crypto_transfer', 'fiat_settlement', 'atomic_swap', 'cross_chain', 'layer2', 'lightning'
  final String status; // 'completed', 'pending', 'failed', 'cancelled'
  final String cryptoSymbol;
  final double amount;
  final String from;
  final String to;
  final double fee;
  final DateTime createdAt;
  final Map<String, dynamic>? details;

  SettlementRecord({
    required this.recordId,
    required this.type,
    required this.status,
    required this.cryptoSymbol,
    required this.amount,
    required this.from,
    required this.to,
    required this.fee,
    required this.createdAt,
    this.details,
  });

  factory SettlementRecord.fromJson(Map<String, dynamic> json) {
    return SettlementRecord(
      recordId: json['record_id'] ?? '',
      type: json['type'] ?? '',
      status: json['status'] ?? '',
      cryptoSymbol: json['crypto_symbol'] ?? '',
      amount: (json['amount'] is int) 
          ? (json['amount'] as int).toDouble() 
          : json['amount']?.toDouble() ?? 0.0,
      from: json['from'] ?? '',
      to: json['to'] ?? '',
      fee: (json['fee'] is int) 
          ? (json['fee'] as int).toDouble() 
          : json['fee']?.toDouble() ?? 0.0,
      createdAt: DateTime.parse(json['created_at'] ?? DateTime.now().toIso8601String()),
      details: json['details'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'record_id': recordId,
      'type': type,
      'status': status,
      'crypto_symbol': cryptoSymbol,
      'amount': amount,
      'from': from,
      'to': to,
      'fee': fee,
      'created_at': createdAt.toIso8601String(),
      'details': details,
    };
  }
}

class TransactionStatus {
  final String transactionId;
  final String status;
  final int confirmations;
  final String blockHash;
  final int blockNumber;
  final double amount;
  final String cryptoSymbol;
  final String fromAddress;
  final String toAddress;
  final double networkFee;
  final DateTime timestamp;
  final Map<String, dynamic>? rawResponse;

  TransactionStatus({
    required this.transactionId,
    required this.status,
    required this.confirmations,
    required this.blockHash,
    required this.blockNumber,
    required this.amount,
    required this.cryptoSymbol,
    required this.fromAddress,
    required this.toAddress,
    required this.networkFee,
    required this.timestamp,
    this.rawResponse,
  });

  factory TransactionStatus.fromJson(Map<String, dynamic> json) {
    return TransactionStatus(
      transactionId: json['transaction_id'] ?? '',
      status: json['status'] ?? 'unknown',
      confirmations: json['confirmations'] ?? 0,
      blockHash: json['block_hash'] ?? '',
      blockNumber: json['block_number'] ?? 0,
      amount: (json['amount'] is int) 
          ? (json['amount'] as int).toDouble() 
          : json['amount']?.toDouble() ?? 0.0,
      cryptoSymbol: json['crypto_symbol'] ?? '',
      fromAddress: json['from_address'] ?? '',
      toAddress: json['to_address'] ?? '',
      networkFee: (json['network_fee'] is int) 
          ? (json['network_fee'] as int).toDouble() 
          : json['network_fee']?.toDouble() ?? 0.0,
      timestamp: DateTime.parse(json['timestamp'] ?? DateTime.now().toIso8601String()),
      rawResponse: json['raw_response'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'transaction_id': transactionId,
      'status': status,
      'confirmations': confirmations,
      'block_hash': blockHash,
      'block_number': blockNumber,
      'amount': amount,
      'crypto_symbol': cryptoSymbol,
      'from_address': fromAddress,
      'to_address': toAddress,
      'network_fee': networkFee,
      'timestamp': timestamp.toIso8601String(),
      'raw_response': rawResponse,
    };
  }
}