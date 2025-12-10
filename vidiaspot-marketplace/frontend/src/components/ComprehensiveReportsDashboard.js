import React, { useState, useEffect } from 'react';
import { withAuth } from '../utils/withAuth';
import reportService from '../services/reportService';
import { BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, Legend, ResponsiveContainer, PieChart, Pie, Cell, LineChart, Line, AreaChart, Area } from 'recharts';

const ComprehensiveReportsDashboard = () => {
  const [activeTab, setActiveTab] = useState('revenue');
  const [reportType, setReportType] = useState('subscription-analytics');
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
        // Revenue & Financial Reports
        case 'subscription-analytics':
          data = await reportService.getSubscriptionAnalytics(params);
          break;
        case 'premium-service':
          data = await reportService.getPremiumServiceUsage(params);
          break;
        case 'commission-category':
          data = await reportService.getCommissionByCategory(params);
          break;
        case 'payment-processing':
          data = await reportService.getPaymentProcessing(params);
          break;
        case 'cost-acquisition':
          data = await reportService.getCostPerAcquisition(params);
          break;
          
        // Content & Quality Reports
        case 'content-moderation':
          data = await reportService.getContentModeration(params);
          break;
        case 'listing-quality':
          data = await reportService.getListingQuality(params);
          break;
        case 'fraud-detection':
          data = await reportService.getFraudDetection(params);
          break;
        case 'search-analytics':
          data = await reportService.getSearchAnalytics(params);
          break;
        case 'ugc':
          data = await reportService.getUserGeneratedContent(params);
          break;
          
        // Market Intelligence Reports
        case 'price-analytics':
          data = await reportService.getPriceAnalytics(params);
          break;
        case 'demand-forecast':
          data = await reportService.getDemandForecast(params);
          break;
        case 'geographic-performance':
          data = await reportService.getGeographicPerformance(params);
          break;
        case 'seasonal-trends':
          data = await reportService.getSeasonalTrends(params);
          break;
        case 'market-saturation':
          data = await reportService.getMarketSaturation(params);
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
    const isPieChart = ['subscription-analytics', 'listing-quality', 'fraud-detection'].includes(reportType);
    const isLineChart = ['payment-processing', 'search-analytics', 'seasonal-trends'].includes(reportType);
    const isAreaChart = ['premium-service'].includes(reportType);
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
              <linearGradient id="colorFeatured" x1="0" y1="0" x2="0" y2="1">
                <stop offset="5%" stopColor="#8884d8" stopOpacity={0.8}/>
                <stop offset="95%" stopColor="#8884d8" stopOpacity={0}/>
              </linearGradient>
              <linearGradient id="colorBoost" x1="0" y1="0" x2="0" y2="1">
                <stop offset="5%" stopColor="#00C49F" stopOpacity={0.8}/>
                <stop offset="95%" stopColor="#00C49F" stopOpacity={0}/>
              </linearGradient>
            </defs>
            <XAxis dataKey="name" />
            <YAxis />
            <CartesianGrid strokeDasharray="3 3" />
            <Tooltip />
            <Area type="monotone" dataKey="featured" stackId="1" stroke="#8884d8" fillOpacity={1} fill="url(#colorFeatured)" name="Featured" />
            <Area type="monotone" dataKey="boost" stackId="2" stroke="#00C49F" fillOpacity={1} fill="url(#colorBoost)" name="Boost" />
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
      case 'subscription-analytics': return 'Subscription Analytics';
      case 'premium-service': return 'Premium Service Usage';
      case 'commission-category': return 'Commission Reports by Category';
      case 'payment-processing': return 'Payment Processing Analytics';
      case 'cost-acquisition': return 'Cost Per Acquisition';
      case 'content-moderation': return 'Content Moderation Analytics';
      case 'listing-quality': return 'Listing Quality Metrics';
      case 'fraud-detection': return 'Fraud Detection Reports';
      case 'search-analytics': return 'Search Analytics';
      case 'ugc': return 'User-Generated Content Metrics';
      case 'price-analytics': return 'Price Analytics';
      case 'demand-forecast': return 'Demand Forecasting';
      case 'geographic-performance': return 'Geographic Performance';
      case 'seasonal-trends': return 'Seasonal Trends Analysis';
      case 'market-saturation': return 'Market Saturation Analysis';
      default: return 'Comprehensive Reports Dashboard';
    }
  };

  // Get report description based on report type
  const getReportDescription = () => {
    switch (reportType) {
      case 'subscription-analytics': return 'Plan performance, renewal rates, and upgrade/downgrade patterns';
      case 'premium-service': return 'Featured listings, boost performance, and additional services usage';
      case 'commission-category': return 'Fees by category, user tier, and geographic region';
      case 'payment-processing': return 'Success/failure rates, refund requests, and chargebacks';
      case 'cost-acquisition': return 'Marketing spend versus user acquisition metrics';
      case 'content-moderation': return 'Flagged items, policy violations, and actions taken';
      case 'listing-quality': return 'Photo quality, description completeness, and accuracy';
      case 'fraud-detection': return 'Suspicious listings, fake accounts, and scam reports';
      case 'search-analytics': return 'Popular search terms and search results performance';
      case 'ugc': return 'Reviews, ratings, and user interaction metrics';
      case 'price-analytics': return 'Market pricing trends, competitor analysis, and pricing recommendations';
      case 'demand-forecast': return 'Predicted demand by category and location';
      case 'geographic-performance': return 'Regional market performance and expansion opportunities';
      case 'seasonal-trends': return 'Cyclical demand patterns and planning insights';
      case 'market-saturation': return 'Competition levels and opportunity analysis';
      default: return 'Comprehensive analytics report';
    }
  };

  // Render the component
  return (
    <div className="max-w-7xl mx-auto p-6">
      <div className="mb-8">
        <h1 className="text-3xl font-bold text-gray-900">Comprehensive Reports Dashboard</h1>
        <p className="text-gray-600">Complete analytics for your business operations</p>
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
              setActiveTab('revenue');
              setReportType('subscription-analytics');
            }}
            className={`whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm ${
              activeTab === 'revenue'
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            }`}
          >
            Revenue & Financial
          </button>
          <button
            onClick={() => {
              setActiveTab('content');
              setReportType('content-moderation');
            }}
            className={`whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm ${
              activeTab === 'content'
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            }`}
          >
            Content & Quality
          </button>
          <button
            onClick={() => {
              setActiveTab('intelligence');
              setReportType('price-analytics');
            }}
            className={`whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm ${
              activeTab === 'intelligence'
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            }`}
          >
            Market Intelligence
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
              {activeTab === 'revenue' && (
                <>
                  <option value="subscription-analytics">Subscription Analytics</option>
                  <option value="premium-service">Premium Service Usage</option>
                  <option value="commission-category">Commission by Category</option>
                  <option value="payment-processing">Payment Processing</option>
                  <option value="cost-acquisition">Cost Per Acquisition</option>
                </>
              )}
              {activeTab === 'content' && (
                <>
                  <option value="content-moderation">Content Moderation</option>
                  <option value="listing-quality">Listing Quality</option>
                  <option value="fraud-detection">Fraud Detection</option>
                  <option value="search-analytics">Search Analytics</option>
                  <option value="ugc">User-Generated Content</option>
                </>
              )}
              {activeTab === 'intelligence' && (
                <>
                  <option value="price-analytics">Price Analytics</option>
                  <option value="demand-forecast">Demand Forecasting</option>
                  <option value="geographic-performance">Geographic Performance</option>
                  <option value="seasonal-trends">Seasonal Trends</option>
                  <option value="market-saturation">Market Saturation</option>
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
            <label className="block text-sm font-medium text-gray-700 mb-1">Filter</label>
            <select
              value={filters.filter || ''}
              onChange={(e) => setFilters({...filters, filter: e.target.value})}
              className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <option value="">All</option>
              <option value="daily">Daily</option>
              <option value="weekly">Weekly</option>
              <option value="monthly">Monthly</option>
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
                    {filters.filter || 'All Data'}
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

export default withAuth(ComprehensiveReportsDashboard, ['admin', 'seller', 'store_owner']);