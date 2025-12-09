// lib/screens/chat_screen.dart
import 'package:flutter/material.dart';
import '../widgets/translation_widget.dart';
import '../models/message_model.dart';
import '../models/conversation_model.dart';
import '../services/smart_messaging_service.dart';

class ChatScreen extends StatefulWidget {
  final Conversation conversation;
  final String currentLanguage;

  const ChatScreen({
    Key? key,
    required this.conversation,
    this.currentLanguage = 'en',
  }) : super(key: key);

  @override
  _ChatScreenState createState() => _ChatScreenState();
}

class _ChatScreenState extends State<ChatScreen> {
  final _messageController = TextEditingController();
  final _scrollController = ScrollController();
  final SmartMessagingService _smartMessagingService = SmartMessagingService();
  List<Message> _messages = [];
  List<String> _smartReplies = [];
  bool _isLoading = true;
  bool _isSending = false;
  bool _showSmartReplies = false;

  @override
  void initState() {
    super.initState();
    _loadMessages();
  }

  Future<void> _loadMessages() async {
    setState(() {
      _isLoading = true;
    });

    try {
      final messages = await _smartMessagingService.getConversationHistory(widget.conversation.id);
      setState(() {
        _messages = messages;
        _isLoading = false;
      });
    } catch (e) {
      setState(() {
        _isLoading = false;
      });
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Failed to load messages: $e')),
      );
    }
  }

  Future<void> _sendMessage() async {
    final text = _messageController.text.trim();
    if (text.isEmpty || _isSending) return;

    setState(() {
      _isSending = true;
    });

    try {
      final newMessage = await _smartMessagingService.sendMessage(
        widget.conversation.id,
        text,
      );

      setState(() {
        _messages.add(newMessage);
        _messageController.clear();
        _showSmartReplies = false;
        _smartReplies = [];
      });

      // Scroll to the bottom
      WidgetsBinding.instance.addPostFrameCallback((_) {
        if (_scrollController.hasClients) {
          _scrollController.animateTo(
            _scrollController.position.maxScrollExtent,
            duration: Duration(milliseconds: 300),
            curve: Curves.easeOut,
          );
        }
      });
    } catch (e) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Failed to send message: $e')),
      );
    } finally {
      setState(() {
        _isSending = false;
      });
    }
  }

  Future<void> _getSmartReplies() async {
    final text = _messageController.text.trim();
    if (text.isEmpty) return;

    try {
      final replies = await _smartMessagingService.getSmartReplies(text);
      setState(() {
        _smartReplies = replies;
        _showSmartReplies = true;
      });
    } catch (e) {
      // If error, just continue without smart replies
    }
  }

  void _selectSmartReply(String reply) {
    _messageController.text = reply;
    _showSmartReplies = false;
    _smartReplies = [];
  }

  Widget _buildMessageBubble(Message message) {
    bool isMe = message.senderId == 1; // Assuming 1 is current user ID for demo

    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 4.0),
      child: Align(
        alignment: isMe ? Alignment.centerRight : Alignment.centerLeft,
        child: Container(
          margin: EdgeInsets.only(
            left: isMe ? 50 : 10,
            right: isMe ? 10 : 50,
          ),
          padding: EdgeInsets.all(12),
          decoration: BoxDecoration(
            color: isMe ? Colors.blue[700] : Colors.grey[300],
            borderRadius: BorderRadius.circular(18),
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                message.content,
                style: TextStyle(
                  color: isMe ? Colors.white : Colors.black87,
                ),
              ),
              SizedBox(height: 4),
              Text(
                '${message.createdAt.hour}:${message.createdAt.minute.toString().padLeft(2, '0')}',
                style: TextStyle(
                  fontSize: 10,
                  color: isMe ? Colors.blue[200] : Colors.grey[600],
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: TranslationWidget(
          text: widget.conversation.title ?? 'Chat',
          to: widget.currentLanguage,
          style: const TextStyle(
            fontSize: 18,
            fontWeight: FontWeight.bold,
          ),
        ),
        backgroundColor: Colors.blue,
        foregroundColor: Colors.white,
        actions: [
          PopupMenuButton(
            icon: Icon(Icons.more_vert),
            itemBuilder: (context) => [
              PopupMenuItem(
                child: Text('Video Call'),
                onTap: () {
                  // Implement video call
                },
              ),
              PopupMenuItem(
                child: Text('Schedule Meeting'),
                onTap: () {
                  // Implement scheduling
                },
              ),
              PopupMenuItem(
                child: Text('View Profile'),
                onTap: () {
                  // Implement view profile
                },
              ),
            ],
          ),
        ],
      ),
      body: Column(
        children: [
          // Messages list
          Expanded(
            child: _isLoading
                ? Center(child: CircularProgressIndicator())
                : _messages.isEmpty
                    ? Center(
                        child: Text(
                          'No messages yet. Be the first to say hello!',
                          style: TextStyle(color: Colors.grey[600]),
                        ),
                      )
                    : ListView.builder(
                        controller: _scrollController,
                        itemCount: _messages.length,
                        itemBuilder: (context, index) {
                          return _buildMessageBubble(_messages[index]);
                        },
                      ),
          ),

          // Smart replies section
          if (_showSmartReplies && _smartReplies.isNotEmpty)
            Container(
              height: 60,
              padding: EdgeInsets.symmetric(horizontal: 16, vertical: 8),
              child: ListView.builder(
                scrollDirection: Axis.horizontal,
                itemCount: _smartReplies.length,
                itemBuilder: (context, index) {
                  return Container(
                    margin: EdgeInsets.only(right: 8),
                    child: ElevatedButton(
                      onPressed: () => _selectSmartReply(_smartReplies[index]),
                      style: ElevatedButton.styleFrom(
                        backgroundColor: Colors.grey[200],
                        foregroundColor: Colors.black87,
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(20),
                        ),
                      ),
                      child: Text(
                        _smartReplies[index],
                        style: TextStyle(fontSize: 12),
                      ),
                    ),
                  );
                },
              ),
            ),

          // Input area
          Container(
            padding: EdgeInsets.all(8),
            child: Row(
              children: [
                Expanded(
                  child: TextField(
                    controller: _messageController,
                    onSubmitted: (text) => _sendMessage(),
                    onChanged: (text) => _getSmartReplies(),
                    decoration: InputDecoration(
                      hintText: 'Type a message...',
                      filled: true,
                      fillColor: Colors.grey[100],
                      border: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(24),
                        borderSide: BorderSide.none,
                      ),
                      contentPadding: EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                    ),
                  ),
                ),
                SizedBox(width: 8),
                IconButton(
                  onPressed: _isSending ? null : _sendMessage,
                  icon: _isSending
                      ? SizedBox(width: 24, height: 24, child: CircularProgressIndicator(strokeWidth: 2))
                      : Icon(Icons.send, color: Colors.blue),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  @override
  void dispose() {
    _messageController.dispose();
    _scrollController.dispose();
    super.dispose();
  }
}