import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'dart:io';
import 'package:image_picker/image_picker.dart';
import '../services/delivery_management/delivery_service.dart';

class DeliveryPackageDetailScreen extends StatefulWidget {
  final DeliveryPackage deliveryPackage;

  const DeliveryPackageDetailScreen({Key? key, required this.deliveryPackage}) : super(key: key);

  @override
  _DeliveryPackageDetailScreenState createState() => _DeliveryPackageDetailScreenState();
}

class _DeliveryPackageDetailScreenState extends State<DeliveryPackageDetailScreen> {
  File? _capturedImage;
  String? _signature;
  String _notes = '';

  @override
  Widget build(BuildContext context) {
    final deliveryService = Provider.of<DeliveryManagementService>(context);

    return Scaffold(
      appBar: AppBar(
        title: Text('Package Details'),
        backgroundColor: Colors.blue,
        foregroundColor: Colors.white,
      ),
      body: SingleChildScrollView(
        padding: EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Package info card
            Card(
              child: Padding(
                padding: EdgeInsets.all(16),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Text(
                          widget.deliveryPackage.trackingNumber,
                          style: TextStyle(
                            fontSize: 18,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                        Container(
                          padding: EdgeInsets.all(8),
                          decoration: BoxDecoration(
                            color: _getStatusColor(widget.deliveryPackage.status),
                            borderRadius: BorderRadius.circular(12),
                          ),
                          child: Text(
                            _getStatusString(widget.deliveryPackage.status),
                            style: TextStyle(color: Colors.white),
                          ),
                        ),
                      ],
                    ),
                    SizedBox(height: 16),
                    _buildInfoRow('Customer', widget.deliveryPackage.recipientName),
                    _buildInfoRow('Phone', widget.deliveryPackage.recipientPhone),
                    _buildInfoRow('Address', widget.deliveryPackage.deliveryAddress),
                    _buildInfoRow('Package Details', widget.deliveryPackage.packageDetails),
                    _buildInfoRow('Weight', '${widget.deliveryPackage.weight} kg'),
                    _buildInfoRow('Volume', '${widget.deliveryPackage.volume} m³'),
                    _buildInfoRow('Priority', _getPriorityString(widget.deliveryPackage.priority)),
                  ],
                ),
              ),
            ),
            
            SizedBox(height: 16),
            
            // Delivery actions
            if (widget.deliveryPackage.status == DeliveryStatus.outForDelivery)
              Card(
                child: Padding(
                  padding: EdgeInsets.all(16),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        'Delivery Actions',
                        style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
                      ),
                      SizedBox(height: 16),
                      
                      // Capture photo
                      if (widget.deliveryPackage.requiresPhoto && _capturedImage == null)
                        Column(
                          children: [
                            ElevatedButton.icon(
                              onPressed: _capturePhoto,
                              icon: Icon(Icons.camera_alt),
                              label: Text('Capture Delivery Photo'),
                              style: ElevatedButton.styleFrom(
                                backgroundColor: Colors.blue,
                                padding: EdgeInsets.symmetric(horizontal: 20, vertical: 12),
                              ),
                            ),
                            SizedBox(height: 8),
                          ],
                        ),
                      
                      if (_capturedImage != null)
                        Column(
                          children: [
                            Container(
                              height: 200,
                              width: double.infinity,
                              decoration: BoxDecoration(
                                border: Border.all(color: Colors.grey),
                                borderRadius: BorderRadius.circular(8),
                              ),
                              child: ClipRRect(
                                borderRadius: BorderRadius.circular(8),
                                child: Image.file(
                                  _capturedImage!,
                                  fit: BoxFit.cover,
                                ),
                              ),
                            ),
                            SizedBox(height: 8),
                          ],
                        ),
                      
                      // Signature input
                      if (widget.deliveryPackage.requiresSignature && _signature == null)
                        Column(
                          children: [
                            Text('Signature Required'),
                            SizedBox(height: 8),
                            Container(
                              height: 150,
                              width: double.infinity,
                              decoration: BoxDecoration(
                                border: Border.all(color: Colors.grey),
                                borderRadius: BorderRadius.circular(8),
                              ),
                              child: ElevatedButton(
                                onPressed: _captureSignature,
                                child: Text('Capture Signature'),
                              ),
                            ),
                            SizedBox(height: 8),
                          ],
                        ),
                      
                      if (_signature != null)
                        Column(
                          children: [
                            Text('Signature Captured'),
                            SizedBox(height: 8),
                            Container(
                              height: 150,
                              width: double.infinity,
                              decoration: BoxDecoration(
                                border: Border.all(color: Colors.grey),
                                borderRadius: BorderRadius.circular(8),
                              ),
                              child: Center(
                                child: Text(_signature!),
                              ),
                            ),
                            SizedBox(height: 8),
                          ],
                        ),
                      
                      // Notes
                      TextField(
                        maxLines: 3,
                        decoration: InputDecoration(
                          labelText: 'Delivery Notes',
                          border: OutlineInputBorder(),
                        ),
                        onChanged: (value) {
                          setState(() {
                            _notes = value;
                          });
                        },
                      ),
                      
                      SizedBox(height: 16),
                      
                      // Mark as delivered button
                      ElevatedButton(
                        onPressed: _canMarkAsDelivered()
                            ? () {
                                deliveryService.markPackageAsDelivered(
                                  widget.deliveryPackage.id,
                                  signature: _signature,
                                  photo: _capturedImage?.path,
                                  notes: _notes,
                                );
                                
                                Navigator.pop(context);
                                
                                ScaffoldMessenger.of(context).showSnackBar(
                                  SnackBar(
                                    content: Text('Package marked as delivered'),
                                  ),
                                );
                              }
                            : null,
                        style: ElevatedButton.styleFrom(
                          backgroundColor: Colors.green,
                          padding: EdgeInsets.symmetric(horizontal: 30, vertical: 15),
                        ),
                        child: Text(
                          'Mark as Delivered',
                          style: TextStyle(color: Colors.white),
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            
            SizedBox(height: 16),
            
            // Package history
            Card(
              child: Padding(
                padding: EdgeInsets.all(16),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      'Delivery History',
                      style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
                    ),
                    SizedBox(height: 8),
                    // In a real app, we would show delivery events here
                    Text('Delivery history will be displayed here'),
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildInfoRow(String label, String value) {
    return Padding(
      padding: EdgeInsets.symmetric(vertical: 4),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Expanded(
            flex: 1,
            child: Text(
              '$label: ',
              style: TextStyle(fontWeight: FontWeight.bold),
            ),
          ),
          Expanded(
            flex: 2,
            child: Text(value),
          ),
        ],
      ),
    );
  }

  Color _getStatusColor(DeliveryStatus status) {
    switch (status) {
      case DeliveryStatus.pending:
        return Colors.grey;
      case DeliveryStatus.inTransit:
        return Colors.blue;
      case DeliveryStatus.outForDelivery:
        return Colors.orange;
      case DeliveryStatus.delivered:
        return Colors.green;
      case DeliveryStatus.failed:
        return Colors.red;
      case DeliveryStatus.returned:
        return Colors.purple;
    }
  }

  String _getStatusString(DeliveryStatus status) {
    switch (status) {
      case DeliveryStatus.pending:
        return 'Pending';
      case DeliveryStatus.inTransit:
        return 'In Transit';
      case DeliveryStatus.outForDelivery:
        return 'Out for Delivery';
      case DeliveryStatus.delivered:
        return 'Delivered';
      case DeliveryStatus.failed:
        return 'Failed';
      case DeliveryStatus.returned:
        return 'Returned';
    }
  }

  String _getPriorityString(int priority) {
    switch (priority) {
      case 1:
        return 'High';
      case 2:
        return 'Medium-High';
      case 3:
        return 'Medium';
      case 4:
        return 'Medium-Low';
      case 5:
        return 'Low';
      default:
        return 'Medium';
    }
  }

  void _capturePhoto() async {
    final picker = ImagePicker();
    final pickedFile = await picker.pickImage(source: ImageSource.camera);

    if (pickedFile != null) {
      setState(() {
        _capturedImage = File(pickedFile.path);
      });
    }
  }

  void _captureSignature() {
    // In a real app, you would use a signature pad here
    setState(() {
      _signature = 'Signature captured at ${DateTime.now()}';
    });
  }

  bool _canMarkAsDelivered() {
    bool hasRequiredPhoto = !widget.deliveryPackage.requiresPhoto || _capturedImage != null;
    bool hasRequiredSignature = !widget.deliveryPackage.requiresSignature || _signature != null;
    
    return hasRequiredPhoto && hasRequiredSignature;
  }
}