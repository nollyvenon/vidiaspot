<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>User Chat - VidiaSpot</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Chat CSS -->
    <style>
        :root {
            --chat-bg: #f8fafc;
            --chat-border: #e2e8f0;
            --user-msg-bg: #dbeafe;
            --other-msg-bg: #f1f5f9;
        }
        
        body {
            font-family: 'Figtree', sans-serif;
            margin: 0;
            padding: 0;
            background-color: var(--chat-bg);
        }
        
        .chat-container {
            display: flex;
            min-height: 100vh;
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .chat-sidebar {
            width: 300px;
            border-right: 1px solid var(--chat-border);
            display: flex;
            flex-direction: column;
        }
        
        .chat-main {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        .chat-header {
            padding: 1rem;
            border-bottom: 1px solid var(--chat-border);
            font-weight: 600;
        }
        
        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 1rem;
            display: flex;
            flex-direction: column;
        }
        
        .message {
            max-width: 70%;
            padding: 0.75rem;
            margin-bottom: 0.5rem;
            border-radius: 0.5rem;
            position: relative;
        }
        
        .message.user {
            align-self: flex-end;
            background-color: var(--user-msg-bg);
        }
        
        .message.other {
            align-self: flex-start;
            background-color: var(--other-msg-bg);
        }
        
        .message-info {
            font-size: 0.75rem;
            opacity: 0.7;
            margin-top: 0.25rem;
            text-align: right;
        }
        
        .chat-input {
            padding: 1rem;
            border-top: 1px solid var(--chat-border);
            display: flex;
        }
        
        .chat-input input {
            flex: 1;
            padding: 0.75rem;
            border: 1px solid var(--chat-border);
            border-radius: 0.5rem;
            margin-right: 0.5rem;
        }
        
        .chat-input button {
            padding: 0.75rem 1.5rem;
            background-color: #3b82f6;
            color: white;
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
        }
        
        .chat-participants {
            padding: 1rem;
            border-bottom: 1px solid var(--chat-border);
        }
        
        .chat-participants h3 {
            margin: 0 0 0.5rem 0;
            font-size: 1rem;
        }
        
        .chat-participant {
            display: flex;
            align-items: center;
            padding: 0.5rem;
            border-radius: 0.5rem;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        
        .chat-participant:hover {
            background-color: #f1f5f9;
        }
        
        .chat-participant.active {
            background-color: #dbeafe;
        }
        
        .chat-participant img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 0.75rem;
            object-fit: cover;
        }
        
        .participant-info {
            flex: 1;
        }
        
        .participant-name {
            font-weight: 500;
        }
        
        .participant-last-message {
            font-size: 0.8rem;
            color: #64748b;
        }
        
        .unread-count {
            background-color: #ef4444;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
        }
    </style>
</head>
<body>
    <div class="chat-container">
        <!-- Chat Sidebar -->
        <div class="chat-sidebar">
            <div class="chat-participants">
                <h3>Recent Chats</h3>
                <div id="participants-list">
                    <!-- Participants will be loaded here -->
                </div>
            </div>
        </div>
        
        <!-- Chat Main -->
        <div class="chat-main">
            <div class="chat-header" id="chat-header">
                Select a conversation
            </div>
            <div class="chat-messages" id="chat-messages">
                <!-- Messages will be loaded here -->
            </div>
            <div class="chat-input" id="chat-input-container" style="display: none;">
                <input type="text" id="message-input" placeholder="Type a message...">
                <button id="send-message">Send</button>
            </div>
        </div>
    </div>

    <script>
        let currentUserId = {{ auth()->id() ?? 'null' }};
        let currentPartnerId = null;
        
        // Load chat participants
        async function loadParticipants() {
            try {
                const response = await fetch('/api/chat/users', {
                    headers: {
                        'Authorization': 'Bearer ' + getAuthToken(),
                        'Content-Type': 'application/json',
                    }
                });
                
                if (!response.ok) throw new Error('Failed to load participants');
                
                const data = await response.json();
                const participantsList = document.getElementById('participants-list');
                
                participantsList.innerHTML = '';
                data.users.forEach(user => {
                    const participantDiv = document.createElement('div');
                    participantDiv.className = 'chat-participant';
                    participantDiv.dataset.userId = user.id;
                    
                    participantDiv.innerHTML = `
                        <img src="${user.avatar || '/images/default-avatar.png'}" alt="${user.name}">
                        <div class="participant-info">
                            <div class="participant-name">${user.name}</div>
                            <div class="participant-last-message">${user.last_message || 'No messages yet'}</div>
                        </div>
                        ${user.unread_count > 0 ? `<div class="unread-count">${user.unread_count}</div>` : ''}
                    `;
                    
                    participantDiv.addEventListener('click', () => selectParticipant(user.id, user.name));
                    participantsList.appendChild(participantDiv);
                });
            } catch (error) {
                console.error('Error loading participants:', error);
            }
        }
        
        // Select a participant to chat with
        async function selectParticipant(userId, userName) {
            currentPartnerId = userId;
            
            // Update active class
            document.querySelectorAll('.chat-participant').forEach(el => {
                el.classList.remove('active');
            });
            document.querySelector(`.chat-participant[data-user-id="${userId}"]`).classList.add('active');
            
            // Update chat header
            document.getElementById('chat-header').textContent = userName;
            
            // Show chat input
            document.getElementById('chat-input-container').style.display = 'flex';
            
            // Load chat history
            await loadChatHistory(userId);
            
            // Mark messages as read
            markMessagesAsRead(userId);
        }
        
        // Load chat history
        async function loadChatHistory(userId) {
            try {
                const response = await fetch(`/api/chat/history/${userId}`, {
                    headers: {
                        'Authorization': 'Bearer ' + getAuthToken(),
                        'Content-Type': 'application/json',
                    }
                });
                
                if (!response.ok) throw new Error('Failed to load chat history');
                
                const data = await response.json();
                const messagesDiv = document.getElementById('chat-messages');
                
                messagesDiv.innerHTML = '';
                data.chats.data.forEach(chat => {
                    addMessageToChat(chat);
                });
                
                // Scroll to bottom
                messagesDiv.scrollTop = messagesDiv.scrollHeight;
            } catch (error) {
                console.error('Error loading chat history:', error);
            }
        }
        
        // Add message to chat display
        function addMessageToChat(chat) {
            const messagesDiv = document.getElementById('chat-messages');
            const messageDiv = document.createElement('div');
            
            const isCurrentUser = chat.sender_id === currentUserId;
            messageDiv.className = `message ${isCurrentUser ? 'user' : 'other'}`;
            
            messageDiv.innerHTML = `
                <div class="message-text">${chat.message}</div>
                <div class="message-info">${new Date(chat.created_at).toLocaleTimeString()}</div>
            `;
            
            messagesDiv.appendChild(messageDiv);
            
            // Scroll to bottom
            messagesDiv.scrollTop = messagesDiv.scrollHeight;
        }
        
        // Send a message
        async function sendMessage() {
            const input = document.getElementById('message-input');
            const message = input.value.trim();
            
            if (!message || !currentPartnerId) return;
            
            try {
                const response = await fetch('/api/chat/send', {
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + getAuthToken(),
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        receiver_id: currentPartnerId,
                        message: message
                    })
                });
                
                if (!response.ok) throw new Error('Failed to send message');
                
                const data = await response.json();
                
                // Clear input
                input.value = '';
                
                // Add to chat display
                addMessageToChat(data.chat);
            } catch (error) {
                console.error('Error sending message:', error);
            }
        }
        
        // Mark messages as read
        async function markMessagesAsRead(userId) {
            try {
                await fetch(`/api/chat/mark-as-read/${userId}`, {
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + getAuthToken(),
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
            } catch (error) {
                console.error('Error marking messages as read:', error);
            }
        }
        
        // Get auth token
        function getAuthToken() {
            // This would be your actual auth token implementation
            return localStorage.getItem('auth_token') || '{{ auth()->user()?->createToken("chat")->plainTextToken ?? "" }}';
        }
        
        // Event listeners
        document.getElementById('send-message').addEventListener('click', sendMessage);
        document.getElementById('message-input').addEventListener('keypress', (e) => {
            if (e.key === 'Enter') sendMessage();
        });
        
        // Initial load
        if (currentUserId) {
            loadParticipants();
        } else {
            document.getElementById('chat-messages').innerHTML = '<div class="text-center p-4">Please login to access chat</div>';
        }
    </script>
</body>
</html>