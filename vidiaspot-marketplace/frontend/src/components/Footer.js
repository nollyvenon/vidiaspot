import React from 'react';
import { Container, Row, Col } from 'react-bootstrap';

const Footer = () => {
  const currentYear = new Date().getFullYear();

  return (
    <footer className="bg-light mt-auto py-4">
      <Container>
        <Row>
          <Col md={4}>
            <h5>VidiAspot Marketplace</h5>
            <p>Your trusted platform for buying and selling locally.</p>
          </Col>
          <Col md={2}>
            <h6>Quick Links</h6>
            <ul className="list-unstyled">
              <li><a href="/ads" className="text-decoration-none">Browse Ads</a></li>
              <li><a href="/categories" className="text-decoration-none">Categories</a></li>
              <li><a href="/create-ad" className="text-decoration-none">Post Ad</a></li>
            </ul>
          </Col>
          <Col md={2}>
            <h6>Support</h6>
            <ul className="list-unstyled">
              <li><a href="#" className="text-decoration-none">Help Center</a></li>
              <li><a href="#" className="text-decoration-none">Safety Tips</a></li>
              <li><a href="#" className="text-decoration-none">Contact Us</a></li>
            </ul>
          </Col>
          <Col md={4}>
            <h6>Download Our App</h6>
            <p>Get the VidiAspot app for a better experience on mobile devices.</p>
            <div className="d-flex">
              <button className="btn btn-dark me-2">Apple App Store</button>
              <button className="btn btn-dark">Google Play</button>
            </div>
          </Col>
        </Row>
        <hr />
        <Row>
          <Col className="text-center">
            <p className="mb-0">&copy; {currentYear} VidiAspot Marketplace. All rights reserved.</p>
          </Col>
        </Row>
      </Container>
    </footer>
  );
};

export default Footer;