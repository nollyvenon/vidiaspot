// lib/services/defi/staking_service.dart
import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import '../../models/defi/staking_model.dart';

class StakingService {
  final String baseUrl = 'http://10.0.2.2:8000/api';
  String? _authToken;

  StakingService() {
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

  // Get all staking pools
  Future<List<StakingPool>> getStakingPools({
    String? blockchain,
    String? rewardToken,
    double? minApr,
    bool? isVerified,
    int page = 1,
    int perPage = 20,
  }) async {
    String url = '$baseUrl/defi/staking/pools?page=$page&per_page=$perPage';
    if (blockchain != null) url += '&blockchain=$blockchain';
    if (rewardToken != null) url += '&reward_token=$rewardToken';
    if (minApr != null) url += '&min_apr=$minApr';
    if (isVerified != null) url += '&verified=${isVerified ? 1 : 0}';

    final response = await http.get(Uri.parse(url), headers: getHeaders());

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] ?? false) {
        List<dynamic> pools = data['data'];
        return pools.map((json) => StakingPool.fromJson(json)).toList();
      } else {
        throw Exception(data['message'] ?? 'Failed to load staking pools');
      }
    } else {
      throw Exception('Failed to load staking pools: ${response.statusCode}');
    }
  }

  // Get staking pool by ID
  Future<StakingPool> getStakingPool(int poolId) async {
    final response = await http.get(
      Uri.parse('$baseUrl/defi/staking/pools/$poolId'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return StakingPool.fromJson(data['data']);
      } else {
        throw Exception(data['message'] ?? 'Failed to load staking pool');
      }
    } else {
      throw Exception('Failed to load staking pool: ${response.statusCode}');
    }
  }

  // Get user's staking positions
  Future<List<UserStakingPosition>> getUserStakingPositions() async {
    final response = await http.get(
      Uri.parse('$baseUrl/defi/staking/positions'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] ?? false) {
        List<dynamic> positions = data['data'];
        return positions.map((json) => UserStakingPosition.fromJson(json)).toList();
      } else {
        throw Exception(data['message'] ?? 'Failed to load staking positions');
      }
    } else {
      throw Exception('Failed to load staking positions: ${response.statusCode}');
    }
  }

  // Stake in a pool
  Future<UserStakingPosition> stakeInPool({
    required int poolId,
    required double amount,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/defi/staking/pools/$poolId/stake'),
      headers: getHeaders(),
      body: jsonEncode({
        'amount': amount,
      }),
    );

    if (response.statusCode == 201) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return UserStakingPosition.fromJson(data['data']);
      } else {
        throw Exception(data['message'] ?? 'Failed to stake in pool');
      }
    } else {
      throw Exception('Failed to stake in pool: ${response.statusCode}');
    }
  }

  // Unstake from a pool
  Future<bool> unstakeFromPool({
    required int positionId,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/defi/staking/positions/$positionId/unstake'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      return data['success'] ?? false;
    } else {
      throw Exception('Failed to unstake from pool: ${response.statusCode}');
    }
  }

  // Claim rewards from staking position
  Future<double> claimRewards({
    required int positionId,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/defi/staking/positions/$positionId/claim-rewards'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return (data['reward_amount'] is int)
            ? (data['reward_amount'] as int).toDouble()
            : data['reward_amount']?.toDouble() ?? 0.0;
      } else {
        throw Exception(data['message'] ?? 'Failed to claim rewards');
      }
    } else {
      throw Exception('Failed to claim rewards: ${response.statusCode}');
    }
  }

  // Get yield farming pools (liquidity pools)
  Future<List<LiquidityPool>> getYieldFarmingPools({
    String? blockchain,
    String? poolType,
    double? minApr,
    int page = 1,
    int perPage = 20,
  }) async {
    String url = '$baseUrl/defi/yield-farming/pools?page=$page&per_page=$perPage';
    if (blockchain != null) url += '&blockchain=$blockchain';
    if (poolType != null) url += '&pool_type=$poolType';
    if (minApr != null) url += '&min_apr=$minApr';

    final response = await http.get(Uri.parse(url), headers: getHeaders());

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] ?? false) {
        List<dynamic> pools = data['data'];
        return pools.map((json) => LiquidityPool.fromJson(json)).toList();
      } else {
        throw Exception(data['message'] ?? 'Failed to load yield farming pools');
      }
    } else {
      throw Exception('Failed to load yield farming pools: ${response.statusCode}');
    }
  }

  // Get user's yield farming positions
  Future<List<YieldFarmingPosition>> getUserYieldFarmingPositions() async {
    final response = await http.get(
      Uri.parse('$baseUrl/defi/yield-farming/positions'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] ?? false) {
        List<dynamic> positions = data['data'];
        return positions.map((json) => YieldFarmingPosition.fromJson(json)).toList();
      } else {
        throw Exception(data['message'] ?? 'Failed to load yield farming positions');
      }
    } else {
      throw Exception('Failed to load yield farming positions: ${response.statusCode}');
    }
  }

  // Add liquidity to a pool
  Future<YieldFarmingPosition> addLiquidity({
    required int poolId,
    required double amount,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/defi/yield-farming/pools/$poolId/add-liquidity'),
      headers: getHeaders(),
      body: jsonEncode({
        'amount': amount,
      }),
    );

    if (response.statusCode == 201) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return YieldFarmingPosition.fromJson(data['data']);
      } else {
        throw Exception(data['message'] ?? 'Failed to add liquidity');
      }
    } else {
      throw Exception('Failed to add liquidity: ${response.statusCode}');
    }
  }

  // Remove liquidity from a pool
  Future<bool> removeLiquidity({
    required int positionId,
    required double share,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/defi/yield-farming/positions/$positionId/remove-liquidity'),
      headers: getHeaders(),
      body: jsonEncode({
        'share': share,
      }),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      return data['success'] ?? false;
    } else {
      throw Exception('Failed to remove liquidity: ${response.statusCode}');
    }
  }

  // Get NFT marketplace
  Future<List<NFTMarketplace>> getNFTMarketplaces({
    String? blockchain,
    bool? isVerified,
    int page = 1,
    int perPage = 20,
  }) async {
    String url = '$baseUrl/defi/nft/marketplaces?page=$page&per_page=$perPage';
    if (blockchain != null) url += '&blockchain=$blockchain';
    if (isVerified != null) url += '&verified=${isVerified ? 1 : 0}';

    final response = await http.get(Uri.parse(url), headers: getHeaders());

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] ?? false) {
        List<dynamic> marketplaces = data['data'];
        return marketplaces.map((json) => NFTMarketplace.fromJson(json)).toList();
      } else {
        throw Exception(data['message'] ?? 'Failed to load NFT marketplaces');
      }
    } else {
      throw Exception('Failed to load NFT marketplaces: ${response.statusCode}');
    }
  }

  // Get NFTs in a marketplace
  Future<List<NFTToken>> getNFTsInMarketplace({
    required int marketplaceId,
    String? collection,
    String? trait,
    int page = 1,
    int perPage = 20,
  }) async {
    String url = '$baseUrl/defi/nft/marketplaces/$marketplaceId/nfts?page=$page&per_page=$perPage';
    if (collection != null) url += '&collection=$collection';
    if (trait != null) url += '&trait=$trait';

    final response = await http.get(Uri.parse(url), headers: getHeaders());

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] ?? false) {
        List<dynamic> nfts = data['data'];
        return nfts.map((json) => NFTToken.fromJson(json)).toList();
      } else {
        throw Exception(data['message'] ?? 'Failed to load NFTs');
      }
    } else {
      throw Exception('Failed to load NFTs: ${response.statusCode}');
    }
  }

  // Get user's NFTs
  Future<List<NFTToken>> getUserNFTs() async {
    final response = await http.get(
      Uri.parse('$baseUrl/defi/nft/user-nfts'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] ?? false) {
        List<dynamic> nfts = data['data'];
        return nfts.map((json) => NFTToken.fromJson(json)).toList();
      } else {
        throw Exception(data['message'] ?? 'Failed to load user NFTs');
      }
    } else {
      throw Exception('Failed to load user NFTs: ${response.statusCode}');
    }
  }

  // Get DeFi protocols
  Future<Map<String, dynamic>> getDeFiProtocols() async {
    final response = await http.get(
      Uri.parse('$baseUrl/defi/protocols'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] ?? false) {
        return data['data'];
      } else {
        throw Exception(data['message'] ?? 'Failed to load DeFi protocols');
      }
    } else {
      throw Exception('Failed to load DeFi protocols: ${response.statusCode}');
    }
  }

  // Get cross-chain bridges
  Future<Map<String, dynamic>> getCrossChainBridges() async {
    final response = await http.get(
      Uri.parse('$baseUrl/defi/cross-chain/bridges'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] ?? false) {
        return data['data'];
      } else {
        throw Exception(data['message'] ?? 'Failed to load cross-chain bridges');
      }
    } else {
      throw Exception('Failed to load cross-chain bridges: ${response.statusCode}');
    }
  }

  // Get prediction markets
  Future<Map<String, dynamic>> getPredictionMarkets() async {
    final response = await http.get(
      Uri.parse('$baseUrl/defi/prediction-markets'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] ?? false) {
        return data['data'];
      } else {
        throw Exception(data['message'] ?? 'Failed to load prediction markets');
      }
    } else {
      throw Exception('Failed to load prediction markets: ${response.statusCode}');
    }
  }

  // Get synthetic assets
  Future<Map<String, dynamic>> getSyntheticAssets() async {
    final response = await http.get(
      Uri.parse('$baseUrl/defi/synthetic-assets'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] ?? false) {
        return data['data'];
      } else {
        throw Exception(data['message'] ?? 'Failed to load synthetic assets');
      }
    } else {
      throw Exception('Failed to load synthetic assets: ${response.statusCode}');
    }
  }

  // Get DAO governance options
  Future<Map<String, dynamic>> getDAOGovernance() async {
    final response = await http.get(
      Uri.parse('$baseUrl/defi/dao/governance'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] ?? false) {
        return data['data'];
      } else {
        throw Exception(data['message'] ?? 'Failed to load DAO governance options');
      }
    } else {
      throw Exception('Failed to load DAO governance options: ${response.statusCode}');
    }
  }

  // Vote in a proposal
  Future<bool> voteInProposal({
    required String proposalId,
    required bool vote,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/defi/dao/proposals/$proposalId/vote'),
      headers: getHeaders(),
      body: jsonEncode({
        'vote': vote,
      }),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      return data['success'] ?? false;
    } else {
      throw Exception('Failed to vote in proposal: ${response.statusCode}');
    }
  }
}