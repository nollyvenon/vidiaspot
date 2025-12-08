// lib/screens/video_call_screen.dart
import 'package:flutter/material.dart';
import 'package:jitsi_meet_jack/jitsi_meet_jack.dart';
import '../models/video_call_model.dart';
import '../services/smart_messaging_service.dart';

class VideoCallScreen extends StatefulWidget {
  final VideoCall? videoCall;
  final int? recipientUserId;
  final int? adId;
  final String? callerName;

  const VideoCallScreen({
    Key? key,
    this.videoCall,
    this.recipientUserId,
    this.adId,
    this.callerName,
  }) : super(key: key);

  @override
  _VideoCallScreenState createState() => _VideoCallScreenState();
}

class _VideoCallScreenState extends State<VideoCallScreen> {
  final SmartMessagingService _smartMessagingService = SmartMessagingService();
  JitsiMeetingOptions? _options;
  bool _isLoading = false;

  @override
  void initState() {
    super.initState();
    _setupCall();
  }

  Future<void> _setupCall() async {
    setState(() {
      _isLoading = true;
    });

    try {
      // If no existing video call, create one
      VideoCall call = widget.videoCall ?? 
          await _smartMessagingService.createVideoCall(
            recipientUserId: widget.recipientUserId!,
            adId: widget.adId,
            callType: 'video',
          );

      // Setup Jitsi options
      _options = JitsiMeetingOptions(
        room: call.roomId,
        serverURL: "https://meet.jit.si", // Use your Jitsi server URL
        subject: "Marketplace Video Call",
        token: null,
        audioOnly: false,
        audioMuted: false,
        videoMuted: false,
        features: JitsiMeetingOptions.features()
          ..set(FeatureFlagEnum.CALL_INTEGRATION_ENABLED, false)
          ..set(FeatureFlagEnum.WELCOME_PAGE_ENABLED, false),
      );
    } catch (e) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Failed to setup video call: $e')),
      );
    } finally {
      setState(() {
        _isLoading = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Stack(
        children: [
          // Video call UI
          if (!_isLoading && _options != null) ...[
            // Jitsi Meet implementation would go here
            // For this demo, showing a placeholder
            Container(
              decoration: BoxDecoration(
                gradient: LinearGradient(
                  begin: Alignment.topCenter,
                  end: Alignment.bottomCenter,
                  colors: [Colors.blue[800]!, Colors.blue[600]!],
                ),
              ),
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  CircleAvatar(
                    radius: 60,
                    backgroundColor: Colors.white.withOpacity(0.2),
                    child: CircleAvatar(
                      radius: 50,
                      backgroundImage: AssetImage('assets/images/profile_placeholder.png'),
                      backgroundColor: Colors.white,
                    ),
                  ),
                  SizedBox(height: 20),
                  Text(
                    widget.callerName ?? 'Connecting...',
                    style: TextStyle(
                      color: Colors.white,
                      fontSize: 24,
                      fontWeight: FontWeight.w500,
                    ),
                  ),
                  SizedBox(height: 10),
                  Text(
                    'Video Call Connected',
                    style: TextStyle(
                      color: Colors.white70,
                      fontSize: 16,
                    ),
                  ),
                  SizedBox(height: 40),
                  Text(
                    'Room: ${_options!.room}',
                    style: TextStyle(
                      color: Colors.white54,
                      fontSize: 14,
                    ),
                  ),
                ],
              ),
            ),
          ] else if (_isLoading) ...[
            // Loading state
            Container(
              color: Colors.black,
              child: Center(
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    CircularProgressIndicator(valueColor: AlwaysStoppedAnimation<Color>(Colors.blue)),
                    SizedBox(height: 20),
                    Text(
                      'Setting up video call...',
                      style: TextStyle(
                        color: Colors.white,
                        fontSize: 16,
                      ),
                    ),
                  ],
                ),
              ),
            ),
          ] else ...[
            // Error state
            Container(
              color: Colors.black,
              child: Center(
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    Icon(
                      Icons.videocam_off,
                      size: 80,
                      color: Colors.grey[600],
                    ),
                    SizedBox(height: 20),
                    Text(
                      'Video call failed to connect',
                      style: TextStyle(
                        color: Colors.white,
                        fontSize: 16,
                      ),
                    ),
                    SizedBox(height: 10),
                    ElevatedButton(
                      onPressed: _setupCall,
                      child: Text('Retry Connection'),
                      style: ElevatedButton.styleFrom(
                        backgroundColor: Colors.blue,
                        foregroundColor: Colors.white,
                      ),
                    ),
                  ],
                ),
              ),
            ),
          ],

          // Controls overlay
          Positioned(
            bottom: 30,
            left: 0,
            right: 0,
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceEvenly,
              children: [
                // Toggle mute audio
                Container(
                  width: 60,
                  height: 60,
                  decoration: BoxDecoration(
                    color: Colors.white.withOpacity(0.2),
                    borderRadius: BorderRadius.circular(30),
                  ),
                  child: Icon(
                    Icons.mic,
                    color: Colors.white,
                    size: 30,
                  ),
                ),
                // End call
                Container(
                  width: 70,
                  height: 70,
                  decoration: BoxDecoration(
                    color: Colors.red,
                    borderRadius: BorderRadius.circular(35),
                  ),
                  child: Icon(
                    Icons.call_end,
                    color: Colors.white,
                    size: 35,
                  ),
                ),
                // Toggle camera
                Container(
                  width: 60,
                  height: 60,
                  decoration: BoxDecoration(
                    color: Colors.white.withOpacity(0.2),
                    borderRadius: BorderRadius.circular(30),
                  ),
                  child: Icon(
                    Icons.videocam,
                    color: Colors.white,
                    size: 30,
                  ),
                ),
              ],
            ),
          ),

          // Back button
          Positioned(
            top: 50,
            left: 20,
            child: Container(
              width: 40,
              height: 40,
              decoration: BoxDecoration(
                color: Colors.black.withOpacity(0.5),
                borderRadius: BorderRadius.circular(20),
              ),
              child: IconButton(
                icon: Icon(Icons.arrow_back, color: Colors.white),
                onPressed: () {
                  Navigator.pop(context);
                },
              ),
            ),
          ),
        ],
      ),
    );
  }
}