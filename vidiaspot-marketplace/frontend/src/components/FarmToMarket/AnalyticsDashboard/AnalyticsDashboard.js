import React, { useState, useEffect } from 'react';
import { AreaChart, Area, BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, Legend, ResponsiveContainer, PieChart, Pie, Cell } from 'recharts';

const AnalyticsDashboard = ({ farmId }) => {
  const [analyticsData, setAnalyticsData] = useState(null);
  const [period, setPeriod] = useState('monthly');
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    loadAnalyticsData();
  }, [farmId, period]);

  const loadAnalyticsData = async () => {
    setLoading(true);
    try {
      // Simulate API call
      await new Promise(resolve => setTimeout(resolve, 1000));
      
      // Mock data for demonstration
      const mockData = {
        totalRevenue: 12500.75,
        totalOrders: 342,
        totalCustomers: 120,
        avgOrderValue: 36.56,
        newCustomers: 24,
        conversionRate: 2.4,
        monthlyGrowth: 8.5,
        salesOverTime: [
          { date: 'Jan', revenue: 8500.00 },
          { date: 'Feb', revenue: 9200.50 },
          { date: 'Mar', revenue: 10100.75 },
          { date: 'Apr', revenue: 11500.25 },
          { date: 'May', revenue: 12300.80 },
          { date: 'Jun', revenue: 12900.60 },
          { date: 'Jul', revenue: 13500.90 },
          { date: 'Aug', revenue: 14200.30 },
          { date: 'Sep', revenue: 13800.75 },
          { date: 'Oct', revenue: 14100.20 },
          { date: 'Nov', revenue: 14500.50 },
          { date: 'Dec', revenue: 15200.75 },
        ],
        topSellingProducts: [
          { name: 'Fresh Tomatoes', unitsSold: 245, revenue: 732.55, percentage: 15.8 },
          { name: 'Organic Lettuce', unitsSold: 189, revenue: 376.11, percentage: 8.1 },
          { name: 'Farm Fresh Eggs', unitsSold: 156, revenue: 778.44, percentage: 16.8 },
          { name: 'Organic Potatoes', unitsSold: 134, revenue: 266.66, percentage: 5.7 },
          { name: 'Fresh Herbs', unitsSold: 120, revenue: 238.80, percentage: 5.1 },
        ],
      };
      
      setAnalyticsData(mockData);
      setLoading(false);
    } catch (error) {
      console.error('Error loading analytics data:', error);
      setLoading(false);
    }
  };

  if (loading) {
    return (
      <div className="loading-container">
        <div className="spinner"></div>
        <p>Loading Analytics...</p>
      </div>
    );
  }

  return (
    <div className="analytics-dashboard">
      <div className="dashboard-header">
        <h2>Analytics Dashboard</h2>
        <div className="period-selector">
          <select 
            value={period} 
            onChange={(e) => setPeriod(e.target.value)}
            className="period-dropdown"
          >
            <option value="daily">Daily</option>
            <option value="weekly">Weekly</option>
            <option value="monthly">Monthly</option>
            <option value="yearly">Yearly</option>
          </select>
        </div>
      </div>

      {/* Key Metrics */}
      <div className="analytics-metrics">
        <div className="metric-card">
          <div className="metric-header">
            <h3>Total Revenue</h3>
            <div className="metric-icon revenue">
              <i className="fas fa-dollar-sign"></i>
            </div>
          </div>
          <div className="metric-value">${analyticsData.totalRevenue.toLocaleString()}</div>
          <div className="metric-change positive">↑ {analyticsData.monthlyGrowth}% from last period</div>
        </div>

        <div className="metric-card">
          <div className="metric-header">
            <h3>Total Orders</h3>
            <div className="metric-icon orders">
              <i className="fas fa-shopping-bag"></i>
            </div>
          </div>
          <div className="metric-value">{analyticsData.totalOrders}</div>
          <div className="metric-change positive">+12% from last period</div>
        </div>

        <div className="metric-card">
          <div className="metric-header">
            <h3>Customers</h3>
            <div className="metric-icon customers">
              <i className="fas fa-users"></i>
            </div>
          </div>
          <div className="metric-value">{analyticsData.totalCustomers}</div>
          <div className="metric-change positive">+15 new customers</div>
        </div>

        <div className="metric-card">
          <div className="metric-header">
            <h3>Avg Order Value</h3>
            <div className="metric-icon avg-value">
              <i className="fas fa-chart-line"></i>
            </div>
          </div>
          <div className="metric-value">${analyticsData.avgOrderValue.toFixed(2)}</div>
          <div className="metric-change positive">↑ 5% from last period</div>
        </div>
      </div>

      {/* Charts */}
      <div className="analytics-charts">
        <div className="chart-container">
          <h3>Sales Over Time</h3>
          <ResponsiveContainer width="100%" height={300}>
            <AreaChart data={analyticsData.salesOverTime}>
              <CartesianGrid strokeDasharray="3 3" />
              <XAxis dataKey="date" />
              <YAxis />
              <Tooltip formatter={(value) => [`\$${value.toLocaleString()}`, 'Revenue']} />
              <Legend />
              <Area 
                type="monotone" 
                dataKey="revenue" 
                stroke="#4ade80" 
                fill="#dcfce7" 
                name="Revenue ($)" 
              />
            </AreaChart>
          </ResponsiveContainer>
        </div>

        <div className="chart-container">
          <h3>Top Selling Products</h3>
          <ResponsiveContainer width="100%" height={300}>
            <BarChart data={analyticsData.topSellingProducts}>
              <CartesianGrid strokeDasharray="3 3" />
              <XAxis dataKey="name" />
              <YAxis />
              <Tooltip 
                formatter={(value, name) => name === 'Units Sold' ? [value, name] : [`\$${value}`, name]}
              />
              <Legend />
              <Bar dataKey="unitsSold" fill="#4ade80" name="Units Sold" />
              <Bar dataKey="revenue" fill="#8b5cf6" name="Revenue ($)" />
            </BarChart>
          </ResponsiveContainer>
        </div>

        <div className="chart-container">
          <h3>Product Distribution</h3>
          <ResponsiveContainer width="100%" height={300}>
            <PieChart>
              <Pie
                data={analyticsData.topSellingProducts}
                cx="50%"
                cy="50%"
                labelLine={false}
                outerRadius={80}
                fill="#8884d8"
                dataKey="revenue"
                label={({ name, percent }) => `${name} ${(percent * 100).toFixed(0)}%`}
              >
                {analyticsData.topSellingProducts.map((entry, index) => (
                  <Cell key={`cell-${index}`} fill={['#4ade80', '#8b5cf6', '#3b82f6', '#f59e0b', '#ef4444'][index % 5]} />
                ))}
              </Pie>
              <Tooltip formatter={(value) => [`\$${value}`, 'Revenue']} />
            </PieChart>
          </ResponsiveContainer>
        </div>
      </div>

      {/* Performance Indicators */}
      <div className="performance-indicators">
        <div className="indicator-card">
          <h3>Conversion Rate</h3>
          <div className="indicator-value">{analyticsData.conversionRate}%</div>
          <div className="progress-bar">
            <div 
              className="progress-fill conversion" 
              style={{ width: `${analyticsData.conversionRate}%` }}
            ></div>
          </div>
          <p className="indicator-desc">Percentage of visitors who place an order</p>
        </div>

        <div className="indicator-card">
          <h3>Customer Satisfaction</h3>
          <div className="indicator-value">4.8/5.0</div>
          <div className="progress-bar">
            <div className="progress-fill satisfaction" style={{ width: '96%' }}></div>
          </div>
          <p className="indicator-desc">Average rating from customer reviews</p>
        </div>

        <div className="indicator-card">
          <h3>Delivery Performance</h3>
          <div className="indicator-value">95%</div>
          <div className="progress-bar">
            <div className="progress-fill delivery" style={{ width: '95%' }}></div>
          </div>
          <p className="indicator-desc">On-time delivery rate</p>
        </div>
      </div>

      {/* Top Products Table */}
      <div className="top-products-section">
        <h3>Top Selling Products</h3>
        <div className="table-responsive">
          <table className="analytics-table">
            <thead>
              <tr>
                <th>Product</th>
                <th>Units Sold</th>
                <th>Revenue</th>
                <th>% of Total</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              {analyticsData.topSellingProducts.map((product, index) => (
                <tr key={product.id}>
                  <td>
                    <div className="product-info">
                      <div className="product-image">
                        {product.mainImage ? (
                          <img src={product.mainImage} alt={product.name} />
                        ) : (
                          <div className="image-placeholder">🌱</div>
                        )}
                      </div>
                      <div className="product-details">
                        <div className="product-name">{product.name}</div>
                        <div className="category">{product.category}</div>
                      </div>
                    </div>
                  </td>
                  <td>{product.unitsSold}</td>
                  <td>${product.revenueGenerated.toFixed(2)}</td>
                  <td>{product.percentageOfTotal.toFixed(1)}%</td>
                  <td>
                    <button className="view-btn">View Details</button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
};

export default AnalyticsDashboard;