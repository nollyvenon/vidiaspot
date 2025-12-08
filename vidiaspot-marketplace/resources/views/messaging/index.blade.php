@extends('layouts.app')

@section('title', 'Messages')
@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Conversations</h5>
                    <button class="btn btn-sm btn-success" id="new-conversation-btn">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush" id="conversations-list">
                        <!-- Conversations will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0" id="current-conversation-title">Select a conversation</h5>
                    <div>
                        <button class="btn btn-sm btn-outline-secondary me-1" id="schedule-btn" title="Schedule meeting">
                            <i class="fas fa-calendar"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-success" id="video-call-btn" title="Video call">
                            <i class="fas fa-video"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="chat-container" style="height: 400px; overflow-y: auto;">
                        <div id="messages-container">
                            <!-- Messages will be loaded here -->
                        </div>
                    </div>
                    <div class="border-top p-3">
                        <div class="input-group">
                            <textarea id="message-input" class="form-control" rows="2" placeholder="Type a message..."></textarea>
                            <button class="btn btn-success" type="button" id="send-message-btn">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                        <div class="mt-2">
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-outline-secondary" id="smart-reply-btn">
                                    <i class="fas fa-lightbulb"></i> Smart Reply
                                </button>
                                <button type="button" class="btn btn-outline-secondary" id="voice-message-btn">
                                    <i class="fas fa-microphone"></i> Voice
                                </button>
                                <button type="button" class="btn btn-outline-secondary" id="image-message-btn">
                                    <i class="fas fa-image"></i> Image
                                </button>
                            </div>
                        </div>
                        <div id="smart-replies-container" class="mt-2" style="display: none;">
                            <div class="d-flex flex-wrap gap-2">
                                <!-- Smart replies will appear here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentConversationId = null;
    let currentAdId = null;

    // Load conversations
    loadConversations();

    // Event listeners
    document.getElementById('send-message-btn').addEventListener('click', sendMessage);
    document.getElementById('message-input').addEventListener('keypress', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    document.getElementById('smart-reply-btn').addEventListener('click', function() {
        const messageInput = document.getElementById('message-input').value.trim();
        const smartRepliesContainer = document.getElementById('smart-replies-container');
        
        fetch('/messaging/smart-replies', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                message: messageInput,
                context: {}
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const repliesContainer = smartRepliesContainer.querySelector('.d-flex');
                repliesContainer.innerHTML = '';
                
                data.replies.forEach(reply => {
                    const replyBtn = document.createElement('button');
                    replyBtn.className = 'btn btn-sm btn-outline-primary';
                    replyBtn.textContent = reply;
                    replyBtn.addEventListener('click', function() {
                        document.getElementById('message-input').value = reply;
                        smartRepliesContainer.style.display = 'none';
                    });
                    repliesContainer.appendChild(replyBtn);
                });
                
                smartRepliesContainer.style.display = 'block';
            }
        })
        .catch(error => console.error('Error getting smart replies:', error));
    });

    function loadConversations() {
        fetch('/messaging/conversations', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            const conversationsList = document.getElementById('conversations-list');
            conversationsList.innerHTML = '';
            
            if (data.success) {
                data.conversations.forEach(conversation => {
                    const conversationDiv = document.createElement('div');
                    conversationDiv.className = 'list-group-item list-group-item-action';
                    conversationDiv.innerHTML = `
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">${conversation.title || conversation.ad?.title || 'Conversation'}</h6>
                            <small>${conversation.last_message_at ? new Date(conversation.last_message_at).toLocaleTimeString() : ''}</small>
                        </div>
                        <p class="mb-1">${conversation.latest_message?.content || 'No messages yet'}</p>
                        ${conversation.unread_count > 0 ? 
                            `<span class="badge bg-danger">${conversation.unread_count}</span>` : ''}
                        <small>With ${conversation.user1_id === {{ Auth::id() }} ? 
                            (conversation.user2?.name || 'Unknown') : 
                            (conversation.user1?.name || 'Unknown')}</small>
                    `;
                    
                    conversationDiv.addEventListener('click', function() {
                        loadConversation(conversation.id);
                        currentAdId = conversation.ad_id;
                    });
                    
                    conversationsList.appendChild(conversationDiv);
                });
            }
        })
        .catch(error => console.error('Error loading conversations:', error));
    }

    function loadConversation(conversationId) {
        currentConversationId = conversationId;
        
        fetch(`/messaging/conversations/${conversationId}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('current-conversation-title').textContent = 
                    data.conversation.title || 'Conversation';
                
                const messagesContainer = document.getElementById('messages-container');
                messagesContainer.innerHTML = '';
                
                data.messages.forEach(message => {
                    addMessageToChat(message);
                });
                
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }
        })
        .catch(error => console.error('Error loading conversation:', error));
    }

    function sendMessage() {
        const messageInput = document.getElementById('message-input');
        const content = messageInput.value.trim();
        
        if (!content || !currentConversationId) return;
        
        const messageData = {
            conversation_id: currentConversationId,
            content: content
        };
        
        fetch(`/messaging/conversations/${currentConversationId}/messages`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(messageData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                messageInput.value = '';
                document.getElementById('smart-replies-container').style.display = 'none';
                
                // Add the new message to the chat
                addMessageToChat(data.message);
                
                // Scroll to the bottom
                const chatContainer = document.querySelector('.chat-container');
                chatContainer.scrollTop = chatContainer.scrollHeight;
            }
        })
        .catch(error => console.error('Error sending message:', error));
    }

    function addMessageToChat(message) {
        const messagesContainer = document.getElementById('messages-container');
        const messageDiv = document.createElement('div');
        messageDiv.className = `d-flex mb-3 ${message.sender_id === {{ Auth::id() }} ? 'justify-content-end' : 'justify-content-start'}`;
        
        const isCurrentUser = message.sender_id === {{ Auth::id() }};
        const senderName = isCurrentUser ? 'You' : message.sender?.name || 'User';
        
        messageDiv.innerHTML = `
            <div class="${isCurrentUser ? 'bg-success text-white' : 'bg-light'} p-3 rounded" style="max-width: 70%;">
                <small class="d-block mb-1"><strong>${senderName}</strong></small>
                <div>${message.content}</div>
                <small class="text-muted">${new Date(message.created_at).toLocaleTimeString()}</small>
            </div>
        `;
        
        messagesContainer.appendChild(messageDiv);
    }
});
</script>
@endsection