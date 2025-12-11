// lib/screens/crypto_p2p/create_crypto_listing_screen.dart
import 'package:flutter/material.dart';
import '../../services/crypto_p2p_service.dart';
import '../../models/crypto_p2p/crypto_listing_model.dart';

class CreateCryptoListingScreen extends StatefulWidget {
  const CreateCryptoListingScreen({Key? key}) : super(key: key);

  @override
  _CreateCryptoListingScreenState createState() => _CreateCryptoListingScreenState();
}

class _CreateCryptoListingScreenState extends State<CreateCryptoListingScreen> {
  final _formKey = GlobalKey<FormState>();
  final CryptoP2PService _cryptoP2PService = CryptoP2PService();
  
  String _cryptoCurrency = 'BTC';
  String _fiatCurrency = 'NGN';
  String _tradeType = 'sell';
  double _pricePerUnit = 0.0;
  double _minTradeAmount = 0.0;
  double _maxTradeAmount = 0.0;
  double _availableAmount = 0.0;
  double _tradingFeePercent = 0.0;
  String _location = '';
  double _locationRadius = 0.0;
  bool _negotiable = false;
  bool _autoAccept = false;
  int _verificationLevelRequired = 1;
  int _tradeSecurityLevel = 1;
  bool _isPublic = true;
  
  List<String> _selectedPaymentMethods = [];
  List<String> _availablePaymentMethods = [
    'bank_transfer', 'mobile_money', 'cash', 'online_wallet'
  ];
  
  bool _isLoading = false;

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Create Listing'),
        backgroundColor: Colors.blue,
        foregroundColor: Colors.white,
      ),
      body: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Form(
          key: _formKey,
          child: ListView(
            children: [
              // Crypto Currency Selector
              _buildCurrencySelector(),
              
              const SizedBox(height: 20),
              
              // Trade Type
              _buildTradeTypeSelector(),
              
              const SizedBox(height: 20),
              
              // Price and Amounts Section
              _buildPriceAndAmounts(),
              
              const SizedBox(height: 20),
              
              // Available Amount (for sell listings)
              if (_tradeType == 'sell') ...[
                _buildAvailableAmount(),
                const SizedBox(height: 20),
              ],
              
              // Payment Methods
              _buildPaymentMethods(),
              
              const SizedBox(height: 20),
              
              // Fees and Settings
              _buildFeesAndSettings(),
              
              const SizedBox(height: 20),
              
              // Location (Optional)
              _buildLocationSection(),
              
              const SizedBox(height: 30),
              
              // Create Button
              _buildCreateButton(),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildCurrencySelector() {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.grey[100],
        borderRadius: BorderRadius.circular(12),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text(
            'Cryptocurrency & Fiat',
            style: TextStyle(
              fontSize: 16,
              fontWeight: FontWeight.bold,
            ),
          ),
          const SizedBox(height: 10),
          Row(
            children: [
              Expanded(
                child: DropdownButtonFormField<String>(
                  value: _cryptoCurrency,
                  decoration: const InputDecoration(
                    labelText: 'Crypto Currency',
                    border: OutlineInputBorder(),
                  ),
                  items: [
                    DropdownMenuItem(value: 'BTC', child: Text('Bitcoin (BTC)')),
                    DropdownMenuItem(value: 'ETH', child: Text('Ethereum (ETH)')),
                    DropdownMenuItem(value: 'USDT', child: Text('Tether (USDT)')),
                    DropdownMenuItem(value: 'USDC', child: Text('USD Coin (USDC)')),
                    DropdownMenuItem(value: 'BNB', child: Text('Binance Coin (BNB)')),
                    DropdownMenuItem(value: 'ADA', child: Text('Cardano (ADA)')),
                    DropdownMenuItem(value: 'SOL', child: Text('Solana (SOL)')),
                    DropdownMenuItem(value: 'DOT', child: Text('Polkadot (DOT)')),
                    DropdownMenuItem(value: 'DOGE', child: Text('Dogecoin (DOGE)')),
                    DropdownMenuItem(value: 'LTC', child: Text('Litecoin (LTC)')),
                  ],
                  onChanged: (value) {
                    if (value != null) {
                      setState(() {
                        _cryptoCurrency = value;
                      });
                    }
                  },
                ),
              ),
              const SizedBox(width: 10),
              Expanded(
                child: DropdownButtonFormField<String>(
                  value: _fiatCurrency,
                  decoration: const InputDecoration(
                    labelText: 'Fiat Currency',
                    border: OutlineInputBorder(),
                  ),
                  items: [
                    DropdownMenuItem(value: 'NGN', child: Text('Nigerian Naira (NGN)')),
                    DropdownMenuItem(value: 'USD', child: Text('US Dollar (USD)')),
                    DropdownMenuItem(value: 'EUR', child: Text('Euro (EUR)')),
                    DropdownMenuItem(value: 'GBP', child: Text('British Pound (GBP)')),
                    DropdownMenuItem(value: 'GHS', child: Text('Ghanaian Cedi (GHS)')),
                  ],
                  onChanged: (value) {
                    if (value != null) {
                      setState(() {
                        _fiatCurrency = value;
                      });
                    }
                  },
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildTradeTypeSelector() {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.grey[100],
        borderRadius: BorderRadius.circular(12),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text(
            'Trade Type',
            style: TextStyle(
              fontSize: 16,
              fontWeight: FontWeight.bold,
            ),
          ),
          const SizedBox(height: 10),
          SegmentedButton<String>(
            segments: const [
              ButtonSegment(
                value: 'buy',
                label: Text('Buy Crypto'),
                icon: Icon(Icons.trending_down),
              ),
              ButtonSegment(
                value: 'sell',
                label: Text('Sell Crypto'),
                icon: Icon(Icons.trending_up),
              ),
            ],
            selected: {_tradeType},
            onSelectionChanged: (Set<String> newSelection) {
              setState(() {
                _tradeType = newSelection.first;
              });
            },
          ),
        ],
      ),
    );
  }

  Widget _buildPriceAndAmounts() {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.grey[100],
        borderRadius: BorderRadius.circular(12),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text(
            'Price & Amounts',
            style: TextStyle(
              fontSize: 16,
              fontWeight: FontWeight.bold,
            ),
          ),
          const SizedBox(height: 10),
          TextFormField(
            decoration: InputDecoration(
              labelText: 'Price per Unit ($fiatCurrency)',
              border: const OutlineInputBorder(),
              prefixText: '${_fiatCurrency} ',
            ),
            keyboardType: const TextInputType.numberWithOptions(decimal: true),
            validator: (value) {
              if (value == null || value.isEmpty) {
                return 'Please enter a price';
              }
              final price = double.tryParse(value);
              if (price == null || price <= 0) {
                return 'Please enter a valid price';
              }
              return null;
            },
            onChanged: (value) {
              final price = double.tryParse(value);
              if (price != null) {
                setState(() {
                  _pricePerUnit = price;
                });
              }
            },
          ),
          const SizedBox(height: 10),
          Row(
            children: [
              Expanded(
                child: TextFormField(
                  decoration: InputDecoration(
                    labelText: 'Min Trade Amount ($fiatCurrency)',
                    border: const OutlineInputBorder(),
                    prefixText: '${_fiatCurrency} ',
                  ),
                  keyboardType: const TextInputType.numberWithOptions(decimal: true),
                  validator: (value) {
                    if (value == null || value.isEmpty) {
                      return 'Please enter minimum trade amount';
                    }
                    final amount = double.tryParse(value);
                    if (amount == null || amount < 0) {
                      return 'Please enter a valid amount';
                    }
                    return null;
                  },
                  onChanged: (value) {
                    final amount = double.tryParse(value);
                    if (amount != null) {
                      setState(() {
                        _minTradeAmount = amount;
                      });
                    }
                  },
                ),
              ),
              const SizedBox(width: 10),
              Expanded(
                child: TextFormField(
                  decoration: InputDecoration(
                    labelText: 'Max Trade Amount ($fiatCurrency)',
                    border: const OutlineInputBorder(),
                    prefixText: '${_fiatCurrency} ',
                  ),
                  keyboardType: const TextInputType.numberWithOptions(decimal: true),
                  validator: (value) {
                    if (value == null || value.isEmpty) {
                      return 'Please enter maximum trade amount';
                    }
                    final amount = double.tryParse(value);
                    if (amount == null || amount < 0) {
                      return 'Please enter a valid amount';
                    }
                    if (amount < _minTradeAmount) {
                      return 'Max amount must be greater than min amount';
                    }
                    return null;
                  },
                  onChanged: (value) {
                    final amount = double.tryParse(value);
                    if (amount != null) {
                      setState(() {
                        _maxTradeAmount = amount;
                      });
                    }
                  },
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildAvailableAmount() {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.grey[100],
        borderRadius: BorderRadius.circular(12),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text(
            'Available Amount',
            style: TextStyle(
              fontSize: 16,
              fontWeight: FontWeight.bold,
            ),
          ),
          const SizedBox(height: 10),
          TextFormField(
            decoration: InputDecoration(
              labelText: 'Available Crypto Amount (${_cryptoCurrency})',
              border: const OutlineInputBorder(),
              prefixText: '${_cryptoCurrency} ',
            ),
            keyboardType: const TextInputType.numberWithOptions(decimal: true),
            validator: (value) {
              if (value == null || value.isEmpty) {
                return 'Please enter available amount';
              }
              final amount = double.tryParse(value);
              if (amount == null || amount < 0) {
                return 'Please enter a valid amount';
              }
              return null;
            },
            onChanged: (value) {
              final amount = double.tryParse(value);
              if (amount != null) {
                setState(() {
                  _availableAmount = amount;
                });
              }
            },
          ),
        ],
      ),
    );
  }

  Widget _buildPaymentMethods() {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.grey[100],
        borderRadius: BorderRadius.circular(12),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text(
            'Payment Methods',
            style: TextStyle(
              fontSize: 16,
              fontWeight: FontWeight.bold,
            ),
          ),
          const SizedBox(height: 10),
          Wrap(
            spacing: 8.0,
            runSpacing: 4.0,
            children: _availablePaymentMethods.map((method) {
              bool isSelected = _selectedPaymentMethods.contains(method);
              return ChoiceChip(
                label: Text(_formatPaymentMethod(method)),
                selected: isSelected,
                onSelected: (selected) {
                  setState(() {
                    if (selected) {
                      _selectedPaymentMethods.add(method);
                    } else {
                      _selectedPaymentMethods.remove(method);
                    }
                  });
                },
              );
            }).toList(),
          ),
          if (_selectedPaymentMethods.isEmpty)
            const Padding(
              padding: EdgeInsets.only(top: 8.0),
              child: Text(
                'Please select at least one payment method',
                style: TextStyle(
                  color: Colors.red,
                  fontSize: 12,
                ),
              ),
            ),
        ],
      ),
    );
  }

  Widget _buildFeesAndSettings() {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.grey[100],
        borderRadius: BorderRadius.circular(12),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text(
            'Fees & Settings',
            style: TextStyle(
              fontSize: 16,
              fontWeight: FontWeight.bold,
            ),
          ),
          const SizedBox(height: 10),
          TextFormField(
            decoration: InputDecoration(
              labelText: 'Trading Fee (%)',
              border: const OutlineInputBorder(),
              suffixText: '%',
            ),
            keyboardType: const TextInputType.numberWithOptions(decimal: true),
            onChanged: (value) {
              final fee = double.tryParse(value);
              if (fee != null) {
                setState(() {
                  _tradingFeePercent = fee;
                });
              }
            },
          ),
          const SizedBox(height: 10),
          Row(
            children: [
              Expanded(
                child: SwitchListTile(
                  title: const Text('Negotiable'),
                  value: _negotiable,
                  onChanged: (value) {
                    setState(() {
                      _negotiable = value;
                    });
                  },
                ),
              ),
              Expanded(
                child: SwitchListTile(
                  title: const Text('Auto Accept'),
                  value: _autoAccept,
                  onChanged: (value) {
                    setState(() {
                      _autoAccept = value;
                    });
                  },
                ),
              ),
            ],
          ),
          const SizedBox(height: 10),
          Row(
            children: [
              Expanded(
                child: Container(
                  padding: const EdgeInsets.symmetric(horizontal: 16),
                  decoration: BoxDecoration(
                    border: Border.all(color: Colors.grey),
                    borderRadius: BorderRadius.circular(4),
                  ),
                  child: DropdownButton<int>(
                    value: _verificationLevelRequired,
                    isExpanded: true,
                    underline: Container(),
                    items: [
                      const DropdownMenuItem(value: 1, child: Text('Basic (Level 1)')),
                      const DropdownMenuItem(value: 2, child: Text('Verified (Level 2)')),
                      const DropdownMenuItem(value: 3, child: Text('Premium (Level 3)')),
                    ],
                    onChanged: (value) {
                      if (value != null) {
                        setState(() {
                          _verificationLevelRequired = value;
                        });
                      }
                    },
                  ),
                ),
              ),
              const SizedBox(width: 10),
              Expanded(
                child: Container(
                  padding: const EdgeInsets.symmetric(horizontal: 16),
                  decoration: BoxDecoration(
                    border: Border.all(color: Colors.grey),
                    borderRadius: BorderRadius.circular(4),
                  ),
                  child: DropdownButton<int>(
                    value: _tradeSecurityLevel,
                    isExpanded: true,
                    underline: Container(),
                    items: [
                      const DropdownMenuItem(value: 1, child: Text('Low')),
                      const DropdownMenuItem(value: 2, child: Text('Medium')),
                      const DropdownMenuItem(value: 3, child: Text('High')),
                    ],
                    onChanged: (value) {
                      if (value != null) {
                        setState(() {
                          _tradeSecurityLevel = value;
                        });
                      }
                    },
                  ),
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildLocationSection() {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.grey[100],
        borderRadius: BorderRadius.circular(12),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text(
            'Location (Optional)',
            style: TextStyle(
              fontSize: 16,
              fontWeight: FontWeight.bold,
            ),
          ),
          const SizedBox(height: 10),
          TextFormField(
            decoration: const InputDecoration(
              labelText: 'Location (City, State, Country)',
              border: OutlineInputBorder(),
            ),
            onChanged: (value) {
              setState(() {
                _location = value;
              });
            },
          ),
          const SizedBox(height: 10),
          TextFormField(
            decoration: InputDecoration(
              labelText: 'Radius (km)',
              border: const OutlineInputBorder(),
              suffixText: 'km',
            ),
            keyboardType: TextInputType.number,
            onChanged: (value) {
              final radius = double.tryParse(value);
              if (radius != null) {
                setState(() {
                  _locationRadius = radius;
                });
              }
            },
          ),
        ],
      ),
    );
  }

  Widget _buildCreateButton() {
    return SizedBox(
      height: 50,
      child: ElevatedButton(
        onPressed: _isLoading ? null : _createListing,
        child: _isLoading 
            ? const CircularProgressIndicator()
            : const Text('Create Listing'),
      ),
    );
  }

  String _formatPaymentMethod(String method) {
    switch (method) {
      case 'bank_transfer':
        return 'Bank Transfer';
      case 'mobile_money':
        return 'Mobile Money';
      case 'cash':
        return 'Cash';
      case 'online_wallet':
        return 'Online Wallet';
      default:
        return method;
    }
  }

  void _createListing() async {
    if (_formKey.currentState!.validate()) {
      if (_selectedPaymentMethods.isEmpty) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('Please select at least one payment method'),
            backgroundColor: Colors.red,
          ),
        );
        return;
      }

      setState(() {
        _isLoading = true;
      });

      try {
        final listing = await _cryptoP2PService.createListing(
          cryptoCurrency: _cryptoCurrency,
          fiatCurrency: _fiatCurrency,
          tradeType: _tradeType,
          pricePerUnit: _pricePerUnit,
          minTradeAmount: _minTradeAmount,
          maxTradeAmount: _maxTradeAmount,
          availableAmount: _tradeType == 'sell' ? _availableAmount : null,
          paymentMethods: _selectedPaymentMethods,
          tradingFeePercent: _tradingFeePercent,
          negotiable: _negotiable,
          autoAccept: _autoAccept,
          verificationLevelRequired: _verificationLevelRequired,
          tradeSecurityLevel: _tradeSecurityLevel,
          isPublic: _isPublic,
          location: _location.isEmpty ? null : _location,
          locationRadius: _locationRadius > 0 ? _locationRadius : null,
        );

        if (context.mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('Listing created successfully!'),
              backgroundColor: Colors.green,
            ),
          );
          
          Navigator.pop(context, listing);
        }
      } catch (e) {
        if (context.mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text('Failed to create listing: $e'),
              backgroundColor: Colors.red,
            ),
          );
        }
      } finally {
        if (context.mounted) {
          setState(() {
            _isLoading = false;
          });
        }
      }
    }
  }
}