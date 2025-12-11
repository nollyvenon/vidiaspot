// lib/models/defi/staking_model.dart
class StakingPool {
  final int id;
  final String name;
  final String symbol;
  final String description;
  final String contractAddress;
  final String blockchain;
  final double apr; // Annual Percentage Rate
  final double tvl; // Total Value Locked
  final String rewardToken;
  final double rewardRate;
  final int lockPeriod; // in days
  final int minStakeAmount;
  final int maxStakeAmount;
  final bool isVerified;
  final String status; // 'active', 'inactive', 'ended'
  final DateTime createdAt;
  final DateTime? endedAt;
  final double totalStaked;
  final String iconUrl;
  final List<String> stakeTokens; // tokens that can be staked
  final List<String> features; // 'auto-compound', 'early-withdraw', etc.

  StakingPool({
    required this.id,
    required this.name,
    required this.symbol,
    required this.description,
    required this.contractAddress,
    required this.blockchain,
    required this.apr,
    required this.tvl,
    required this.rewardToken,
    required this.rewardRate,
    required this.lockPeriod,
    required this.minStakeAmount,
    required this.maxStakeAmount,
    required this.isVerified,
    required this.status,
    required this.createdAt,
    this.endedAt,
    required this.totalStaked,
    required this.iconUrl,
    required this.stakeTokens,
    required this.features,
  });

  factory StakingPool.fromJson(Map<String, dynamic> json) {
    return StakingPool(
      id: json['id'] ?? 0,
      name: json['name'] ?? '',
      symbol: json['symbol'] ?? '',
      description: json['description'] ?? '',
      contractAddress: json['contract_address'] ?? '',
      blockchain: json['blockchain'] ?? '',
      apr: (json['apr'] is int)
          ? (json['apr'] as int).toDouble()
          : json['apr']?.toDouble() ?? 0.0,
      tvl: (json['tvl'] is int)
          ? (json['tvl'] as int).toDouble()
          : json['tvl']?.toDouble() ?? 0.0,
      rewardToken: json['reward_token'] ?? '',
      rewardRate: (json['reward_rate'] is int)
          ? (json['reward_rate'] as int).toDouble()
          : json['reward_rate']?.toDouble() ?? 0.0,
      lockPeriod: json['lock_period'] ?? 0,
      minStakeAmount: json['min_stake_amount'] ?? 0,
      maxStakeAmount: json['max_stake_amount'] ?? 0,
      isVerified: json['is_verified'] ?? false,
      status: json['status'] ?? 'active',
      createdAt: DateTime.parse(json['created_at'] ?? DateTime.now().toIso8601String()),
      endedAt: json['ended_at'] != null ? DateTime.parse(json['ended_at']) : null,
      totalStaked: (json['total_staked'] is int)
          ? (json['total_staked'] as int).toDouble()
          : json['total_staked']?.toDouble() ?? 0.0,
      iconUrl: json['icon_url'] ?? '',
      stakeTokens: List<String>.from(json['stake_tokens'] ?? []),
      features: List<String>.from(json['features'] ?? []),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'symbol': symbol,
      'description': description,
      'contract_address': contractAddress,
      'blockchain': blockchain,
      'apr': apr,
      'tvl': tvl,
      'reward_token': rewardToken,
      'reward_rate': rewardRate,
      'lock_period': lockPeriod,
      'min_stake_amount': minStakeAmount,
      'max_stake_amount': maxStakeAmount,
      'is_verified': isVerified,
      'status': status,
      'created_at': createdAt.toIso8601String(),
      'ended_at': endedAt?.toIso8601String(),
      'total_staked': totalStaked,
      'icon_url': iconUrl,
      'stake_tokens': stakeTokens,
      'features': features,
    };
  }
}

class UserStakingPosition {
  final int id;
  final int userId;
  final int stakingPoolId;
  final double stakedAmount;
  final double rewardEarned;
  final double rewardClaimed;
  final DateTime stakedAt;
  final DateTime? unlockAt;
  final String status; // 'active', 'pending_unlock', 'withdrawn'
  final StakingPool poolDetails;

  UserStakingPosition({
    required this.id,
    required this.userId,
    required this.stakingPoolId,
    required this.stakedAmount,
    required this.rewardEarned,
    required this.rewardClaimed,
    required this.stakedAt,
    this.unlockAt,
    required this.status,
    required this.poolDetails,
  });

  factory UserStakingPosition.fromJson(Map<String, dynamic> json) {
    return UserStakingPosition(
      id: json['id'] ?? 0,
      userId: json['user_id'] ?? 0,
      stakingPoolId: json['staking_pool_id'] ?? 0,
      stakedAmount: (json['staked_amount'] is int)
          ? (json['staked_amount'] as int).toDouble()
          : json['staked_amount']?.toDouble() ?? 0.0,
      rewardEarned: (json['reward_earned'] is int)
          ? (json['reward_earned'] as int).toDouble()
          : json['reward_earned']?.toDouble() ?? 0.0,
      rewardClaimed: (json['reward_claimed'] is int)
          ? (json['reward_claimed'] as int).toDouble()
          : json['reward_claimed']?.toDouble() ?? 0.0,
      stakedAt: DateTime.parse(json['staked_at'] ?? DateTime.now().toIso8601String()),
      unlockAt: json['unlock_at'] != null ? DateTime.parse(json['unlock_at']) : null,
      status: json['status'] ?? 'active',
      poolDetails: json['pool_details'] != null
          ? StakingPool.fromJson(json['pool_details'])
          : StakingPool(
              id: 0,
              name: '',
              symbol: '',
              description: '',
              contractAddress: '',
              blockchain: '',
              apr: 0.0,
              tvl: 0.0,
              rewardToken: '',
              rewardRate: 0.0,
              lockPeriod: 0,
              minStakeAmount: 0,
              maxStakeAmount: 0,
              isVerified: false,
              status: 'active',
              createdAt: DateTime.now(),
              totalStaked: 0.0,
              iconUrl: '',
              stakeTokens: [],
              features: [],
            ),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'user_id': userId,
      'staking_pool_id': stakingPoolId,
      'staked_amount': stakedAmount,
      'reward_earned': rewardEarned,
      'reward_claimed': rewardClaimed,
      'staked_at': stakedAt.toIso8601String(),
      'unlock_at': unlockAt?.toIso8601String(),
      'status': status,
      'pool_details': poolDetails.toJson(),
    };
  }
}

class LiquidityPool {
  final int id;
  final String name;
  final String symbol;
  final String poolType; // 'constant_product', 'stable', 'weighted'
  final String contractAddress;
  final String blockchain;
  final double tvl;
  final double feeRate; // trading fee percentage
  final double apr;
  final List<LiquidityToken> tokens;
  final double totalShares;
  final bool isActive;
  final DateTime createdAt;
  final String iconUrl;
  final Map<String, dynamic> metadata;

  LiquidityPool({
    required this.id,
    required this.name,
    required this.symbol,
    required this.poolType,
    required this.contractAddress,
    required this.blockchain,
    required this.tvl,
    required this.feeRate,
    required this.apr,
    required this.tokens,
    required this.totalShares,
    required this.isActive,
    required this.createdAt,
    required this.iconUrl,
    required this.metadata,
  });

  factory LiquidityPool.fromJson(Map<String, dynamic> json) {
    return LiquidityPool(
      id: json['id'] ?? 0,
      name: json['name'] ?? '',
      symbol: json['symbol'] ?? '',
      poolType: json['pool_type'] ?? 'constant_product',
      contractAddress: json['contract_address'] ?? '',
      blockchain: json['blockchain'] ?? '',
      tvl: (json['tvl'] is int)
          ? (json['tvl'] as int).toDouble()
          : json['tvl']?.toDouble() ?? 0.0,
      feeRate: (json['fee_rate'] is int)
          ? (json['fee_rate'] as int).toDouble()
          : json['fee_rate']?.toDouble() ?? 0.0,
      apr: (json['apr'] is int)
          ? (json['apr'] as int).toDouble()
          : json['apr']?.toDouble() ?? 0.0,
      tokens: (json['tokens'] as List?)
          ?.map((t) => LiquidityToken.fromJson(t))
          .toList() ?? [],
      totalShares: (json['total_shares'] is int)
          ? (json['total_shares'] as int).toDouble()
          : json['total_shares']?.toDouble() ?? 0.0,
      isActive: json['is_active'] ?? true,
      createdAt: DateTime.parse(json['created_at'] ?? DateTime.now().toIso8601String()),
      iconUrl: json['icon_url'] ?? '',
      metadata: json['metadata'] ?? {},
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'symbol': symbol,
      'pool_type': poolType,
      'contract_address': contractAddress,
      'blockchain': blockchain,
      'tvl': tvl,
      'fee_rate': feeRate,
      'apr': apr,
      'tokens': tokens.map((t) => t.toJson()).toList(),
      'total_shares': totalShares,
      'is_active': isActive,
      'created_at': createdAt.toIso8601String(),
      'icon_url': iconUrl,
      'metadata': metadata,
    };
  }
}

class LiquidityToken {
  final String symbol;
  final String tokenAddress;
  final double amount;
  final double weight; // for weighted pools
  final double balance; // total balance in pool

  LiquidityToken({
    required this.symbol,
    required this.tokenAddress,
    required this.amount,
    required this.weight,
    required this.balance,
  });

  factory LiquidityToken.fromJson(Map<String, dynamic> json) {
    return LiquidityToken(
      symbol: json['symbol'] ?? '',
      tokenAddress: json['token_address'] ?? '',
      amount: (json['amount'] is int)
          ? (json['amount'] as int).toDouble()
          : json['amount']?.toDouble() ?? 0.0,
      weight: (json['weight'] is int)
          ? (json['weight'] as int).toDouble()
          : json['weight']?.toDouble() ?? 0.0,
      balance: (json['balance'] is int)
          ? (json['balance'] as int).toDouble()
          : json['balance']?.toDouble() ?? 0.0,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'symbol': symbol,
      'token_address': tokenAddress,
      'amount': amount,
      'weight': weight,
      'balance': balance,
    };
  }
}

class YieldFarmingPosition {
  final int id;
  final int userId;
  final int liquidityPoolId;
  final double liquidityProvided;
  final double poolShare;
  final double rewardEarned;
  final double rewardClaimed;
  final DateTime addedAt;
  final String status; // 'active', 'withdrawn'
  final LiquidityPool poolDetails;

  YieldFarmingPosition({
    required this.id,
    required this.userId,
    required this.liquidityPoolId,
    required this.liquidityProvided,
    required this.poolShare,
    required this.rewardEarned,
    required this.rewardClaimed,
    required this.addedAt,
    required this.status,
    required this.poolDetails,
  });

  factory YieldFarmingPosition.fromJson(Map<String, dynamic> json) {
    return YieldFarmingPosition(
      id: json['id'] ?? 0,
      userId: json['user_id'] ?? 0,
      liquidityPoolId: json['liquidity_pool_id'] ?? 0,
      liquidityProvided: (json['liquidity_provided'] is int)
          ? (json['liquidity_provided'] as int).toDouble()
          : json['liquidity_provided']?.toDouble() ?? 0.0,
      poolShare: (json['pool_share'] is int)
          ? (json['pool_share'] as int).toDouble()
          : json['pool_share']?.toDouble() ?? 0.0,
      rewardEarned: (json['reward_earned'] is int)
          ? (json['reward_earned'] as int).toDouble()
          : json['reward_earned']?.toDouble() ?? 0.0,
      rewardClaimed: (json['reward_claimed'] is int)
          ? (json['reward_claimed'] as int).toDouble()
          : json['reward_claimed']?.toDouble() ?? 0.0,
      addedAt: DateTime.parse(json['added_at'] ?? DateTime.now().toIso8601String()),
      status: json['status'] ?? 'active',
      poolDetails: json['pool_details'] != null
          ? LiquidityPool.fromJson(json['pool_details'])
          : LiquidityPool(
              id: 0,
              name: '',
              symbol: '',
              poolType: 'constant_product',
              contractAddress: '',
              blockchain: '',
              tvl: 0.0,
              feeRate: 0.0,
              apr: 0.0,
              tokens: [],
              totalShares: 0.0,
              isActive: false,
              createdAt: DateTime.now(),
              iconUrl: '',
              metadata: {},
            ),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'user_id': userId,
      'liquidity_pool_id': liquidityPoolId,
      'liquidity_provided': liquidityProvided,
      'pool_share': poolShare,
      'reward_earned': rewardEarned,
      'reward_claimed': rewardClaimed,
      'added_at': addedAt.toIso8601String(),
      'status': status,
      'pool_details': poolDetails.toJson(),
    };
  }
}

class NFTMarketplace {
  final int id;
  final String name;
  final String contractAddress;
  final String blockchain;
  final String symbol; // NFT collection symbol
  final String description;
  final String imageUrl;
  final String bannerUrl;
  final double floorPrice;
  final int totalVolume;
  final int itemsCount;
  final int ownersCount;
  final bool isVerified;
  final double feeRate;
  final String creatorAddress;
  final DateTime createdAt;
  final Map<String, dynamic> metadata;

  NFTMarketplace({
    required this.id,
    required this.name,
    required this.contractAddress,
    required this.blockchain,
    required this.symbol,
    required this.description,
    required this.imageUrl,
    required this.bannerUrl,
    required this.floorPrice,
    required this.totalVolume,
    required this.itemsCount,
    required this.ownersCount,
    required this.isVerified,
    required this.feeRate,
    required this.creatorAddress,
    required this.createdAt,
    required this.metadata,
  });

  factory NFTMarketplace.fromJson(Map<String, dynamic> json) {
    return NFTMarketplace(
      id: json['id'] ?? 0,
      name: json['name'] ?? '',
      contractAddress: json['contract_address'] ?? '',
      blockchain: json['blockchain'] ?? '',
      symbol: json['symbol'] ?? '',
      description: json['description'] ?? '',
      imageUrl: json['image_url'] ?? '',
      bannerUrl: json['banner_url'] ?? '',
      floorPrice: (json['floor_price'] is int)
          ? (json['floor_price'] as int).toDouble()
          : json['floor_price']?.toDouble() ?? 0.0,
      totalVolume: json['total_volume'] ?? 0,
      itemsCount: json['items_count'] ?? 0,
      ownersCount: json['owners_count'] ?? 0,
      isVerified: json['is_verified'] ?? false,
      feeRate: (json['fee_rate'] is int)
          ? (json['fee_rate'] as int).toDouble()
          : json['fee_rate']?.toDouble() ?? 0.0,
      creatorAddress: json['creator_address'] ?? '',
      createdAt: DateTime.parse(json['created_at'] ?? DateTime.now().toIso8601String()),
      metadata: json['metadata'] ?? {},
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'contract_address': contractAddress,
      'blockchain': blockchain,
      'symbol': symbol,
      'description': description,
      'image_url': imageUrl,
      'banner_url': bannerUrl,
      'floor_price': floorPrice,
      'total_volume': totalVolume,
      'items_count': itemsCount,
      'owners_count': ownersCount,
      'is_verified': isVerified,
      'fee_rate': feeRate,
      'creator_address': creatorAddress,
      'created_at': createdAt.toIso8601String(),
      'metadata': metadata,
    };
  }
}

class NFTToken {
  final int id;
  final int marketplaceId;
  final String tokenId;
  final String name;
  final String description;
  final String imageUrl;
  final String animationUrl;
  final String contractAddress;
  final String blockchain;
  final String ownerAddress;
  final double price;
  final String status; // 'listed', 'sold', 'transferred', 'burned'
  final DateTime createdAt;
  final DateTime? listedAt;
  final DateTime? soldAt;
  final List<NFTAttribute> attributes;
  final Map<String, dynamic> properties;

  NFTToken({
    required this.id,
    required this.marketplaceId,
    required this.tokenId,
    required this.name,
    required this.description,
    required this.imageUrl,
    required this.animationUrl,
    required this.contractAddress,
    required this.blockchain,
    required this.ownerAddress,
    required this.price,
    required this.status,
    required this.createdAt,
    this.listedAt,
    this.soldAt,
    required this.attributes,
    required this.properties,
  });

  factory NFTToken.fromJson(Map<String, dynamic> json) {
    return NFTToken(
      id: json['id'] ?? 0,
      marketplaceId: json['marketplace_id'] ?? 0,
      tokenId: json['token_id'] ?? '',
      name: json['name'] ?? '',
      description: json['description'] ?? '',
      imageUrl: json['image_url'] ?? '',
      animationUrl: json['animation_url'] ?? '',
      contractAddress: json['contract_address'] ?? '',
      blockchain: json['blockchain'] ?? '',
      ownerAddress: json['owner_address'] ?? '',
      price: (json['price'] is int)
          ? (json['price'] as int).toDouble()
          : json['price']?.toDouble() ?? 0.0,
      status: json['status'] ?? 'transferred',
      createdAt: DateTime.parse(json['created_at'] ?? DateTime.now().toIso8601String()),
      listedAt: json['listed_at'] != null ? DateTime.parse(json['listed_at']) : null,
      soldAt: json['sold_at'] != null ? DateTime.parse(json['sold_at']) : null,
      attributes: (json['attributes'] as List?)
          ?.map((a) => NFTAttribute.fromJson(a))
          .toList() ?? [],
      properties: json['properties'] ?? {},
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'marketplace_id': marketplaceId,
      'token_id': tokenId,
      'name': name,
      'description': description,
      'image_url': imageUrl,
      'animation_url': animationUrl,
      'contract_address': contractAddress,
      'blockchain': blockchain,
      'owner_address': ownerAddress,
      'price': price,
      'status': status,
      'created_at': createdAt.toIso8601String(),
      'listed_at': listedAt?.toIso8601String(),
      'sold_at': soldAt?.toIso8601String(),
      'attributes': attributes.map((a) => a.toJson()).toList(),
      'properties': properties,
    };
  }
}

class NFTAttribute {
  final String traitType;
  final dynamic value;
  final String displayType; // 'string', 'number', 'date', etc.

  NFTAttribute({
    required this.traitType,
    required this.value,
    required this.displayType,
  });

  factory NFTAttribute.fromJson(Map<String, dynamic> json) {
    return NFTAttribute(
      traitType: json['trait_type'] ?? '',
      value: json['value'],
      displayType: json['display_type'] ?? 'string',
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'trait_type': traitType,
      'value': value,
      'display_type': displayType,
    };
  }
}