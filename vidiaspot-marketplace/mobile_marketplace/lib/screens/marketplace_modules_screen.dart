// lib/screens/marketplace_modules_screen.dart
import 'package:flutter/material.dart';

class MarketplaceModulesScreen extends StatefulWidget {
  const MarketplaceModulesScreen({Key? key}) : super(key: key);

  @override
  _MarketplaceModulesScreenState createState() => _MarketplaceModulesScreenState();
}

class _MarketplaceModulesScreenState extends State<MarketplaceModulesScreen> {
  final List<Map<String, dynamic>> modules = [
    {
      'title': 'P2P Crypto Marketplace',
      'description': 'Trade cryptocurrencies peer-to-peer with security',
      'icon': Icons.currency_bitcoin,
      'color': Colors.blue,
      'route': '/crypto-p2p',
    },
    {
      'title': 'E-commerce Platform',
      'description': 'Buy and sell products online with ease',
      'icon': Icons.shopping_bag,
      'color': Colors.green,
      'route': '/ecommerce',
    },
    {
      'title': 'Food Vending & Delivery',
      'description': 'Order food from local vendors and restaurants',
      'icon': Icons.restaurant,
      'color': Colors.red,
      'route': '/food-vending',
    },
    {
      'title': 'Logistics & Supply Chain',
      'description': 'Track shipments and manage logistics',
      'icon': Icons.local_shipping,
      'color': Colors.teal,
      'route': '/logistics',
    },
    {
      'title': 'IoT Integration',
      'description': 'Connect and control IoT devices',
      'icon': Icons.devices,
      'color': Colors.purple,
      'route': '/iot',
    },
    {
      'title': 'NFT Marketplace',
      'description': 'Buy, sell, and trade NFTs',
      'icon': Icons.collections,
      'color': Colors.orange,
      'route': '/nft',
    },
  ];

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Marketplace Modules'),
        backgroundColor: Colors.blue,
        foregroundColor: Colors.white,
      ),
      body: Padding(
        padding: const EdgeInsets.all(16.0),
        child: GridView.builder(
          gridDelegate: const SliverGridDelegateWithMaxCrossAxisExtent(
            maxCrossAxisExtent: 200,
            mainAxisSpacing: 10,
            crossAxisSpacing: 10,
            childAspectRatio: 0.8,
          ),
          itemCount: modules.length,
          itemBuilder: (context, index) {
            final module = modules[index];
            return _buildModuleCard(module);
          },
        ),
      ),
    );
  }

  Widget _buildModuleCard(Map<String, dynamic> module) {
    return GestureDetector(
      onTap: () {
        Navigator.pushNamed(context, module['route']);
      },
      child: Card(
        elevation: 4,
        child: Container(
          padding: const EdgeInsets.all(16),
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(12),
            gradient: LinearGradient(
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
              colors: [
                module['color'].withOpacity(0.1),
                module['color'].withOpacity(0.3),
              ],
            ),
          ),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Container(
                width: 60,
                height: 60,
                decoration: BoxDecoration(
                  color: module['color'].withOpacity(0.2),
                  borderRadius: BorderRadius.circular(30),
                ),
                child: Icon(
                  module['icon'],
                  size: 30,
                  color: module['color'],
                ),
              ),
              const SizedBox(height: 12),
              Text(
                module['title'],
                style: const TextStyle(
                  fontSize: 16,
                  fontWeight: FontWeight.bold,
                  height: 1.3,
                ),
                textAlign: TextAlign.center,
              ),
              const SizedBox(height: 8),
              Text(
                module['description'],
                style: const TextStyle(
                  fontSize: 12,
                  color: Colors.grey,
                ),
                textAlign: TextAlign.center,
              ),
              const SizedBox(height: 12),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                decoration: BoxDecoration(
                  color: module['color'].withOpacity(0.1),
                  borderRadius: BorderRadius.circular(20),
                ),
                child: const Text(
                  'Explore',
                  style: TextStyle(
                    fontSize: 12,
                    fontWeight: FontWeight.w500,
                    color: Colors.blue,
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}