// Farmer Dashboard Component
import React, { useState, useEffect } from 'react';
import './FarmerDashboard.css';
import ProductManagement from './ProductManagement/ProductManagement';
import OrderManagement from './OrderManagement/OrderManagement';
import Analytics from './Analytics/Analytics';
import FarmProfile from './FarmProfile/FarmProfile';

const FarmerDashboard = () => {
  const [activeTab, setActiveTab] = useState('dashboard');
  const [farmData, setFarmData] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    // Load farm data when component mounts
    loadFarmData();
  }, []);

  const loadFarmData = async () => {
    try {
      // In a real app, this would fetch from the API
      setTimeout(() => {
        setFarmData({
          id: 'farm-1',
          name: 'Green Valley Farms',
          ownerName: 'John Doe',
          email: 'john@greenvalley.com',
          phone: '+1234567890',
          address: '123 Farm Road, Agriculture District',
          description: 'Sustainable organic farming producing fresh vegetables and fruits',
          categories: ['Vegetables', 'Fruits', 'Organic'],
          rating: 4.8,
          numReviews: 120,
          isActive: true,
          acceptsOnlineOrders: true,
          offersDelivery: true,
          offersPickup: true,
          deliveryRadius: '15 km',
          operatingHours: ['Mon-Fri: 8AM-6PM', 'Sat: 8AM-4PM', 'Sun: Closed'],
          logoUrl: '',
          bannerImage: '',
          paymentMethods: ['Cash', 'Card', 'Mobile Money'],
          yearsInBusiness: 5,
          certifications: ['Organic Certified', 'Fair Trade'],
        });
        setLoading(false);
      }, 1000);
    } catch (error) {
      console.error('Error loading farm data:', error);
      setLoading(false);
    }
  };

  const renderTabContent = () => {
    switch (activeTab) {
      case 'dashboard':
        return <DashboardView farmData={farmData} />;
      case 'products':
        return <ProductManagement />;
      case 'orders':
        return <OrderManagement />;
      case 'analytics':
        return <Analytics />;
      case 'profile':
        return <FarmProfile farmData={farmData} />;
      default:
        return <DashboardView farmData={farmData} />;
    }
  };

  if (loading) {
    return (
      <div className="loading-container">
        <div className="spinner"></div>
        <p>Loading Farmer Dashboard...</p>
      </div>
    );
  }

  return (
    <div className="farmer-dashboard">
      <div className="dashboard-header">
        <h1>Farmer Dashboard</h1>
        <div className="user-info">
          <span>Welcome, {farmData?.ownerName || 'Farmer'}!</span>
          <div className="status-indicator active">Online</div>
        </div>
      </div>

      <div className="dashboard-content">
        <nav className="sidebar">
          <ul>
            <li>
              <button 
                className={activeTab === 'dashboard' ? 'active' : ''}
                onClick={() => setActiveTab('dashboard')}
              >
                <i className="icon-dashboard"></i>
                Dashboard
              </button>
            </li>
            <li>
              <button 
                className={activeTab === 'products' ? 'active' : ''}
                onClick={() => setActiveTab('products')}
              >
                <i className="icon-products"></i>
                Products
              </button>
            </li>
            <li>
              <button 
                className={activeTab === 'orders' ? 'active' : ''}
                onClick={() => setActiveTab('orders')}
              >
                <i className="icon-orders"></i>
                Orders
              </button>
            </li>
            <li>
              <button 
                className={activeTab === 'analytics' ? 'active' : ''}
                onClick={() => setActiveTab('analytics')}
              >
                <i className="icon-analytics"></i>
                Analytics
              </button>
            </li>
            <li>
              <button 
                className={activeTab === 'profile' ? 'active' : ''}
                onClick={() => setActiveTab('profile')}
              >
                <i className="icon-profile"></i>
                Farm Profile
              </button>
            </li>
          </ul>
        </nav>

        <main className="main-content">
          {renderTabContent()}
        </main>
      </div>
    </div>
  );
};

// Dashboard View Component
const DashboardView = ({ farmData }) => {
  const [stats, setStats] = useState({
    totalRevenue: 0,
    totalOrders: 0,
    activeProducts: 0,
    pendingOrders: 0,
    avgRating: 0,
    newCustomers: 0,
  });

  useEffect(() => {
    // Load dashboard statistics
    loadDashboardStats();
  }, []);

  const loadDashboardStats = () => {
    // In a real app, this would fetch from the API
    setTimeout(() => {
      setStats({
        totalRevenue: 12500.75,
        totalOrders: 342,
        activeProducts: 45,
        pendingOrders: 12,
        avgRating: 4.8,
        newCustomers: 24,
      });
    }, 500);
  };

  return (
    <div className="dashboard-view">
      <div className="farm-overview">
        <h2>Farm Overview</h2>
        <div className="farm-info">
          <div className="farm-logo">
            {farmData?.logoUrl ? (
              <img src={farmData.logoUrl} alt="Farm Logo" />
            ) : (
              <div className="placeholder-logo">GV</div>
            )}
          </div>
          <div className="farm-details">
            <h3>{farmData?.name}</h3>
            <p>{farmData?.description}</p>
            <div className="farm-stats">
              <span className="rating">★ {farmData?.rating} ({farmData?.numReviews} reviews)</span>
              <span className="status">{farmData?.isActive ? 'Active' : 'Inactive'}</span>
              <span className="years">5 years in business</span>
            </div>
          </div>
        </div>
      </div>

      <div className="performance-metrics">
        <h2>Performance Metrics</h2>
        <div className="metrics-grid">
          <div className="metric-card revenue">
            <div className="metric-icon">💰</div>
            <div className="metric-info">
              <h3>Total Revenue</h3>
              <p>${stats.totalRevenue.toLocaleString()}</p>
            </div>
          </div>
          <div className="metric-card orders">
            <div className="metric-icon">📦</div>
            <div className="metric-info">
              <h3>Total Orders</h3>
              <p>{stats.totalOrders}</p>
            </div>
          </div>
          <div className="metric-card products">
            <div className="metric-icon">🌱</div>
            <div className="metric-info">
              <h3>Active Products</h3>
              <p>{stats.activeProducts}</p>
            </div>
          </div>
          <div className="metric-card pending">
            <div className="metric-icon">⏳</div>
            <div className="metric-info">
              <h3>Pending Orders</h3>
              <p>{stats.pendingOrders}</p>
            </div>
          </div>
        </div>
      </div>

      <div className="recent-activity">
        <h2>Recent Activity</h2>
        <div className="activity-list">
          <div className="activity-item">
            <div className="activity-icon">✅</div>
            <div className="activity-details">
              <h4>New Order Received</h4>
              <p>Order #ORD-2023-001 from Jane Smith for $85.50</p>
              <span className="time-ago">2 hours ago</span>
            </div>
          </div>
          <div className="activity-item">
            <div className="activity-icon">⭐</div>
            <div className="activity-details">
              <h4>New Review Received</h4>
              <p>5-star review from Michael Johnson for Fresh Tomatoes</p>
              <span className="time-ago">5 hours ago</span>
            </div>
          </div>
          <div className="activity-item">
            <div className="activity-icon">➕</div>
            <div className="activity-details">
              <h4>New Product Added</h4>
              <p>Added Organic Carrots to your product list</p>
              <span className="time-ago">1 day ago</span>
            </div>
          </div>
        </div>
      </div>

      <div className="quick-actions">
        <h2>Quick Actions</h2>
        <div className="actions-grid">
          <button className="action-btn">
            <i className="icon-add-product"></i>
            <span>Add Product</span>
          </button>
          <button className="action-btn">
            <i className="icon-view-orders"></i>
            <span>View Orders</span>
          </button>
          <button className="action-btn">
            <i className="icon-update-profile"></i>
            <span>Update Profile</span>
          </button>
          <button className="action-btn">
            <i className="icon-analyze"></i>
            <span>View Analytics</span>
          </button>
        </div>
      </div>
    </div>
  );
};

export default FarmerDashboard;