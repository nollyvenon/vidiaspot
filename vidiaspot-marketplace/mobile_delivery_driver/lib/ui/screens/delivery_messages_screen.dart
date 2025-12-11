import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'delivery_conversation_screen.dart';
import '../services/delivery_management/communication_service.dart';

class DeliveryMessagesScreen extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    final communicationService = Provider.of<CommunicationService>(context);
    
    return Scaffold(
      appBar: AppBar(
        title: Text('Messages'),
        backgroundColor: Colors.blue,
        foregroundColor: Colors.white,
      ),
      body: Consumer<CommunicationService>(
        builder: (context, service, child) {
          return ListView.builder(
            itemCount: service.conversations.length,
            itemBuilder: (context, index) {
              final conversation = service.conversations[index];
              final lastMessage = conversation.messages.isNotEmpty 
                  ? conversation.messages.last 
                  : null;
                  
              return Card(
                margin: EdgeInsets.all(8),
                child: ListTile(
                  contentPadding: EdgeInsets.all(16),
                  leading: CircleAvatar(
                    backgroundColor: Colors.blue,
                    child: Text(
                      conversation.customerName.split(' ').map((n) => n[0]).join().toUpperCase(),
                      style: TextStyle(color: Colors.white),
                    ),
                  ),
                  title: Text(
                    conversation.customerName,
                    style: TextStyle(fontWeight: FontWeight.bold),
                  ),
                  subtitle: lastMessage != null 
                      ? Text(
                          lastMessage.content.length > 50 
                              ? '${lastMessage.content.substring(0, 50)}...' 
                              : lastMessage.content,
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                        )
                      : Text('No messages yet'),
                  trailing: Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Text(
                        _formatTime(conversation.lastMessageAt),
                        style: TextStyle(fontSize: 12, color: Colors.grey),
                      ),
                      if (conversation.unreadCount > 0)
                        Container(
                          padding: EdgeInsets.all(4),
                          decoration: BoxDecoration(
                            color: Colors.red,
                            borderRadius: BorderRadius.circular(10),
                          ),
                          constraints: BoxConstraints(
                            minWidth: 16,
                            minHeight: 16,
                          ),
                          child: Text(
                            conversation.unreadCount.toString(),
                            style: TextStyle(
                              color: Colors.white,
                              fontSize: 10,
                            ),
                            textAlign: TextAlign.center,
                          ),
                        ),
                    ],
                  ),
                  onTap: () {
                    Navigator.push(
                      context,
                      MaterialPageRoute(
                        builder: (context) => DeliveryConversationScreen(
                          conversation: conversation,
                        ),
                      ),
                    );
                  },
                ),
              );
            },
          );
        },
      ),
    );
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