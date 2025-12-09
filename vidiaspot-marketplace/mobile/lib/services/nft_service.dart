// lib/services/nft_service.dart
import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import '../models/nft/nft_model.dart';
import '../models/nft/nft_collection_model.dart';

class NftService {
  final String baseUrl = 'http://10.0.2.2:8000'; // For Android emulator, adjust as needed
  String? _authToken;

  NftService() {
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

  // Get marketplace NFTs
  Future<List<Nft>> getMarketplaceNfts({
    String? collectionId,
    String? blockchain,
    int? creatorId,
    double? priceMin,
    double? priceMax,
    String? search,
    int perPage = 20,
  }) async {
    String url = '$baseUrl/nfts/marketplace?page=1&per_page=$perPage';
    
    if (collectionId != null) url += '&collection_id=$collectionId';
    if (blockchain != null) url += '&blockchain=$blockchain';
    if (creatorId != null) url += '&creator_id=$creatorId';
    if (priceMin != null) url += '&price_min=$priceMin';
    if (priceMax != null) url += '&price_max=$priceMax';
    if (search != null) url += '&search=$search';

    final response = await http.get(Uri.parse(url), headers: getHeaders());

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] ?? false) {
        return (data['nfts']['data'] as List)
            .map((json) => Nft.fromJson(json))
            .toList();
      } else {
        throw Exception(data['message'] ?? 'Failed to load NFTs');
      }
    } else {
      throw Exception('Failed to load NFTs: ${response.statusCode}');
    }
  }

  // Get NFT details
  Future<Nft> getNftDetails(int nftId) async {
    final response = await http.get(
      Uri.parse('$baseUrl/nfts/$nftId'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      return Nft.fromJson(data);
    } else {
      throw Exception('Failed to load NFT: ${response.statusCode}');
    }
  }

  // Get NFT collections
  Future<List<NftCollection>> getCollections({
    int? creatorId,
    String? category,
    bool? verified,
    int perPage = 20,
  }) async {
    String url = '$baseUrl/nft-collections?page=1&per_page=$perPage';
    
    if (creatorId != null) url += '&creator_id=$creatorId';
    if (category != null) url += '&category=$category';
    if (verified != null) url += '&verified=$verified';

    final response = await http.get(Uri.parse(url), headers: getHeaders());

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] ?? false) {
        return (data['collections']['data'] as List)
            .map((json) => NftCollection.fromJson(json))
            .toList();
      } else {
        throw Exception(data['message'] ?? 'Failed to load collections');
      }
    } else {
      throw Exception('Failed to load collections: ${response.statusCode}');
    }
  }

  // Get user's NFTs
  Future<List<Nft>> getUserNfts() async {
    final response = await http.get(
      Uri.parse('$baseUrl/nfts/user'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return (data['nfts'] as List)
            .map((json) => Nft.fromJson(json))
            .toList();
      } else {
        throw Exception(data['message'] ?? 'Failed to load user NFTs');
      }
    } else {
      throw Exception('Failed to load user NFTs: ${response.statusCode}');
    }
  }

  // Get user's collections
  Future<List<NftCollection>> getUserCollections() async {
    final response = await http.get(
      Uri.parse('$baseUrl/nft-collections/user'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return (data['collections'] as List)
            .map((json) => NftCollection.fromJson(json))
            .toList();
      } else {
        throw Exception(data['message'] ?? 'Failed to load user collections');
      }
    } else {
      throw Exception('Failed to load user collections: ${response.statusCode}');
    }
  }

  // Create a new NFT collection
  Future<NftCollection> createCollection({
    required String name,
    String? description,
    String? externalUrl,
    String? twitterUsername,
    String? instagramUsername,
    String? discordUrl,
    String category = 'art',
    String blockchain = 'ethereum',
    String tokenStandard = 'ERC-721',
    double royaltyPercentage = 0.0,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/nft-collections'),
      headers: getHeaders(),
      body: jsonEncode({
        'name': name,
        'description': description ?? '',
        'external_url': externalUrl,
        'twitter_username': twitterUsername,
        'instagram_username': instagramUsername,
        'discord_url': discordUrl,
        'category': category,
        'blockchain': blockchain,
        'token_standard': tokenStandard,
        'royalty_percentage': royaltyPercentage,
      }),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return NftCollection.fromJson(data['collection']);
      } else {
        throw Exception(data['message'] ?? 'Failed to create collection');
      }
    } else {
      throw Exception('Failed to create collection: ${response.statusCode}');
    }
  }

  // Create a new NFT
  Future<Nft> createNft({
    required String name,
    required String imageUrl,
    String? description,
    int? collectionId,
    String? externalUrl,
    String? animationUrl,
    String blockchain = 'ethereum',
    double price = 0.0,
    String currency = 'ETH',
    bool isListed = false,
    double listPrice = 0.0,
    String listCurrency = 'ETH',
    double royaltyPercentage = 0.0,
    Map<String, dynamic>? properties,
    Map<String, dynamic>? levels,
    Map<String, dynamic>? stats,
    int supply = 1,
    int maxSupply = 1,
    bool isSoulbound = false,
    bool transferable = true,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/nfts'),
      headers: getHeaders(),
      body: jsonEncode({
        'name': name,
        'description': description ?? '',
        'collection_id': collectionId,
        'external_url': externalUrl,
        'image_url': imageUrl,
        'animation_url': animationUrl,
        'blockchain': blockchain,
        'price': price,
        'currency': currency,
        'is_listed': isListed,
        'list_price': listPrice,
        'list_currency': listCurrency,
        'royalty_percentage': royaltyPercentage,
        'properties': properties ?? {},
        'levels': levels ?? {},
        'stats': stats ?? {},
        'supply': supply,
        'max_supply': maxSupply,
        'is_soulbound': isSoulbound,
        'transferable': transferable,
      }),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return Nft.fromJson(data['nft']);
      } else {
        throw Exception(data['message'] ?? 'Failed to create NFT');
      }
    } else {
      throw Exception('Failed to create NFT: ${response.statusCode}');
    }
  }

  // List NFT for sale
  Future<Nft> listNftForSale({
    required int nftId,
    required double price,
    String currency = 'ETH',
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/nfts/$nftId/list'),
      headers: getHeaders(),
      body: jsonEncode({
        'price': price,
        'currency': currency,
      }),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return Nft.fromJson(data['nft']);
      } else {
        throw Exception(data['message'] ?? 'Failed to list NFT for sale');
      }
    } else {
      throw Exception('Failed to list NFT for sale: ${response.statusCode}');
    }
  }

  // Buy NFT
  Future<Map<String, dynamic>> buyNft(int nftId) async {
    final response = await http.post(
      Uri.parse('$baseUrl/nfts/$nftId/buy'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      return jsonDecode(response.body);
    } else {
      throw Exception('Failed to buy NFT: ${response.statusCode}');
    }
  }

  // Get collection details
  Future<NftCollection> getCollectionDetails(int collectionId) async {
    final response = await http.get(
      Uri.parse('$baseUrl/nft-collections/$collectionId'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      return NftCollection.fromJson(data);
    } else {
      throw Exception('Failed to load collection: ${response.statusCode}');
    }
  }

  // Get NFTs in a collection
  Future<List<Nft>> getNftsInCollection(int collectionId) async {
    final response = await http.get(
      Uri.parse('$baseUrl/nft-collections/$collectionId/nfts'),
      headers: getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        return (data['nfts'] as List)
            .map((json) => Nft.fromJson(json))
            .toList();
      } else {
        throw Exception(data['message'] ?? 'Failed to load collection NFTs');
      }
    } else {
      throw Exception('Failed to load collection NFTs: ${response.statusCode}');
    }
  }
}