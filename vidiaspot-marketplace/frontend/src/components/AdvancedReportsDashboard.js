import React, { useState, useEffect } from 'react';
import { withAuth } from '../utils/withAuth';
import reportService from '../services/reportService';
import { BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, Legend, ResponsiveContainer, PieChart, Pie, Cell, LineChart, Line, AreaChart, Area, RadarChart, Radar, PolarGrid, PolarAngleAxis, PolarRadiusAxis } from 'recharts';

const AdvancedReportsDashboard = () => {
  const [activeTab, setActiveTab] = useState('sales');
  const [reportType, setReportType] = useState('sales-dashboard');
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
        // Sales Performance Reports
        case 'sales-dashboard':
          data = await reportService.getSalesDashboard(params);
          break;
        case 'product-performance':
          data = await reportService.getProductPerformance(params);
          break;
        case 'customer-lifetime-value':
          data = await reportService.getCustomerLifetimeValue(params);
          break;
        case 'seasonal-sales-analysis':
          data = await reportService.getSeasonalSalesAnalysis(params);
          break;
        case 'sales-channel-performance':
          data = await reportService.getSalesChannelPerformance(params);
          break;
          
        // Inventory Management Reports
        case 'stock-level-reports':
          data = await reportService.getStockLevelReports(params);
          break;
        case 'inventory-turnover':
          data = await reportService.getInventoryTurnover(params);
          break;
        case 'supplier-performance':
          data = await reportService.getSupplierPerformance(params);
          break;
        case 'dead-stock-analysis':
          data = await reportService.getDeadStockAnalysis(params);
          break;
        case 'demand-forecasting':
          data = await reportService.getInventoryDemandForecast(params);
          break;
          
        // Marketing & Customer Reports
        case 'conversion-funnel':
          data = await reportService.getConversionFunnel(params);
          break;
        case 'marketing-roi':
          data = await reportService.getMarketingROI(params);
          break;
        case 'customer-segmentation':
          data = await reportService.getCustomerSegmentation(params);
          break;
        case 'email-marketing':
          data = await reportService.getEmailMarketing(params);
          break;
        case 'seo-performance':
          data = await reportService.getSEOPerformance(params);
          break;
          
        // Financial & Operational Reports
        case 'profit-margins':
          data = await reportService.getProfitMargins(params);
          break;
        case 'shipping-analytics':
          data = await reportService.getShippingAnalytics(params);
          break;
        case 'return-refund-analysis':
          data = await reportService.getReturnRefundAnalysis(params);
          break;
        case 'tax-compliance':
          data = await reportService.getTaxCompliance(params);
          break;
        case 'payment-processing':
          data = await reportService.getPaymentProcessing(params);
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
    const isPieChart = ['sales-channel-performance', 'customer-segmentation', 'email-marketing'].includes(reportType);
    const isLineChart = ['sales-dashboard', 'seo-performance', 'seasonal-sales-analysis'].includes(reportType);
    const isAreaChart = ['customer-lifetime-value'].includes(reportType);
    const isRadarChart = ['supplier-performance'].includes(reportType);
    const isBarChart = !isPieChart && !isLineChart && !isAreaChart && !isRadarChart;

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
              <linearGradient id="colorCLV" x1="0" y1="0" x2="0" y2="1">
                <stop offset="5%" stopColor="#8884d8" stopOpacity={0.8}/>
                <stop offset="95%" stopColor="#8884d8" stopOpacity={0}/>
              </linearGradient>
            </defs>
            <XAxis dataKey="name" />
            <YAxis />
            <CartesianGrid strokeDasharray="3 3" />
            <Tooltip />
            <Area type="monotone" dataKey="clv" stackId="1" stroke="#8884d8" fillOpacity={1} fill="url(#colorCLV)" name="Customer Lifetime Value" />
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
    } else if (isRadarChart) {
      return (
        <ResponsiveContainer width="100%" height={400}>
          <RadarChart cx="50%" cy="50%" outerRadius="80%" data={reportData}>
            <PolarGrid />
            <PolarAngleAxis dataKey="subject" />
            <PolarRadiusAxis />
            <Radar name="Performance" dataKey="performance" stroke="#8884d8" fill="#8884d8" fillOpacity={0.6} />
          </RadarChart>
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
      case 'sales-dashboard': return 'Sales Dashboard';
      case 'product-performance': return 'Product Performance';
      case 'customer-lifetime-value': return 'Customer Lifetime Value';
      case 'seasonal-sales-analysis': return 'Seasonal Sales Analysis';
      case 'sales-channel-performance': return 'Sales Channel Performance';
      case 'stock-level-reports': return 'Stock Level Reports';
      case 'inventory-turnover': return 'Inventory Turnover';
      case 'supplier-performance': return 'Supplier Performance';
      case 'dead-stock-analysis': return 'Dead Stock Analysis';
      case 'demand-forecasting': return 'Demand Forecasting';
      case 'conversion-funnel': return 'Conversion Funnel';
      case 'marketing-roi': return 'Marketing ROI';
      case 'customer-segmentation': return 'Customer Segmentation';
      case 'email-marketing': return 'Email Marketing';
      case 'seo-performance': return 'SEO Performance';
      case 'profit-margins': return 'Profit Margins';
      case 'shipping-analytics': return 'Shipping Analytics';
      case 'return-refund-analysis': return 'Return & Refund Analysis';
      case 'tax-compliance': return 'Tax Compliance';
      case 'payment-processing': return 'Payment Processing';
      default: return 'Advanced Reports Dashboard';
    }
  };

  // Get report description based on report type
  const getReportDescription = () => {
    switch (reportType) {
      case 'sales-dashboard': return 'Daily/monthly sales, revenue trends, and growth metrics';
      case 'product-performance': return 'Best sellers, slow movers, and product profitability';
      case 'customer-lifetime-value': return 'CLV analysis, cohort retention, and customer segments';
      case 'seasonal-sales-analysis': return 'Holiday performance, seasonal trends, and planning insights';
      case 'sales-channel-performance': return 'Web, mobile, social commerce, and marketplace sales';
      case 'stock-level-reports': return 'Current inventory, reorder points, and safety stock';
      case 'inventory-turnover': return 'Fast/slow moving items, carrying costs, and optimization';
      case 'supplier-performance': return 'Lead times, quality metrics, and cost analysis';
      case 'dead-stock-analysis': return 'Slow-moving inventory and liquidation recommendations';
      case 'demand-forecasting': return 'Predictive analytics for inventory planning';
      case 'conversion-funnel': return 'Abandonment rates and optimization opportunities';
      case 'marketing-roi': return 'Campaign performance, channel attribution, and cost per acquisition';
      case 'customer-segmentation': return 'Demographics, behavior, and purchase history analysis';
      case 'email-marketing': return 'Open rates, click-through rates, and conversion tracking';
      case 'seo-performance': return 'Organic traffic, keyword rankings, and search visibility';
      case 'profit-margins': return 'Profit margins by product, category, and customer segment';
      case 'shipping-analytics': return 'Cost analysis, carrier performance, and delivery times';
      case 'return-refund-analysis': return 'Return rates, reasons for returns, and financial impact';
      case 'tax-compliance': return 'Sales tax reports and jurisdictional compliance';
      case 'payment-processing': return 'Gateway performance, transaction fees, and success rates';
      default: return 'Advanced analytics report';
    }
  };

  // Render the component
  return (
    <div className="max-w-7xl mx-auto p-6">
      <div className="mb-8">
        <h1 className="text-3xl font-bold text-gray-900">Advanced Reports Dashboard</h1>
        <p className="text-gray-600">Advanced analytics for your business operations</p>
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
              setActiveTab('sales');
              setReportType('sales-dashboard');
            }}
            className={`whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm ${
              activeTab === 'sales'
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            }`}
          >
            Sales Performance
          </button>
          <button
            onClick={() => {
              setActiveTab('inventory');
              setReportType('stock-level-reports');
            }}
            className={`whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm ${
              activeTab === 'inventory'
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            }`}
          >
            Inventory Mgmt
          </button>
          <button
            onClick={() => {
              setActiveTab('marketing');
              setReportType('conversion-funnel');
            }}
            className={`whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm ${
              activeTab === 'marketing'
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            }`}
          >
            Marketing & Customer
          </button>
          <button
            onClick={() => {
              setActiveTab('financial');
              setReportType('profit-margins');
            }}
            className={`whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm ${
              activeTab === 'financial'
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            }`}
          >
            Financial & Operational
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
              {activeTab === 'sales' && (
                <>
                  <option value="sales-dashboard">Sales Dashboard</option>
                  <option value="product-performance">Product Performance</option>
                  <option value="customer-lifetime-value">Customer Lifetime Value</option>
                  <option value="seasonal-sales-analysis">Seasonal Sales Analysis</option>
                  <option value="sales-channel-performance">Sales Channel Performance</option>
                </>
              )}
              {activeTab === 'inventory' && (
                <>
                  <option value="stock-level-reports">Stock Level Reports</option>
                  <option value="inventory-turnover">Inventory Turnover</option>
                  <option value="supplier-performance">Supplier Performance</option>
                  <option value="dead-stock-analysis">Dead Stock Analysis</option>
                  <option value="demand-forecasting">Demand Forecasting</option>
                </>
              )}
              {activeTab === 'marketing' && (
                <>
                  <option value="conversion-funnel">Conversion Funnel</option>
                  <option value="marketing-roi">Marketing ROI</option>
                  <option value="customer-segmentation">Customer Segmentation</option>
                  <option value="email-marketing">Email Marketing</option>
                  <option value="seo-performance">SEO Performance</option>
                </>
              )}
              {activeTab === 'financial' && (
                <>
                  <option value="profit-margins">Profit Margins</option>
                  <option value="shipping-analytics">Shipping Analytics</option>
                  <option value="return-refund-analysis">Return & Refund Analysis</option>
                  <option value="tax-compliance">Tax Compliance</option>
                  <option value="payment-processing">Payment Processing</option>
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
              <option value="category">By Category</option>
              <option value="vendor">By Vendor</option>
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

export default withAuth(AdvancedReportsDashboard, ['admin', 'seller', 'store_owner']);