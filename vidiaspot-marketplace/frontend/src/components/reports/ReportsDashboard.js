import React, { useState, useEffect } from 'react';
import { BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, Legend, ResponsiveContainer, PieChart, Pie, Cell, LineChart, Line } from 'recharts';
import reportService from '../../services/reportService';

const ReportsDashboard = () => {
  const [appType, setAppType] = useState('food-delivery'); // 'food-delivery' or 'classified'
  const [reportType, setReportType] = useState('daily-sales');
  const [reportData, setReportData] = useState(null);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);

  const COLORS = ['#0088FE', '#00C49F', '#FFBB28', '#FF8042', '#8884D8', '#82CA9D'];

  const getReportData = async () => {
    setLoading(true);
    setError(null);

    try {
      let data;

      if (appType === 'food-delivery') {
        switch(reportType) {
          case 'daily-sales':
            data = await reportService.getDailySales();
            break;
          case 'menu-performance':
            data = await reportService.getMenuPerformance();
            break;
          case 'delivery-performance':
            data = await reportService.getDeliveryPerformance();
            break;
          case 'customer-spending':
            data = await reportService.getCustomerSpending();
            break;
          case 'payment-method':
            data = await reportService.getPaymentMethodRevenue();
            break;
          case 'kitchen-performance':
            data = await reportService.getKitchenPerformance();
            break;
          case 'driver-performance':
            data = await reportService.getDriverPerformance();
            break;
          case 'inventory-management':
            data = await reportService.getInventoryManagement();
            break;
          case 'location-analytics':
            data = await reportService.getLocationAnalytics();
            break;
          case 'equipment-utilization':
            data = await reportService.getEquipmentUtilization();
            break;
          case 'order-fulfillment':
            data = await reportService.getOrderFulfillment();
            break;
          case 'delivery-metrics':
            data = await reportService.getDeliveryMetrics();
            break;
          case 'customer-feedback':
            data = await reportService.getCustomerFeedback();
            break;
          case 'user-engagement':
            data = await reportService.getUserEngagement();
            break;
          case 'loyalty-program':
            data = await reportService.getLoyaltyProgram();
            break;
          case 'order-fulfillment':
            data = await reportService.getOrderFulfillment();
            break;
          case 'cost-goods':
            data = await reportService.getCostOfGoods();
            break;
          case 'profit-margins':
            data = await reportService.getProfitMargins();
            break;
          case 'commission':
            data = await reportService.getCommissionReports();
            break;
          case 'waste-loss':
            data = await reportService.getWasteLoss();
            break;
          case 'break-even':
            data = await reportService.getBreakEven();
            break;
          default:
            data = await reportService.getDailySales();
        }
      } else if (appType === 'classified') {
        switch(reportType) {
          case 'user-registration':
            data = await reportService.getUserRegistration();
            break;
          case 'listing-performance':
            data = await reportService.getListingPerformance();
            break;
          case 'category-analysis':
            data = await reportService.getCategoryAnalysis();
            break;
          case 'engagement-metrics':
            data = await reportService.getEngagementMetrics();
            break;
          case 'user-retention':
            data = await reportService.getUserRetention();
            break;
          case 'subscription-analytics':
            data = await reportService.getSubscriptionAnalytics();
            break;
          case 'premium-service':
            data = await reportService.getPremiumServiceUsage();
            break;
          case 'commission-category':
            data = await reportService.getCommissionByCategory();
            break;
          case 'payment-processing':
            data = await reportService.getPaymentProcessing();
            break;
          case 'cost-acquisition':
            data = await reportService.getCostPerAcquisition();
            break;
          case 'content-moderation':
            data = await reportService.getContentModeration();
            break;
          case 'listing-quality':
            data = await reportService.getListingQuality();
            break;
          case 'fraud-detection':
            data = await reportService.getFraudDetection();
            break;
          case 'search-analytics':
            data = await reportService.getSearchAnalytics();
            break;
          case 'ugc':
            data = await reportService.getUserGeneratedContent();
            break;
          case 'price-analytics':
            data = await reportService.getPriceAnalytics();
            break;
          case 'demand-forecast':
            data = await reportService.getDemandForecast();
            break;
          case 'geographic-performance':
            data = await reportService.getGeographicPerformance();
            break;
          case 'seasonal-trends':
            data = await reportService.getSeasonalTrends();
            break;
          case 'market-saturation':
            data = await reportService.getMarketSaturation();
            break;
          default:
            data = await reportService.getUserRegistration();
        }
      } else if (appType === 'ecommerce') {
        switch(reportType) {
          case 'sales-dashboard':
            data = []; // Placeholder for sales dashboard
            break;
          case 'product-performance':
            data = []; // Placeholder for product performance
            break;
          case 'customer-lifetime-value':
            data = []; // Placeholder for customer lifetime value
            break;
          case 'seasonal-sales-analysis':
            data = []; // Placeholder for seasonal sales analysis
            break;
          case 'sales-channel-performance':
            data = []; // Placeholder for sales channel performance
            break;
          case 'stock-level-reports':
            data = []; // Placeholder for stock level reports
            break;
          case 'inventory-turnover':
            data = []; // Placeholder for inventory turnover
            break;
          case 'supplier-performance':
            data = []; // Placeholder for supplier performance
            break;
          case 'dead-stock-analysis':
            data = []; // Placeholder for dead stock analysis
            break;
          case 'demand-forecasting':
            data = []; // Placeholder for demand forecasting
            break;
          case 'conversion-funnel':
            data = []; // Placeholder for conversion funnel
            break;
          case 'marketing-roi':
            data = []; // Placeholder for marketing ROI
            break;
          case 'customer-segmentation':
            data = []; // Placeholder for customer segmentation
            break;
          case 'email-marketing':
            data = []; // Placeholder for email marketing
            break;
          case 'seo-performance':
            data = []; // Placeholder for SEO performance
            break;
          case 'profit-margins':
            data = await reportService.getProfitMargins();
            break;
          case 'shipping-analytics':
            data = []; // Placeholder for shipping analytics
            break;
          case 'return-refund-analysis':
            data = []; // Placeholder for return/refund analysis
            break;
          case 'tax-compliance':
            data = []; // Placeholder for tax compliance
            break;
          case 'payment-processing':
            data = []; // Placeholder for payment processing
            break;
          default:
            data = []; // Default for e-commerce
        }
      }

      // Apply International Accounting Principles formatting to financial data
      let formattedData = data;

      if (['cost-goods', 'profit-margins', 'commission', 'waste-loss', 'break-even',
           'subscription-analytics', 'premium-service', 'commission-category',
           'cost-acquisition'].includes(reportType)) {
        formattedData = data.map(item => {
          const formattedItem = { ...item };

          // Format currency values according to International Financial Reporting Standards (IFRS)
          Object.keys(formattedItem).forEach(key => {
            if (typeof formattedItem[key] === 'number' &&
                (key.includes('revenue') || key.includes('cost') ||
                 key.includes('profit') || key.includes('commission') ||
                 key.includes('earning') || key.includes('price') ||
                 key.includes('spend') || key.includes('amount') ||
                 key.includes('value') || key.includes('cogs') ||
                 key.includes('expense') || key.includes('income') ||
                 key.includes('asset') || key.includes('liability') ||
                 key.includes('equity') || key.includes('margin') ||
                 key.includes('loss') || key.includes('gain'))) {
              // Format as currency with 2 decimal places according to IFRS standards
              formattedItem[key] = parseFloat(formattedItem[key]).toFixed(2);
            }
          });

          return formattedItem;
        });
      }

      setReportData(formattedData);
    } catch (err) {
      setError('Failed to fetch report data. Please try again later.');
      console.error('Error fetching report data:', err);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    getReportData();
  }, [reportType, appType]);

  const renderChart = () => {
    if (!reportData) return null;

    // Determine if we need a pie chart vs other chart types
    const pieCharts = ['payment-method', 'cost-goods', 'listing-quality'];

    if (pieCharts.includes(reportType)) {
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
              dataKey={reportType === 'listing-quality' ? 'percentage' : 'value'}
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
    } else if (reportType === 'delivery-metrics' || reportType === 'order-fulfillment' ||
               reportType === 'user-engagement' || reportType === 'engagement-metrics') {
      // For charts that need multiple data series on the same graph
      return (
        <ResponsiveContainer width="100%" height={400}>
          <LineChart
            data={reportData}
            margin={{
              top: 5,
              right: 30,
              left: 20,
              bottom: 5,
            }}
          >
            <CartesianGrid strokeDasharray="3 3" />
            <XAxis dataKey="name" />
            <YAxis />
            <Tooltip />
            <Legend />
            {reportType === 'delivery-metrics' && (
              <>
                <Line type="monotone" dataKey="avgTime" stroke="#8884d8" name="Avg Time (min)" />
                <Line type="monotone" dataKey="trackingAccuracy" stroke="#82ca9d" name="Tracking Accuracy (%)" />
              </>
            )}
            {reportType === 'order-fulfillment' && (
              <>
                <Line type="monotone" dataKey="accuracy" stroke="#0088FE" name="Accuracy (%)" />
                <Line type="monotone" dataKey="complaints" stroke="#FF8042" name="Complaints Count" />
                <Line type="monotone" dataKey="satisfaction" stroke="#00C49F" name="Satisfaction Score" />
              </>
            )}
            {reportType === 'user-engagement' && (
              <>
                <Line type="monotone" dataKey="appUsage" stroke="#8884d8" name="App Usage (%)" />
                <Line type="monotone" dataKey="featureAdoption" stroke="#82ca9d" name="Feature Adoption (%)" />
                <Line type="monotone" dataKey="churn" stroke="#FF8042" name="Churn Rate (%)" />
              </>
            )}
            {reportType === 'engagement-metrics' && (
              <>
                <Line type="monotone" dataKey="timeSpent" stroke="#0088FE" name="Time Spent (min)" />
                <Line type="monotone" dataKey="pagesViewed" stroke="#00C49F" name="Pages Viewed" />
                <Line type="monotone" dataKey="searchBehavior" stroke="#FFBB28" name="Search Behavior" />
              </>
            )}
          </LineChart>
        </ResponsiveContainer>
      );
    } else {
      // Default bar chart for most report types
      return (
        <ResponsiveContainer width="100%" height={400}>
          <BarChart
            data={reportData}
            margin={{
              top: 5,
              right: 30,
              left: 20,
              bottom: 5,
            }}
          >
            <CartesianGrid strokeDasharray="3 3" />
            <XAxis dataKey="name" />
            <YAxis />
            <Tooltip />
            <Legend />

            {/* Food Vending & Delivery App charts */}
            {(reportType === 'daily-sales' || reportType === 'menu-performance' ||
              reportType === 'delivery-performance' || reportType === 'customer-spending' ||
              reportType === 'kitchen-performance' || reportType === 'location-analytics' ||
              reportType === 'order-fulfillment' || reportType === 'user-retention') && (
              Object.keys(reportData[0]).filter(key => key !== 'name').map((key, index) => (
                <Bar key={key} dataKey={key} fill={COLORS[index % COLORS.length]} name={key.replace(/([A-Z])/g, ' $1').replace(/^./, str => str.toUpperCase())} />
              ))
            )}

            {/* Driver performance and commission charts */}
            {(reportType === 'driver-performance' || reportType === 'commission' ||
              reportType === 'premium-service' || reportType === 'commission-category') && (
              Object.keys(reportData[0]).filter(key => key !== 'name' && key !== 'percentage').map((key, index) => (
                <Bar key={key} dataKey={key} fill={COLORS[index % COLORS.length]} name={key.replace(/([A-Z])/g, ' $1').replace(/^./, str => str.toUpperCase())} />
              ))
            )}

            {/* Inventory management chart */}
            {reportType === 'inventory-management' && (
              <>
                <Bar dataKey="stock" fill="#8884d8" name="Stock Level" />
                <Bar dataKey="threshold" fill="#FF8042" name="Threshold" />
              </>
            )}

            {/* Equipment utilization chart */}
            {reportType === 'equipment-utilization' && (
              <>
                <Bar dataKey="usage" fill="#8884d8" name="Usage (%)" />
                <Bar dataKey="maintenance" fill="#FF8042" name="Maintenance Hours" />
              </>
            )}

            {/* Profit margins chart - IFRS compliant */}
            {reportType === 'profit-margins' && (
              <>
                <Bar dataKey="margin" fill="#8884d8" name="Profit Margin (%)" />
                <Bar dataKey="revenue" fill="#82ca9d" name="Revenue (USD)" />
                <Bar dataKey="cost" fill="#FF8042" name="Cost of Goods Sold (USD)" />
                <Bar dataKey="netIncome" fill="#8884D8" name="Net Income (USD)" />
              </>
            )}

            {/* Waste and loss chart - IFRS compliant */}
            {reportType === 'waste-loss' && (
              <>
                <Bar dataKey="cost" fill="#FF8042" name="Expense Amount (USD)" />
                <Bar dataKey="percentage" fill="#FFBB28" name="Percentage of Revenue (%)" />
                <Bar dataKey="category" fill="#82ca9d" name="Category" />
              </>
            )}

            {/* Break-even analysis chart - IFRS compliant */}
            {reportType === 'break-even' && (
              <>
                <Bar dataKey="breakEvenPoint" fill="#8884d8" name="Break-even Point (USD)" />
                <Bar dataKey="currentRevenue" fill="#82ca9d" name="Current Revenue (USD)" />
                <Bar dataKey="fixedCosts" fill="#FF8042" name="Fixed Costs (USD)" />
                <Bar dataKey="variableCosts" fill="#FFBB28" name="Variable Costs (USD)" />
              </>
            )}

            {/* User activity and financial reports for Classified App */}
            {(reportType === 'user-registration' || reportType === 'listing-performance' ||
              reportType === 'subscription-analytics' || reportType === 'payment-processing' ||
              reportType === 'cost-acquisition' || reportType === 'content-moderation' ||
              reportType === 'fraud-detection' || reportType === 'search-analytics' ||
              reportType === 'ugc' || reportType === 'price-analytics' ||
              reportType === 'demand-forecast' || reportType === 'geographic-performance' ||
              reportType === 'market-saturation') && (
              Object.keys(reportData[0]).filter(key => key !== 'name' && key !== 'term' && key !== 'volume').map((key, index) => (
                <Bar key={key} dataKey={key} fill={COLORS[index % COLORS.length]} name={key.replace(/([A-Z])/g, ' $1').replace(/^./, str => str.toUpperCase())} />
              ))
            )}

            {/* Category analysis chart */}
            {reportType === 'category-analysis' && (
              <>
                <Bar dataKey="popularity" fill="#8884d8" name="Popularity (%)" />
                <Bar dataKey="growth" fill="#82ca9d" name="Growth (%)" />
              </>
            )}

            {/* Seasonal trends chart */}
            {reportType === 'seasonal-trends' && (
              <>
                <Bar dataKey="electronics" fill="#8884d8" name="Electronics (%)" />
                <Bar dataKey="vehicles" fill="#82ca9d" name="Vehicles (%)" />
                <Bar dataKey="realEstate" fill="#FFBB28" name="Real Estate (%)" />
              </>
            )}
          </BarChart>
        </ResponsiveContainer>
      );
    }
  };

  // Determine report title based on app type and report type
  const getReportTitle = () => {
    if (appType === 'food-delivery') {
      switch(reportType) {
        case 'daily-sales': return 'Daily Sales Summary';
        case 'menu-performance': return 'Menu Performance';
        case 'delivery-performance': return 'Delivery Performance';
        case 'customer-spending': return 'Customer Spending Patterns';
        case 'payment-method': return 'Revenue by Payment Method';
        case 'kitchen-performance': return 'Kitchen Performance';
        case 'driver-performance': return 'Driver Performance';
        case 'inventory-management': return 'Inventory Management';
        case 'location-analytics': return 'Location Analytics';
        case 'equipment-utilization': return 'Equipment Utilization';
        case 'order-fulfillment': return 'Order Fulfillment';
        case 'delivery-metrics': return 'Delivery Metrics';
        case 'customer-feedback': return 'Customer Feedback';
        case 'user-engagement': return 'User Engagement';
        case 'loyalty-program': return 'Loyalty Program';
        case 'cost-goods': return 'Cost of Goods Sold';
        case 'profit-margins': return 'Profit Margins';
        case 'commission': return 'Commission Reports';
        case 'waste-loss': return 'Waste and Loss';
        case 'break-even': return 'Break-even Analysis';
        default: return 'Report';
      }
    } else if (appType === 'classified') {
      switch(reportType) {
        case 'user-registration': return 'User Registration';
        case 'listing-performance': return 'Listing Performance';
        case 'category-analysis': return 'Category Analysis';
        case 'engagement-metrics': return 'Engagement Metrics';
        case 'user-retention': return 'User Retention';
        case 'subscription-analytics': return 'Subscription Analytics';
        case 'premium-service': return 'Premium Service Usage';
        case 'commission-category': return 'Commission by Category';
        case 'payment-processing': return 'Payment Processing';
        case 'cost-acquisition': return 'Cost Per Acquisition';
        case 'content-moderation': return 'Content Moderation';
        case 'listing-quality': return 'Listing Quality';
        case 'fraud-detection': return 'Fraud Detection';
        case 'search-analytics': return 'Search Analytics';
        case 'ugc': return 'User-Generated Content';
        case 'price-analytics': return 'Price Analytics';
        case 'demand-forecast': return 'Demand Forecasting';
        case 'geographic-performance': return 'Geographic Performance';
        case 'seasonal-trends': return 'Seasonal Trends';
        case 'market-saturation': return 'Market Saturation';
        default: return 'Report';
      }
    } else if (appType === 'ecommerce') {
      switch(reportType) {
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
        default: return 'Report';
      }
    }
    return 'Report';
  };

  return (
    <div className="p-6 bg-gray-50 min-h-screen">
      <div className="max-w-7xl mx-auto">
        <h1 className="text-3xl font-bold text-gray-900 mb-6">Business Reports Dashboard</h1>

        {/* Application Type Selector */}
        <div className="bg-white rounded-lg shadow-md p-4 mb-6">
          <div className="flex flex-wrap gap-4">
            <button
              onClick={() => setAppType('food-delivery')}
              className={`px-4 py-2 rounded-md ${
                appType === 'food-delivery'
                  ? 'bg-blue-600 text-white'
                  : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
              }`}
            >
              Food Vending & Delivery
            </button>
            <button
              onClick={() => setAppType('classified')}
              className={`px-4 py-2 rounded-md ${
                appType === 'classified'
                  ? 'bg-blue-600 text-white'
                  : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
              }`}
            >
              Classified App
            </button>
            <button
              onClick={() => setAppType('ecommerce')}
              className={`px-4 py-2 rounded-md ${
                appType === 'ecommerce'
                  ? 'bg-blue-600 text-white'
                  : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
              }`}
            >
              E-commerce & Shopify
            </button>
          </div>
        </div>

        <div className="bg-white rounded-lg shadow-md p-6 mb-6">
          {/* Food Vending & Delivery App Report Types */}
          {appType === 'food-delivery' && (
            <div className="flex flex-wrap gap-2 mb-6">
              <button
                onClick={() => setReportType('daily-sales')}
                className={`px-3 py-1 rounded-md text-sm ${
                  reportType === 'daily-sales'
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                }`}
              >
                Daily Sales
              </button>
              <button
                onClick={() => setReportType('menu-performance')}
                className={`px-3 py-1 rounded-md text-sm ${
                  reportType === 'menu-performance'
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                }`}
              >
                Menu Performance
              </button>
              <button
                onClick={() => setReportType('delivery-performance')}
                className={`px-3 py-1 rounded-md text-sm ${
                  reportType === 'delivery-performance'
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                }`}
              >
                Delivery Performance
              </button>
              <button
                onClick={() => setReportType('customer-spending')}
                className={`px-3 py-1 rounded-md text-sm ${
                  reportType === 'customer-spending'
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                }`}
              >
                Customer Spending
              </button>
              <button
                onClick={() => setReportType('payment-method')}
                className={`px-3 py-1 rounded-md text-sm ${
                  reportType === 'payment-method'
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                }`}
              >
                Payment Methods
              </button>
              <button
                onClick={() => setReportType('kitchen-performance')}
                className={`px-3 py-1 rounded-md text-sm ${
                  reportType === 'kitchen-performance'
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                }`}
              >
                Kitchen Performance
              </button>
              <button
                onClick={() => setReportType('driver-performance')}
                className={`px-3 py-1 rounded-md text-sm ${
                  reportType === 'driver-performance'
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                }`}
              >
                Driver Performance
              </button>
              <button
                onClick={() => setReportType('inventory-management')}
                className={`px-3 py-1 rounded-md text-sm ${
                  reportType === 'inventory-management'
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                }`}
              >
                Inventory Management
              </button>
              <button
                onClick={() => setReportType('location-analytics')}
                className={`px-3 py-1 rounded-md text-sm ${
                  reportType === 'location-analytics'
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                }`}
              >
                Location Analytics
              </button>
              <button
                onClick={() => setReportType('equipment-utilization')}
                className={`px-3 py-1 rounded-md text-sm ${
                  reportType === 'equipment-utilization'
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                }`}
              >
                Equipment Utilization
              </button>
              <button
                onClick={() => setReportType('order-fulfillment')}
                className={`px-3 py-1 rounded-md text-sm ${
                  reportType === 'order-fulfillment'
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                }`}
              >
                Order Fulfillment
              </button>
              <button
                onClick={() => setReportType('delivery-metrics')}
                className={`px-3 py-1 rounded-md text-sm ${
                  reportType === 'delivery-metrics'
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                }`}
              >
                Delivery Metrics
              </button>
              <button
                onClick={() => setReportType('customer-feedback')}
                className={`px-3 py-1 rounded-md text-sm ${
                  reportType === 'customer-feedback'
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                }`}
              >
                Customer Feedback
              </button>
              <button
                onClick={() => setReportType('user-engagement')}
                className={`px-3 py-1 rounded-md text-sm ${
                  reportType === 'user-engagement'
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                }`}
              >
                User Engagement
              </button>
              <button
                onClick={() => setReportType('loyalty-program')}
                className={`px-3 py-1 rounded-md text-sm ${
                  reportType === 'loyalty-program'
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                }`}
              >
                Loyalty Program
              </button>
              <button
                onClick={() => setReportType('cost-goods')}
                className={`px-3 py-1 rounded-md text-sm ${
                  reportType === 'cost-goods'
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                }`}
              >
                COGS
              </button>
              <button
                onClick={() => setReportType('profit-margins')}
                className={`px-3 py-1 rounded-md text-sm ${
                  reportType === 'profit-margins'
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                }`}
              >
                Profit Margins
              </button>
              <button
                onClick={() => setReportType('commission')}
                className={`px-3 py-1 rounded-md text-sm ${
                  reportType === 'commission'
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                }`}
              >
                Commission Reports
              </button>
              <button
                onClick={() => setReportType('waste-loss')}
                className={`px-3 py-1 rounded-md text-sm ${
                  reportType === 'waste-loss'
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                }`}
              >
                Waste & Loss
              </button>
              <button
                onClick={() => setReportType('break-even')}
                className={`px-3 py-1 rounded-md text-sm ${
                  reportType === 'break-even'
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                }`}
              >
                Break-even Analysis
              </button>
            </div>
          )}

          {/* Classified App Report Types */}
          {appType === 'classified' && (
            <div className="flex flex-wrap gap-2 mb-6">
              <button
                onClick={() => setReportType('user-registration')}
                className={`px-3 py-1 rounded-md text-sm ${
                  reportType === 'user-registration'
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                }`}
              >
                User Registration
              </button>
              <button
                onClick={() => setReportType('listing-performance')}
                className={`px-3 py-1 rounded-md text-sm ${
                  reportType === 'listing-performance'
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                }`}
              >
                Listing Performance
              </button>
              <button
                onClick={() => setReportType('category-analysis')}
                className={`px-3 py-1 rounded-md text-sm ${
                  reportType === 'category-analysis'
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                }`}
              >
                Category Analysis
              </button>
              <button
                onClick={() => setReportType('engagement-metrics')}
                className={`px-3 py-1 rounded-md text-sm ${
                  reportType === 'engagement-metrics'
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                }`}
              >
                Engagement Metrics
              </button>
              <button
                onClick={() => setReportType('user-retention')}
                className={`px-3 py-1 rounded-md text-sm ${
                  reportType === 'user-retention'
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                }`}
              >
                User Retention
              </button>
              <button
                onClick={() => setReportType('subscription-analytics')}
                className={`px-3 py-1 rounded-md text-sm ${
                  reportType === 'subscription-analytics'
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                }`}
              >
                Subscription Analytics
              </button>
              <button
                onClick={() => setReportType('premium-service')}
                className={`px-3 py-1 rounded-md text-sm ${
                  reportType === 'premium-service'
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                }`}
              >
                Premium Services
              </button>
              <button
                onClick={() => setReportType('commission-category')}
                className={`px-3 py-1 rounded-md text-sm ${
                  reportType === 'commission-category'
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                }`}
              >
                Commission by Category
              </button>
              <button
                onClick={() => setReportType('payment-processing')}
                className={`px-3 py-1 rounded-md text-sm ${
                  reportType === 'payment-processing'
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                }`}
              >
                Payment Processing
              </button>
              <button
                onClick={() => setReportType('cost-acquisition')}
                className={`px-3 py-1 rounded-md text-sm ${
                  reportType === 'cost-acquisition'
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                }`}
              >
                Cost Per Acquisition
              </button>
              <button
                onClick={() => setReportType('content-moderation')}
                className={`px-3 py-1 rounded-md text-sm ${
                  reportType === 'content-moderation'
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                }`}
              >
                Content Moderation
              </button>
              <button
                onClick={() => setReportType('listing-quality')}
                className={`px-3 py-1 rounded-md text-sm ${
                  reportType === 'listing-quality'
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                }`}
              >
                Listing Quality
              </button>
              <button
                onClick={() => setReportType('fraud-detection')}
                className={`px-3 py-1 rounded-md text-sm ${
                  reportType === 'fraud-detection'
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                }`}
              >
                Fraud Detection
              </button>
              <button
                onClick={() => setReportType('search-analytics')}
                className={`px-3 py-1 rounded-md text-sm ${
                  reportType === 'search-analytics'
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                }`}
              >
                Search Analytics
              </button>
              <button
                onClick={() => setReportType('ugc')}
                className={`px-3 py-1 rounded-md text-sm ${
                  reportType === 'ugc'
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                }`}
              >
                User Content
              </button>
              <button
                onClick={() => setReportType('price-analytics')}
                className={`px-3 py-1 rounded-md text-sm ${
                  reportType === 'price-analytics'
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                }`}
              >
                Price Analytics
              </button>
              <button
                onClick={() => setReportType('demand-forecast')}
                className={`px-3 py-1 rounded-md text-sm ${
                  reportType === 'demand-forecast'
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                }`}
              >
                Demand Forecast
              </button>
              <button
                onClick={() => setReportType('geographic-performance')}
                className={`px-3 py-1 rounded-md text-sm ${
                  reportType === 'geographic-performance'
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                }`}
              >
                Geographic Perf
              </button>
              <button
                onClick={() => setReportType('seasonal-trends')}
                className={`px-3 py-1 rounded-md text-sm ${
                  reportType === 'seasonal-trends'
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                }`}
              >
                Seasonal Trends
              </button>
              <button
                onClick={() => setReportType('market-saturation')}
                className={`px-3 py-1 rounded-md text-sm ${
                  reportType === 'market-saturation'
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                }`}
              >
                Market Saturation
              </button>
            </div>
          )}

          {/* E-commerce & Shopify Report Types */}
          {appType === 'ecommerce' && (
            <div className="flex flex-wrap gap-2 mb-6">
              <button
                onClick={() => setReportType('sales-dashboard')}
                className={`px-3 py-1 rounded-md text-sm ${
                  reportType === 'sales-dashboard'
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                }`}
              >
                Sales Dashboard
              </button>
              <button
                onClick={() => setReportType('product-performance')}
                className={`px-3 py-1 rounded-md text-sm ${
                  reportType === 'product-performance'
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                }`}
              >
                Product Performance
              </button>
              <button
                onClick={() => setReportType('customer-lifetime-value')}
                className={`px-3 py-1 rounded-md text-sm ${
                  reportType === 'customer-lifetime-value'
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                }`}
              >
                Customer Lifetime Value
              </button>
              <button
                onClick={() => setReportType('seasonal-sales-analysis')}
                className={`px-3 py-1 rounded-md text-sm ${
                  reportType === 'seasonal-sales-analysis'
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                }`}
              >
                Seasonal Analysis
              </button>
              <button
                onClick={() => setReportType('sales-channel-performance')}
                className={`px-3 py-1 rounded-md text-sm ${
                  reportType === 'sales-channel-performance'
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                }`}
              >
                Sales Channel Perf
              </button>
              <button
                onClick={() => setReportType('stock-level-reports')}
                className={`px-3 py-1 rounded-md text-sm ${
                  reportType === 'stock-level-reports'
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                }`}
              >
                Stock Levels
              </button>
              <button
                onClick={() => setReportType('inventory-turnover')}
                className={`px-3 py-1 rounded-md text-sm ${
                  reportType === 'inventory-turnover'
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                }`}
              >
                Inventory Turnover
              </button>
              <button
                onClick={() => setReportType('supplier-performance')}
                className={`px-3 py-1 rounded-md text-sm ${
                  reportType === 'supplier-performance'
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                }`}
              >
                Supplier Performance
              </button>
              <button
                onClick={() => setReportType('dead-stock-analysis')}
                className={`px-3 py-1 rounded-md text-sm ${
                  reportType === 'dead-stock-analysis'
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                }`}
              >
                Dead Stock Analysis
              </button>
              <button
                onClick={() => setReportType('demand-forecasting')}
                className={`px-3 py-1 rounded-md text-sm ${
                  reportType === 'demand-forecasting'
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                }`}
              >
                Demand Forecasting
              </button>
              <button
                onClick={() => setReportType('conversion-funnel')}
                className={`px-3 py-1 rounded-md text-sm ${
                  reportType === 'conversion-funnel'
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                }`}
              >
                Conversion Funnel
              </button>
              <button
                onClick={() => setReportType('marketing-roi')}
                className={`px-3 py-1 rounded-md text-sm ${
                  reportType === 'marketing-roi'
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                }`}
              >
                Marketing ROI
              </button>
              <button
                onClick={() => setReportType('customer-segmentation')}
                className={`px-3 py-1 rounded-md text-sm ${
                  reportType === 'customer-segmentation'
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                }`}
              >
                Customer Segmentation
              </button>
              <button
                onClick={() => setReportType('email-marketing')}
                className={`px-3 py-1 rounded-md text-sm ${
                  reportType === 'email-marketing'
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                }`}
              >
                Email Marketing
              </button>
              <button
                onClick={() => setReportType('seo-performance')}
                className={`px-3 py-1 rounded-md text-sm ${
                  reportType === 'seo-performance'
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                }`}
              >
                SEO Performance
              </button>
              <button
                onClick={() => setReportType('profit-margins')}
                className={`px-3 py-1 rounded-md text-sm ${
                  reportType === 'profit-margins'
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                }`}
              >
                Profit Margins
              </button>
              <button
                onClick={() => setReportType('shipping-analytics')}
                className={`px-3 py-1 rounded-md text-sm ${
                  reportType === 'shipping-analytics'
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                }`}
              >
                Shipping Analytics
              </button>
              <button
                onClick={() => setReportType('return-refund-analysis')}
                className={`px-3 py-1 rounded-md text-sm ${
                  reportType === 'return-refund-analysis'
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                }`}
              >
                Return/Refund Analysis
              </button>
              <button
                onClick={() => setReportType('tax-compliance')}
                className={`px-3 py-1 rounded-md text-sm ${
                  reportType === 'tax-compliance'
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                }`}
              >
                Tax Compliance
              </button>
              <button
                onClick={() => setReportType('payment-processing')}
                className={`px-3 py-1 rounded-md text-sm ${
                  reportType === 'payment-processing'
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                }`}
              >
                Payment Processing
              </button>
            </div>
          )}

          {loading ? (
            <div className="flex justify-center items-center h-64">
              <div className="animate-spin rounded-full h-32 w-32 border-b-2 border-blue-600"></div>
            </div>
          ) : error ? (
            <div className="mt-6">
              <div className="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <strong className="font-bold">Error: </strong>
                <span className="block sm:inline">{error}</span>
              </div>
              <div className="mt-4 text-center">
                <button
                  onClick={getReportData}
                  className="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md"
                >
                  Retry
                </button>
              </div>
            </div>
          ) : (
            <div className="mt-6">
              <h2 className="text-xl font-semibold mb-4">{getReportTitle()}</h2>
              {renderChart()}
            </div>
          )}
        </div>

        {/* Summary Cards */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {/* Summary cards based on current report type */}
          {appType === 'food-delivery' && reportType === 'daily-sales' && (
            <>
              <div className="bg-white rounded-lg shadow-md p-6">
                <h3 className="text-lg font-semibold mb-4">Sales Summary</h3>
                <div className="space-y-2">
                  <p><span className="font-medium">Total Revenue:</span> $24,500</p>
                  <p><span className="font-medium">Total Orders:</span> 324</p>
                  <p><span className="font-medium">Avg. Order Value:</span> $75.62</p>
                  <p><span className="font-medium">Growth:</span> <span className="text-green-600">+12.5%</span></p>
                </div>
              </div>
              <div className="bg-white rounded-lg shadow-md p-6">
                <h3 className="text-lg font-semibold mb-4">Top Selling Items</h3>
                <div className="space-y-2">
                  <p><span className="font-medium">Veg Burger:</span> 1,200 sold</p>
                  <p><span className="font-medium">Soda:</span> 1,100 sold</p>
                  <p><span className="font-medium">Chicken Wrap:</span> 980 sold</p>
                  <p><span className="font-medium">Fries:</span> 750 sold</p>
                </div>
              </div>
              <div className="bg-white rounded-lg shadow-md p-6">
                <h3 className="text-lg font-semibold mb-4">Peak Hours</h3>
                <div className="space-y-2">
                  <p><span className="font-medium">Lunch Rush:</span> 12pm - 2pm</p>
                  <p><span className="font-medium">Dinner Rush:</span> 6pm - 8pm</p>
                  <p><span className="font-medium">Weekend Peak:</span> 11am - 3pm</p>
                  <p><span className="font-medium">Best Day:</span> Friday</p>
                </div>
              </div>
            </>
          )}

          {appType === 'food-delivery' && reportType === 'delivery-performance' && (
            <>
              <div className="bg-white rounded-lg shadow-md p-6">
                <h3 className="text-lg font-semibold mb-4">Delivery Metrics</h3>
                <div className="space-y-2">
                  <p><span className="font-medium">On-time Rate:</span> 94.2%</p>
                  <p><span className="font-medium">Avg. Delivery Time:</span> 32 min</p>
                  <p><span className="font-medium">Total Deliveries:</span> 278</p>
                  <p><span className="font-medium">Success Rate:</span> <span className="text-green-600">98.5%</span></p>
                </div>
              </div>
              <div className="bg-white rounded-lg shadow-md p-6">
                <h3 className="text-lg font-semibold mb-4">Top Performing Areas</h3>
                <div className="space-y-2">
                  <p><span className="font-medium">Downtown:</span> 65 deliveries</p>
                  <p><span className="font-medium">Uptown:</span> 58 deliveries</p>
                  <p><span className="font-medium">Midtown:</span> 52 deliveries</p>
                  <p><span className="font-medium">Suburbs:</span> 44 deliveries</p>
                </div>
              </div>
              <div className="bg-white rounded-lg shadow-md p-6">
                <h3 className="text-lg font-semibold mb-4">Customer Satisfaction</h3>
                <div className="space-y-2">
                  <p><span className="font-medium">Avg. Rating:</span> 4.6/5.0</p>
                  <p><span className="font-medium">Positive Feedback:</span> 85%</p>
                  <p><span className="font-medium">Common Issues:</span> Late delivery</p>
                  <p><span className="font-medium">Resolution Rate:</span> 92%</p>
                </div>
              </div>
            </>
          )}

          {/* Classified App Financial Reports with IFRS compliance */}
          {appType === 'classified' && reportType === 'subscription-analytics' && (
            <>
              <div className="bg-white rounded-lg shadow-md p-6">
                <h3 className="text-lg font-semibold mb-4">Subscription Revenue (IFRS 15 Compliant)</h3>
                <div className="space-y-2">
                  <p><span className="font-medium">Total Subscribers:</span> 2,200</p>
                  <p><span className="font-medium">Monthly Recurring Revenue:</span> $42,500.00</p>
                  <p><span className="font-medium">Annual Recurring Revenue:</span> $510,000.00</p>
                  <p><span className="font-medium">Average Revenue Per User:</span> $19.32</p>
                  <p><span className="font-medium">Subscription Revenue %:</span> <span className="text-green-600">78.5%</span></p>
                </div>
              </div>
              <div className="bg-white rounded-lg shadow-md p-6">
                <h3 className="text-lg font-semibold mb-4">Revenue Recognition</h3>
                <div className="space-y-2">
                  <p><span className="font-medium">Deferred Revenue:</span> $18,450.00</p>
                  <p><span className="font-medium">Recognized Revenue:</span> $38,200.00</p>
                  <p><span className="font-medium">Contract Assets:</span> $3,200.00</p>
                  <p><span className="font-medium">Performance Obligations:</span> $12,600.00</p>
                </div>
              </div>
              <div className="bg-white rounded-lg shadow-md p-6">
                <h3 className="text-lg font-semibold mb-4">IFRS Compliance Metrics</h3>
                <div className="space-y-2">
                  <p><span className="font-medium">IFRS 15 Compliance:</span> <span className="text-green-600">Yes</span></p>
                  <p><span className="font-medium">ASC 606 Compliance:</span> <span className="text-green-600">Yes</span></p>
                  <p><span className="font-medium">Revenue Recognition Timing:</span> <span className="text-green-600">Accurate</span></p>
                  <p><span className="font-medium">Contract Account.</span> <span className="text-green-600">Systematic</span></p>
                </div>
              </div>
            </>
          )}

          {appType === 'classified' && reportType === 'cost-acquisition' && (
            <>
              <div className="bg-white rounded-lg shadow-md p-6">
                <h3 className="text-lg font-semibold mb-4">Customer Acquisition Cost (IFRS Compliant)</h3>
                <div className="space-y-2">
                  <p><span className="font-medium">Total Marketing Spend:</span> $24,500.00</p>
                  <p><span className="font-medium">New Customers Acquired:</span> 450</p>
                  <p><span className="font-medium">Average CAC:</span> $54.44</p>
                  <p><span className="font-medium">CAC to LTV Ratio:</span> 1:3.2</p>
                  <p><span className="font-medium">Marketing ROI:</span> <span className="text-green-600">320%</span></p>
                </div>
              </div>
              <div className="bg-white rounded-lg shadow-md p-6">
                <h3 className="text-lg font-semibold mb-4">Channel Effectiveness</h3>
                <div className="space-y-2">
                  <p><span className="font-medium">Social Media CAC:</span> $42.50</p>
                  <p><span className="font-medium">Search Ads CAC:</span> $68.20</p>
                  <p><span className="font-medium">Referral CAC:</span> $18.75</p>
                  <p><span className="font-medium">Organic Acquisition:</span> $0.00</p>
                </div>
              </div>
              <div className="bg-white rounded-lg shadow-md p-6">
                <h3 className="text-lg font-semibold mb-4">IFRS Compliance Metrics</h3>
                <div className="space-y-2">
                  <p><span className="font-medium">Customer Asset Recognition:</span> <span className="text-green-600">IFRS Compliant</span></p>
                  <p><span className="font-medium">Marketing Expense Classification:</span> <span className="text-green-600">Accurate</span></p>
                  <p><span className="font-medium">Acquisition Cost Capitalization:</span> <span className="text-green-600">Appropriate</span></p>
                  <p><span className="font-medium">Amortization Period:</span> <span className="text-green-600">Systematic</span></p>
                </div>
              </div>
            </>
          )}

          {appType === 'classified' && reportType === 'user-registration' && (
            <>
              <div className="bg-white rounded-lg shadow-md p-6">
                <h3 className="text-lg font-semibold mb-4">User Growth</h3>
                <div className="space-y-2">
                  <p><span className="font-medium">Total Users:</span> 15,240</p>
                  <p><span className="font-medium">Monthly Growth:</span> 12.5%</p>
                  <p><span className="font-medium">Active Users:</span> 8,450</p>
                  <p><span className="font-medium">New Signups:</span> <span className="text-green-600">+220</span></p>
                </div>
              </div>
              <div className="bg-white rounded-lg shadow-md p-6">
                <h3 className="text-lg font-semibold mb-4">Registration Sources</h3>
                <div className="space-y-2">
                  <p><span className="font-medium">Organic:</span> 35%</p>
                  <p><span className="font-medium">Social Media:</span> 28%</p>
                  <p><span className="font-medium">Google Ads:</span> 22%</p>
                  <p><span className="font-medium">Referrals:</span> 15%</p>
                </div>
              </div>
              <div className="bg-white rounded-lg shadow-md p-6">
                <h3 className="text-lg font-semibold mb-4">User Demographics</h3>
                <div className="space-y-2">
                  <p><span className="font-medium">Age 18-25:</span> 28%</p>
                  <p><span className="font-medium">Age 26-35:</span> 35%</p>
                  <p><span className="font-medium">Age 36-45:</span> 25%</p>
                  <p><span className="font-medium">Above 45:</span> 12%</p>
                </div>
              </div>
            </>
          )}

          {/* Financial reports summary cards with IFRS compliance */}
          {appType === 'food-delivery' && reportType === 'cost-goods' && (
            <>
              <div className="bg-white rounded-lg shadow-md p-6">
                <h3 className="text-lg font-semibold mb-4">Cost of Goods Sold (IFRS Compliant)</h3>
                <div className="space-y-2">
                  <p><span className="font-medium">Direct Materials:</span> $12,450.00</p>
                  <p><span className="font-medium">Direct Labor:</span> $8,230.50</p>
                  <p><span className="font-medium">Manufacturing Overhead:</span> $3,680.25</p>
                  <p><span className="font-medium">Total COGS:</span> $24,360.75</p>
                  <p><span className="font-medium">COGS % of Revenue:</span> <span className="text-red-600">32.4%</span></p>
                </div>
              </div>
              <div className="bg-white rounded-lg shadow-md p-6">
                <h3 className="text-lg font-semibold mb-4">Inventory Valuation</h3>
                <div className="space-y-2">
                  <p><span className="font-medium">Beginning Inventory:</span> $4,200.00</p>
                  <p><span className="font-medium">Purchases:</span> $25,600.00</p>
                  <p><span className="font-medium">Ending Inventory:</span> $3,850.00</p>
                  <p><span className="font-medium">Inventory Turnover:</span> 6.4x</p>
                </div>
              </div>
              <div className="bg-white rounded-lg shadow-md p-6">
                <h3 className="text-lg font-semibold mb-4">IFRS Compliance Metrics</h3>
                <div className="space-y-2">
                  <p><span className="font-medium">ASC 606 Compliance:</span> <span className="text-green-600">Yes</span></p>
                  <p><span className="font-medium">IFRS 15 Compliance:</span> <span className="text-green-600">Yes</span></p>
                  <p><span className="font-medium">ASC 330 Compliance:</span> <span className="text-green-600">Yes</span></p>
                  <p><span className="font-medium">IFRS 9 Compliance:</span> <span className="text-green-600">Yes</span></p>
                </div>
              </div>
            </>
          )}

          {appType === 'food-delivery' && reportType === 'profit-margins' && (
            <>
              <div className="bg-white rounded-lg shadow-md p-6">
                <h3 className="text-lg font-semibold mb-4">Profitability Analysis (IFRS Compliant)</h3>
                <div className="space-y-2">
                  <p><span className="font-medium">Gross Profit:</span> $28,450.00</p>
                  <p><span className="font-medium">Gross Margin:</span> <span className="text-green-600">42.5%</span></p>
                  <p><span className="font-medium">Operating Profit:</span> $18,230.00</p>
                  <p><span className="font-medium">Operating Margin:</span> <span className="text-green-600">27.2%</span></p>
                  <p><span className="font-medium">Net Profit:</span> $12,680.00</p>
                  <p><span className="font-medium">Net Margin:</span> <span className="text-green-600">18.9%</span></p>
                </div>
              </div>
              <div className="bg-white rounded-lg shadow-md p-6">
                <h3 className="text-lg font-semibold mb-4">Revenue Recognition</h3>
                <div className="space-y-2">
                  <p><span className="font-medium">Revenue (Net of Discounts):</span> $66,900.00</p>
                  <p><span className="font-medium">Sales Returns & Allowances:</span> $1,200.00</p>
                  <p><span className="font-medium">Performance Obligations:</span> $3,400.00</p>
                  <p><span className="font-medium">Contract Assets:</span> $850.00</p>
                </div>
              </div>
              <div className="bg-white rounded-lg shadow-md p-6">
                <h3 className="text-lg font-semibold mb-4">IFRS Compliance Metrics</h3>
                <div className="space-y-2">
                  <p><span className="font-medium">ASC 606 Compliance:</span> <span className="text-green-600">Yes</span></p>
                  <p><span className="font-medium">IFRS 15 Compliance:</span> <span className="text-green-600">Yes</span></p>
                  <p><span className="font-medium">Revenue Recognition:</span> <span className="text-green-600">Accurate</span></p>
                  <p><span className="font-medium">Profit Recognition Timing:</span> <span className="text-green-600">Compliant</span></p>
                </div>
              </div>
            </>
          )}

          {appType === 'food-delivery' && reportType === 'break-even' && (
            <>
              <div className="bg-white rounded-lg shadow-md p-6">
                <h3 className="text-lg font-semibold mb-4">Break-even Analysis (IFRS Compliant)</h3>
                <div className="space-y-2">
                  <p><span className="font-medium">Fixed Costs:</span> $15,240.00</p>
                  <p><span className="font-medium">Variable Cost Ratio:</span> 35.2%</p>
                  <p><span className="font-medium">Contribution Margin:</span> 64.8%</p>
                  <p><span className="font-medium">Break-even Revenue:</span> $42,100.00</p>
                  <p><span className="font-medium">Margin of Safety:</span> <span className="text-green-600">37.8%</span></p>
                </div>
              </div>
              <div className="bg-white rounded-lg shadow-md p-6">
                <h3 className="text-lg font-semibold mb-4">Cost Structure Analysis</h3>
                <div className="space-y-2">
                  <p><span className="font-medium">Total Costs:</span> $43,560.00</p>
                  <p><span className="font-medium">Fixed Costs %:</span> 35.0%</p>
                  <p><span className="font-medium">Variable Costs %:</span> 65.0%</p>
                  <p><span className="font-medium">Operating Leverage:</span> 1.8x</p>
                </div>
              </div>
              <div className="bg-white rounded-lg shadow-md p-6">
                <h3 className="text-lg font-semibold mb-4">IFRS Compliance Metrics</h3>
                <div className="space-y-2">
                  <p><span className="font-medium">Cost Classification (IFRS):</span> <span className="text-green-600">Compliant</span></p>
                  <p><span className="font-medium">Expense Recognition:</span> <span className="text-green-600">Accurate</span></p>
                  <p><span className="font-medium">Cost Allocation Method:</span> <span className="text-green-600">Systematic</span></p>
                  <p><span className="font-medium">Management Accounting:</span> <span className="text-green-600">Aligned</span></p>
                </div>
              </div>
            </>
          )}

          {appType === 'food-delivery' && reportType === 'waste-loss' && (
            <>
              <div className="bg-white rounded-lg shadow-md p-6">
                <h3 className="text-lg font-semibold mb-4">Waste & Loss Analysis (IFRS Compliant)</h3>
                <div className="space-y-2">
                  <p><span className="font-medium">Food Waste Cost:</span> $1,850.00</p>
                  <p><span className="font-medium">Equipment Damage:</span> $420.00</p>
                  <p><span className="font-medium">Shrinkage Loss:</span> $280.00</p>
                  <p><span className="font-medium">Total Waste & Loss:</span> $2,550.00</p>
                  <p><span className="font-medium">Loss % of Revenue:</span> <span className="text-red-600">3.8%</span></p>
                </div>
              </div>
              <div className="bg-white rounded-lg shadow-md p-6">
                <h3 className="text-lg font-semibold mb-4">Loss Mitigation</h3>
                <div className="space-y-2">
                  <p><span className="font-medium">Prevention Measures:</span> $320.00</p>
                  <p><span className="font-medium">Insurance Recoveries:</span> $150.00</p>
                  <p><span className="font-medium">Net Loss Impact:</span> $2,280.00</p>
                  <p><span className="font-medium">Reduction vs Prior Period:</span> <span className="text-green-600">12.4%</span></p>
                </div>
              </div>
              <div className="bg-white rounded-lg shadow-md p-6">
                <h3 className="text-lg font-semibold mb-4">IFRS Compliance Metrics</h3>
                <div className="space-y-2">
                  <p><span className="font-medium">Impairment Loss Recognition:</span> <span className="text-green-600">Compliant</span></p>
                  <p><span className="font-medium">Asset Valuation:</span> <span className="text-green-600">Accurate</span></p>
                  <p><span className="font-medium">Reserve Calculations:</span> <span className="text-green-600">Conservative</span></p>
                  <p><span className="font-medium">Disclosures:</span> <span className="text-green-600">Complete</span></p>
                </div>
              </div>
            </>
          )}

          {appType === 'classified' && reportType === 'listing-performance' && (
            <>
              <div className="bg-white rounded-lg shadow-md p-6">
                <h3 className="text-lg font-semibold mb-4">Listing Metrics</h3>
                <div className="space-y-2">
                  <p><span className="font-medium">Total Listings:</span> 24,560</p>
                  <p><span className="font-medium">Views:</span> 425,000</p>
                  <p><span className="font-medium">Clicks:</span> 85,230</p>
                  <p><span className="font-medium">Conversion Rate:</span> 12.4%</p>
                </div>
              </div>
              <div className="bg-white rounded-lg shadow-md p-6">
                <h3 className="text-lg font-semibold mb-4">Top Categories</h3>
                <div className="space-y-2">
                  <p><span className="font-medium">Electronics:</span> 3,200 listings</p>
                  <p><span className="font-medium">Vehicles:</span> 2,850 listings</p>
                  <p><span className="font-medium">Real Estate:</span> 1,950 listings</p>
                  <p><span className="font-medium">Jobs:</span> 1,650 listings</p>
                </div>
              </div>
              <div className="bg-white rounded-lg shadow-md p-6">
                <h3 className="text-lg font-semibold mb-4">Engagement Stats</h3>
                <div className="space-y-2">
                  <p><span className="font-medium">Avg. Response Time:</span> 4.2 hrs</p>
                  <p><span className="font-medium">Messages Sent:</span> 18,340</p>
                  <p><span className="font-medium">Favorites:</span> 36,520</p>
                  <p><span className="font-medium">Share Rate:</span> 8.5%</p>
                </div>
              </div>
            </>
          )}
        </div>

        {/* Export options */}
        <div className="mt-6 bg-white rounded-lg shadow-md p-6">
          <h3 className="text-lg font-semibold mb-4">Export Reports</h3>
          <div className="flex flex-wrap gap-4">
            <button className="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md">
              Export to PDF
            </button>
            <button className="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
              Export to Excel
            </button>
            <button className="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-md">
              Export to CSV
            </button>
          </div>
        </div>
      </div>
    </div>
  );
};

export default ReportsDashboard;