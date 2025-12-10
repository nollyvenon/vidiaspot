import apiClient from './api';

const deliveryService = {
  // Delivery Performance Reports
  getOnTimeDeliveryReports: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/delivery/on-time', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching on-time delivery reports:', error);
      throw error;
    }
  },

  getAverageDeliveryTimes: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/delivery/average-times', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching average delivery times:', error);
      throw error;
    }
  },

  getFailedDeliveryReports: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/delivery/failed', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching failed delivery reports:', error);
      throw error;
    }
  },

  getPackageHandlingReports: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/delivery/handling', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching package handling reports:', error);
      throw error;
    }
  },

  getCustomerSatisfactionReports: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/delivery/satisfaction', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching customer satisfaction reports:', error);
      throw error;
    }
  },

  // Financial Reports
  getRevenueReports: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/financial/revenue', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching revenue reports:', error);
      throw error;
    }
  },

  getCostPerDeliveryReports: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/financial/cost-per-delivery', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching cost per delivery reports:', error);
      throw error;
    }
  },

  getProfitMarginReports: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/financial/profit-margins', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching profit margin reports:', error);
      throw error;
    }
  },

  getOutstandingInvoiceReports: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/financial/outstanding-invoices', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching outstanding invoice reports:', error);
      throw error;
    }
  },

  getExpenseBreakdownReports: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/financial/expense-breakdown', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching expense breakdown reports:', error);
      throw error;
    }
  },

  getRevenueForecastReports: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/financial/revenue-forecast', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching revenue forecast reports:', error);
      throw error;
    }
  },

  // Route Efficiency Reports
  getDistanceVsOptimalReports: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/route/distance-vs-optimal', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching distance vs optimal reports:', error);
      throw error;
    }
  },

  getFuelConsumptionReports: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/route/fuel-consumption', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching fuel consumption reports:', error);
      throw error;
    }
  },

  getDriverPerformanceReports: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/route/driver-performance', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching driver performance reports:', error);
      throw error;
    }
  },

  getRouteProfitabilityReports: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/route/profitability', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching route profitability reports:', error);
      throw error;
    }
  },

  getTimeUtilizationReports: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/route/time-utilization', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching time utilization reports:', error);
      throw error;
    }
  },

  getTrafficPatternReports: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/route/traffic-patterns', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching traffic pattern reports:', error);
      throw error;
    }
  },

  // Client Management Reports
  getClientActivityTrends: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/client/activity-trends', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching client activity trends:', error);
      throw error;
    }
  },

  getSLAComplianceReports: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/client/sla-compliance', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching SLA compliance reports:', error);
      throw error;
    }
  },

  getClientRetentionReports: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/client/retention', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching client retention reports:', error);
      throw error;
    }
  },

  getRevenuePerClientReports: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/client/revenue-per-client', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching revenue per client reports:', error);
      throw error;
    }
  },

  getDeliveryFrequencyReports: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/client/delivery-frequency', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching delivery frequency reports:', error);
      throw error;
    }
  },

  getClientPerformanceScorecards: async (params = {}) => {
    try {
      const response = await apiClient.get('/reports/client/performance-scorecards', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching client performance scorecards:', error);
      throw error;
    }
  }
};

export default deliveryService;