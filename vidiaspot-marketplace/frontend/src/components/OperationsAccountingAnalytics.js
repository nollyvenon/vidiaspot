import React, { useState, useEffect } from 'react';
import { withAuth } from '../utils/withAuth';
import clientService from '../services/clientService';
import { BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, Legend, ResponsiveContainer, PieChart, Pie, Cell, LineChart, Line, HeatMap, Treemap, AreaChart, Area } from 'recharts';

const OperationsAccountingAnalytics = () => {
  const [activeTab, setActiveTab] = useState('operational-analytics');
  const [reportType, setReportType] = useState('fleet-utilization');
  const [reportData, setReportData] = useState([]);
  const [loading, setLoading] = useState(true);
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
      let params = { ...dateRange, ...filters };

      let data;
      switch (reportType) {
        // Operational Analytics
        case 'fleet-utilization':
          data = await clientService.getFleetUtilization(params);
          break;
        case 'driver-productivity':
          data = await clientService.getDriverProductivity(params);
          break;
        case 'peak-demand':
          data = await clientService.getPeakDemandAnalysis(params);
          break;
        case 'geographic-heatmaps':
          data = await clientService.getGeographicHeatMaps(params);
          break;
        case 'exception-handling':
          data = await clientService.getExceptionHandlingReports(params);
          break;
        case 'resource-allocation':
          data = await clientService.getResourceAllocationOptimization(params);
          break;
          
        // Accounting Integration Reports
        case 'general-ledger-reconciliation':
          data = await clientService.getGeneralLedgerReconciliation(params);
          break;
        case 'accounts-receivable-aging':
          data = await clientService.getAccountsReceivableAging(params);
          break;
        case 'accounts-payable-aging':
          data = await clientService.getAccountsPayableAging(params);
          break;
        case 'tax-reporting-compliance':
          data = await clientService.getTaxReportingCompliance(params);
          break;
        case 'audit-trails':
          data = await clientService.getAuditTrails(params);
          break;
        case 'cost-center-analysis':
          data = await clientService.getCostCenterAnalysis(params);
          break;
        case 'financial-statements':
          data = await clientService.getFinancialStatements(params);
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

    const isPieChart = ['fleet-utilization', 'driver-productivity'].includes(reportType);
    const isLineChart = ['peak-demand', 'accounts-receivable-aging', 'accounts-payable-aging'].includes(reportType);
    const isAreaChart = ['geographic-heatmaps', 'cost-center-analysis'].includes(reportType);
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
              <linearGradient id="colorValue" x1="0" y1="0" x2="0" y2="1">
                <stop offset="5%" stopColor="#8884d8" stopOpacity={0.8}/>
                <stop offset="95%" stopColor="#8884d8" stopOpacity={0}/>
              </linearGradient>
            </defs>
            <XAxis dataKey="name" />
            <YAxis />
            <CartesianGrid strokeDasharray="3 3" />
            <Tooltip />
            <Area type="monotone" dataKey="value" stackId="1" stroke="#8884d8" fillOpacity={1} fill="url(#colorValue)" name="Value" />
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

  const getReportTitle = () => {
    switch (reportType) {
      case 'fleet-utilization': return 'Fleet Utilization Rates';
      case 'driver-productivity': return 'Driver Productivity & Overtime Analysis';
      case 'peak-demand': return 'Peak Demand Periods & Capacity Planning';
      case 'geographic-heatmaps': return 'Geographic Heat Maps - Delivery Density';
      case 'exception-handling': return 'Exception Handling Reports';
      case 'resource-allocation': return 'Resource Allocation Optimization';
      case 'general-ledger-reconciliation': return 'General Ledger Reconciliation';
      case 'accounts-receivable-aging': return 'Accounts Receivable Aging';
      case 'accounts-payable-aging': return 'Accounts Payable Aging';
      case 'tax-reporting-compliance': return 'Tax Reporting & Compliance';
      case 'audit-trails': return 'Audit Trails for Financial Transactions';
      case 'cost-center-analysis': return 'Cost Center Analysis';
      case 'financial-statements': return 'Monthly/Quarterly Financial Statements';
      default: return 'Operations & Accounting Analytics';
    }
  };

  const getReportDescription = () => {
    switch (reportType) {
      case 'fleet-utilization': return 'Vehicle utilization rates and performance metrics';
      case 'driver-productivity': return 'Driver productivity and overtime analysis';
      case 'peak-demand': return 'Peak demand periods and capacity planning insights';
      case 'geographic-heatmaps': return 'Geographic heat maps showing delivery density';
      case 'exception-handling': return 'Exception handling reports (delays, damages, returns)';
      case 'resource-allocation': return 'Resource allocation optimization recommendations';
      case 'general-ledger-reconciliation': return 'General ledger reconciliation and balance confirmation';
      case 'accounts-receivable-aging': return 'Accounts receivable aging analysis';
      case 'accounts-payable-aging': return 'Accounts payable aging analysis';
      case 'tax-reporting-compliance': return 'Tax reporting and compliance documentation';
      case 'audit-trails': return 'Complete audit trails for all financial transactions';
      case 'cost-center-analysis': return 'Cost center analysis for operational expenses';
      case 'financial-statements': return 'Monthly/quarterly financial statements';
      default: return 'Operations and accounting analytics report';
    }
  };

  return (
    <div className="max-w-7xl mx-auto p-6">
      <div className="mb-8">
        <h1 className="text-3xl font-bold text-gray-900">Operations & Accounting Analytics Dashboard</h1>
        <p className="text-gray-600">Comprehensive operational and financial analytics for your business</p>
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
              setActiveTab('operational-analytics');
              setReportType('fleet-utilization');
            }}
            className={`whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm ${
              activeTab === 'operational-analytics'
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            }`}
          >
            Operational Analytics
          </button>
          <button
            onClick={() => {
              setActiveTab('accounting-integration');
              setReportType('general-ledger-reconciliation');
            }}
            className={`whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm ${
              activeTab === 'accounting-integration'
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            }`}
          >
            Accounting Integration
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
              {activeTab === 'operational-analytics' && (
                <>
                  <option value="fleet-utilization">Fleet Utilization Rates</option>
                  <option value="driver-productivity">Driver Productivity & Overtime</option>
                  <option value="peak-demand">Peak Demand Periods</option>
                  <option value="geographic-heatmaps">Geographic Heat Maps</option>
                  <option value="exception-handling">Exception Handling Reports</option>
                  <option value="resource-allocation">Resource Allocation Optimization</option>
                </>
              )}
              {activeTab === 'accounting-integration' && (
                <>
                  <option value="general-ledger-reconciliation">General Ledger Reconciliation</option>
                  <option value="accounts-receivable-aging">Accounts Receivable Aging</option>
                  <option value="accounts-payable-aging">Accounts Payable Aging</option>
                  <option value="tax-reporting-compliance">Tax Reporting & Compliance</option>
                  <option value="audit-trails">Audit Trails</option>
                  <option value="cost-center-analysis">Cost Center Analysis</option>
                  <option value="financial-statements">Financial Statements</option>
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
              <option value="department">By Department</option>
              <option value="region">By Region</option>
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

export default withAuth(OperationsAccountingAnalytics, ['admin', 'accountant', 'operations_manager']);