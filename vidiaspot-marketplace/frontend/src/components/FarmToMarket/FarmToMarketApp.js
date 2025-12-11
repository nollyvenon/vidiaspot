import React, { useState, useEffect } from 'react';
import { BrowserRouter as Router, Routes, Route, Link, useNavigate } from 'react-router-dom';
import { Provider, useDispatch, useSelector } from 'react-redux';
import './FarmToMarket.css';
import FarmDashboard from './components/FarmToMarket/FarmDashboard';
import ProductManagement from './components/FarmToMarket/ProductManagement';
import OrderManagement from './components/FarmToMarket/OrderManagement';
import FarmProfile from './components/FarmToMarket/FarmProfile';
import Analytics from './components/FarmToMarket/Analytics';
import LocationManagement from './components/FarmToMarket/LocationManagement';
import farmManagementService from './services/farmManagement/farmManagementService';

const FarmToMarketApp = () => {
  const [farmData, setFarmData] = useState(null);
  const [loading, setLoading] = useState(true);
  const [activeTab, setActiveTab] = useState('dashboard');

  useEffect(() => {
    loadFarmData();
  }, []);

  const loadFarmData = async () => {
    try {
      const data = await farmManagementService.getFarmData('farm-1'); // In a real app, this would be the logged-in farm's ID
      setFarmData(data);
      setLoading(false);
    } catch (error) {
      console.error('Error loading farm data:', error);
      setLoading(false);
    }
  };

  const handleLogout = () => {
    // Implement logout functionality
    localStorage.removeItem('farmToken');
    window.location.href = '/login';
  };

  if (loading) {
    return (
      <div className="loading-container">
        <div className="spinner"></div>
        <p>Loading Farm Dashboard...</p>
      </div>
    );
  }

  return (
    <div className="farm-to-market-app">
      <header className="app-header">
        <div className="header-content">
          <Link to="/farm/dashboard" className="logo-link">
            <div className="logo-container">
              <img src="/farm-logo.png" alt="Farm Logo" className="farm-logo" />
              <h1>FarmDirect</h1>
            </div>
          </Link>
          <div className="header-actions">
            <button className="notification-btn">
              <i className="icon-bell"></i>
              <span className="notification-badge">3</span>
            </button>
            <div className="user-menu">
              <img src={farmData?.logoUrl || '/user-avatar.png'} alt="User" className="user-avatar" />
              <span className="user-name">{farmData?.name}</span>
              <button className="dropdown-arrow" onClick={handleLogout}>▼</button>
            </div>
          </div>
        </div>
      </header>

      <div className="app-layout">
        <nav className="sidebar-nav">
          <ul className="nav-menu">
            <li>
              <Link 
                to="/farm/dashboard" 
                className={`nav-link ${activeTab === 'dashboard' ? 'active' : ''}`}
                onClick={() => setActiveTab('dashboard')}
              >
                <i className="icon-dashboard"></i>
                <span>Dashboard</span>
              </Link>
            </li>
            <li>
              <Link 
                to="/farm/products" 
                className={`nav-link ${activeTab === 'products' ? 'active' : ''}`}
                onClick={() => setActiveTab('products')}
              >
                <i className="icon-products"></i>
                <span>Products</span>
                <span className="badge">12</span>
              </Link>
            </li>
            <li>
              <Link 
                to="/farm/orders" 
                className={`nav-link ${activeTab === 'orders' ? 'active' : ''}`}
                onClick={() => setActiveTab('orders')}
              >
                <i className="icon-orders"></i>
                <span>Orders</span>
                <span className="badge">5</span>
              </Link>
            </li>
            <li>
              <Link 
                to="/farm/analytics" 
                className={`nav-link ${activeTab === 'analytics' ? 'active' : ''}`}
                onClick={() => setActiveTab('analytics')}
              >
                <i className="icon-analytics"></i>
                <span>Analytics</span>
              </Link>
            </li>
            <li>
              <Link 
                to="/farm/location" 
                className={`nav-link ${activeTab === 'location' ? 'active' : ''}`}
                onClick={() => setActiveTab('location')}
              >
                <i className="icon-location"></i>
                <span>Location</span>
              </Link>
            </li>
            <li>
              <Link 
                to="/farm/profile" 
                className={`nav-link ${activeTab === 'profile' ? 'active' : ''}`}
                onClick={() => setActiveTab('profile')}
              >
                <i className="icon-profile"></i>
                <span>Profile</span>
              </Link>
            </li>
          </ul>
        </nav>

        <main className="main-content">
          <Routes>
            <Route path="/farm/dashboard" element={<FarmDashboard farmData={farmData} />} />
            <Route path="/farm/products" element={<ProductManagement farmId={farmData.id} />} />
            <Route path="/farm/orders" element={<OrderManagement farmId={farmData.id} />} />
            <Route path="/farm/analytics" element={<Analytics farmId={farmData.id} />} />
            <Route path="/farm/location" element={<LocationManagement farmData={farmData} />} />
            <Route path="/farm/profile" element={<FarmProfile farmData={farmData} />} />
          </Routes>
        </main>
      </div>
    </div>
  );
};

// Dashboard Component
const FarmDashboard = ({ farmData }) => {
  const [stats, setStats] = useState({
    totalRevenue: 0,
    totalOrders: 0,
    activeProducts: 0,
    pendingOrders: 0,
    avgRating: 0,
    newCustomers: 0,
  });

  useEffect(() => {
    loadDashboardStats();
  }, []);

  const loadDashboardStats = async () => {
    try {
      const analytics = await farmManagementService.getFarmAnalytics(farmData.id);
      setStats({
        totalRevenue: analytics.totalRevenue,
        totalOrders: analytics.totalOrders,
        activeProducts: 12, // Would come from products API
        pendingOrders: 5, // Would come from orders API
        avgRating: farmData.rating,
        newCustomers: analytics.newCustomers,
      });
    } catch (error) {
      console.error('Error loading dashboard stats:', error);
    }
  };

  return (
    <div className="dashboard-container">
      <div className="dashboard-header">
        <h2>Welcome back, {farmData.ownerName}!</h2>
        <p>Here's what's happening with your farm today.</p>
      </div>

      <div className="dashboard-stats">
        <div className="stat-card revenue">
          <div className="stat-icon">
            <i className="icon-revenue"></i>
          </div>
          <div className="stat-info">
            <h3>Total Revenue</h3>
            <p className="stat-value">${stats.totalRevenue.toLocaleString()}</p>
            <p className="stat-change">+12.5% from last month</p>
          </div>
        </div>

        <div className="stat-card orders">
          <div className="stat-icon">
            <i className="icon-orders"></i>
          </div>
          <div className="stat-info">
            <h3>Total Orders</h3>
            <p className="stat-value">{stats.totalOrders}</p>
            <p className="stat-change">+8.2% from last month</p>
          </div>
        </div>

        <div className="stat-card products">
          <div className="stat-icon">
            <i className="icon-products"></i>
          </div>
          <div className="stat-info">
            <h3>Active Products</h3>
            <p className="stat-value">{stats.activeProducts}</p>
            <p className="stat-change">+3 new this week</p>
          </div>
        </div>

        <div className="stat-card customers">
          <div className="stat-icon">
            <i className="icon-customers"></i>
          </div>
          <div className="stat-info">
            <h3>New Customers</h3>
            <p className="stat-value">{stats.newCustomers}</p>
            <p className="stat-change">+5 this week</p>
          </div>
        </div>
      </div>

      <div className="dashboard-content">
        <div className="left-column">
          <div className="section-card">
            <h3>Pending Orders</h3>
            <div className="order-list">
              <div className="order-item">
                <div className="order-info">
                  <h4>Order #VID-FTM-2023-001</h4>
                  <p>John Doe - 2 items</p>
                  <p className="order-total">$12.37</p>
                </div>
                <div className="order-actions">
                  <button className="btn btn-primary">View Details</button>
                </div>
              </div>
              <div className="order-item">
                <div className="order-info">
                  <h4>Order #VID-FTM-2023-002</h4>
                  <p>Jane Smith - 1 item</p>
                  <p className="order-total">$8.24</p>
                </div>
                <div className="order-actions">
                  <button className="btn btn-primary">View Details</button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div className="right-column">
          <div className="section-card">
            <h3>Top Products</h3>
            <div className="product-list">
              <div className="product-item">
                <div className="product-image">
                  <img src="https://example.com/tomatoes.jpg" alt="Tomatoes" />
                </div>
                <div className="product-info">
                  <h4>Fresh Tomatoes</h4>
                  <p>Sold: 245 units</p>
                  <p className="revenue">$732.55</p>
                </div>
              </div>
              <div className="product-item">
                <div className="product-image">
                  <img src="https://example.com/lettuce.jpg" alt="Lettuce" />
                </div>
                <div className="product-info">
                  <h4>Organic Lettuce</h4>
                  <p>Sold: 189 units</p>
                  <p className="revenue">$376.11</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default FarmToMarketApp;