import React, { useState } from 'react';

const Communication = () => {
  const [conversations, setConversations] = useState([
    {
      id: 'conv-1',
      customerId: 'cust-1',
      customerName: 'John Doe',
      lastMessage: 'Hi, I will be home between 2pm and 4pm today',
      lastMessageAt: new Date(Date.now() - 30 * 60000), // 30 minutes ago
      unreadCount: 1,
      messages: [
        {
          id: 'msg-1',
          sender: 'driver',
          content: 'Hi John, I will be delivering your package between 2pm and 4pm today',
          timestamp: new Date(Date.now() - 60 * 60000) // 1 hour ago
        },
        {
          id: 'msg-2',
          sender: 'customer',
          content: 'Hi, I will be home between 2pm and 4pm today',
          timestamp: new Date(Date.now() - 30 * 60000) // 30 minutes ago
        }
      ]
    },
    {
      id: 'conv-2',
      customerId: 'cust-2',
      customerName: 'Jane Smith',
      lastMessage: 'Can you please reschedule for tomorrow?',
      lastMessageAt: new Date(Date.now() - 2 * 60 * 60000), // 2 hours ago
      unreadCount: 0,
      messages: [
        {
          id: 'msg-3',
          sender: 'driver',
          content: 'Your delivery is scheduled for today between 10am and 12pm',
          timestamp: new Date(Date.now() - 3 * 60 * 60000) // 3 hours ago
        },
        {
          id: 'msg-4',
          sender: 'customer',
          content: 'Can you please reschedule for tomorrow?',
          timestamp: new Date(Date.now() - 2 * 60 * 60000) // 2 hours ago
        }
      ]
    },
    {
      id: 'conv-3',
      customerId: 'cust-3',
      customerName: 'Bob Johnson',
      lastMessage: 'Thanks for the delivery!',
      lastMessageAt: new Date(Date.now() - 1 * 60 * 60000), // 1 hour ago
      unreadCount: 0,
      messages: [
        {
          id: 'msg-5',
          sender: 'driver',
          content: 'Package delivered successfully. Thanks!',
          timestamp: new Date(Date.now() - 45 * 60000) // 45 minutes ago
        },
        {
          id: 'msg-6',
          sender: 'customer',
          content: 'Thanks for the delivery!',
          timestamp: new Date(Date.now() - 15 * 60000) // 15 minutes ago
        }
      ]
    }
  ]);

  const [activeConversation, setActiveConversation] = useState(null);
  const [newMessage, setNewMessage] = useState('');

  const handleSendMessage = (conversationId) => {
    if (!newMessage.trim()) return;

    const updatedConversations = conversations.map(conv => {
      if (conv.id === conversationId) {
        const newMsg = {
          id: `msg-${Date.now()}`,
          sender: 'driver',
          content: newMessage.trim(),
          timestamp: new Date()
        };
        
        return {
          ...conv,
          messages: [...conv.messages, newMsg],
          lastMessage: newMessage.trim(),
          lastMessageAt: new Date(),
          unreadCount: 0
        };
      }
      return conv;
    });

    setConversations(updatedConversations);
    setNewMessage('');
  };

  const formatTime = (date) => {
    const now = new Date();
    const diff = now - date;
    const minutes = Math.floor(diff / 60000);
    const hours = Math.floor(minutes / 60);
    const days = Math.floor(hours / 24);

    if (days > 0) return `${days}d ago`;
    if (hours > 0) return `${hours}h ago`;
    if (minutes > 0) return `${minutes}m ago`;
    return 'Just now';
  };

  return (
    <div className="flex h-full">
      {/* Conversation List */}
      {!activeConversation ? (
        <div className="w-full md:w-1/3 border-r border-gray-200">
          <div className="p-4 border-b border-gray-200">
            <h2 className="text-2xl font-bold">Messages</h2>
          </div>
          
          <div className="overflow-y-auto">
            {conversations.map((conversation) => (
              <div
                key={conversation.id}
                className={`p-4 border-b border-gray-200 cursor-pointer hover:bg-gray-50 ${
                  conversation.unreadCount > 0 ? 'bg-blue-50' : ''
                }`}
                onClick={() => setActiveConversation(conversation)}
              >
                <div className="flex items-center">
                  <div className="flex-shrink-0 w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold">
                    {conversation.customerName.charAt(0)}
                  </div>
                  <div className="ml-3 flex-1 min-w-0">
                    <div className="flex items-center justify-between">
                      <h3 className="text-sm font-medium truncate">{conversation.customerName}</h3>
                      <span className="text-xs text-gray-500">
                        {formatTime(conversation.lastMessageAt)}
                      </span>
                    </div>
                    <p className="text-sm text-gray-500 truncate">{conversation.lastMessage}</p>
                  </div>
                  {conversation.unreadCount > 0 && (
                    <div className="ml-2 bg-blue-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                      {conversation.unreadCount}
                    </div>
                  )}
                </div>
              </div>
            ))}
          </div>
        </div>
      ) : (
        /* Conversation Detail View */
        <div className="w-full">
          <div className="border-b border-gray-200 p-4 flex items-center">
            <button 
              onClick={() => setActiveConversation(null)}
              className="md:hidden mr-3 text-gray-500 hover:text-gray-700"
            >
              <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
              </svg>
            </button>
            <div className="flex items-center">
              <div className="flex-shrink-0 w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold">
                {activeConversation.customerName.charAt(0)}
              </div>
              <div className="ml-3">
                <h3 className="text-lg font-semibold">{activeConversation.customerName}</h3>
                <p className="text-sm text-gray-500">Online</p>
              </div>
            </div>
          </div>

          {/* Messages Area */}
          <div className="h-96 overflow-y-auto p-4">
            {activeConversation.messages.map((message) => (
              <div
                key={message.id}
                className={`mb-4 flex ${
                  message.sender === 'driver' ? 'justify-end' : 'justify-start'
                }`}
              >
                <div
                  className={`max-w-xs md:max-w-md px-4 py-2 rounded-lg ${
                    message.sender === 'driver'
                      ? 'bg-blue-500 text-white'
                      : 'bg-gray-200 text-gray-800'
                  }`}
                >
                  <p>{message.content}</p>
                  <p
                    className={`text-xs mt-1 ${
                      message.sender === 'driver' ? 'text-blue-100' : 'text-gray-500'
                    }`}
                  >
                    {formatTime(message.timestamp)}
                  </p>
                </div>
              </div>
            ))}
          </div>

          {/* Message Input */}
          <div className="border-t border-gray-200 p-4">
            <div className="flex">
              <input
                type="text"
                value={newMessage}
                onChange={(e) => setNewMessage(e.target.value)}
                onKeyPress={(e) => {
                  if (e.key === 'Enter' && newMessage.trim()) {
                    handleSendMessage(activeConversation.id);
                  }
                }}
                placeholder="Type a message..."
                className="flex-1 border border-gray-300 rounded-l-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
              <button
                onClick={() => handleSendMessage(activeConversation.id)}
                disabled={!newMessage.trim()}
                className="bg-blue-500 text-white px-4 py-2 rounded-r-lg hover:bg-blue-600 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                Send
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};

export default Communication;