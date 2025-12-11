import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../services/delivery_management/communication_service.dart';

class DeliveryConversationScreen extends StatefulWidget {
  final Conversation conversation;

  const DeliveryConversationScreen({Key? key, required this.conversation}) : super(key: key);

  @override
  _DeliveryConversationScreenState createState() => _DeliveryConversationScreenState();
}

class _DeliveryConversationScreenState extends State<DeliveryConversationScreen> {
  final TextEditingController _messageController = TextEditingController();

  @override
  void initState() {
    super.initState();
    // Mark conversation as read when opening
    WidgetsBinding.instance.addPostFrameCallback((_) {
      final communicationService = Provider.of<CommunicationService>(context, listen: false);
      communicationService.markAsRead(widget.conversation.id);
    });
  }

  @override
  Widget build(BuildContext context) {
    final communicationService = Provider.of<CommunicationService>(context);

    return Scaffold(
      appBar: AppBar(
        title: Text(widget.conversation.customerName),
        backgroundColor: Colors.blue,
        foregroundColor: Colors.white,
      ),
      body: Column(
        children: [
          // Messages list
          Expanded(
            child: ListView.builder(
              itemCount: widget.conversation.messages.length,
              itemBuilder: (context, index) {
                final message = widget.conversation.messages[index];
                bool isFromDriver = message.sender == 'driver';
                
                return Align(
                  alignment: isFromDriver ? Alignment.centerRight : Alignment.centerLeft,
                  child: Container(
                    margin: EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                    padding: EdgeInsets.all(12),
                    decoration: BoxDecoration(
                      color: isFromDriver ? Colors.blue : Colors.grey[300],
                      borderRadius: BorderRadius.only(
                        topLeft: Radius.circular(12),
                        topRight: Radius.circular(12),
                        bottomLeft: isFromDriver ? Radius.circular(12) : Radius.zero,
                        bottomRight: isFromDriver ? Radius.zero : Radius.circular(12),
                      ),
                    ),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          message.content,
                          style: TextStyle(
                            color: isFromDriver ? Colors.white : Colors.black,
                          ),
                        ),
                        SizedBox(height: 4),
                        Text(
                          _formatTime(message.timestamp),
                          style: TextStyle(
                            fontSize: 10,
                            color: isFromDriver ? Colors.blue[100] : Colors.grey[600],
                          ),
                        ),
                      ],
                    ),
                  ),
                );
              },
            ),
          ),
          
          // Message input
          Container(
            padding: EdgeInsets.all(8),
            child: Row(
              children: [
                Expanded(
                  child: TextField(
                    controller: _messageController,
                    decoration: InputDecoration(
                      hintText: 'Type a message...',
                      border: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(20),
                      ),
                      contentPadding: EdgeInsets.symmetric(horizontal: 16),
                    ),
                  ),
                ),
                SizedBox(width: 8),
                FloatingActionButton(
                  onPressed: _sendMessage,
                  backgroundColor: Colors.blue,
                  child: Icon(Icons.send, color: Colors.white),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  void _sendMessage() {
    if (_messageController.text.trim().isEmpty) return;

    final communicationService = Provider.of<CommunicationService>(context, listen: false);
    communicationService.sendMessage(widget.conversation.id, _messageController.text);
    
    _messageController.clear();
  }

  String _formatTime(DateTime dateTime) {
    final now = DateTime.now();
    final difference = now.difference(dateTime);
    
    if (difference.inDays > 0) {
      return '${difference.inDays}d ago';
    } else if (difference.inHours > 0) {
      return '${difference.inHours}h ago';
    } else if (difference.inMinutes > 0) {
      return '${difference.inMinutes}m ago';
    } else {
      return 'Just now';
    }
  }
}