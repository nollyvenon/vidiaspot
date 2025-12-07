import 'package:flutter/material.dart';
import '../widgets/translation_widget.dart';

class MessagesScreen extends StatefulWidget {
  const MessagesScreen({Key? key}) : super(key: key);

  @override
  _MessagesScreenState createState() => _MessagesScreenState();
}

class _MessagesScreenState extends State<MessagesScreen> {
  String _currentLanguage = 'en';

  final List<_Message> _messages = [
    _Message(
      id: 1,
      senderName: 'Jane Smith',
      lastMessage: 'Hi, I\'m interested in your iPhone',
      time: '2 min ago',
      unread: true,
      avatar: 'https://via.placeholder.com/50x50',
    ),
    _Message(
      id: 2,
      senderName: 'Mike Johnson',
      lastMessage: 'The car is still available?',
      time: '1 hour ago',
      unread: false,
      avatar: 'https://via.placeholder.com/50x50',
    ),
    _Message(
      id: 3,
      senderName: 'Sarah Williams',
      lastMessage: 'Thanks for the quick response',
      time: '3 hours ago',
      unread: false,
      avatar: 'https://via.placeholder.com/50x50',
    ),
  ];

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: TranslationWidget(
          text: 'Messages',
          to: _currentLanguage,
          style: const TextStyle(
            fontSize: 20,
            fontWeight: FontWeight.bold,
          ),
        ),
        backgroundColor: Colors.blue,
        foregroundColor: Colors.white,
        actions: [
          IconButton(
            icon: const Icon(Icons.search),
            onPressed: () {
              // Search functionality
            },
          ),
        ],
      ),
      body: ListView.builder(
        itemCount: _messages.length,
        itemBuilder: (context, index) {
          final message = _messages[index];
          return _buildMessageItem(message);
        },
      ),
    );
  }

  Widget _buildMessageItem(_Message message) {
    return Card(
      margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 4),
      child: ListTile(
        leading: CircleAvatar(
          backgroundImage: NetworkImage(message.avatar),
          backgroundColor: Colors.grey[300],
        ),
        title: Row(
          children: [
            TranslationWidget(
              text: message.senderName,
              to: _currentLanguage,
              style: const TextStyle(
                fontWeight: FontWeight.bold,
              ),
            ),
            if (message.unread)
              Container(
                margin: const EdgeInsets.only(left: 8),
                padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                decoration: BoxDecoration(
                  color: Colors.blue,
                  borderRadius: BorderRadius.circular(10),
                ),
                child: const Text(
                  'New',
                  style: TextStyle(
                    color: Colors.white,
                    fontSize: 10,
                  ),
                ),
              ),
          ],
        ),
        subtitle: TranslationWidget(
          text: message.lastMessage,
          to: _currentLanguage,
          style: TextStyle(
            color: message.unread ? Colors.black : Colors.grey[600],
          ),
        ),
        trailing: Text(
          message.time,
          style: TextStyle(
            color: message.unread ? Colors.blue : Colors.grey[600],
            fontSize: 12,
          ),
        ),
        onTap: () {
          // Navigate to chat screen
        },
      ),
    );
  }
}

class _Message {
  final int id;
  final String senderName;
  final String lastMessage;
  final String time;
  final bool unread;
  final String avatar;

  _Message({
    required this.id,
    required this.senderName,
    required this.lastMessage,
    required this.time,
    required this.unread,
    required this.avatar,
  });
}