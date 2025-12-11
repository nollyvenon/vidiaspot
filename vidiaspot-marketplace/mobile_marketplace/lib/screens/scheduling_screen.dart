// lib/screens/scheduling_screen.dart
import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../models/scheduling_model.dart';
import '../services/smart_messaging_service.dart';

class SchedulingScreen extends StatefulWidget {
  final int adId;
  final int recipientUserId;
  final String recipientUsername;

  const SchedulingScreen({
    Key? key,
    required this.adId,
    required this.recipientUserId,
    required this.recipientUsername,
  }) : super(key: key);

  @override
  _SchedulingScreenState createState() => _SchedulingScreenState();
}

class _SchedulingScreenState extends State<SchedulingScreen> {
  final SmartMessagingService _smartMessagingService = SmartMessagingService();
  final _formKey = GlobalKey<FormState>();
  final _titleController = TextEditingController();
  final _locationController = TextEditingController();
  final _descriptionController = TextEditingController();
  DateTime? _selectedDateTime;
  String _selectedType = 'pickup';

  @override
  void dispose() {
    _titleController.dispose();
    _locationController.dispose();
    _descriptionController.dispose();
    super.dispose();
  }

  Future<void> _selectDateTime() async {
    final DateTime? pickedDate = await showDatePicker(
      context: context,
      initialDate: DateTime.now().add(Duration(days: 1)),
      firstDate: DateTime.now(),
      lastDate: DateTime.now().add(Duration(days: 30)),
    );

    if (pickedDate != null) {
      final TimeOfDay? pickedTime = await showTimePicker(
        context: context,
        initialTime: TimeOfDay.now(),
      );

      if (pickedTime != null) {
        setState(() {
          _selectedDateTime = DateTime(
            pickedDate.year,
            pickedDate.month,
            pickedDate.day,
            pickedTime.hour,
            pickedTime.minute,
          );
        });
      }
    }
  }

  Future<void> _scheduleMeeting() async {
    if (_formKey.currentState!.validate() && _selectedDateTime != null) {
      try {
        await _smartMessagingService.scheduleMeeting(
          adId: widget.adId,
          recipientUserId: widget.recipientUserId,
          title: _titleController.text,
          location: _locationController.text,
          description: _descriptionController.text,
          scheduledDateTime: _selectedDateTime!,
        );

        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Schedule request sent successfully!')),
        );
        
        Navigator.pop(context);
      } catch (e) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Failed to schedule: $e')),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Schedule Meeting'),
        backgroundColor: Colors.blue,
        foregroundColor: Colors.white,
      ),
      body: Padding(
        padding: EdgeInsets.all(16),
        child: Form(
          key: _formKey,
          child: SingleChildScrollView(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  'Schedule with ${widget.recipientUsername}',
                  style: TextStyle(
                    fontSize: 18,
                    fontWeight: FontWeight.bold,
                  ),
                ),
                SizedBox(height: 20),
                
                // Title field
                TextFormField(
                  controller: _titleController,
                  decoration: InputDecoration(
                    labelText: 'Title',
                    hintText: 'e.g. Pickup Item, Inspection, Meeting',
                    border: OutlineInputBorder(),
                  ),
                  validator: (value) {
                    if (value == null || value.isEmpty) {
                      return 'Please enter a title';
                    }
                    return null;
                  },
                ),
                SizedBox(height: 16),
                
                // Type selection
                Text('Type', style: TextStyle(fontWeight: FontWeight.w500)),
                SizedBox(height: 8),
                Wrap(
                  spacing: 8.0,
                  children: [
                    ChoiceChip(
                      label: Text('Pickup'),
                      selected: _selectedType == 'pickup',
                      onSelected: (selected) {
                        setState(() {
                          _selectedType = selected ? 'pickup' : _selectedType;
                        });
                      },
                    ),
                    ChoiceChip(
                      label: Text('Meeting'),
                      selected: _selectedType == 'meeting',
                      onSelected: (selected) {
                        setState(() {
                          _selectedType = selected ? 'meeting' : _selectedType;
                        });
                      },
                    ),
                    ChoiceChip(
                      label: Text('Inspection'),
                      selected: _selectedType == 'inspection',
                      onSelected: (selected) {
                        setState(() {
                          _selectedType = selected ? 'inspection' : _selectedType;
                        });
                      },
                    ),
                  ],
                ),
                SizedBox(height: 16),
                
                // Location field
                TextFormField(
                  controller: _locationController,
                  decoration: InputDecoration(
                    labelText: 'Location',
                    hintText: 'Enter meeting/pickup location',
                    border: OutlineInputBorder(),
                  ),
                  validator: (value) {
                    if (value == null || value.isEmpty) {
                      return 'Please enter a location';
                    }
                    return null;
                  },
                ),
                SizedBox(height: 16),
                
                // Date and time picker
                Container(
                  width: double.infinity,
                  child: ElevatedButton.icon(
                    onPressed: _selectDateTime,
                    icon: Icon(Icons.calendar_today),
                    label: Text(
                      _selectedDateTime != null
                          ? DateFormat('MMM dd, yyyy - hh:mm a').format(_selectedDateTime!)
                          : 'Select Date & Time',
                    ),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: Colors.blue,
                      foregroundColor: Colors.white,
                    ),
                  ),
                ),
                SizedBox(height: 16),
                
                // Description field
                TextFormField(
                  controller: _descriptionController,
                  maxLines: 3,
                  decoration: InputDecoration(
                    labelText: 'Description (Optional)',
                    hintText: 'Any additional details...',
                    border: OutlineInputBorder(),
                  ),
                ),
                SizedBox(height: 24),
                
                // Submit button
                SizedBox(
                  width: double.infinity,
                  child: ElevatedButton(
                    onPressed: _scheduleMeeting,
                    child: Text('Send Schedule Request'),
                    style: ElevatedButton.styleFrom(
                      padding: EdgeInsets.symmetric(vertical: 16),
                      backgroundColor: Colors.green,
                      foregroundColor: Colors.white,
                    ),
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}