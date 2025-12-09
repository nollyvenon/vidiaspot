// lib/models/nft/nft_collection_model.dart
class NftCollection {
  final int id;
  final String name;
  final String description;
  final String slug;
  final String? bannerImageUrl;
  final String imageUrl;
  final int creatorId;
  final int ownerId;
  final String? externalUrl;
  final String? twitterUsername;
  final String? instagramUsername;
  final String? discordUrl;
  final String category;
  final String status;
  final bool verified;
  final int totalSupply;
  final int mintedSupply;
  final String? contractAddress;
  final String blockchain;
  final String tokenStandard;
  final double royaltyPercentage;
  final int? royaltyRecipient;
  final double feesOnSale;
  final Map<String, dynamic>? metadata;

  NftCollection({
    required this.id,
    required this.name,
    required this.description,
    required this.slug,
    this.bannerImageUrl,
    required this.imageUrl,
    required this.creatorId,
    required this.ownerId,
    this.externalUrl,
    this.twitterUsername,
    this.instagramUsername,
    this.discordUrl,
    required this.category,
    required this.status,
    required this.verified,
    required this.totalSupply,
    required this.mintedSupply,
    this.contractAddress,
    required this.blockchain,
    required this.tokenStandard,
    required this.royaltyPercentage,
    this.royaltyRecipient,
    required this.feesOnSale,
    this.metadata,
  });

  factory NftCollection.fromJson(Map<String, dynamic> json) {
    return NftCollection(
      id: json['id'] ?? 0,
      name: json['name'] ?? '',
      description: json['description'] ?? '',
      slug: json['slug'] ?? '',
      bannerImageUrl: json['banner_image_url'],
      imageUrl: json['image_url'] ?? '',
      creatorId: json['creator_id'] ?? 0,
      ownerId: json['owner_id'] ?? 0,
      externalUrl: json['external_url'],
      twitterUsername: json['twitter_username'],
      instagramUsername: json['instagram_username'],
      discordUrl: json['discord_url'],
      category: json['category'] ?? 'art',
      status: json['status'] ?? 'active',
      verified: json['verified'] ?? false,
      totalSupply: json['total_supply'] ?? 0,
      mintedSupply: json['minted_supply'] ?? 0,
      contractAddress: json['contract_address'],
      blockchain: json['blockchain'] ?? 'ethereum',
      tokenStandard: json['token_standard'] ?? 'ERC-721',
      royaltyPercentage: (json['royalty_percentage'] is int) 
          ? (json['royalty_percentage'] as int).toDouble() 
          : json['royalty_percentage']?.toDouble() ?? 0.0,
      royaltyRecipient: json['royalty_recipient'],
      feesOnSale: (json['fees_on_sale'] is int) 
          ? (json['fees_on_sale'] as int).toDouble() 
          : json['fees_on_sale']?.toDouble() ?? 0.0,
      metadata: json['metadata'] ?? {},
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'description': description,
      'slug': slug,
      'banner_image_url': bannerImageUrl,
      'image_url': imageUrl,
      'creator_id': creatorId,
      'owner_id': ownerId,
      'external_url': externalUrl,
      'twitter_username': twitterUsername,
      'instagram_username': instagramUsername,
      'discord_url': discordUrl,
      'category': category,
      'status': status,
      'verified': verified,
      'total_supply': totalSupply,
      'minted_supply': mintedSupply,
      'contract_address': contractAddress,
      'blockchain': blockchain,
      'token_standard': tokenStandard,
      'royalty_percentage': royaltyPercentage,
      'royalty_recipient': royaltyRecipient,
      'fees_on_sale': feesOnSale,
      'metadata': metadata,
    };
  }
}