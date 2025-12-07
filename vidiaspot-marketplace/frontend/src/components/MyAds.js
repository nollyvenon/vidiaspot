import React, { useState, useEffect } from 'react';
import { Row, Col, Card, Button, Alert, Badge } from 'react-bootstrap';
import axios from 'axios';

const MyAds = () => {
  const [ads, setAds] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    fetchMyAds();
  }, []);

  const fetchMyAds = async () => {
    try {
      const token = localStorage.getItem('token');
      if (!token) {
        throw new Error('You must be logged in to view your ads');
      }

      const response = await axios.get('http://localhost:8000/api/my-ads', {
        headers: {
          'Authorization': `Bearer ${token}`
        }
      });

      setAds(response.data.data.data || response.data.data || []);
      setLoading(false);
    } catch (err) {
      setError('Failed to load your ads. Please try again.');
      setLoading(false);
    }
  };

  const deleteAd = async (adId) => {
    if (!window.confirm('Are you sure you want to delete this ad?')) {
      return;
    }

    try {
      const token = localStorage.getItem('token');
      await axios.delete(`http://localhost:8000/api/ads/${adId}`, {
        headers: {
          'Authorization': `Bearer ${token}`
        }
      });

      // Remove the ad from the list
      setAds(prev => prev.filter(ad => ad.id !== adId));
    } catch (err) {
      setError('Failed to delete ad. Please try again.');
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
      <div className="d-flex justify-content-between align-items-center mb-4">
        <h2>My Ads</h2>
        <Button variant="success" href="/create-ad">Create New Ad</Button>
      </div>

      {ads.length === 0 ? (
        <div className="text-center py-5">
          <h4>You haven't posted any ads yet</h4>
          <p className="text-muted">Start by creating your first ad</p>
          <Button variant="success" href="/create-ad">Create Ad</Button>
        </div>
      ) : (
        <Row>
          {ads.map(ad => (
            <Col key={ad.id} xs={12} sm={6} md={4} lg={3} className="mb-4">
              <Card className="h-100 shadow-sm">
                <div style={{ height: '150px', background: '#f0f0f0', display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
                  {ad.images && ad.images.length > 0 ? (
                    <Card.Img 
                      variant="top" 
                      src={ad.images[0]?.image_url || 'https://via.placeholder.com/150'} 
                      style={{ height: '150px', objectFit: 'cover' }}
                    />
                  ) : (
                    <div className="text-muted">No Image</div>
                  )}
                </div>
                <Card.Body className="d-flex flex-column">
                  <Card.Title className="mb-1">{ad.title.length > 30 ? ad.title.substring(0, 30) + '...' : ad.title}</Card.Title>
                  <Card.Text className="text-muted mb-2">
                    ₦{parseFloat(ad.price).toLocaleString()}
                  </Card.Text>
                  <div className="mb-2">
                    <Badge 
                      bg={ad.status === 'active' ? 'success' : 
                          ad.status === 'sold' ? 'danger' : 'warning'}
                    >
                      {ad.status.charAt(0).toUpperCase() + ad.status.slice(1)}
                    </Badge>
                    <span className="text-muted small ms-2">{ad.condition.replace('_', ' ')}</span>
                  </div>
                  <Card.Text className="text-muted small mb-auto">
                    {ad.location}
                  </Card.Text>
                  <div className="mt-2">
                    <div className="d-grid gap-2">
                      <Button 
                        variant="primary" 
                        size="sm" 
                        onClick={() => window.location.href = `/ads/${ad.id}`}
                      >
                        View
                      </Button>
                      <Button 
                        variant="outline-primary" 
                        size="sm" 
                        onClick={() => window.location.href = `/ads/${ad.id}/edit`}
                      >
                        Edit
                      </Button>
                      <Button 
                        variant="outline-danger" 
                        size="sm" 
                        onClick={() => deleteAd(ad.id)}
                      >
                        Delete
                      </Button>
                    </div>
                  </div>
                </Card.Body>
              </Card>
            </Col>
          ))}
        </Row>
      )}
    </div>
  );
};

export default MyAds;