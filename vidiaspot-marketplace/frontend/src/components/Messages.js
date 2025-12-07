import React, { useState, useEffect } from 'react';
import { Row, Col, Card, ListGroup, Form, Button, Alert } from 'react-bootstrap';
import axios from 'axios';

const Messages = () => {
  const [conversations, setConversations] = useState([]);
  const [selectedConversation, setSelectedConversation] = useState(null);
  const [messages, setMessages] = useState([]);
  const [newMessage, setNewMessage] = useState('');
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    fetchConversations();
  }, []);

  const fetchConversations = async () => {
    try {
      setLoading(true);
      const token = localStorage.getItem('token');
      if (!token) {
        throw new Error('You must be logged in to view messages');
      }

      const response = await axios.get('http://localhost:8000/api/messages/conversations', {
        headers: {
          'Authorization': `Bearer ${token}`
        }
      });

      setConversations(response.data.data || []);
      setLoading(false);
    } catch (err) {
      setError('Failed to load conversations. Please try again.');
      setLoading(false);
    }
  };

  const fetchMessages = async (conversationId) => {
    try {
      setSelectedConversation(conversationId);
      const token = localStorage.getItem('token');
      if (!token) return;

      const response = await axios.get(`http://localhost:8000/api/messages?partner_id=${conversationId}`, {
        headers: {
          'Authorization': `Bearer ${token}`
        }
      });

      setMessages(response.data.data.data || response.data.data || []);
    } catch (err) {
      setError('Failed to load messages. Please try again.');
    }
  };

  const handleSendMessage = async (e) => {
    e.preventDefault();
    
    if (!newMessage.trim()) return;

    try {
      const token = localStorage.getItem('token');
      if (!token) {
        throw new Error('You must be logged in to send messages');
      }

      // In a real implementation, we would get the receiver_id from the selected conversation
      // For now we'll use a placeholder (this would require more complex logic in a real app)
      const response = await axios.post('http://localhost:8000/api/messages', {
        receiver_id: selectedConversation, // This would be the actual user ID in a real app
        content: newMessage,
        ad_id: null // Optional ad reference
      }, {
        headers: {
          'Authorization': `Bearer ${token}`
        }
      });

      // Add the new message to the message list
      setMessages(prev => [...prev, response.data.data]);
      setNewMessage('');
    } catch (err) {
      setError('Failed to send message. Please try again.');
    }
  };

  if (loading) {
    return (
      <div className="text-center py-5">
        <div className="spinner-border" role="status">
          <span className="visually-hidden">Loading...</span>
        </div>
      </div>
    );
  }

  if (error) {
    return <Alert variant="danger">{error}</Alert>;
  }

  return (
    <div>
      <h2 className="mb-4">Messages</h2>
      
      <Row>
        {/* Conversations List */}
        <Col md={4} className="border-end">
          <h5>Conversations</h5>
          <ListGroup>
            {conversations.map((conversation) => (
              <ListGroup.Item
                key={conversation.id}
                action
                active={selectedConversation === conversation.sender_id || selectedConversation === conversation.receiver_id}
                onClick={() => fetchMessages(
                  conversation.sender_id === JSON.parse(localStorage.getItem('user')).id 
                    ? conversation.receiver_id 
                    : conversation.sender_id
                )}
              >
                <div className="d-flex align-items-center">
                  <div className="bg-secondary rounded-circle d-flex align-items-center justify-content-center" style={{ width: '40px', height: '40px' }}>
                    <span className="text-white fw-bold">
                      {conversation.sender_id === JSON.parse(localStorage.getItem('user')).id 
                        ? conversation.receiver?.name?.charAt(0) || 'U' 
                        : conversation.sender?.name?.charAt(0) || 'U'}
                    </span>
                  </div>
                  <div className="ms-3 flex-grow-1">
                    <div className="fw-bold">
                      {conversation.sender_id === JSON.parse(localStorage.getItem('user')).id 
                        ? conversation.receiver?.name || 'Unknown' 
                        : conversation.sender?.name || 'Unknown'}
                    </div>
                    <small className="text-muted">
                      {conversation.content.length > 30 
                        ? conversation.content.substring(0, 30) + '...' 
                        : conversation.content}
                    </small>
                  </div>
                </div>
              </ListGroup.Item>
            ))}
          </ListGroup>
        </Col>

        {/* Messages Area */}
        <Col md={8}>
          {selectedConversation ? (
            <div>
              <div className="border-bottom pb-2 mb-3">
                <h5>
                  {conversations.find(c => 
                    c.sender_id === selectedConversation || c.receiver_id === selectedConversation
                  )?.sender_id === JSON.parse(localStorage.getItem('user')).id 
                    ? conversations.find(c => 
                        c.sender_id === selectedConversation || c.receiver_id === selectedConversation
                      )?.receiver?.name || 'User' 
                    : conversations.find(c => 
                        c.sender_id === selectedConversation || c.receiver_id === selectedConversation
                      )?.sender?.name || 'User'}
                </h5>
              </div>

              <div className="messages-container" style={{ height: '400px', overflowY: 'scroll', marginBottom: '20px' }}>
                <ListGroup>
                  {messages.map((message) => (
                    <ListGroup.Item
                      key={message.id}
                      className={`${
                        message.sender_id === JSON.parse(localStorage.getItem('user')).id 
                          ? 'text-end' 
                          : 'text-start'
                      }`}
                    >
                      <div className={`d-inline-block p-2 rounded ${message.sender_id === JSON.parse(localStorage.getItem('user')).id ? 'bg-primary text-white' : 'bg-light'}`}>
                        {message.content}
                      </div>
                      <div className="text-muted small mt-1">
                        {new Date(message.created_at).toLocaleString()}
                      </div>
                    </ListGroup.Item>
                  ))}
                </ListGroup>
              </div>

              {/* Message Input */}
              <Form onSubmit={handleSendMessage}>
                <div className="d-flex">
                  <Form.Control
                    type="text"
                    value={newMessage}
                    onChange={(e) => setNewMessage(e.target.value)}
                    placeholder="Type your message..."
                    className="me-2"
                  />
                  <Button type="submit" variant="primary">Send</Button>
                </div>
              </Form>
            </div>
          ) : (
            <div className="text-center py-5">
              <h4>Select a conversation to start messaging</h4>
              <p className="text-muted">Choose a conversation from the list to view and send messages</p>
            </div>
          )}
        </Col>
      </Row>
    </div>
  );
};

export default Messages;