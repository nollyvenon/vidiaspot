import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:google_maps_flutter/google_maps_flutter.dart';
import 'package:flutter_polyline_points/flutter_polyline_points.dart';
import '../services/delivery_management/route_optimization_service.dart';

class DeliveryRouteScreen extends StatefulWidget {
  @override
  _DeliveryRouteScreenState createState() => _DeliveryRouteScreenState();
}

class _DeliveryRouteScreenState extends State<DeliveryRouteScreen> {
  GoogleMapController? _mapController;
  final Completer<GoogleMapController> _controller = Completer<GoogleMapController>();
  Set<Polyline> _polylines = <Polyline>{};
  Set<Marker> _markers = <Marker>{};

  @override
  void initState() {
    super.initState();
    _updateRoutePolylines();
  }

  void _updateRoutePolylines() {
    final routeService = context.read<RouteOptimizationService>();
    final optimizedRoute = routeService.optimizedRoute;
    
    if (optimizedRoute.length < 2) {
      setState(() {
        _polylines = <Polyline>{};
        _markers = <Marker>{};
      });
      return;
    }

    List<LatLng> points = [];
    
    for (int i = 0; i < optimizedRoute.length - 1; i++) {
      points.add(LatLng(optimizedRoute[i].latitude, optimizedRoute[i].longitude));
      points.add(LatLng(optimizedRoute[i + 1].latitude, optimizedRoute[i + 1].longitude));
    }

    setState(() {
      _polylines = {
        Polyline(
          polylineId: PolylineId('route'),
          points: points,
          color: Colors.blue,
          width: 6,
        ),
      };
      
      _markers = {
        for (int i = 0; i < optimizedRoute.length; i++)
          Marker(
            markerId: MarkerId('stop_$i'),
            position: LatLng(optimizedRoute[i].latitude, optimizedRoute[i].longitude),
            infoWindow: InfoWindow(
              title: 'Stop $i+1',
              snippet: optimizedRoute[i].address,
            ),
            icon: BitmapDescriptor.defaultMarkerWithHue(
              i == 0 ? BitmapDescriptor.hueGreen : // Start point
              i == optimizedRoute.length - 1 ? BitmapDescriptor.hueRed : // End point
              BitmapDescriptor.hueOrange, // Intermediate stops
            ),
          ),
      };
    });
  }

  @override
  Widget build(BuildContext context) {
    final routeService = Provider.of<RouteOptimizationService>(context);
    
    return Scaffold(
      appBar: AppBar(
        title: Text('Delivery Route'),
        backgroundColor: Colors.blue,
        foregroundColor: Colors.white,
        actions: [
          IconButton(
            icon: Icon(Icons.route),
            onPressed: () {
              routeService.optimizeRoute();
              _updateRoutePolylines();
            },
          ),
        ],
      ),
      body: Stack(
        children: [
          GoogleMap(
            mapType: MapType.normal,
            initialCameraPosition: CameraPosition(
              target: LatLng(6.5244, 3.3792), // Default to Lagos, Nigeria
              zoom: 12.0,
            ),
            onMapCreated: (GoogleMapController controller) {
              _controller.complete(controller);
              _mapController = controller;
            },
            polylines: _polylines,
            markers: _markers,
            myLocationEnabled: true,
            myLocationButtonEnabled: true,
          ),
          // Route info panel
          Positioned(
            top: 10,
            left: 10,
            right: 10,
            child: Card(
              elevation: 4,
              child: Padding(
                padding: EdgeInsets.all(16),
                child: Column(
                  children: [
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                      children: [
                        _buildInfoCard('Stops', routeService.optimizedRoute.length.toString(), Icons.local_shipping),
                        _buildInfoCard('Distance', '${routeService.getEstimatedTotalDistance().toStringAsFixed(1)} km', Icons.straighten),
                        _buildInfoCard('Time', '${routeService.getEstimatedTotalTime().toStringAsFixed(0)} min', Icons.access_time),
                      ],
                    ),
                  ],
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildInfoCard(String title, String value, IconData icon) {
    return Container(
      width: 100,
      child: Card(
        child: Padding(
          padding: EdgeInsets.all(8),
          child: Column(
            children: [
              Icon(icon, size: 24, color: Colors.blue),
              SizedBox(height: 4),
              Text(title, style: TextStyle(fontSize: 12, color: Colors.grey)),
              Text(value, style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
            ],
          ),
        ),
      ),
    );
  }
}