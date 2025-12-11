import 'package:flutter/material.dart';
import '../widgets/translation_widget.dart';
import 'chat_screen.dart';
import '../models/conversation_model.dart';
import '../services/smart_messaging_service.dart';

class MessagesScreen extends StatefulWidget {
  const MessagesScreen({Key? key}) : super(key: key);

  @override
  _MessagesScreenState createState() => _MessagesScreenState();
}

class _MessagesScreenState extends State<MessagesScreen> {
  String _currentLanguage = 'en';
  final SmartMessagingService _smartMessagingService = SmartMessagingService();
  List<Conversation> _conversations = [];
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadConversations();
  }

  Future<void> _loadConversations() async {
    setState(() {
      _isLoading = true;
    });

    try {
      final conversations = await _smartMessagingService.getUserConversations();
      setState(() {
        _conversations = conversations;
        _isLoading = false;
      });
    } catch (e) {
      setState(() {
        _isLoading = false;
      });
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Failed to load conversations: $e')),
      );
    }
  }

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
          IconButton(
            icon: const Icon(Icons.video_call),
            onPressed: () {
              // Start video call
            },
          ),
        ],
      ),
      floatingActionButton: FloatingActionButton(
        onPressed: () {
          // Start new conversation
          // Implement user selection for new conversation
        },
        child: Icon(Icons.add_comment),
        backgroundColor: Colors.blue,
      ),
      body: _isLoading
          ? Center(child: CircularProgressIndicator())
          : RefreshIndicator(
              onRefresh: _loadConversations,
              child: ListView.builder(
                itemCount: _conversations.length,
                itemBuilder: (context, index) {
                  final conversation = _conversations[index];
                  return _buildConversationItem(conversation);
                },
              ),
            ),
    );
  }

  Widget _buildConversationItem(Conversation conversation) {
    // For this example, assuming we're getting the last message from somewhere
    // In a real implementation, we'd fetch this from the server along with conversations
    return Card(
      margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 4),
      child: ListTile(
        leading: CircleAvatar(
          backgroundColor: Colors.grey[300],
          child: Icon(
            Icons.person,
            color: Colors.blue,
          ),
        ),
        title: Row(
          children: [
            TranslationWidget(
              text: conversation.title ?? 'Conversation',
              to: _currentLanguage,
              style: const TextStyle(
                fontWeight: FontWeight.bold,
              ),
            ),
            if (conversation.unreadCount > 0)
              Container(
                margin: const EdgeInsets.only(left: 8),
                padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                decoration: BoxDecoration(
                  color: Colors.red,
                  borderRadius: BorderRadius.circular(10),
                ),
                child: Text(
                  conversation.unreadCount.toString(),
                  style: TextStyle(
                    color: Colors.white,
                    fontSize: 10,
                  ),
                ),
              ),
          ],
        ),
        subtitle: Text(
          'Last message: ${conversation.lastMessageAt?.toString() ?? 'No messages yet'}',
          maxLines: 1,
          overflow: TextOverflow.ellipsis,
        ),
        trailing: Text(
          conversation.lastMessageAt?.hour.toString().padLeft(2, '0') +
          ':' +
          conversation.lastMessageAt?.minute.toString().padLeft(2, '0'),
          style: TextStyle(
            color: Colors.grey[600],
            fontSize: 12,
          ),
        ),
        onTap: () {
          Navigator.push(
            context,
            MaterialPageRoute(
              builder: (context) => ChatScreen(
                conversation: conversation,
                currentLanguage: _currentLanguage,
              ),
            ),
          );
        },
      ),
    );
  }
}