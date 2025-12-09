// lib/screens/iot/iot_home_screen.dart
import 'package:flutter/material.dart';
import 'package:shimmer/shimmer.dart';
import 'package:intl/intl.dart';
import '../../services/iot_service.dart';
import '../../models/iot/iot_device_model.dart';

class IoTHomeScreen extends StatefulWidget {
  const IoTHomeScreen({Key? key}) : super(key: key);

  @override
  _IoTHomeScreenState createState() => _IoTHomeScreenState();
}

class _IoTHomeScreenState extends State<IoTHomeScreen> {
  final IoTService _iotService = IoTService();
  List<IoTDevice> _devices = [];
  bool _isLoading = true;
  bool _isOnline = true; // Assume online for demo

  @override
  void initState() {
    super.initState();
    _loadDevices();
  }

  void _loadDevices() async {
    setState(() {
      _isLoading = true;
    });

    try {
      final devices = await _iotService.getSmartHomeDevices();
      setState(() {
        _devices = devices;
      });
    } catch (e) {
      // Handle error
      print('Error loading devices: $e');
    } finally {
      setState(() {
        _isLoading = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Smart Home'),
        backgroundColor: Colors.blue,
        foregroundColor: Colors.white,
        actions: [
          IconButton(
            onPressed: () {
              // Refresh devices
              _loadDevices();
            },
            icon: const Icon(Icons.refresh),
          ),
        ],
      ),
      body: RefreshIndicator(
        onRefresh: () async {
          _loadDevices();
        },
        child: SingleChildScrollView(
          child: Padding(
            padding: const EdgeInsets.all(16.0),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // Quick Stats
                _buildQuickStats(),
                
                const SizedBox(height: 20),
                
                // Add Device Button
                _buildAddDeviceButton(),
                
                const SizedBox(height: 20),
                
                // Connected Devices Section
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Text(
                      'Smart Home Devices',
                      style: TextStyle(
                        fontSize: 18,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                    Text(
                      '${_devices.length} devices',
                      style: TextStyle(
                        fontSize: 14,
                        color: Colors.grey[600],
                      ),
                    ),
                  ],
                ),
                
                const SizedBox(height: 10),
                
                _isLoading
                    ? _buildShimmerDevices()
                    : _devices.isEmpty
                        ? _buildEmptyState()
                        : _buildDevicesList(),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildQuickStats() {
    int connectedCount = _devices.where((device) => device.isConnected).length;
    int criticalCount = _devices.where((device) => device.status == 'critical').length;

    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.blue[50],
        borderRadius: BorderRadius.circular(12),
      ),
      child: Column(
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceAround,
            children: [
              _buildStatCard('Connected', connectedCount.toString(), Icons.wifi),
              _buildStatCard('Total', _devices.length.toString(), Icons.devices),
              _buildStatCard('Critical', criticalCount.toString(), Icons.warning),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildStatCard(String title, String value, IconData icon) {
    return Expanded(
      child: Container(
        margin: const EdgeInsets.symmetric(horizontal: 4),
        padding: const EdgeInsets.all(12),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(8),
          boxShadow: [
            BoxShadow(
              color: Colors.grey.withOpacity(0.1),
              spreadRadius: 1,
              blurRadius: 3,
              offset: const Offset(0, 1),
            ),
          ],
        ),
        child: Column(
          children: [
            Icon(icon, size: 24, color: Colors.blue),
            const SizedBox(height: 4),
            Text(
              value,
              style: const TextStyle(
                fontSize: 18,
                fontWeight: FontWeight.bold,
              ),
            ),
            Text(
              title,
              style: const TextStyle(
                fontSize: 12,
                color: Colors.grey,
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildAddDeviceButton() {
    return Container(
      width: double.infinity,
      child: ElevatedButton.icon(
        onPressed: () {
          _showAddDeviceDialog();
        },
        icon: const Icon(Icons.add),
        label: const Text('Add New Device'),
        style: ElevatedButton.styleFrom(
          padding: const EdgeInsets.symmetric(vertical: 12),
        ),
      ),
    );
  }

  Widget _buildDevicesList() {
    return ListView.builder(
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      itemCount: _devices.length,
      itemBuilder: (context, index) {
        final device = _devices[index];
        return _buildDeviceCard(device);
      },
    );
  }

  Widget _buildDeviceCard(IoTDevice device) {
    Color statusColor = _getStatusColor(device.connectionStatus);
    
    return Card(
      margin: const EdgeInsets.only(bottom: 10),
      child: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Row(
                  children: [
                    Icon(
                      _getDeviceIcon(device.deviceType),
                      color: statusColor,
                      size: 24,
                    ),
                    const SizedBox(width: 10),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            device.name,
                            style: const TextStyle(
                              fontSize: 16,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                          Text(
                            '${device.brand} ${device.model}',
                            style: const TextStyle(
                              fontSize: 14,
                              color: Colors.grey,
                            ),
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
                Switch(
                  value: device.isConnected,
                  onChanged: (value) async {
                    try {
                      if (value) {
                        await _iotService.connectDevice(device.deviceId);
                      } else {
                        await _iotService.disconnectDevice(device.deviceId);
                      }
                      // Refresh the device list
                      _loadDevices();
                    } catch (e) {
                      ScaffoldMessenger.of(context).showSnackBar(
                        SnackBar(
                          content: Text('Failed to update device: $e'),
                          backgroundColor: Colors.red,
                        ),
                      );
                    }
                  },
                ),
              ],
            ),
            const SizedBox(height: 10),
            Row(
              children: [
                Container(
                  padding: const EdgeInsets.symmetric(
                    horizontal: 8,
                    vertical: 4,
                  ),
                  decoration: BoxDecoration(
                    color: statusColor.withOpacity(0.1),
                    borderRadius: BorderRadius.circular(12),
                    border: Border.all(color: statusColor),
                  ),
                  child: Row(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Icon(
                        Icons.circle,
                        size: 10,
                        color: statusColor,
                      ),
                      const SizedBox(width: 4),
                      Text(
                        device.connectionStatus.toUpperCase(),
                        style: TextStyle(
                          fontSize: 12,
                          color: statusColor,
                        ),
                      ),
                    ],
                  ),
                ),
                const SizedBox(width: 10),
                Container(
                  padding: const EdgeInsets.symmetric(
                    horizontal: 8,
                    vertical: 4,
                  ),
                  decoration: BoxDecoration(
                    color: _getStatusColor(device.status).withOpacity(0.1),
                    borderRadius: BorderRadius.circular(12),
                    border: Border.all(color: _getStatusColor(device.status)),
                  ),
                  child: Text(
                    device.status.toUpperCase(),
                    style: TextStyle(
                      fontSize: 12,
                      color: _getStatusColor(device.status),
                    ),
                  ),
                ),
              ],
            ),
            if (device.lastSeen != null)
              Padding(
                padding: const EdgeInsets.only(top: 8.0),
                child: Text(
                  'Last seen: ${DateFormat('MMM dd, yyyy - HH:mm').format(device.lastSeen!)}',
                  style: const TextStyle(
                    fontSize: 12,
                    color: Colors.grey,
                  ),
                ),
              ),
            const SizedBox(height: 10),
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceEvenly,
              children: [
                _buildControlButton(
                  icon: Icons.power_settings_new,
                  label: 'Control',
                  onPressed: () {
                    _showDeviceControlDialog(device);
                  },
                ),
                _buildControlButton(
                  icon: Icons.settings,
                  label: 'Settings',
                  onPressed: () {
                    _showDeviceSettingsDialog(device);
                  },
                ),
                _buildControlButton(
                  icon: Icons.history,
                  label: 'History',
                  onPressed: () {
                    _showDeviceHistory(device);
                  },
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildControlButton({
    required IconData icon,
    required String label,
    required VoidCallback onPressed,
  }) {
    return Expanded(
      child: TextButton.icon(
        onPressed: onPressed,
        icon: Icon(icon, size: 16),
        label: Text(
          label,
          style: const TextStyle(fontSize: 12),
        ),
      ),
    );
  }

  Widget _buildShimmerDevices() {
    return ListView.builder(
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      itemCount: 3,
      itemBuilder: (context, index) {
        return Card(
          margin: const EdgeInsets.only(bottom: 10),
          child: Shimmer.fromColors(
            baseColor: Colors.grey[300]!,
            highlightColor: Colors.grey[100]!,
            child: Padding(
              padding: const EdgeInsets.all(16.0),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Container(
                    width: double.infinity,
                    height: 20,
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(4),
                    ),
                  ),
                  const SizedBox(height: 10),
                  Container(
                    width: 200,
                    height: 16,
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(4),
                    ),
                  ),
                  const SizedBox(height: 10),
                  Container(
                    width: 100,
                    height: 14,
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(4),
                    ),
                  ),
                  const SizedBox(height: 10),
                  Container(
                    width: 80,
                    height: 40,
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(4),
                    ),
                  ),
                ],
              ),
            ),
          ),
        );
      },
    );
  }

  Widget _buildEmptyState() {
    return Container(
      height: 200,
      child: const Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(
              Icons.devices_other,
              size: 64,
              color: Colors.grey,
            ),
            SizedBox(height: 16),
            Text(
              'No devices found',
              style: TextStyle(
                fontSize: 16,
                color: Colors.grey,
              ),
            ),
            SizedBox(height: 8),
            Text(
              'Add your first smart home device',
              style: TextStyle(
                fontSize: 14,
                color: Colors.grey,
              ),
            ),
          ],
        ),
      ),
    );
  }

  Color _getStatusColor(String status) {
    switch (status.toLowerCase()) {
      case 'connected':
      case 'active':
        return Colors.green;
      case 'disconnected':
      case 'inactive':
        return Colors.red;
      case 'pending':
      case 'connecting':
        return Colors.orange;
      case 'critical':
        return Colors.red;
      default:
        return Colors.grey;
    }
  }

  IconData _getDeviceIcon(String deviceType) {
    switch (deviceType.toLowerCase()) {
      case 'lighting':
        return Icons.lightbulb;
      case 'thermostat':
        return Icons.thermostat;
      case 'security':
        return Icons.security;
      case 'entertainment':
        return Icons.tv;
      case 'appliances':
        return Icons.kitchen;
      case 'sensors':
        return Icons.sensors;
      default:
        return Icons.devices_other;
    }
  }

  void _showAddDeviceDialog() {
    final TextEditingController deviceIdController = TextEditingController();
    final TextEditingController nameController = TextEditingController();
    String deviceType = 'lighting';
    String brand = 'Generic';
    String model = 'Model';

    showDialog(
      context: context,
      builder: (context) {
        return AlertDialog(
          title: const Text('Add New Device'),
          content: SingleChildScrollView(
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                TextField(
                  controller: deviceIdController,
                  decoration: const InputDecoration(
                    labelText: 'Device ID',
                    border: OutlineInputBorder(),
                  ),
                ),
                const SizedBox(height: 10),
                TextField(
                  controller: nameController,
                  decoration: const InputDecoration(
                    labelText: 'Device Name',
                    border: OutlineInputBorder(),
                  ),
                ),
                const SizedBox(height: 10),
                DropdownButtonFormField<String>(
                  value: deviceType,
                  decoration: const InputDecoration(
                    labelText: 'Device Type',
                    border: OutlineInputBorder(),
                  ),
                  items: [
                    const DropdownMenuItem(value: 'lighting', child: Text('Lighting')),
                    const DropdownMenuItem(value: 'thermostat', child: Text('Thermostat')),
                    const DropdownMenuItem(value: 'security', child: Text('Security')),
                    const DropdownMenuItem(value: 'entertainment', child: Text('Entertainment')),
                    const DropdownMenuItem(value: 'appliances', child: Text('Appliances')),
                    const DropdownMenuItem(value: 'sensors', child: Text('Sensors')),
                  ],
                  onChanged: (value) {
                    if (value != null) {
                      setState(() {
                        deviceType = value;
                      });
                    }
                  },
                ),
                const SizedBox(height: 10),
                TextField(
                  decoration: const InputDecoration(
                    labelText: 'Brand',
                    border: OutlineInputBorder(),
                  ),
                  onChanged: (value) {
                    brand = value;
                  },
                ),
                const SizedBox(height: 10),
                TextField(
                  decoration: const InputDecoration(
                    labelText: 'Model',
                    border: OutlineInputBorder(),
                  ),
                  onChanged: (value) {
                    model = value;
                  },
                ),
              ],
            ),
          ),
          actions: [
            TextButton(
              onPressed: () {
                Navigator.of(context).pop();
              },
              child: const Text('Cancel'),
            ),
            ElevatedButton(
              onPressed: () async {
                try {
                  await _iotService.registerDevice(
                    deviceId: deviceIdController.text,
                    name: nameController.text,
                    deviceType: deviceType,
                    brand: brand,
                    model: model,
                  );
                  
                  Navigator.of(context).pop();
                  _loadDevices();
                  
                  ScaffoldMessenger.of(context).showSnackBar(
                    const SnackBar(
                      content: Text('Device added successfully!'),
                      backgroundColor: Colors.green,
                    ),
                  );
                } catch (e) {
                  Navigator.of(context).pop();
                  
                  ScaffoldMessenger.of(context).showSnackBar(
                    SnackBar(
                      content: Text('Failed to add device: $e'),
                      backgroundColor: Colors.red,
                    ),
                  );
                }
              },
              child: const Text('Add'),
            ),
          ],
        );
      },
    );
  }

  void _showDeviceControlDialog(IoTDevice device) {
    showDialog(
      context: context,
      builder: (context) {
        return AlertDialog(
          title: Text('Control ${device.name}'),
          content: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              ListTile(
                leading: const Icon(Icons.power_settings_new),
                title: const Text('Power On/Off'),
                onTap: () async {
                  try {
                    await _iotService.controlDevice(
                      device.deviceId,
                      'power_toggle',
                      {},
                    );
                    Navigator.of(context).pop();
                    ScaffoldMessenger.of(context).showSnackBar(
                      const SnackBar(
                        content: Text('Command sent successfully!'),
                        backgroundColor: Colors.green,
                      ),
                    );
                  } catch (e) {
                    Navigator.of(context).pop();
                    
                    ScaffoldMessenger.of(context).showSnackBar(
                      SnackBar(
                        content: Text('Failed to control device: $e'),
                        backgroundColor: Colors.red,
                      ),
                    );
                  }
                },
              ),
              if (device.deviceType == 'lighting')
                ListTile(
                  leading: const Icon(Icons.brightness_6),
                  title: const Text('Adjust Brightness'),
                  onTap: () {
                    // Open brightness control slider
                    _showBrightnessControl(device);
                    Navigator.of(context).pop();
                  },
                ),
              if (device.deviceType == 'thermostat')
                ListTile(
                  leading: const Icon(Icons.thermostat),
                  title: const Text('Adjust Temperature'),
                  onTap: () {
                    // Open temperature control
                    _showTemperatureControl(device);
                    Navigator.of(context).pop();
                  },
                ),
              // Add more control options based on device type
            ],
          ),
          actions: [
            TextButton(
              onPressed: () {
                Navigator.of(context).pop();
              },
              child: const Text('Close'),
            ),
          ],
        );
      },
    );
  }

  void _showBrightnessControl(IoTDevice device) {
    double brightness = 50.0; // Default value
    
    showDialog(
      context: context,
      builder: (context) {
        return AlertDialog(
          title: Text('Adjust Brightness for ${device.name}'),
          content: StatefulBuilder(
            builder: (context, setState) {
              return Column(
                mainAxisSize: MainAxisSize.min,
                children: [
                  Slider(
                    value: brightness,
                    min: 0,
                    max: 100,
                    divisions: 100,
                    label: brightness.round().toString(),
                    onChanged: (value) {
                      setState(() {
                        brightness = value;
                      });
                    },
                  ),
                  Text('${brightness.round()}%'),
                ],
              );
            },
          ),
          actions: [
            TextButton(
              onPressed: () {
                Navigator.of(context).pop();
              },
              child: const Text('Cancel'),
            ),
            ElevatedButton(
              onPressed: () async {
                try {
                  await _iotService.controlDevice(
                    device.deviceId,
                    'set_brightness',
                    {'brightness': brightness.round()},
                  );
                  Navigator.of(context).pop();
                  ScaffoldMessenger.of(context).showSnackBar(
                    const SnackBar(
                      content: Text('Brightness updated successfully!'),
                      backgroundColor: Colors.green,
                    ),
                  );
                } catch (e) {
                  Navigator.of(context).pop();
                  
                  ScaffoldMessenger.of(context).showSnackBar(
                    SnackBar(
                      content: Text('Failed to update brightness: $e'),
                      backgroundColor: Colors.red,
                    ),
                  );
                }
              },
              child: const Text('Set'),
            ),
          ],
        );
      },
    );
  }

  void _showTemperatureControl(IoTDevice device) {
    double temperature = 22.0; // Default value
    
    showDialog(
      context: context,
      builder: (context) {
        return AlertDialog(
          title: Text('Adjust Temperature for ${device.name}'),
          content: StatefulBuilder(
            builder: (context, setState) {
              return Column(
                mainAxisSize: MainAxisSize.min,
                children: [
                  Slider(
                    value: temperature,
                    min: 16,
                    max: 30,
                    divisions: 140,
                    label: temperature.toStringAsFixed(1),
                    onChanged: (value) {
                      setState(() {
                        temperature = value;
                      });
                    },
                  ),
                  Text('${temperature.toStringAsFixed(1)}°C'),
                ],
              );
            },
          ),
          actions: [
            TextButton(
              onPressed: () {
                Navigator.of(context).pop();
              },
              child: const Text('Cancel'),
            ),
            ElevatedButton(
              onPressed: () async {
                try {
                  await _iotService.controlDevice(
                    device.deviceId,
                    'set_temperature',
                    {'temperature': temperature},
                  );
                  Navigator.of(context).pop();
                  ScaffoldMessenger.of(context).showSnackBar(
                    const SnackBar(
                      content: Text('Temperature updated successfully!'),
                      backgroundColor: Colors.green,
                    ),
                  );
                } catch (e) {
                  Navigator.of(context).pop();
                  
                  ScaffoldMessenger.of(context).showSnackBar(
                    SnackBar(
                      content: Text('Failed to update temperature: $e'),
                      backgroundColor: Colors.red,
                    ),
                  );
                }
              },
              child: const Text('Set'),
            ),
          ],
        );
      },
    );
  }

  void _showDeviceSettingsDialog(IoTDevice device) {
    // Placeholder for device settings dialog
    showDialog(
      context: context,
      builder: (context) {
        return AlertDialog(
          title: Text('Settings for ${device.name}'),
          content: const Text('Device settings would be shown here.'),
          actions: [
            TextButton(
              onPressed: () {
                Navigator.of(context).pop();
              },
              child: const Text('Close'),
            ),
          ],
        );
      },
    );
  }

  void _showDeviceHistory(IoTDevice device) {
    // Placeholder for device history
    showDialog(
      context: context,
      builder: (context) {
        return AlertDialog(
          title: Text('History for ${device.name}'),
          content: const Text('Device history would be shown here.'),
          actions: [
            TextButton(
              onPressed: () {
                Navigator.of(context).pop();
              },
              child: const Text('Close'),
            ),
          ],
        );
      },
    );
  }
}