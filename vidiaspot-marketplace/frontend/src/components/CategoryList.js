import React, { useState, useEffect } from 'react';
import { Row, Col, Card, ListGroup, Alert } from 'react-bootstrap';
import axios from 'axios';

const CategoryList = () => {
  const [categories, setCategories] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    fetchCategories();
  }, []);

  const fetchCategories = async () => {
    try {
      const response = await axios.get('http://localhost:8000/api/categories');
      setCategories(response.data.data || []);
      setLoading(false);
    } catch (err) {
      setError('Failed to load categories. Please try again.');
      setLoading(false);
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
      <h2 className="mb-4">Browse Categories</h2>

      <Row>
        {categories.map(category => (
          <Col key={category.id} xs={12} sm={6} md={4} lg={3} className="mb-4">
            <Card 
              className="h-100 text-center cursor-pointer shadow-sm"
              onClick={() => window.location.href = `/ads?category_id=${category.id}`}
            >
              <Card.Body className="d-flex flex-column">
                <div className="mb-3">
                  <div className="bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto" style={{ width: '60px', height: '60px' }}>
                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" className="bi bi-grid-3x3-gap" viewBox="0 0 16 16">
                      <path d="M4 2v2H2V2h2zm1 12v-2a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1zm0-5V7a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1zm0-5V2a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1zm5 10v-2a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1zm0-5V7a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1zm0-5V2a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1zm5 10v-2a1 1 0 0 0-1-1h-2a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1zm0-5V7a1 1 0 0 0-1-1h-2a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1zm0-5V2a1 1 0 0 0-1-1h-2a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1z"/>
                    </svg>
                  </div>
                </div>
                <Card.Title className="mb-2">{category.name}</Card.Title>
                <Card.Text className="text-muted flex-grow-1">
                  {category.description}
                </Card.Text>
                <div className="mt-auto">
                  <small className="text-muted">
                    {category.ads_count || 0} ads
                  </small>
                </div>
              </Card.Body>
            </Card>
          </Col>
        ))}
      </Row>

      {categories.length === 0 && (
        <div className="text-center py-5">
          <h4>No categories available</h4>
          <p className="text-muted">Check back later for more categories</p>
        </div>
      )}
    </div>
  );
};

export default CategoryList;