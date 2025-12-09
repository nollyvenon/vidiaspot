// lib/screens/comprehensive_messaging_screen.dart
import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../models/message_model.dart';
import '../models/conversation_model.dart';
import '../models/video_call_model.dart';
import '../models/scheduling_model.dart';
import '../models/escrow_model.dart';
import '../services/smart_messaging_service.dart';
import 'chat_screen.dart';
import 'video_call_screen.dart';
import 'scheduling_screen.dart';
import 'escrow_screen.dart';
import '../widgets/voice_message_widget.dart';

class ComprehensiveMessagingScreen extends StatefulWidget {
  final String currentLanguage;

  const ComprehensiveMessagingScreen({
    Key? key,
    this.currentLanguage = 'en',
  }) : super(key: key);

  @override
  _ComprehensiveMessagingScreenState createState() => _ComprehensiveMessagingScreenState();
}

class _ComprehensiveMessagingScreenState extends State<ComprehensiveMessagingScreen> {
  final SmartMessagingService _smartMessagingService = SmartMessagingService();
  late TabController _tabController;
  List<Conversation> _conversations = [];
  List<Map<String, dynamic>> _notifications = [];
  List<Message> _recentMessages = [];
  bool _isLoadingConversations = true;
  bool _isLoadingNotifications = true;

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 4, vsync: this);
    _loadAllData();
  }

  Future<void> _loadAllData() async {
    await Future.wait([
      _loadConversations(),
      _loadNotifications(),
      _loadRecentMessages(),
    ]).then((_) {
      setState(() {
        // Data loaded
      });
    });
  }

  Future<void> _loadConversations() async {
    try {
      final conversations = await _smartMessagingService.getUserConversations();
      setState(() {
        _conversations = conversations;
        _isLoadingConversations = false;
      });
    } catch (e) {
      setState(() {
        _isLoadingConversations = false;
      });
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Failed to load conversations: $e')),
      );
    }
  }

  Future<void> _loadNotifications() async {
    try {
      final notifications = await _smartMessagingService.getNotifications();
      setState(() {
        _notifications = [];
        _isLoadingNotifications = false;
      });
    } catch (e) {
      setState(() {
        _isLoadingNotifications = false;
      });
    }
  }

  Future<void> _loadRecentMessages() async {
    try {
      // Mock data - in a real app, this would come from the service
      setState(() {
        _recentMessages = [
          Message(
            id: 1,
            conversationId: 1,
            senderId: 2,
            receiverId: 1,
            content: 'Hi there! Are you still interested in that laptop?',
            messageType: 'text',
            language: 'en',
            status: 'received',
            createdAt: DateTime.now().subtract(Duration(minutes: 5)),
          ),
          Message(
            id: 2,
            conversationId: 2,
            senderId: 3,
            receiverId: 1,
            content: 'Can we set up a time to pick up the furniture?',
            messageType: 'text',
            language: 'en',
            status: 'received',
            createdAt: DateTime.now().subtract(Duration(hours: 2)),
          ),
        ];
      });
    } catch (e) {
      // Handle error
    }
  }

  Widget _buildTabBar() {
    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        boxShadow: [
          BoxShadow(
            color: Colors.grey.withOpacity(0.2),
            spreadRadius: 1,
            blurRadius: 2,
            offset: Offset(0, 2),
          ),
        ],
      ),
      child: TabBar(
        controller: _tabController,
        labelColor: Colors.blue,
        unselectedLabelColor: Colors.grey,
        indicatorColor: Colors.blue,
        tabs: [
          Tab(text: 'Chats'),
          Tab(text: 'Scheduled'),
          Tab(text: 'Escrow'),
          Tab(text: 'More'),
        ],
      ),
    );
  }

  Widget _buildConversationsTab() {
    if (_isLoadingConversations) {
      return Center(child: CircularProgressIndicator());
    }

    if (_conversations.isEmpty) {
      return Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(
              Icons.chat_outlined,
              size: 80,
              color: Colors.grey[300],
            ),
            SizedBox(height: 20),
            Text(
              'No conversations yet',
              style: TextStyle(
                fontSize: 18,
                color: Colors.grey[600],
              ),
            ),
            SizedBox(height: 10),
            Text(
              'Start a conversation to connect with buyers and sellers',
              style: TextStyle(
                color: Colors.grey[500],
              ),
              textAlign: TextAlign.center,
            ),
          ],
        ),
      );
    }

    return RefreshIndicator(
      onRefresh: _loadConversations,
      child: ListView.builder(
        itemCount: _conversations.length,
        itemBuilder: (context, index) {
          final conversation = _conversations[index];
          return Card(
            margin: EdgeInsets.symmetric(horizontal: 16, vertical: 4),
            child: ListTile(
              leading: CircleAvatar(
                backgroundColor: Colors.grey[300],
                child: Icon(Icons.person, color: Colors.blue),
              ),
              title: Row(
                children: [
                  Text(
                    conversation.title ?? 'Conversation',
                    style: TextStyle(fontWeight: FontWeight.bold),
                  ),
                  if (conversation.unreadCount > 0)
                    Container(
                      margin: EdgeInsets.only(left: 8),
                      padding: EdgeInsets.symmetric(horizontal: 6, vertical: 2),
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
                'Last message: ${conversation.lastMessageAt?.toString() ?? 'No messages'}',
                maxLines: 1,
                overflow: TextOverflow.ellipsis,
              ),
              trailing: Text(
                conversation.lastMessageAt != null
                    ? DateFormat('hh:mm a').format(conversation.lastMessageAt!)
                    : '',
                style: TextStyle(
                  color: Colors.grey[600],
                ),
              ),
              onTap: () {
                Navigator.push(
                  context,
                  MaterialPageRoute(
                    builder: (context) => ChatScreen(
                      conversation: conversation,
                      currentLanguage: widget.currentLanguage,
                    ),
                  ),
                );
              },
            ),
          );
        },
      ),
    );
  }

  Widget _buildScheduledTab() {
    return Container(
      padding: EdgeInsets.all(16),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            'Scheduled Events',
            style: TextStyle(
              fontSize: 20,
              fontWeight: FontWeight.bold,
            ),
          ),
          SizedBox(height: 20),
          Expanded(
            child: ListView(
              children: [
                // Scheduled pickups/meetings tile
                Card(
                  child: ListTile(
                    leading: CircleAvatar(
                      backgroundColor: Colors.blue[100],
                      child: Icon(Icons.calendar_today, color: Colors.blue),
                    ),
                    title: Text('Laptop Pickup'),
                    subtitle: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text('With John Doe'),
                        Text('Tomorrow at 2:00 PM'),
                        Text('Lekki, Lagos'),
                      ],
                    ),
                    trailing: Icon(Icons.event_note, color: Colors.blue),
                    onTap: () {
                      // Navigate to scheduling details
                      ScaffoldMessenger.of(context).showSnackBar(
                        SnackBar(content: Text('Scheduling feature coming soon!')),
                      );
                    },
                  ),
                ),
                SizedBox(height: 10),
                
                // Schedule new meeting
                Card(
                  color: Colors.blue,
                  child: ListTile(
                    leading: Icon(Icons.add, color: Colors.white),
                    title: Text(
                      'Schedule New Meeting',
                      style: TextStyle(
                        color: Colors.white,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                    onTap: () {
                      // Navigate to scheduling screen
                      ScaffoldMessenger.of(context).showSnackBar(
                        SnackBar(content: Text('Scheduling feature coming soon!')),
                      );
                    },
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildEscrowTab() {
    return Container(
      padding: EdgeInsets.all(16),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            'Escrow Protection',
            style: TextStyle(
              fontSize: 20,
              fontWeight: FontWeight.bold,
            ),
          ),
          SizedBox(height: 20),
          Expanded(
            child: ListView(
              children: [
                // Active escrow
                Card(
                  child: ListTile(
                    leading: CircleAvatar(
                      backgroundColor: Colors.green[100],
                      child: Icon(Icons.shield, color: Colors.green),
                    ),
                    title: Text('iPhone Purchase'),
                    subtitle: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text('Amount: ₦ 250,000'),
                        Text('Status: Pending Release'),
                      ],
                    ),
                    trailing: Icon(Icons.lock, color: Colors.green),
                    onTap: () {
                      // Navigate to escrow details
                      Navigator.push(
                        context,
                        MaterialPageRoute(
                          builder: (context) => EscrowScreen(
                            transactionId: 1,
                            adId: 1,
                            sellerId: 1,
                            amount: 250000.0,
                          ),
                        ),
                      );
                    },
                  ),
                ),
                SizedBox(height: 10),
                
                // How escrow works
                Card(
                  child: Padding(
                    padding: EdgeInsets.all(16),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          'How Escrow Works',
                          style: TextStyle(
                            fontWeight: FontWeight.bold,
                            fontSize: 16,
                          ),
                        ),
                        SizedBox(height: 8),
                        Text(
                          '• Your payment is securely held until you receive the item\n• Seller gets paid only after your confirmation\n• Blockchain verifies all transactions\n• Dispute resolution with AI assistance',
                          style: TextStyle(color: Colors.grey[700]),
                        ),
                        SizedBox(height: 16),
                        ElevatedButton(
                          onPressed: () {
                            // Navigate to escrow info
                          },
                          child: Text('Learn More'),
                          style: ElevatedButton.styleFrom(
                            backgroundColor: Colors.green,
                            foregroundColor: Colors.white,
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildMoreTab() {
    return Container(
      padding: EdgeInsets.all(16),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            'More Features',
            style: TextStyle(
              fontSize: 20,
              fontWeight: FontWeight.bold,
            ),
          ),
          SizedBox(height: 20),
          Expanded(
            child: GridView.count(
              crossAxisCount: 2,
              crossAxisSpacing: 16,
              mainAxisSpacing: 16,
              children: [
                _buildFeatureCard(
                  icon: Icons.video_call,
                  title: 'Video Call',
                  onTap: () {
                    Navigator.push(
                      context,
                      MaterialPageRoute(
                        builder: (context) => VideoCallScreen(
                          callerName: 'Demo Caller',
                        ),
                      ),
                    );
                  },
                ),
                _buildFeatureCard(
                  icon: Icons.translate,
                  title: 'Translate',
                  onTap: () {
                    ScaffoldMessenger.of(context).showSnackBar(
                      SnackBar(content: Text('Translation feature coming soon!')),
                    );
                  },
                ),
                _buildFeatureCard(
                  icon: Icons.smart_toy,
                  title: 'Smart Replies',
                  onTap: () {
                    ScaffoldMessenger.of(context).showSnackBar(
                      SnackBar(content: Text('Smart Replies feature coming soon!')),
                    );
                  },
                ),
                _buildFeatureCard(
                  icon: Icons.voice_chat,
                  title: 'Voice Messages',
                  onTap: () {
                    showDialog(
                      context: context,
                      builder: (context) => Dialog(
                        child: Padding(
                          padding: EdgeInsets.all(16),
                          child: VoiceMessageWidget(
                            currentLanguage: widget.currentLanguage,
                            onMessageSent: (message) {
                              Navigator.pop(context);
                              ScaffoldMessenger.of(context).showSnackBar(
                                SnackBar(content: Text('Voice message sent: $message')),
                              );
                            },
                          ),
                        ),
                      ),
                    );
                  },
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildFeatureCard({
    required IconData icon,
    required String title,
    required VoidCallback onTap,
  }) {
    return Card(
      child: InkWell(
        onTap: onTap,
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(icon, size: 48, color: Colors.blue),
            SizedBox(height: 12),
            Text(
              title,
              textAlign: TextAlign.center,
              style: TextStyle(fontWeight: FontWeight.w500),
            ),
          ],
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Smart Messaging'),
        backgroundColor: Colors.blue,
        foregroundColor: Colors.white,
        actions: [
          IconButton(
            icon: Icon(Icons.search),
            onPressed: () {
              // Search functionality
            },
          ),
          IconButton(
            icon: Icon(Icons.notifications),
            onPressed: () {
              // Notifications
            },
          ),
        ],
      ),
      body: Column(
        children: [
          _buildTabBar(),
          Expanded(
            child: TabBarView(
              controller: _tabController,
              children: [
                _buildConversationsTab(),
                _buildScheduledTab(),
                _buildEscrowTab(),
                _buildMoreTab(),
              ],
            ),
          ),
        ],
      ),
    );
  }

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }
}