import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../ui/providers/farm_provider.dart';
import '../models/farm_data.dart';

class FarmProfileScreen extends StatefulWidget {
  @override
  _FarmProfileScreenState createState() => _FarmProfileScreenState();
}

class _FarmProfileScreenState extends State<FarmProfileScreen> {
  @override
  Widget build(BuildContext context) {
    final farmProvider = Provider.of<FarmProvider>(context);
    final farmData = farmProvider.farmData;

    return Scaffold(
      appBar: AppBar(
        title: Text('Farm Profile'),
        backgroundColor: Colors.green[400],
        foregroundColor: Colors.white,
      ),
      body: farmProvider.isLoading || farmData == null
          ? Center(child: CircularProgressIndicator())
          : SingleChildScrollView(
              child: Padding(
                padding: EdgeInsets.all(16),
                child: Column(
                  children: [
                    // Farm banner
                    Container(
                      height: 200,
                      width: double.infinity,
                      decoration: BoxDecoration(
                        borderRadius: BorderRadius.circular(12),
                        color: Colors.green[200],
                      ),
                      child: ClipRRect(
                        borderRadius: BorderRadius.circular(12),
                        child: farmData.bannerImage.isNotEmpty
                            ? Image.network(
                                farmData.bannerImage,
                                fit: BoxFit.cover,
                                errorBuilder: (context, error, stackTrace) {
                                  return Icon(
                                    Icons.landscape,
                                    size: 80,
                                    color: Colors.grey[600],
                                  );
                                },
                              )
                            : Icon(
                                Icons.landscape,
                                size: 80,
                                color: Colors.grey[600],
                              ),
                      ),
                    ),
                    SizedBox(height: 16),
                    
                    // Farm logo and name
                    Center(
                      child: Stack(
                        children: [
                          Container(
                            width: 120,
                            height: 120,
                            decoration: BoxDecoration(
                              shape: BoxShape.circle,
                              color: Colors.grey[300],
                              border: Border.all(
                                color: Colors.green[400]!,
                                width: 3,
                              ),
                            ),
                            child: ClipOval(
                              child: farmData.logoUrl.isNotEmpty
                                  ? Image.network(
                                      farmData.logoUrl,
                                      fit: BoxFit.cover,
                                      errorBuilder: (context, error, stackTrace) {
                                        return Icon(
                                          Icons.agriculture,
                                          size: 60,
                                          color: Colors.grey[600],
                                        );
                                      },
                                    )
                                  : Icon(
                                      Icons.agriculture,
                                      size: 60,
                                      color: Colors.grey[600],
                                    ),
                            ),
                          ),
                          Positioned(
                            bottom: 0,
                            right: 0,
                            child: Container(
                              width: 35,
                              height: 35,
                              decoration: BoxDecoration(
                                color: Colors.green[400],
                                shape: BoxShape.circle,
                              ),
                              child: Icon(
                                Icons.edit,
                                size: 20,
                                color: Colors.white,
                              ),
                            ),
                          ),
                        ],
                      ),
                    ),
                    SizedBox(height: 16),
                    
                    Text(
                      farmData.name,
                      style: TextStyle(
                        fontSize: 24,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                    SizedBox(height: 5),
                    Text(
                      farmData.categories.join(' • '),
                      style: TextStyle(
                        fontSize: 16,
                        color: Colors.grey[600],
                      ),
                    ),
                    SizedBox(height: 10),
                    
                    // Rating and stats
                    Row(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        Icon(
                          Icons.star,
                          color: Colors.orange,
                          size: 18,
                        ),
                        SizedBox(width: 5),
                        Text(
                          '${farmData.rating.toStringAsFixed(1)} (${farmData.numReviews} reviews)',
                          style: TextStyle(fontSize: 14, color: Colors.grey[700]),
                        ),
                        SizedBox(width: 15),
                        Container(
                          padding: EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                          decoration: BoxDecoration(
                            color: farmData.isActive ? Colors.green[100] : Colors.red[100],
                            borderRadius: BorderRadius.circular(10),
                          ),
                          child: Text(
                            farmData.isActive ? 'Open' : 'Closed',
                            style: TextStyle(
                              color: farmData.isActive ? Colors.green[800] : Colors.red[800],
                              fontWeight: FontWeight.w500,
                            ),
                          ),
                        ),
                      ],
                    ),
                    SizedBox(height: 20),
                    
                    // Farm information cards
                    Card(
                      elevation: 4,
                      child: Padding(
                        padding: EdgeInsets.all(16),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              'Farm Information',
                              style: TextStyle(
                                fontSize: 18,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                            SizedBox(height: 15),
                            
                            _buildInfoRow('Owner', farmData.ownerName),
                            _buildInfoRow('Email', farmData.email),
                            _buildInfoRow('Phone', farmData.phone),
                            _buildInfoRow('Address', farmData.address),
                            _buildInfoRow('Operating Hours', farmData.operatingHours),
                            _buildInfoRow('Delivery Radius', farmData.deliveryRadius),
                            _buildInfoRow('Certification', farmData.certification),
                            _buildInfoRow('Years in Business', '${farmData.yearsInBusiness} years'),
                          ],
                        ),
                      ),
                    ),
                    SizedBox(height: 20),
                    
                    // Operating hours
                    Card(
                      elevation: 4,
                      child: Padding(
                        padding: EdgeInsets.all(16),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              'Operating Hours',
                              style: TextStyle(
                                fontSize: 18,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                            SizedBox(height: 10),
                            ...farmData.operatingHours.map((hour) =>
                              Padding(
                                padding: EdgeInsets.symmetric(vertical: 4),
                                child: Text(
                                  hour,
                                  style: TextStyle(fontSize: 14),
                                ),
                              ),
                            ),
                          ],
                        ),
                      ),
                    ),
                    SizedBox(height: 20),
                    
                    // Services offered
                    Card(
                      elevation: 4,
                      child: Padding(
                        padding: EdgeInsets.all(16),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              'Services',
                              style: TextStyle(
                                fontSize: 18,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                            SizedBox(height: 10),
                            _buildServiceInfo('Accepts Online Orders', farmData.acceptsOnlineOrders),
                            _buildServiceInfo('Offers Pickup', farmData.offersPickup),
                            _buildServiceInfo('Offers Delivery', farmData.offersDelivery),
                            _buildServiceInfo('Payment Methods', farmData.paymentMethods),
                          ],
                        ),
                      ),
                    ),
                    SizedBox(height: 20),
                    
                    // Edit button
                    ElevatedButton(
                      onPressed: () {
                        _editFarmProfile(context, farmData);
                      },
                      style: ElevatedButton.styleFrom(
                        backgroundColor: Colors.green[400],
                        padding: EdgeInsets.symmetric(horizontal: 40, vertical: 15),
                      ),
                      child: Text(
                        'Edit Farm Profile',
                        style: TextStyle(
                          fontSize: 16,
                          color: Colors.white,
                        ),
                      ),
                    ),
                  ],
                ),
              ),
            ),
    );
  }

  Widget _buildInfoRow(String label, String value) {
    return Padding(
      padding: EdgeInsets.only(bottom: 10),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          SizedBox(
            width: 120,
            child: Text(
              '$label: ',
              style: TextStyle(
                fontWeight: FontWeight.w500,
                color: Colors.grey[700],
              ),
            ),
          ),
          Expanded(
            child: Text(
              value,
              style: TextStyle(
                color: Colors.grey[800],
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildServiceInfo(String label, dynamic value) {
    String displayValue = '';
    IconData icon = Icons.check;
    Color color = Colors.green;
    
    if (value is bool) {
      displayValue = value ? 'Yes' : 'No';
      icon = value ? Icons.check_circle : Icons.cancel;
      color = value ? Colors.green : Colors.red;
    } else {
      displayValue = value.toString();
    }
    
    return Padding(
      padding: EdgeInsets.symmetric(vertical: 5),
      child: Row(
        children: [
          Icon(icon, color: color, size: 16),
          SizedBox(width: 10),
          Expanded(
            child: Text(
              '$label: $displayValue',
              style: TextStyle(fontSize: 14),
            ),
          ),
        ],
      ),
    );
  }

  void _editFarmProfile(BuildContext context, FarmData farmData) {
    // In a real app, this would navigate to an edit screen
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: Text('Edit Farm Profile'),
        content: Text('In a full implementation, you would be able to edit your farm profile details here.'),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context),
            child: Text('OK'),
          ),
        ],
      ),
    );
  }
}