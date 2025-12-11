import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:qr_code_scanner/qr_code_scanner.dart';
import 'dart:io';
import '../services/delivery_management/delivery_service.dart';

class PackageScannerScreen extends StatefulWidget {
  @override
  _PackageScannerScreenState createState() => _PackageScannerScreenState();
}

class _PackageScannerScreenState extends State<PackageScannerScreen> {
  final GlobalKey qrKey = GlobalKey(debugLabel: 'QR');
  QRViewController? controller;
  Barcode? barcode;

  @override
  void reassemble() {
    super.reassemble();
    if (Platform.isAndroid) {
      controller!.pauseCamera();
    } else if (Platform.isIOS) {
      controller!.resumeCamera();
    }
  }

  @override
  Widget build(BuildContext context) {
    final deliveryService = Provider.of<DeliveryManagementService>(context);
    
    return Scaffold(
      appBar: AppBar(
        title: Text('Package Scanner'),
        backgroundColor: Colors.blue,
        foregroundColor: Colors.white,
      ),
      body: Column(
        children: [
          Expanded(
            flex: 4,
            child: QRView(
              key: qrKey,
              onQRViewCreated: _onQRViewCreated,
              overlay: QrScannerOverlayShape(
                borderColor: Colors.blue,
                borderRadius: 10,
                borderLength: 30,
                borderWidth: 10,
                cutOutSize: 300,
              ),
            ),
          ),
          Expanded(
            flex: 1,
            child: Container(
              padding: EdgeInsets.all(20),
              child: Column(
                mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                children: [
                  if (barcode != null)
                    Text(
                      'Scanned: ${barcode!.code}',
                      style: Theme.of(context).textTheme.headlineMedium,
                    )
                  else
                    Text(
                      'Scan a package QR code or barcode',
                      style: Theme.of(context).textTheme.titleLarge,
                    ),
                  ElevatedButton(
                    onPressed: () {
                      // Add scanned package to delivery list
                      if (barcode != null) {
                        // In a real app, you would look up the package details via API
                        DeliveryPackage scannedPackage = DeliveryPackage(
                          id: barcode!.code!,
                          trackingNumber: barcode!.code!,
                          recipientName: 'Scanned Customer',
                          recipientPhone: '+2348012345678',
                          deliveryAddress: 'Address will be retrieved from system',
                          latitude: 0.0,
                          longitude: 0.0,
                          packageDetails: 'Package scanned via QR/Barcode',
                          status: DeliveryStatus.pending,
                        );
                        
                        deliveryService.addPackage(scannedPackage);
                        
                        Navigator.pop(context);
                        
                        ScaffoldMessenger.of(context).showSnackBar(
                          SnackBar(
                            content: Text('Package ${barcode!.code} added to delivery list'),
                          ),
                        );
                      }
                    },
                    style: ElevatedButton.styleFrom(
                      backgroundColor: Colors.blue,
                      padding: EdgeInsets.symmetric(horizontal: 30, vertical: 15),
                    ),
                    child: Text(
                      'Add Package',
                      style: TextStyle(color: Colors.white),
                    ),
                  ),
                ],
              ),
            ),
          )
        ],
      ),
    );
  }

  void _onQRViewCreated(QRViewController controller) {
    this.controller = controller;
    controller.scannedDataStream.listen((scanData) {
      setState(() {
        barcode = scanData;
      });
    });
  }

  @override
  void dispose() {
    controller?.dispose();
    super.dispose();
  }
}