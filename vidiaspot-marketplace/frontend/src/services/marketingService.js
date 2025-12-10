import apiClient from './api';

const marketingService = {
  // Email Marketing
  getEmailCampaigns: async (params = {}) => {
    try {
      const response = await apiClient.get('/marketing/email-campaigns', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching email campaigns:', error);
      throw error;
    }
  },

  createEmailCampaign: async (campaignData) => {
    try {
      const response = await apiClient.post('/marketing/email-campaigns', campaignData);
      return response.data;
    } catch (error) {
      console.error('Error creating email campaign:', error);
      throw error;
    }
  },

  sendEmailCampaign: async (campaignId) => {
    try {
      const response = await apiClient.post(`/marketing/email-campaigns/${campaignId}/send`);
      return response.data;
    } catch (error) {
      console.error('Error sending email campaign:', error);
      throw error;
    }
  },

  getSubscribers: async (params = {}) => {
    try {
      const response = await apiClient.get('/marketing/subscribers', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching subscribers:', error);
      throw error;
    }
  },

  // Abandoned Cart Recovery
  getAbandonedCarts: async (params = {}) => {
    try {
      const response = await apiClient.get('/marketing/abandoned-carts', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching abandoned carts:', error);
      throw error;
    }
  },

  createAbandonedCartRecovery: async (recoveryData) => {
    try {
      const response = await apiClient.post('/marketing/abandoned-cart-recovery', recoveryData);
      return response.data;
    } catch (error) {
      console.error('Error creating abandoned cart recovery sequence:', error);
      throw error;
    }
  },

  sendAbandonedCartRecovery: async (cartId) => {
    try {
      const response = await apiClient.post(`/marketing/abandoned-carts/${cartId}/recover`);
      return response.data;
    } catch (error) {
      console.error('Error sending abandoned cart recovery:', error);
      throw error;
    }
  },

  // Promotional Tools
  getDiscountCodes: async (params = {}) => {
    try {
      const response = await apiClient.get('/marketing/discount-codes', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching discount codes:', error);
      throw error;
    }
  },

  createDiscountCode: async (discountData) => {
    try {
      const response = await apiClient.post('/marketing/discount-codes', discountData);
      return response.data;
    } catch (error) {
      console.error('Error creating discount code:', error);
      throw error;
    }
  },

  updateDiscountCode: async (codeId, discountData) => {
    try {
      const response = await apiClient.put(`/marketing/discount-codes/${codeId}`, discountData);
      return response.data;
    } catch (error) {
      console.error('Error updating discount code:', error);
      throw error;
    }
  },

  deleteDiscountCode: async (codeId) => {
    try {
      const response = await apiClient.delete(`/marketing/discount-codes/${codeId}`);
      return response.data;
    } catch (error) {
      console.error('Error deleting discount code:', error);
      throw error;
    }
  },

  getGiftCards: async (params = {}) => {
    try {
      const response = await apiClient.get('/marketing/gift-cards', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching gift cards:', error);
      throw error;
    }
  },

  createGiftCard: async (giftCardData) => {
    try {
      const response = await apiClient.post('/marketing/gift-cards', giftCardData);
      return response.data;
    } catch (error) {
      console.error('Error creating gift card:', error);
      throw error;
    }
  },

  // Customer Reviews & Ratings
  getReviews: async (params = {}) => {
    try {
      const response = await apiClient.get('/marketing/reviews', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching reviews:', error);
      throw error;
    }
  },

  createReview: async (reviewData) => {
    try {
      const response = await apiClient.post('/marketing/reviews', reviewData);
      return response.data;
    } catch (error) {
      console.error('Error creating review:', error);
      throw error;
    }
  },

  updateReviewStatus: async (reviewId, status) => {
    try {
      const response = await apiClient.put(`/marketing/reviews/${reviewId}/status`, { status });
      return response.data;
    } catch (error) {
      console.error('Error updating review status:', error);
      throw error;
    }
  },

  getProductRatings: async (productId) => {
    try {
      const response = await apiClient.get(`/marketing/ratings/product/${productId}`);
      return response.data;
    } catch (error) {
      console.error('Error fetching product ratings:', error);
      throw error;
    }
  },

  // Social Media Integration
  getSocialMedia: async (params = {}) => {
    try {
      const response = await apiClient.get('/marketing/social-media', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching social media connections:', error);
      throw error;
    }
  },

  connectSocialMedia: async (socialData) => {
    try {
      const response = await apiClient.post('/marketing/social-media', socialData);
      return response.data;
    } catch (error) {
      console.error('Error connecting social media:', error);
      throw error;
    }
  },

  postToSocialMedia: async (postData) => {
    try {
      const response = await apiClient.post('/marketing/social-media/posts', postData);
      return response.data;
    } catch (error) {
      console.error('Error posting to social media:', error);
      throw error;
    }
  },

  getSocialAnalytics: async (params = {}) => {
    try {
      const response = await apiClient.get('/marketing/social-analytics', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching social analytics:', error);
      throw error;
    }
  },

  // Analytics & Conversion Tracking
  getAnalytics: async (params = {}) => {
    try {
      const response = await apiClient.get('/marketing/analytics', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching analytics:', error);
      throw error;
    }
  },

  getConversionRates: async (params = {}) => {
    try {
      const response = await apiClient.get('/marketing/conversion-rates', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching conversion rates:', error);
      throw error;
    }
  },

  getCustomerBehavior: async (params = {}) => {
    try {
      const response = await apiClient.get('/marketing/customer-behavior', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching customer behavior:', error);
      throw error;
    }
  },

  getTrafficSources: async (params = {}) => {
    try {
      const response = await apiClient.get('/marketing/traffic-sources', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching traffic sources:', error);
      throw error;
    }
  },

  // A/B Testing
  getABTests: async (params = {}) => {
    try {
      const response = await apiClient.get('/marketing/ab-tests', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching A/B tests:', error);
      throw error;
    }
  },

  createABTest: async (testData) => {
    try {
      const response = await apiClient.post('/marketing/ab-tests', testData);
      return response.data;
    } catch (error) {
      console.error('Error creating A/B test:', error);
      throw error;
    }
  },

  updateABTest: async (testId, testData) => {
    try {
      const response = await apiClient.put(`/marketing/ab-tests/${testId}`, testData);
      return response.data;
    } catch (error) {
      console.error('Error updating A/B test:', error);
      throw error;
    }
  },

  getABTestResults: async (testId) => {
    try {
      const response = await apiClient.get(`/marketing/ab-tests/${testId}/results`);
      return response.data;
    } catch (error) {
      console.error('Error fetching A/B test results:', error);
      throw error;
    }
  },

  // Customer Segmentation
  getSegments: async (params = {}) => {
    try {
      const response = await apiClient.get('/marketing/segments', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching customer segments:', error);
      throw error;
    }
  },

  createSegment: async (segmentData) => {
    try {
      const response = await apiClient.post('/marketing/segments', segmentData);
      return response.data;
    } catch (error) {
      console.error('Error creating customer segment:', error);
      throw error;
    }
  },

  getCustomerLifecycle: async (customerId) => {
    try {
      const response = await apiClient.get(`/marketing/customers/${customerId}/lifecycle`);
      return response.data;
    } catch (error) {
      console.error('Error fetching customer lifecycle:', error);
      throw error;
    }
  },

  // Loyalty Programs
  getLoyaltyPrograms: async (params = {}) => {
    try {
      const response = await apiClient.get('/marketing/loyalty-programs', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching loyalty programs:', error);
      throw error;
    }
  },

  createLoyaltyProgram: async (programData) => {
    try {
      const response = await apiClient.post('/marketing/loyalty-programs', programData);
      return response.data;
    } catch (error) {
      console.error('Error creating loyalty program:', error);
      throw error;
    }
  },

  getCustomerRewards: async (customerId) => {
    try {
      const response = await apiClient.get(`/marketing/customers/${customerId}/rewards`);
      return response.data;
    } catch (error) {
      console.error('Error fetching customer rewards:', error);
      throw error;
    }
  },

  // Personalization
  getPersonalizationRules: async (params = {}) => {
    try {
      const response = await apiClient.get('/marketing/personalization-rules', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching personalization rules:', error);
      throw error;
    }
  },

  createPersonalizationRule: async (ruleData) => {
    try {
      const response = await apiClient.post('/marketing/personalization-rules', ruleData);
      return response.data;
    } catch (error) {
      console.error('Error creating personalization rule:', error);
      throw error;
    }
  },

  // Influencer Marketing
  getInfluencerCampaigns: async (params = {}) => {
    try {
      const response = await apiClient.get('/marketing/influencer-campaigns', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching influencer campaigns:', error);
      throw error;
    }
  },

  createInfluencerCampaign: async (campaignData) => {
    try {
      const response = await apiClient.post('/marketing/influencer-campaigns', campaignData);
      return response.data;
    } catch (error) {
      console.error('Error creating influencer campaign:', error);
      throw error;
    }
  }
};

export default marketingService;