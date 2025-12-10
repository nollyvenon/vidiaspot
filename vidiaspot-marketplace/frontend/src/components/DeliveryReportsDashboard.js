import React, { useState, useEffect } from 'react';
import { withAuth } from '../utils/withAuth';
import deliveryService from '../services/deliveryService';
import { BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, Legend, ResponsiveContainer, PieChart, Pie, Cell, LineChart, Line } from 'recharts';

const DeliveryReportsDashboard = () => {
  const [activeTab, setActiveTab] = useState('delivery-performance');
  const [reportType, setReportType] = useState('on-time-delivery');
  const [reportData, setReportData] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [dateRange, setDateRange] = useState({ start: '', end: '' });
  const [filters, setFilters] = useState({ client: '', route: '', driver: '' });

  const COLORS = ['#0088FE', '#00C49F', '#FFBB28', '#FF8042', '#8884D8', '#82CA9D', '#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4'];

  useEffect(() => {
    fetchReportData();
  }, [activeTab, reportType, dateRange, filters]);

  const fetchReportData = async () => {
    if (!reportType) return;
    
    setLoading(true);
    setError(null);

    try {
      const params = { ...dateRange, ...filters };

      let data = [];
      switch (reportType) {
        // Delivery Performance Reports
        case 'on-time-delivery':
          data = await deliveryService.getOnTimeDeliveryReports(params);
          break;
        case 'average-delivery-times':
          data = await deliveryService.getAverageDeliveryTimes(params);
          break;
        case 'failed-delivery-reports':
          data = await deliveryService.getFailedDeliveryReports(params);
          break;
        case 'package-handling-reports':
          data = await deliveryService.getPackageHandlingReports(params);
          break;
        case 'customer-satisfaction-reports':
          data = await deliveryService.getCustomerSatisfactionReports(params);
          break;

        // Financial Reports
        case 'revenue-reports':
          data = await deliveryService.getRevenueReports(params);
          break;
        case 'cost-per-delivery':
          data = await deliveryService.getCostPerDeliveryReports(params);
          break;
        case 'profit-margin':
          data = await deliveryService.getProfitMarginReports(params);
          break;
        case 'outstanding-invoices':
          data = await deliveryService.getOutstandingInvoiceReports(params);
          break;
        case 'expense-breakdown':
          data = await deliveryService.getExpenseBreakdownReports(params);
          break;
        case 'revenue-forecast':
          data = await deliveryService.getRevenueForecastReports(params);
          break;

        // Route Efficiency Reports
        case 'distance-vs-optimal':
          data = await deliveryService.getDistanceVsOptimalReports(params);
          break;
        case 'fuel-consumption':
          data = await deliveryService.getFuelConsumptionReports(params);
          break;
        case 'driver-performance':
          data = await deliveryService.getDriverPerformanceReports(params);
          break;
        case 'route-profitability':
          data = await deliveryService.getRouteProfitabilityReports(params);
          break;
        case 'time-utilization':
          data = await deliveryService.getTimeUtilizationReports(params);
          break;
        case 'traffic-patterns':
          data = await deliveryService.getTrafficPatternReports(params);
          break;

        // Client Management Reports
        case 'client-activity-trends':
          data = await deliveryService.getClientActivityTrends(params);
          break;
        case 'sla-compliance':
          data = await deliveryService.getSLAComplianceReports(params);
          break;
        case 'client-retention':
          data = await deliveryService.getClientRetentionReports(params);
          break;
        case 'revenue-per-client':
          data = await deliveryService.getRevenuePerClientReports(params);
          break;
        case 'delivery-frequency':
          data = await deliveryService.getDeliveryFrequencyReports(params);
          break;
        case 'client-performance-scorecards':
          data = await deliveryService.getClientPerformanceScorecards(params);
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

  const renderChart = () => {
    if (!reportData || !Array.isArray(reportData) || reportData.length === 0) {
      return <div className="text-center py-10 text-gray-500">No data available for the selected period</div>;
    }

    const isPieChart = ['on-time-delivery', 'sla-compliance', 'expense-breakdown'].includes(reportType);
    const isLineChart = ['average-delivery-times', 'revenue-forecast', 'client-activity-trends', 'traffic-patterns'].includes(reportType);
    const isBarChart = !isPieChart && !isLineChart;

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

  const getReportTitle = () => {
    switch (reportType) {
      case 'on-time-delivery': return 'On-Time Delivery Percentage';
      case 'average-delivery-times': return 'Average Delivery Times';
      case 'failed-delivery-reports': return 'Failed Delivery Analysis';
      case 'package-handling-reports': return 'Package Handling Accuracy';
      case 'customer-satisfaction-reports': return 'Customer Satisfaction Scores';
      case 'revenue-reports': return 'Revenue by Route, Client & Time Period';
      case 'cost-per-delivery': return 'Cost Per Delivery Analysis';
      case 'profit-margin': return 'Profit Margins by Service Type';
      case 'outstanding-invoices': return 'Outstanding Invoices & Payment Aging';
      case 'expense-breakdown': return 'Expense Breakdown by Category';
      case 'revenue-forecast': return 'Revenue Forecasting';
      case 'distance-vs-optimal': return 'Distance Traveled vs. Optimal Routes';
      case 'fuel-consumption': return 'Fuel Consumption Analysis';
      case 'driver-performance': return 'Driver Performance Metrics';
      case 'route-profitability': return 'Route Profitability Analysis';
      case 'time-utilization': return 'Time Utilization Reports';
      case 'traffic-patterns': return 'Traffic Pattern Analysis';
      case 'client-activity-trends': return 'Client Activity Volume Trends';
      case 'sla-compliance': return 'SLA Compliance Tracking';
      case 'client-retention': return 'Client Retention Analysis';
      case 'revenue-per-client': return 'Revenue per Client & LTV';
      case 'delivery-frequency': return 'Delivery Frequency & Volume Forecasting';
      case 'client-performance-scorecards': return 'Client Performance Scorecards';
      default: return 'Delivery Reports Dashboard';
    }
  };

  const getReportDescription = () => {
    switch (reportType) {
      case 'on-time-delivery': return 'On-time delivery percentage by client, route, and driver';
      case 'average-delivery-times': return 'Average delivery times and performance trends over time';
      case 'failed-delivery-reports': return 'Failed delivery reasons and resolution tracking';
      case 'package-handling-reports': return 'Package handling accuracy and damage reports';
      case 'customer-satisfaction-reports': return 'Customer satisfaction scores and feedback analysis';
      case 'revenue-reports': return 'Revenue per route, client, and time period';
      case 'cost-per-delivery': return 'Cost per delivery analysis (fuel, labor, vehicle maintenance)';
      case 'profit-margin': return 'Profit margins by service type and client segment';
      case 'outstanding-invoices': return 'Outstanding invoices and payment aging reports';
      case 'expense-breakdown': return 'Expense breakdown by category and vehicle fleet';
      case 'revenue-forecast': return 'Revenue forecasting based on historical data';
      case 'distance-vs-optimal': return 'Distance traveled vs. optimal route calculations';
      case 'fuel-consumption': return 'Fuel consumption analysis per route and vehicle';
      case 'driver-performance': return 'Driver performance metrics (speed, idle time, stops)';
      case 'route-profitability': return 'Route profitability analysis showing most/least efficient areas';
      case 'time-utilization': return 'Time utilization reports showing productive vs. idle hours';
      case 'traffic-patterns': return 'Traffic pattern analysis and peak hour optimization data';
      case 'client-activity-trends': return 'Client activity volume trends and seasonal patterns';
      case 'sla-compliance': return 'Service level agreement compliance tracking';
      case 'client-retention': return 'Client retention and growth analysis';
      case 'revenue-per-client': return 'Revenue per client and lifetime value calculations';
      case 'delivery-frequency': return 'Delivery frequency and volume forecasting';
      case 'client-performance-scorecards': return 'Client-specific performance scorecards';
      default: return 'Delivery and logistics analytics report';
    }
  };

  return (
    <div className="max-w-7xl mx-auto p-6">
      <div className="mb-8">
        <h1 className="text-3xl font-bold text-gray-900">Delivery & Logistics Reports Dashboard</h1>
        <p className="text-gray-600">Comprehensive analytics for your logistics operations</p>
      </div>

      {error && (
        <div className="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
          <strong className="font-bold">Error: </strong>
          <span className="block sm:inline">{error}</span>
        </div>
      )}

      {/* Tab Navigation */}
      <div className="border-b border-gray-200 mb-6">
        <nav className="-mb-px flex flex-wrap">
          <button
            onClick={() => {
              setActiveTab('delivery-performance');
              setReportType('on-time-delivery');
            }}
            className={`whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm ${
              activeTab === 'delivery-performance'
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            }`}
          >
            Delivery Performance
          </button>
          <button
            onClick={() => {
              setActiveTab('financial');
              setReportType('revenue-reports');
            }}
            className={`whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm ${
              activeTab === 'financial'
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            }`}
          >
            Financial
          </button>
          <button
            onClick={() => {
              setActiveTab('route-efficiency');
              setReportType('distance-vs-optimal');
            }}
            className={`whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm ${
              activeTab === 'route-efficiency'
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            }`}
          >
            Route Efficiency
          </button>
          <button
            onClick={() => {
              setActiveTab('client-management');
              setReportType('client-activity-trends');
            }}
            className={`whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm ${
              activeTab === 'client-management'
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            }`}
          >
            Client Management
          </button>
        </nav>
      </div>

      {/* Report Type Selection */}
      <div className="mb-6 bg-white rounded-lg shadow-md p-4">
        <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Report Type</label>
            <select
              value={reportType}
              onChange={(e) => setReportType(e.target.value)}
              className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              {activeTab === 'delivery-performance' && (
                <>
                  <option value="on-time-delivery">On-Time Delivery Percentage</option>
                  <option value="average-delivery-times">Average Delivery Times</option>
                  <option value="failed-delivery-reports">Failed Delivery Analysis</option>
                  <option value="package-handling-reports">Package Handling Accuracy</option>
                  <option value="customer-satisfaction-reports">Customer Satisfaction Scores</option>
                </>
              )}
              {activeTab === 'financial' && (
                <>
                  <option value="revenue-reports">Revenue by Route & Client</option>
                  <option value="cost-per-delivery">Cost Per Delivery Analysis</option>
                  <option value="profit-margin">Profit Margins</option>
                  <option value="outstanding-invoices">Outstanding Invoices</option>
                  <option value="expense-breakdown">Expense Breakdown</option>
                  <option value="revenue-forecast">Revenue Forecasting</option>
                </>
              )}
              {activeTab === 'route-efficiency' && (
                <>
                  <option value="distance-vs-optimal">Distance vs. Optimal Routes</option>
                  <option value="fuel-consumption">Fuel Consumption Analysis</option>
                  <option value="driver-performance">Driver Performance Metrics</option>
                  <option value="route-profitability">Route Profitability</option>
                  <option value="time-utilization">Time Utilization</option>
                  <option value="traffic-patterns">Traffic Pattern Analysis</option>
                </>
              )}
              {activeTab === 'client-management' && (
                <>
                  <option value="client-activity-trends">Client Activity Trends</option>
                  <option value="sla-compliance">SLA Compliance</option>
                  <option value="client-retention">Client Retention Analysis</option>
                  <option value="revenue-per-client">Revenue per Client</option>
                  <option value="delivery-frequency">Delivery Frequency</option>
                  <option value="client-performance-scorecards">Client Scorecards</option>
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
              value={filters.client || ''}
              onChange={(e) => setFilters({...filters, client: e.target.value})}
              className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <option value="">All Clients</option>
              <option value="client1">Client 1</option>
              <option value="client2">Client 2</option>
              <option value="client3">Client 3</option>
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
                    {filters.client || 'All Data'}
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

export default withAuth(DeliveryReportsDashboard, ['admin', 'logistics_manager', 'delivery_dispatcher']);