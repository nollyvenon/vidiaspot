import React, { useState, useEffect } from 'react';
import { withAuth } from '../utils/withAuth';
import reportService from '../services/reportService';
import { BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, Legend, ResponsiveContainer, PieChart, Pie, Cell, LineChart, Line, AreaChart, Area } from 'recharts';

const DetailedReportsDashboard = () => {
  const [activeTab, setActiveTab] = useState('customer-experience');
  const [reportType, setReportType] = useState('order-fulfillment');
  const [reportData, setReportData] = useState(null);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);
  const [dateRange, setDateRange] = useState({ start: '', end: '' });
  const [filters, setFilters] = useState({});

  const COLORS = ['#0088FE', '#00C49F', '#FFBB28', '#FF8042', '#8884D8', '#82CA9D', '#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4'];

  useEffect(() => {
    fetchReportData();
  }, [activeTab, reportType, dateRange, filters]);

  const fetchReportData = async () => {
    if (!reportType) return;
    
    setLoading(true);
    setError(null);

    try {
      // Build params object with date range and filters
      let params = { ...dateRange, ...filters };

      let data;
      switch (reportType) {
        // Customer Experience Reports
        case 'order-fulfillment':
          data = await reportService.getOrderFulfillment(params);
          break;
        case 'delivery-metrics':
          data = await reportService.getDeliveryMetrics(params);
          break;
        case 'customer-feedback':
          data = await reportService.getCustomerFeedback(params);
          break;
        case 'user-engagement':
          data = await reportService.getUserEngagement(params);
          break;
        case 'loyalty-program':
          data = await reportService.getLoyaltyProgram(params);
          break;
        
        // Financial Reports
        case 'cost-goods':
          data = await reportService.getCostOfGoods(params);
          break;
        case 'profit-margins':
          data = await reportService.getProfitMargins(params);
          break;
        case 'commission':
          data = await reportService.getCommissionReports(params);
          break;
        case 'waste-loss':
          data = await reportService.getWasteLoss(params);
          break;
        case 'break-even':
          data = await reportService.getBreakEvenAnalysis(params);
          break;
          
        // Classified App Reports
        case 'user-registration':
          data = await reportService.getUserRegistration(params);
          break;
        case 'listing-performance':
          data = await reportService.getListingPerformance(params);
          break;
        case 'category-analysis':
          data = await reportService.getCategoryAnalysis(params);
          break;
        case 'engagement-metrics':
          data = await reportService.getEngagementMetrics(params);
          break;
        case 'user-retention':
          data = await reportService.getUserRetention(params);
          break;
          
        default:
          data = [];
      }

      setReportData(data);
    } catch (err) {
      setError(`Failed to fetch ${reportType.replace('-', ' ')} report. Please try again.`);
      console.error(`Error fetching ${reportType} report:`, err);
    } finally {
      setLoading(false);
    }
  };

  // Function to render appropriate chart based on report type
  const renderChart = () => {
    if (!reportData || !Array.isArray(reportData) || reportData.length === 0) {
      return <div className="text-center py-10 text-gray-500">No data available for the selected period</div>;
    }

    // Define different chart types based on report
    const isPieChart = ['customer-feedback', 'payment-method'].includes(reportType);
    const isLineChart = ['delivery-metrics', 'user-engagement', 'user-retention'].includes(reportType);
    const isAreaChart = ['order-fulfillment'].includes(reportType);
    const isBarChart = !isPieChart && !isLineChart && !isAreaChart;

    if (isPieChart) {
      return (
        <ResponsiveContainer width="100%" height={400}>
          <PieChart>
            <Pie
              data={reportData}
              cx="50%"
              cy="50%"
              labelLine={false}
              label={({ name, percent }) => `${name} ${(percent * 100).toFixed(0)}%`}
              outerRadius={80}
              fill="#8884d8"
              dataKey="value"
            >
              {reportData.map((entry, index) => (
                <Cell key={`cell-${index}`} fill={COLORS[index % COLORS.length]} />
              ))}
            </Pie>
            <Tooltip />
            <Legend />
          </PieChart>
        </ResponsiveContainer>
      );
    } else if (isAreaChart) {
      return (
        <ResponsiveContainer width="100%" height={400}>
          <AreaChart
            data={reportData}
            margin={{ top: 10, right: 30, left: 0, bottom: 0 }}
          >
            <defs>
              <linearGradient id="colorAccuracy" x1="0" y1="0" x2="0" y2="1">
                <stop offset="5%" stopColor="#8884d8" stopOpacity={0.8}/>
                <stop offset="95%" stopColor="#8884d8" stopOpacity={0}/>
              </linearGradient>
              <linearGradient id="colorComplaints" x1="0" y1="0" x2="0" y2="1">
                <stop offset="5%" stopColor="#00C49F" stopOpacity={0.8}/>
                <stop offset="95%" stopColor="#00C49F" stopOpacity={0}/>
              </linearGradient>
            </defs>
            <XAxis dataKey="name" />
            <YAxis />
            <CartesianGrid strokeDasharray="3 3" />
            <Tooltip />
            <Area type="monotone" dataKey="accuracy" stackId="1" stroke="#8884d8" fillOpacity={1} fill="url(#colorAccuracy)" name="Accuracy" />
            <Area type="monotone" dataKey="complaints" stackId="2" stroke="#00C49F" fillOpacity={1} fill="url(#colorComplaints)" name="Complaints" />
          </AreaChart>
        </ResponsiveContainer>
      );
    } else if (isLineChart) {
      return (
        <ResponsiveContainer width="100%" height={400}>
          <LineChart
            data={reportData}
            margin={{ top: 5, right: 30, left: 20, bottom: 5 }}
          >
            <CartesianGrid strokeDasharray="3 3" />
            <XAxis dataKey="name" />
            <YAxis />
            <Tooltip />
            <Legend />
            {Object.keys(reportData[0] || {}).filter(key => key !== 'name').map((key, index) => (
              <Line 
                key={key} 
                type="monotone" 
                dataKey={key} 
                stroke={COLORS[index % COLORS.length]} 
                name={key.replace(/([A-Z])/g, ' $1').replace(/^./, str => str.toUpperCase())} 
                activeDot={{ r: 8 }} 
              />
            ))}
          </LineChart>
        </ResponsiveContainer>
      );
    } else {
      // Default bar chart
      return (
        <ResponsiveContainer width="100%" height={400}>
          <BarChart
            data={reportData}
            margin={{ top: 5, right: 30, left: 20, bottom: 5 }}
          >
            <CartesianGrid strokeDasharray="3 3" />
            <XAxis dataKey="name" />
            <YAxis />
            <Tooltip />
            <Legend />
            {Object.keys(reportData[0] || {}).filter(key => key !== 'name').map((key, index) => (
              <Bar 
                key={key} 
                dataKey={key} 
                fill={COLORS[index % COLORS.length]} 
                name={key.replace(/([A-Z])/g, ' $1').replace(/^./, str => str.toUpperCase())} 
              />
            ))}
          </BarChart>
        </ResponsiveContainer>
      );
    }
  };

  // Get report title based on active tab and report type
  const getReportTitle = () => {
    switch (reportType) {
      case 'order-fulfillment': return 'Order Fulfillment Performance';
      case 'delivery-metrics': return 'Delivery Metrics Analysis';
      case 'customer-feedback': return 'Customer Feedback Analysis';
      case 'user-engagement': return 'User Engagement Metrics';
      case 'loyalty-program': return 'Loyalty Program Effectiveness';
      case 'cost-goods': return 'Cost of Goods Sold Analysis';
      case 'profit-margins': return 'Profit Margins Report';
      case 'commission': return 'Commission Reports';
      case 'waste-loss': return 'Waste and Loss Analysis';
      case 'break-even': return 'Break-even Analysis';
      case 'user-registration': return 'User Registration Analytics';
      case 'listing-performance': return 'Listing Performance Metrics';
      case 'category-analysis': return 'Category Analysis Report';
      case 'engagement-metrics': return 'Engagement Metrics Analysis';
      case 'user-retention': return 'User Retention Analysis';
      default: return 'Report Dashboard';
    }
  };

  // Get report description based on report type
  const getReportDescription = () => {
    switch (reportType) {
      case 'order-fulfillment': return 'Accuracy rates, complaint analysis, and satisfaction scores for order fulfillment';
      case 'delivery-metrics': return 'On-time delivery rates, GPS tracking analytics, and delivery performance metrics';
      case 'customer-feedback': return 'Review analysis, complaint trends, and improvement areas identification';
      case 'user-engagement': return 'App usage patterns, feature adoption rates, and churn analysis';
      case 'loyalty-program': return 'Points redemption, customer retention, and rewards impact analysis';
      case 'cost-goods': return 'Food costs, packaging expenses, and delivery cost breakdown';
      case 'profit-margins': return 'Profit margins by item, location, time period, and vendor';
      case 'commission': return 'Fees paid to vendors and delivery partners analysis';
      case 'waste-loss': return 'Food waste, equipment damage, and theft reports';
      case 'break-even': return 'Break-even analysis per location, per vendor, and per item';
      case 'user-registration': return 'Daily/monthly registrations, source channels, and demographic analysis';
      case 'listing-performance': return 'Views, clicks, responses, and conversion rates for listings';
      case 'category-analysis': return 'Most popular categories, seasonal trends, and geographic distribution';
      case 'engagement-metrics': return 'Time spent, pages viewed, and search behavior analysis';
      case 'user-retention': return 'Active users, churn rates, and engagement over time';
      default: return 'Detailed analytics report';
    }
  };

  // Render the component
  return (
    <div className="max-w-7xl mx-auto p-6">
      <div className="mb-8">
        <h1 className="text-3xl font-bold text-gray-900">Detailed Reports Dashboard</h1>
        <p className="text-gray-600">Comprehensive analytics for your business operations</p>
      </div>

      {error && (
        <div className="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
          <strong className="font-bold">Error: </strong>
          <span className="block sm:inline">{error}</span>
        </div>
      )}

      {/* Main Tab Navigation */}
      <div className="border-b border-gray-200 mb-6">
        <nav className="-mb-px flex flex-wrap">
          <button
            onClick={() => {
              setActiveTab('customer-experience');
              setReportType('order-fulfillment');
            }}
            className={`whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm ${
              activeTab === 'customer-experience'
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            }`}
          >
            Customer Experience
          </button>
          <button
            onClick={() => {
              setActiveTab('financial');
              setReportType('cost-goods');
            }}
            className={`whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm ${
              activeTab === 'financial'
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            }`}
          >
            Financial Reports
          </button>
          <button
            onClick={() => {
              setActiveTab('classified');
              setReportType('user-registration');
            }}
            className={`whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm ${
              activeTab === 'classified'
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            }`}
          >
            Classified App
          </button>
        </nav>
      </div>

      {/* Report Type Selection based on active tab */}
      <div className="mb-6 bg-white rounded-lg shadow-md p-4">
        <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Report Type</label>
            <select
              value={reportType}
              onChange={(e) => setReportType(e.target.value)}
              className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              {activeTab === 'customer-experience' && (
                <>
                  <option value="order-fulfillment">Order Fulfillment</option>
                  <option value="delivery-metrics">Delivery Metrics</option>
                  <option value="customer-feedback">Customer Feedback</option>
                  <option value="user-engagement">User Engagement</option>
                  <option value="loyalty-program">Loyalty Program</option>
                </>
              )}
              {activeTab === 'financial' && (
                <>
                  <option value="cost-goods">Cost of Goods Sold</option>
                  <option value="profit-margins">Profit Margins</option>
                  <option value="commission">Commission Reports</option>
                  <option value="waste-loss">Waste & Loss</option>
                  <option value="break-even">Break-even Analysis</option>
                </>
              )}
              {activeTab === 'classified' && (
                <>
                  <option value="user-registration">User Registration</option>
                  <option value="listing-performance">Listing Performance</option>
                  <option value="category-analysis">Category Analysis</option>
                  <option value="engagement-metrics">Engagement Metrics</option>
                  <option value="user-retention">User Retention</option>
                </>
              )}
            </select>
          </div>
          
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
            <input
              type="date"
              value={dateRange.start}
              onChange={(e) => setDateRange({...dateRange, start: e.target.value})}
              className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>
          
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">End Date</label>
            <input
              type="date"
              value={dateRange.end}
              onChange={(e) => setDateRange({...dateRange, end: e.target.value})}
              className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>
          
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Location</label>
            <select
              value={filters.location || ''}
              onChange={(e) => setFilters({...filters, location: e.target.value})}
              className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <option value="">All Locations</option>
              <option value="main">Main Entrance</option>
              <option value="floor2">Floor 2</option>
              <option value="cafeteria">Cafeteria Area</option>
              <option value="parking">Parking Level B1</option>
            </select>
          </div>
        </div>
      </div>

      {/* Report Content */}
      <div className="bg-white rounded-lg shadow-md p-6">
        <div className="mb-6">
          <h2 className="text-2xl font-bold text-gray-900">{getReportTitle()}</h2>
          <p className="text-gray-600">{getReportDescription()}</p>
        </div>

        {loading ? (
          <div className="flex justify-center items-center h-64">
            <div className="animate-spin rounded-full h-32 w-32 border-b-2 border-blue-600"></div>
          </div>
        ) : (
          <>
            {renderChart()}
            
            {/* Summary Stats */}
            {reportData && Array.isArray(reportData) && reportData.length > 0 && (
              <div className="mt-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div className="bg-blue-50 rounded-lg p-4">
                  <h3 className="font-medium text-blue-900">Total Records</h3>
                  <p className="text-2xl font-bold text-blue-600">{reportData.length}</p>
                </div>
                {reportData[0]?.value && (
                  <div className="bg-green-50 rounded-lg p-4">
                    <h3 className="font-medium text-green-900">Total Value</h3>
                    <p className="text-2xl font-bold text-green-600">
                      ${reportData.reduce((sum, item) => sum + (item.value || 0), 0).toFixed(2)}
                    </p>
                  </div>
                )}
                <div className="bg-purple-50 rounded-lg p-4">
                  <h3 className="font-medium text-purple-900">Report Period</h3>
                  <p className="text-lg font-bold text-purple-600">
                    {dateRange.start ? new Date(dateRange.start).toLocaleDateString() : 'N/A'} 
                    {dateRange.end && ` - ${new Date(dateRange.end).toLocaleDateString()}`}
                  </p>
                </div>
                <div className="bg-yellow-50 rounded-lg p-4">
                  <h3 className="font-medium text-yellow-900">Filter</h3>
                  <p className="text-lg font-bold text-yellow-600">
                    {filters.location || 'All Locations'}
                  </p>
                </div>
              </div>
            )}
          </>
        )}
      </div>
    </div>
  );
};

export default withAuth(DetailedReportsDashboard, ['admin', 'seller', 'store_owner']);