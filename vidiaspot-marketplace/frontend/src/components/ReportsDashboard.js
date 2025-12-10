import React, { useState, useEffect } from 'react';
import { withAuth } from '../utils/withAuth';
import reportService from '../services/reportService';
import { BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, Legend, ResponsiveContainer, PieChart, Pie, Cell, LineChart, Line } from 'recharts';

const ReportsDashboard = () => {
  const [activeTab, setActiveTab] = useState('sales');
  const [reportData, setReportData] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [dateRange, setDateRange] = useState({ start: '', end: '' });
  const [locationFilter, setLocationFilter] = useState('');
  const [vendorFilter, setVendorFilter] = useState('');

  const COLORS = ['#0088FE', '#00C49F', '#FFBB28', '#FF8042', '#8884D8', '#82CA9D'];

  useEffect(() => {
    fetchReportData();
  }, [activeTab, dateRange, locationFilter, vendorFilter]);

  const fetchReportData = async () => {
    try {
      setLoading(true);
      setError(null);

      let params = {};
      if (dateRange.start) params.start_date = dateRange.start;
      if (dateRange.end) params.end_date = dateRange.end;
      if (locationFilter) params.location = locationFilter;
      if (vendorFilter) params.vendor = vendorFilter;

      let data;
      switch (activeTab) {
        case 'sales':
          data = await reportService.getDailySales(params);
          break;
        case 'menu':
          data = await reportService.getMenuPerformance(params);
          break;
        case 'delivery':
          data = await reportService.getDeliveryPerformance(params);
          break;
        case 'spending':
          data = await reportService.getCustomerSpending(params);
          break;
        case 'payment':
          data = await reportService.getPaymentMethodRevenue(params);
          break;
        case 'kitchen':
          data = await reportService.getKitchenPerformance(params);
          break;
        case 'driver':
          data = await reportService.getDriverPerformance(params);
          break;
        case 'inventory':
          data = await reportService.getInventoryManagement(params);
          break;
        case 'location':
          data = await reportService.getLocationAnalytics(params);
          break;
        case 'equipment':
          data = await reportService.getEquipmentUtilization(params);
          break;
        default:
          data = await reportService.getDailySales(params);
      }

      setReportData(data);
    } catch (err) {
      setError('Failed to fetch report data. Please try again.');
      console.error('Error fetching report data:', err);
    } finally {
      setLoading(false);
    }
  };

  const renderChart = () => {
    if (!reportData || !reportData.length) {
      return <div className="text-center py-10 text-gray-500">No data available for the selected period</div>;
    }

    // Determine chart type based on active tab
    const pieTabs = ['payment', 'spending'];
    const lineTabs = ['delivery', 'order', 'metrics'];
    const barTabs = ['sales', 'menu', 'kitchen', 'driver', 'inventory', 'location', 'equipment'];

    if (pieTabs.includes(activeTab)) {
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
    } else if (lineTabs.includes(activeTab)) {
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
            <Line type="monotone" dataKey="value" stroke="#8884d8" name="Value" />
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
            <Bar dataKey="value" fill="#8884d8" name="Value" />
          </BarChart>
        </ResponsiveContainer>
      );
    }
  };

  const getReportTitle = () => {
    switch (activeTab) {
      case 'sales': return 'Daily Sales Summary';
      case 'menu': return 'Menu Performance';
      case 'delivery': return 'Delivery Performance';
      case 'spending': return 'Customer Spending Patterns';
      case 'payment': return 'Revenue by Payment Method';
      case 'kitchen': return 'Kitchen Performance';
      case 'driver': return 'Driver Performance';
      case 'inventory': return 'Inventory Management';
      case 'location': return 'Location Analytics';
      case 'equipment': return 'Equipment Utilization';
      default: return 'Report';
    }
  };

  const getReportDescription = () => {
    switch (activeTab) {
      case 'sales': return 'Revenue by hour, day, location, and vendor';
      case 'menu': return 'Best and worst selling items, seasonal trends';
      case 'delivery': return 'Delivery times, success rates, failure analysis';
      case 'spending': return 'Average order value, frequency, customer loyalty';
      case 'payment': return 'Cash, card, digital wallets, cryptocurrency breakdown';
      case 'kitchen': return 'Order preparation times, kitchen capacity utilization';
      case 'driver': return 'Delivery times, customer ratings, driver earnings';
      case 'inventory': return 'Stock levels, waste reports, reorder alerts';
      case 'location': return 'High-performing locations, site profitability';
      case 'equipment': return 'Vending machine usage, maintenance schedules';
      default: return '';
    }
  };

  if (loading && !reportData) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <div className="animate-spin rounded-full h-32 w-32 border-b-2 border-blue-600"></div>
      </div>
    );
  }

  return (
    <div className="max-w-7xl mx-auto p-6">
      <div className="mb-8">
        <h1 className="text-3xl font-bold text-gray-900">Reports & Analytics Dashboard</h1>
        <p className="text-gray-600">Comprehensive reporting for your business operations</p>
      </div>

      {error && (
        <div className="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
          <strong className="font-bold">Error: </strong>
          <span className="block sm:inline">{error}</span>
        </div>
      )}

      {/* Filter Controls */}
      <div className="mb-6 bg-white rounded-lg shadow-md p-4">
        <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
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
              value={locationFilter}
              onChange={(e) => setLocationFilter(e.target.value)}
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

      {/* Tab Navigation */}
      <div className="border-b border-gray-200 mb-6">
        <nav className="-mb-px flex flex-wrap space-x-8">
          <button
            onClick={() => setActiveTab('sales')}
            className={`whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm ${
              activeTab === 'sales'
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            }`}
          >
            Daily Sales
          </button>
          <button
            onClick={() => setActiveTab('menu')}
            className={`whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm ${
              activeTab === 'menu'
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            }`}
          >
            Menu Performance
          </button>
          <button
            onClick={() => setActiveTab('delivery')}
            className={`whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm ${
              activeTab === 'delivery'
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            }`}
          >
            Delivery Performance
          </button>
          <button
            onClick={() => setActiveTab('spending')}
            className={`whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm ${
              activeTab === 'spending'
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            }`}
          >
            Customer Spending
          </button>
          <button
            onClick={() => setActiveTab('payment')}
            className={`whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm ${
              activeTab === 'payment'
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            }`}
          >
            Payment Methods
          </button>
          <button
            onClick={() => setActiveTab('kitchen')}
            className={`whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm ${
              activeTab === 'kitchen'
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            }`}
          >
            Kitchen Performance
          </button>
          <button
            onClick={() => setActiveTab('driver')}
            className={`whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm ${
              activeTab === 'driver'
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            }`}
          >
            Driver Performance
          </button>
          <button
            onClick={() => setActiveTab('inventory')}
            className={`whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm ${
              activeTab === 'inventory'
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            }`}
          >
            Inventory Mgmt
          </button>
          <button
            onClick={() => setActiveTab('location')}
            className={`whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm ${
              activeTab === 'location'
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            }`}
          >
            Location Analytics
          </button>
          <button
            onClick={() => setActiveTab('equipment')}
            className={`whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm ${
              activeTab === 'equipment'
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            }`}
          >
            Equipment Utilization
          </button>
        </nav>
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
            {reportData && reportData.length > 0 && (
              <div className="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
                <div className="bg-blue-50 rounded-lg p-4">
                  <h3 className="font-medium text-blue-900">Total Value</h3>
                  <p className="text-2xl font-bold text-blue-600">
                    ${reportData.reduce((sum, item) => sum + (item.value || item.total || item.revenue || 0), 0).toFixed(2)}
                  </p>
                </div>
                <div className="bg-green-50 rounded-lg p-4">
                  <h3 className="font-medium text-green-900">Average Value</h3>
                  <p className="text-2xl font-bold text-green-600">
                    ${(reportData.reduce((sum, item) => sum + (item.value || item.total || item.revenue || 0), 0) / reportData.length).toFixed(2)}
                  </p>
                </div>
                <div className="bg-purple-50 rounded-lg p-4">
                  <h3 className="font-medium text-purple-900">Data Points</h3>
                  <p className="text-2xl font-bold text-purple-600">{reportData.length}</p>
                </div>
              </div>
            )}
          </>
        )}
      </div>
    </div>
  );
};

export default withAuth(ReportsDashboard, ['admin', 'seller', 'store_owner']);