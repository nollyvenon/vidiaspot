// lib/models/nft/nft_model.dart
class Nft {
  final int id;
  final String name;
  final String description;
  final int? collectionId;
  final String? externalUrl;
  final String imageUrl;
  final String? animationUrl;
  final String tokenId;
  final String? contractAddress;
  final String blockchain;
  final int creatorId;
  final int ownerId;
  final double price;
  final String currency;
  final String status;
  final Map<String, dynamic>? properties;
  final Map<String, dynamic>? levels;
  final Map<String, dynamic>? stats;
  final bool isListed;
  final double listPrice;
  final String listCurrency;
  final double royaltyPercentage;
  final int? royaltyRecipient;
  final Map<String, dynamic>? metadata;
  final String tokenStandard;
  final int supply;
  final int maxSupply;
  final bool isSoulbound;
  final bool transferable;

  Nft({
    required this.id,
    required this.name,
    required this.description,
    this.collectionId,
    this.externalUrl,
    required this.imageUrl,
    this.animationUrl,
    required this.tokenId,
    this.contractAddress,
    required this.blockchain,
    required this.creatorId,
    required this.ownerId,
    required this.price,
    required this.currency,
    required this.status,
    this.properties,
    this.levels,
    this.stats,
    required this.isListed,
    required this.listPrice,
    required this.listCurrency,
    required this.royaltyPercentage,
    this.royaltyRecipient,
    this.metadata,
    required this.tokenStandard,
    required this.supply,
    required this.maxSupply,
    required this.isSoulbound,
    required this.transferable,
  });

  factory Nft.fromJson(Map<String, dynamic> json) {
    return Nft(
      id: json['id'] ?? 0,
      name: json['name'] ?? '',
      description: json['description'] ?? '',
      collectionId: json['collection_id'],
      externalUrl: json['external_url'],
      imageUrl: json['image_url'] ?? '',
      animationUrl: json['animation_url'],
      tokenId: json['token_id'] ?? '',
      contractAddress: json['contract_address'],
      blockchain: json['blockchain'] ?? 'ethereum',
      creatorId: json['creator_id'] ?? 0,
      ownerId: json['owner_id'] ?? 0,
      price: (json['price'] is int) 
          ? (json['price'] as int).toDouble() 
          : json['price']?.toDouble() ?? 0.0,
      currency: json['currency'] ?? 'ETH',
      status: json['status'] ?? 'draft',
      properties: json['properties'] ?? {},
      levels: json['levels'] ?? {},
      stats: json['stats'] ?? {},
      isListed: json['is_listed'] ?? false,
      listPrice: (json['list_price'] is int) 
          ? (json['list_price'] as int).toDouble() 
          : json['list_price']?.toDouble() ?? 0.0,
      listCurrency: json['list_currency'] ?? 'ETH',
      royaltyPercentage: (json['royalty_percentage'] is int) 
          ? (json['royalty_percentage'] as int).toDouble() 
          : json['royalty_percentage']?.toDouble() ?? 0.0,
      royaltyRecipient: json['royalty_recipient'],
      metadata: json['metadata'] ?? {},
      tokenStandard: json['token_standard'] ?? 'ERC-721',
      supply: json['supply'] ?? 1,
      maxSupply: json['max_supply'] ?? 1,
      isSoulbound: json['is_soulbound'] ?? false,
      transferable: json['transferable'] ?? true,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'description': description,
      'collection_id': collectionId,
      'external_url': externalUrl,
      'image_url': imageUrl,
      'animation_url': animationUrl,
      'token_id': tokenId,
      'contract_address': contractAddress,
      'blockchain': blockchain,
      'creator_id': creatorId,
      'owner_id': ownerId,
      'price': price,
      'currency': currency,
      'status': status,
      'properties': properties,
      'levels': levels,
      'stats': stats,
      'is_listed': isListed,
      'list_price': listPrice,
      'list_currency': listCurrency,
      'royalty_percentage': royaltyPercentage,
      'royalty_recipient': royaltyRecipient,
      'metadata': metadata,
      'token_standard': tokenStandard,
      'supply': supply,
      'max_supply': maxSupply,
      'is_soulbound': isSoulbound,
      'transferable': transferable,
    };
  }
}