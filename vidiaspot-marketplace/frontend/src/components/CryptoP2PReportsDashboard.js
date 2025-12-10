import React, { useState, useEffect } from 'react';
import { withAuth } from '../utils/withAuth';
import reportService from '../services/reportService';
import { BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, Legend, ResponsiveContainer, PieChart, Pie, Cell, LineChart, Line, AreaChart, Area, ScatterChart, Scatter } from 'recharts';

const CryptoP2PReportsDashboard = () => {
  const [activeTab, setActiveTab] = useState('trading-activity');
  const [reportType, setReportType] = useState('volume-analytics');
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
        // Trading Activity Reports
        case 'volume-analytics':
          data = await reportService.getVolumeAnalytics(params);
          break;
        case 'price-movement-analysis':
          data = await reportService.getPriceMovementAnalysis(params);
          break;
        case 'user-trading-behavior':
          data = await reportService.getUserTradingBehavior(params);
          break;
        case 'liquidity-reports':
          data = await reportService.getLiquidityReports(params);
          break;
        case 'order-book-analysis':
          data = await reportService.getOrderBookAnalysis(params);
          break;
          
        // Security & Compliance Reports
        case 'kyc-aml-compliance':
          data = await reportService.getKYCAMLCompliance(params);
          break;
        case 'risk-assessment':
          data = await reportService.getRiskAssessment(params);
          break;
        case 'security-incidents':
          data = await reportService.getSecurityIncidents(params);
          break;
        case 'regulatory-reporting':
          data = await reportService.getRegulatoryReporting(params);
          break;
        case 'audit-trails':
          data = await reportService.getAuditTrails(params);
          break;
          
        // Financial Performance Reports
        case 'fee-revenue':
          data = await reportService.getFeeRevenue(params);
          break;
        case 'profit-margins':
          data = await reportService.getProfitMargins(params);
          break;
        case 'operational-costs':
          data = await reportService.getOperationalCosts(params);
          break;
        case 'cash-flow-analysis':
          data = await reportService.getCashFlowAnalysis(params);
          break;
        case 'exchange-rate-impact':
          data = await reportService.getExchangeRateImpact(params);
          break;
          
        // Cross-Platform Integration Reports
        case 'consolidated-revenue':
          data = await reportService.getConsolidatedRevenue(params);
          break;
        case 'cross-platform-customer-analysis':
          data = await reportService.getCrossPlatformCustomerAnalysis(params);
          break;
        case 'shared-resource-utilization':
          data = await reportService.getSharedResourceUtilization(params);
          break;
        case 'budget-allocation':
          data = await reportService.getBudgetAllocation(params);
          break;
        case 'overall-business-performance':
          data = await reportService.getOverallBusinessPerformance(params);
          break;
        case 'cross-platform-behavior':
          data = await reportService.getCrossPlatformBehavior(params);
          break;
        case 'unified-customer-profile':
          data = await reportService.getUnifiedCustomerProfile(params);
          break;
        case 'lifetime-value':
          data = await reportService.getLifetimeValue(params);
          break;
        case 'churn-analysis':
          data = await reportService.getChurnAnalysis(params);
          break;
        case 'loyalty-program-performance':
          data = await reportService.getLoyaltyProgramPerformance(params);
          break;

        // Additional Cross-Platform Integration Reports
        case 'shared-infrastructure':
          data = await reportService.getSharedInfrastructure(params);
          break;
        case 'staff-productivity':
          data = await reportService.getStaffProductivity(params);
          break;
        case 'process-standardization':
          data = await reportService.getProcessStandardization(params);
          break;
        case 'technology-integration':
          data = await reportService.getTechnologyIntegration(params);
          break;
        case 'scalability-analysis':
          data = await reportService.getScalabilityAnalysis(params);
          break;

        // Risk Management Reports
        case 'cross-platform-risk-assessment':
          data = await reportService.getCrossPlatformRiskAssessment(params);
          break;
        case 'compliance-monitoring':
          data = await reportService.getComplianceMonitoring(params);
          break;
        case 'financial-risk':
          data = await reportService.getFinancialRisk(params);
          break;
        case 'operational-risk':
          data = await reportService.getOperationalRisk(params);
          break;
        case 'market-risk':
          data = await reportService.getMarketRisk(params);
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
    const isPieChart = ['volume-analytics', 'fee-revenue', 'operational-costs', 'budget-allocation'].includes(reportType);
    const isLineChart = ['price-movement-analysis', 'cash-flow-analysis', 'exchange-rate-impact'].includes(reportType);
    const isAreaChart = ['profit-margins', 'user-trading-behavior'].includes(reportType);
    const isScatterChart = ['liquidity-reports', 'order-book-analysis'].includes(reportType);
    const isBarChart = !isPieChart && !isLineChart && !isAreaChart && !isScatterChart;

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
    } else if (isScatterChart) {
      return (
        <ResponsiveContainer width="100%" height={400}>
          <ScatterChart
            margin={{ top: 20, right: 20, bottom: 20, left: 20 }}
          >
            <CartesianGrid />
            <XAxis type="number" dataKey="x" name="X" />
            <YAxis type="number" dataKey="y" name="Y" />
            <Tooltip cursor={{ strokeDasharray: '3 3' }} />
            <Legend />
            <Scatter name="Data Points" data={reportData} fill="#8884d8" />
          </ScatterChart>
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
      case 'volume-analytics': return 'Volume Analytics';
      case 'price-movement-analysis': return 'Price Movement Analysis';
      case 'user-trading-behavior': return 'User Trading Behavior';
      case 'liquidity-reports': return 'Liquidity Reports';
      case 'order-book-analysis': return 'Order Book Analysis';
      case 'kyc-aml-compliance': return 'KYC/AML Compliance';
      case 'risk-assessment': return 'Risk Assessment';
      case 'security-incidents': return 'Security Incidents';
      case 'regulatory-reporting': return 'Regulatory Reporting';
      case 'audit-trails': return 'Audit Trails';
      case 'fee-revenue': return 'Fee Revenue';
      case 'profit-margins': return 'Profit Margins';
      case 'operational-costs': return 'Operational Costs';
      case 'cash-flow-analysis': return 'Cash Flow Analysis';
      case 'exchange-rate-impact': return 'Exchange Rate Impact';
      case 'consolidated-revenue': return 'Consolidated Revenue';
      case 'cross-platform-customer-analysis': return 'Cross-Platform Customer Analysis';
      case 'shared-resource-utilization': return 'Shared Resource Utilization';
      case 'budget-allocation': return 'Budget Allocation';
      case 'overall-business-performance': return 'Overall Business Performance';
      case 'cross-platform-behavior': return 'Cross-Platform Behavior';
      case 'unified-customer-profile': return 'Unified Customer Profile';
      case 'lifetime-value': return 'Lifetime Value';
      case 'churn-analysis': return 'Churn Analysis';
      case 'loyalty-program-performance': return 'Loyalty Program Performance';
      case 'shared-infrastructure': return 'Shared Infrastructure';
      case 'staff-productivity': return 'Staff Productivity';
      case 'process-standardization': return 'Process Standardization';
      case 'technology-integration': return 'Technology Integration';
      case 'scalability-analysis': return 'Scalability Analysis';
      case 'cross-platform-risk-assessment': return 'Cross-Platform Risk Assessment';
      case 'compliance-monitoring': return 'Compliance Monitoring';
      case 'financial-risk': return 'Financial Risk';
      case 'operational-risk': return 'Operational Risk';
      case 'market-risk': return 'Market Risk';
      default: return 'Crypto P2P Reports Dashboard';
    }
  };

  // Get report description based on report type
  const getReportDescription = () => {
    switch (reportType) {
      case 'volume-analytics': return 'Trading volume by pair, time period, and user segment';
      case 'price-movement-analysis': return 'Volatility reports, trend analysis, and market sentiment';
      case 'user-trading-behavior': return 'Trading frequency, risk tolerance, and strategy analysis';
      case 'liquidity-reports': return 'Market depth, bid-ask spreads, and liquidity provision';
      case 'order-book-analysis': return 'Buy/sell patterns and market manipulation detection';
      case 'kyc-aml-compliance': return 'Verification completion and compliance monitoring';
      case 'risk-assessment': return 'Counterparty risk, market risk, and operational risk';
      case 'security-incidents': return 'Fraud attempts, security breaches, and mitigation actions';
      case 'regulatory-reporting': return 'Compliance with local and international regulations';
      case 'audit-trails': return 'Complete transaction logs for regulatory requirements';
      case 'fee-revenue': return 'Trading fees, withdrawal fees, and premium service revenue';
      case 'profit-margins': return 'By trading pair, user tier, and geographic region';
      case 'operational-costs': return 'Infrastructure, compliance, and customer service expenses';
      case 'cash-flow-analysis': return 'Inflow/outflow patterns and liquidity management';
      case 'exchange-rate-impact': return 'Currency fluctuation effects on revenue';
      case 'consolidated-revenue': return 'All platforms combined vs individual platform performance';
      case 'cross-platform-customer-analysis': return 'Users active on multiple platforms';
      case 'shared-resource-utilization': return 'Infrastructure, customer service, and marketing efficiency';
      case 'budget-allocation': return 'Spending across platforms and ROI comparison';
      case 'overall-business-performance': return 'Combined KPIs, growth metrics, and profitability';
      case 'cross-platform-behavior': return 'Customer movement between platforms';
      case 'unified-customer-profile': return 'Complete customer data across all services';
      case 'lifetime-value': return 'Across all platforms and cross-selling opportunities';
      case 'churn-analysis': return 'Customers leaving any or all platforms';
      case 'loyalty-program-performance': return 'Cross-platform rewards and engagement';
      case 'shared-infrastructure': return 'Server costs, bandwidth, maintenance across platforms';
      case 'staff-productivity': return 'Team performance across multiple platform management';
      case 'process-standardization': return 'Common procedures and efficiency improvements';
      case 'technology-integration': return 'API performance, data synchronization, system health';
      case 'scalability-analysis': return 'Resource allocation for growth across platforms';
      case 'cross-platform-risk-assessment': return 'Shared risks and diversification benefits';
      case 'compliance-monitoring': return 'Multi-jurisdictional regulatory requirements';
      case 'financial-risk': return 'Exposure across different business segments';
      case 'operational-risk': return 'Dependencies between platforms, single points of failure';
      case 'market-risk': return 'Impact of external factors on all platforms simultaneously';
      default: return 'Crypto P2P marketplace analytics report';
    }
  };

  // Render the component
  return (
    <div className="max-w-7xl mx-auto p-6">
      <div className="mb-8">
        <h1 className="text-3xl font-bold text-gray-900">Crypto P2P Marketplace Reports</h1>
        <p className="text-gray-600">Advanced analytics for cryptocurrency peer-to-peer trading</p>
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
              setActiveTab('trading-activity');
              setReportType('volume-analytics');
            }}
            className={`whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm ${
              activeTab === 'trading-activity'
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            }`}
          >
            Trading Activity
          </button>
          <button
            onClick={() => {
              setActiveTab('security');
              setReportType('kyc-aml-compliance');
            }}
            className={`whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm ${
              activeTab === 'security'
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            }`}
          >
            Security & Compliance
          </button>
          <button
            onClick={() => {
              setActiveTab('financial');
              setReportType('fee-revenue');
            }}
            className={`whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm ${
              activeTab === 'financial'
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            }`}
          >
            Financial Performance
          </button>
          <button
            onClick={() => {
              setActiveTab('integration');
              setReportType('consolidated-revenue');
            }}
            className={`whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm ${
              activeTab === 'integration'
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            }`}
          >
            Cross-Platform Integration
          </button>
          <button
            onClick={() => {
              setActiveTab('risk');
              setReportType('cross-platform-risk-assessment');
            }}
            className={`whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm ${
              activeTab === 'risk'
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            }`}
          >
            Risk Management
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
              {activeTab === 'trading-activity' && (
                <>
                  <option value="volume-analytics">Volume Analytics</option>
                  <option value="price-movement-analysis">Price Movement Analysis</option>
                  <option value="user-trading-behavior">User Trading Behavior</option>
                  <option value="liquidity-reports">Liquidity Reports</option>
                  <option value="order-book-analysis">Order Book Analysis</option>
                </>
              )}
              {activeTab === 'security' && (
                <>
                  <option value="kyc-aml-compliance">KYC/AML Compliance</option>
                  <option value="risk-assessment">Risk Assessment</option>
                  <option value="security-incidents">Security Incidents</option>
                  <option value="regulatory-reporting">Regulatory Reporting</option>
                  <option value="audit-trails">Audit Trails</option>
                </>
              )}
              {activeTab === 'financial' && (
                <>
                  <option value="fee-revenue">Fee Revenue</option>
                  <option value="profit-margins">Profit Margins</option>
                  <option value="operational-costs">Operational Costs</option>
                  <option value="cash-flow-analysis">Cash Flow Analysis</option>
                  <option value="exchange-rate-impact">Exchange Rate Impact</option>
                </>
              )}
              {activeTab === 'integration' && (
                <>
                  <option value="consolidated-revenue">Consolidated Revenue</option>
                  <option value="cross-platform-customer-analysis">Cross-Platform Customer Analysis</option>
                  <option value="shared-resource-utilization">Shared Resource Utilization</option>
                  <option value="shared-infrastructure">Shared Infrastructure</option>
                  <option value="staff-productivity">Staff Productivity</option>
                  <option value="process-standardization">Process Standardization</option>
                  <option value="technology-integration">Technology Integration</option>
                  <option value="scalability-analysis">Scalability Analysis</option>
                  <option value="budget-allocation">Budget Allocation</option>
                  <option value="overall-business-performance">Overall Business Performance</option>
                  <option value="cross-platform-behavior">Cross-Platform Behavior</option>
                  <option value="unified-customer-profile">Unified Customer Profile</option>
                  <option value="lifetime-value">Lifetime Value</option>
                  <option value="churn-analysis">Churn Analysis</option>
                  <option value="loyalty-program-performance">Loyalty Program Performance</option>
                </>
              )}
              {activeTab === 'risk' && (
                <>
                  <option value="cross-platform-risk-assessment">Cross-Platform Risk Assessment</option>
                  <option value="compliance-monitoring">Compliance Monitoring</option>
                  <option value="financial-risk">Financial Risk</option>
                  <option value="operational-risk">Operational Risk</option>
                  <option value="market-risk">Market Risk</option>
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
              <option value="trading-pair">By Trading Pair</option>
              <option value="user-segment">By User Segment</option>
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

export default withAuth(CryptoP2PReportsDashboard, ['admin', 'crypto_operator']);