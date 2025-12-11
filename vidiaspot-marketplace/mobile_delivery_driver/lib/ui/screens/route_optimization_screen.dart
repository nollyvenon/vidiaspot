import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../services/logistics/route_optimization_service.dart';

class RouteOptimizationScreen extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Route Optimization'),
        backgroundColor: Colors.blue,
        foregroundColor: Colors.white,
      ),
      body: Consumer<RouteOptimizationService>(
        builder: (context, routeService, child) {
          return Column(
            children: [
              // Controls
              Card(
                margin: EdgeInsets.all(16),
                child: Padding(
                  padding: EdgeInsets.all(16),
                  child: Column(
                    children: [
                      Text(
                        'Optimize your delivery route',
                        style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
                      ),
                      SizedBox(height: 16),
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                        children: [
                          ElevatedButton.icon(
                            onPressed: () {
                              // Add a new delivery stop
                              showDialog(
                                context: context,
                                builder: (BuildContext context) {
                                  return _buildAddStopDialog(context, routeService);
                                },
                              );
                            },
                            icon: Icon(Icons.add),
                            label: Text('Add Stop'),
                          ),
                          ElevatedButton.icon(
                            onPressed: () {
                              routeService.optimizeRoute();
                            },
                            icon: Icon(Icons.route),
                            label: Text('Optimize Route'),
                          ),
                        ],
                      ),
                    ],
                  ),
                ),
              ),
              
              // Route Information
              if (routeService.optimizedRoute.isNotEmpty)
                Card(
                  margin: EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                  child: Padding(
                    padding: EdgeInsets.all(16),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          'Optimized Route Information',
                          style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
                        ),
                        SizedBox(height: 8),
                        _buildRouteStat('Total Stops', routeService.optimizedRoute.length.toString()),
                        _buildRouteStat('Estimated Distance', '${routeService.getEstimatedTotalDistance().toStringAsFixed(2)} km'),
                        _buildRouteStat('Estimated Time', '${routeService.getEstimatedTotalTime().toStringAsFixed(0)} min'),
                        _buildRouteStat('Fuel Consumption', '${routeService.getEstimatedFuelConsumption().toStringAsFixed(2)} L'),
                      ],
                    ),
                  ),
                ),
              
              // Delivery stops list
              Expanded(
                child: ListView.builder(
                  itemCount: routeService.optimizedRoute.length,
                  itemBuilder: (context, index) {
                    final stop = routeService.optimizedRoute[index];
                    return Card(
                      margin: EdgeInsets.symmetric(horizontal: 16, vertical: 4),
                      child: ListTile(
                        leading: CircleAvatar(
                          backgroundColor: Colors.blue,
                          child: Text((index + 1).toString()),
                        ),
                        title: Text(stop.address),
                        subtitle: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text('Priority: ${stop.priority}'),
                            Text('Package: ${stop.weight}kg, ${stop.volume}m³'),
                            if (stop.timeWindowStart != null && stop.timeWindowEnd != null)
                              Text('Time: ${stop.timeWindowStart!.hour.toString().padLeft(2, '0')}:${stop.timeWindowStart!.minute.toString().padLeft(2, '0')} - ${stop.timeWindowEnd!.hour.toString().padLeft(2, '0')}:${stop.timeWindowEnd!.minute.toString().padLeft(2, '0')}'),
                          ],
                        ),
                        trailing: IconButton(
                          icon: Icon(Icons.delete, color: Colors.red),
                          onPressed: () {
                            routeService.removeDeliveryStop(stop);
                          },
                        ),
                      ),
                    );
                  },
                ),
              ),
            ],
          );
        },
      ),
    );
  }

  Widget _buildRouteStat(String label, String value) {
    return Padding(
      padding: EdgeInsets.symmetric(vertical: 4),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(label),
          Text(value, style: TextStyle(fontWeight: FontWeight.bold)),
        ],
      ),
    );
  }

  Widget _buildAddStopDialog(BuildContext context, RouteOptimizationService routeService) {
    String address = '';
    double latitude = 0.0;
    double longitude = 0.0;
    int priority = 1;
    double weight = 0.0;
    double volume = 0.0;

    return AlertDialog(
      title: Text('Add Delivery Stop'),
      content: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          TextField(
            decoration: InputDecoration(labelText: 'Address'),
            onChanged: (value) => address = value,
          ),
          TextField(
            decoration: InputDecoration(labelText: 'Latitude'),
            keyboardType: TextInputType.number,
            onChanged: (value) => latitude = double.tryParse(value) ?? 0.0,
          ),
          TextField(
            decoration: InputDecoration(labelText: 'Longitude'),
            keyboardType: TextInputType.number,
            onChanged: (value) => longitude = double.tryParse(value) ?? 0.0,
          ),
          TextField(
            decoration: InputDecoration(labelText: 'Weight (kg)'),
            keyboardType: TextInputType.number,
            onChanged: (value) => weight = double.tryParse(value) ?? 0.0,
          ),
          TextField(
            decoration: InputDecoration(labelText: 'Volume (m³)'),
            keyboardType: TextInputType.number,
            onChanged: (value) => volume = double.tryParse(value) ?? 0.0,
          ),
          DropdownButtonFormField<int>(
            value: priority,
            decoration: InputDecoration(labelText: 'Priority'),
            items: [1, 2, 3, 4, 5]
                .map((priority) => DropdownMenuItem(
                      value: priority,
                      child: Text('$priority'),
                    ))
                .toList(),
            onChanged: (value) {
              priority = value!;
            },
          ),
        ],
      ),
      actions: [
        TextButton(
          onPressed: () => Navigator.of(context).pop(),
          child: Text('Cancel'),
        ),
        ElevatedButton(
          onPressed: () {
            if (address.isNotEmpty) {
              routeService.addDeliveryStop(
                DeliveryStop(
                  id: DateTime.now().millisecondsSinceEpoch.toString(),
                  address: address,
                  latitude: latitude,
                  longitude: longitude,
                  priority: priority,
                  weight: weight,
                  volume: volume,
                ),
              );
              Navigator.of(context).pop();
            }
          },
          child: Text('Add'),
        ),
      ],
    );
  }
}