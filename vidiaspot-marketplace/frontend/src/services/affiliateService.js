import apiClient from './api';

const affiliateService = {
  // Get affiliate program settings
  getAffiliateSettings: async () => {
    try {
      const response = await apiClient.get('/affiliate/settings');
      return response.data;
    } catch (error) {
      console.error('Error fetching affiliate settings:', error);
      throw error;
    }
  },

  // Update affiliate program settings
  updateAffiliateSettings: async (settings) => {
    try {
      const response = await apiClient.put('/affiliate/settings', settings);
      return response.data;
    } catch (error) {
      console.error('Error updating affiliate settings:', error);
      throw error;
    }
  },

  // Get affiliate commission rates
  getCommissionRates: async () => {
    try {
      const response = await apiClient.get('/affiliate/commission-rates');
      return response.data;
    } catch (error) {
      console.error('Error fetching commission rates:', error);
      throw error;
    }
  },

  // Update affiliate commission rates
  updateCommissionRates: async (rates) => {
    try {
      const response = await apiClient.put('/affiliate/commission-rates', rates);
      return response.data;
    } catch (error) {
      console.error('Error updating commission rates:', error);
      throw error;
    }
  },

  // Get user's affiliate status
  getAffiliateStatus: async (userId) => {
    try {
      const response = await apiClient.get(`/affiliate/users/${userId}/status`);
      return response.data;
    } catch (error) {
      console.error('Error fetching affiliate status:', error);
      throw error;
    }
  },

  // Apply to become an affiliate
  applyForAffiliate: async (affiliateData) => {
    try {
      const response = await apiClient.post('/affiliate/apply', affiliateData);
      return response.data;
    } catch (error) {
      console.error('Error applying for affiliate:', error);
      throw error;
    }
  },

  // Get user's affiliate dashboard data
  getAffiliateDashboard: async (userId) => {
    try {
      const response = await apiClient.get(`/affiliate/users/${userId}/dashboard`);
      return response.data;
    } catch (error) {
      console.error('Error fetching affiliate dashboard:', error);
      throw error;
    }
  },

  // Get user's referral code
  getReferralCode: async (userId) => {
    try {
      const response = await apiClient.get(`/affiliate/users/${userId}/referral-code`);
      return response.data;
    } catch (error) {
      console.error('Error fetching referral code:', error);
      throw error;
    }
  },

  // Generate new referral code
  generateReferralCode: async (userId) => {
    try {
      const response = await apiClient.post(`/affiliate/users/${userId}/referral-code`);
      return response.data;
    } catch (error) {
      console.error('Error generating referral code:', error);
      throw error;
    }
  },

  // Get affiliate referrals
  getAffiliateReferrals: async (userId, params = {}) => {
    try {
      const response = await apiClient.get(`/affiliate/users/${userId}/referrals`, { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching affiliate referrals:', error);
      throw error;
    }
  },

  // Get affiliate commissions
  getAffiliateCommissions: async (userId, params = {}) => {
    try {
      const response = await apiClient.get(`/affiliate/users/${userId}/commissions`, { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching affiliate commissions:', error);
      throw error;
    }
  },

  // Get affiliate payouts
  getAffiliatePayouts: async (userId, params = {}) => {
    try {
      const response = await apiClient.get(`/affiliate/users/${userId}/payouts`, { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching affiliate payouts:', error);
      throw error;
    }
  },

  // Request affiliate payout
  requestPayout: async (payoutData) => {
    try {
      const response = await apiClient.post('/affiliate/payouts', payoutData);
      return response.data;
    } catch (error) {
      console.error('Error requesting payout:', error);
      throw error;
    }
  },

  // Track affiliate referral
  trackReferral: async (referralData) => {
    try {
      const response = await apiClient.post('/affiliate/track-referral', referralData);
      return response.data;
    } catch (error) {
      console.error('Error tracking referral:', error);
      throw error;
    }
  },

  // Get affiliate performance metrics
  getPerformanceMetrics: async (userId, params = {}) => {
    try {
      const response = await apiClient.get(`/affiliate/users/${userId}/performance`, { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching performance metrics:', error);
      throw error;
    }
  },

  // Get affiliate leaderboard
  getLeaderboard: async (params = {}) => {
    try {
      const response = await apiClient.get('/affiliate/leaderboard', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching leaderboard:', error);
      throw error;
    }
  },

  // Get affiliate marketing materials
  getMarketingMaterials: async () => {
    try {
      const response = await apiClient.get('/affiliate/marketing-materials');
      return response.data;
    } catch (error) {
      console.error('Error fetching marketing materials:', error);
      throw error;
    }
  },

  // Validate referral code
  validateReferralCode: async (referralCode) => {
    try {
      const response = await apiClient.post('/affiliate/validate-code', { code: referralCode });
      return response.data;
    } catch (error) {
      console.error('Error validating referral code:', error);
      throw error;
    }
  },

  // Get MLM settings
  getMLMSettings: async () => {
    try {
      const response = await apiClient.get('/affiliate/mlm-settings');
      return response.data;
    } catch (error) {
      console.error('Error fetching MLM settings:', error);
      throw error;
    }
  },

  // Update MLM settings
  updateMLMSettings: async (settings) => {
    try {
      const response = await apiClient.put('/affiliate/mlm-settings', settings);
      return response.data;
    } catch (error) {
      console.error('Error updating MLM settings:', error);
      throw error;
    }
  },

  // Get user's downline
  getUserDownline: async (userId, params = {}) => {
    try {
      const response = await apiClient.get(`/affiliate/users/${userId}/downline`, { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching user downline:', error);
      throw error;
    }
  },

  // Get user's MLM commissions
  getMLMCommissions: async (userId, params = {}) => {
    try {
      const response = await apiClient.get(`/affiliate/users/${userId}/mlm-commissions`, { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching MLM commissions:', error);
      throw error;
    }
  },

  // Get MLM commission structure
  getCommissionStructure: async () => {
    try {
      const response = await apiClient.get('/affiliate/mlm-commission-structure');
      return response.data;
    } catch (error) {
      console.error('Error fetching MLM commission structure:', error);
      throw error;
    }
  },

  // Update MLM commission structure
  updateCommissionStructure: async (structure) => {
    try {
      const response = await apiClient.put('/affiliate/mlm-commission-structure', structure);
      return response.data;
    } catch (error) {
      console.error('Error updating MLM commission structure:', error);
      throw error;
    }
  },

  // Calculate MLM commissions for a transaction
  calculateMLMCommissions: async (transactionData) => {
    try {
      const response = await apiClient.post('/affiliate/calculate-mlm-commissions', transactionData);
      return response.data;
    } catch (error) {
      console.error('Error calculating MLM commissions:', error);
      throw error;
    }
  },

  // Get affiliate tiers and levels
  getAffiliateTiers: async () => {
    try {
      const response = await apiClient.get('/affiliate/tiers');
      return response.data;
    } catch (error) {
      console.error('Error fetching affiliate tiers:', error);
      throw error;
    }
  },

  // Update affiliate tiers
  updateAffiliateTiers: async (tiers) => {
    try {
      const response = await apiClient.put('/affiliate/tiers', tiers);
      return response.data;
    } catch (error) {
      console.error('Error updating affiliate tiers:', error);
      throw error;
    }
  },

  // Get commission history for admin
  getCommissionHistory: async (params = {}) => {
    try {
      const response = await apiClient.get('/affiliate/commission-history', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching commission history:', error);
      throw error;
    }
  },

  // Get payout history for admin
  getPayoutHistory: async (params = {}) => {
    try {
      const response = await apiClient.get('/affiliate/payout-history', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching payout history:', error);
      throw error;
    }
  },

  // Admin functions
  getAllAffiliates: async (params = {}) => {
    try {
      const response = await apiClient.get('/affiliate/admin/affiliates', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching all affiliates:', error);
      throw error;
    }
  },

  approveAffiliate: async (affiliateId, approvalData) => {
    try {
      const response = await apiClient.put(`/affiliate/admin/affiliates/${affiliateId}/approve`, approvalData);
      return response.data;
    } catch (error) {
      console.error('Error approving affiliate:', error);
      throw error;
    }
  },

  rejectAffiliate: async (affiliateId, rejectionData) => {
    try {
      const response = await apiClient.put(`/affiliate/admin/affiliates/${affiliateId}/reject`, rejectionData);
      return response.data;
    } catch (error) {
      console.error('Error rejecting affiliate:', error);
      throw error;
    }
  },

  updateAffiliateStatus: async (affiliateId, statusData) => {
    try {
      const response = await apiClient.put(`/affiliate/admin/affiliates/${affiliateId}/status`, statusData);
      return response.data;
    } catch (error) {
      console.error('Error updating affiliate status:', error);
      throw error;
    }
  }
};

export default affiliateService;