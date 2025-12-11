import apiClient from './api';

const paymentService = {
  // Cryptocurrency Payments
  getSupportedCryptocurrencies: async () => {
    try {
      const response = await apiClient.get('/payments/cryptocurrencies');
      return response.data;
    } catch (error) {
      console.error('Error fetching supported cryptocurrencies:', error);
      throw error;
    }
  },

  createCryptoPayment: async (paymentData) => {
    try {
      const response = await apiClient.post('/payments/cryptocurrency', paymentData);
      return response.data;
    } catch (error) {
      console.error('Error creating crypto payment:', error);
      throw error;
    }
  },

  verifyCryptoPayment: async (paymentId) => {
    try {
      const response = await apiClient.get(`/payments/cryptocurrency/${paymentId}/verify`);
      return response.data;
    } catch (error) {
      console.error('Error verifying crypto payment:', error);
      throw error;
    }
  },

  getWalletAddresses: async (userId) => {
    try {
      const response = await apiClient.get(`/users/${userId}/crypto-wallets`);
      return response.data;
    } catch (error) {
      console.error('Error fetching crypto wallet addresses:', error);
      throw error;
    }
  },

  addWalletAddress: async (userId, walletData) => {
    try {
      const response = await apiClient.post(`/users/${userId}/crypto-wallets`, walletData);
      return response.data;
    } catch (error) {
      console.error('Error adding crypto wallet address:', error);
      throw error;
    }
  },

  // Buy-Now-Pay-Later Integration
  getBNPLProviders: async () => {
    try {
      const response = await apiClient.get('/payments/bnpl-providers');
      return response.data;
    } catch (error) {
      console.error('Error fetching BNPL providers:', error);
      throw error;
    }
  },

  validateBNPL: async (userId, orderData) => {
    try {
      const response = await apiClient.post(`/payments/bnpl/validate/${userId}`, orderData);
      return response.data;
    } catch (error) {
      console.error('Error validating BNPL:', error);
      throw error;
    }
  },

  applyForBNPL: async (applicationData) => {
    try {
      const response = await apiClient.post('/payments/bnpl/application', applicationData);
      return response.data;
    } catch (error) {
      console.error('Error applying for BNPL:', error);
      throw error;
    }
  },

  checkCreditEligibility: async (userData) => {
    try {
      const response = await apiClient.post('/payments/bnpl/credit-check', userData);
      return response.data;
    } catch (error) {
      console.error('Error checking credit eligibility:', error);
      throw error;
    }
  },

  getBNPLStatus: async (userId) => {
    try {
      const response = await apiClient.get(`/payments/bnpl/status/${userId}`);
      return response.data;
    } catch (error) {
      console.error('Error fetching BNPL status:', error);
      throw error;
    }
  },

  // Mobile Money Integration
  getMobileMoneyProviders: async () => {
    try {
      const response = await apiClient.get('/payments/mobile-money-providers');
      return response.data;
    } catch (error) {
      console.error('Error fetching mobile money providers:', error);
      throw error;
    }
  },

  createMobileMoneyPayment: async (paymentData) => {
    try {
      const response = await apiClient.post('/payments/mobile-money', paymentData);
      return response.data;
    } catch (error) {
      console.error('Error creating mobile money payment:', error);
      throw error;
    }
  },

  verifyMobileMoneyPayment: async (transactionId) => {
    try {
      const response = await apiClient.get(`/payments/mobile-money/verify/${transactionId}`);
      return response.data;
    } catch (error) {
      console.error('Error verifying mobile money payment:', error);
      throw error;
    }
  },

  validateMobileMoneyAccount: async (accountData) => {
    try {
      const response = await apiClient.post('/payments/mobile-money/validate', accountData);
      return response.data;
    } catch (error) {
      console.error('Error validating mobile money account:', error);
      throw error;
    }
  },

  // QR Code Payments
  generateQRCodePayment: async (paymentData) => {
    try {
      const response = await apiClient.post('/payments/qrcode', paymentData);
      return response.data;
    } catch (error) {
      console.error('Error generating QR code payment:', error);
      throw error;
    }
  },

  processQRCodePayment: async (qrCodeData) => {
    try {
      const response = await apiClient.post('/payments/qrcode/process', qrCodeData);
      return response.data;
    } catch (error) {
      console.error('Error processing QR code payment:', error);
      throw error;
    }
  },

  validateQRCodePayment: async (qrCode, amount) => {
    try {
      const response = await apiClient.post('/payments/qrcode/validate', { qr_code: qrCode, amount });
      return response.data;
    } catch (error) {
      console.error('Error validating QR code payment:', error);
      throw error;
    }
  },

  // Split Payment
  createSplitPayment: async (splitData) => {
    try {
      const response = await apiClient.post('/payments/split', splitData);
      return response.data;
    } catch (error) {
      console.error('Error creating split payment:', error);
      throw error;
    }
  },

  getSplitPaymentDetails: async (splitId) => {
    try {
      const response = await apiClient.get(`/payments/split/${splitId}`);
      return response.data;
    } catch (error) {
      console.error('Error fetching split payment details:', error);
      throw error;
    }
  },

  joinSplitPayment: async (splitId, participantData) => {
    try {
      const response = await apiClient.post(`/payments/split/${splitId}/join`, participantData);
      return response.data;
    } catch (error) {
      console.error('Error joining split payment:', error);
      throw error;
    }
  },

  updateSplitPayment: async (splitId, updateData) => {
    try {
      const response = await apiClient.put(`/payments/split/${splitId}`, updateData);
      return response.data;
    } catch (error) {
      console.error('Error updating split payment:', error);
      throw error;
    }
  },

  // Insurance Integration
  getInsuranceProviders: async () => {
    try {
      const response = await apiClient.get('/payments/insurance-providers');
      return response.data;
    } catch (error) {
      console.error('Error fetching insurance providers:', error);
      throw error;
    }
  },

  getInsuranceOptions: async (orderData) => {
    try {
      const response = await apiClient.post('/payments/insurance-options', orderData);
      return response.data;
    } catch (error) {
      console.error('Error fetching insurance options:', error);
      throw error;
    }
  },

  purchaseInsurance: async (insuranceData) => {
    try {
      const response = await apiClient.post('/payments/insurance', insuranceData);
      return response.data;
    } catch (error) {
      console.error('Error purchasing insurance:', error);
      throw error;
    }
  },

  verifyInsuranceClaim: async (claimId) => {
    try {
      const response = await apiClient.get(`/payments/insurance/claims/${claimId}/verify`);
      return response.data;
    } catch (error) {
      console.error('Error verifying insurance claim:', error);
      throw error;
    }
  },

  getInsuranceCoverage: async (itemId) => {
    try {
      const response = await apiClient.get(`/payments/insurance/item/${itemId}/coverage`);
      return response.data;
    } catch (error) {
      console.error('Error fetching insurance coverage:', error);
      throw error;
    }
  },

  // Payment Processing Utilities
  getPaymentMethods: async (userId) => {
    try {
      const response = await apiClient.get(`/users/${userId}/payment-methods`);
      return response.data;
    } catch (error) {
      console.error('Error fetching payment methods:', error);
      throw error;
    }
  },

  addPaymentMethod: async (userId, paymentMethodData) => {
    try {
      const response = await apiClient.post(`/users/${userId}/payment-methods`, paymentMethodData);
      return response.data;
    } catch (error) {
      console.error('Error adding payment method:', error);
      throw error;
    }
  },

  updatePaymentMethod: async (userId, methodId, paymentMethodData) => {
    try {
      const response = await apiClient.put(`/users/${userId}/payment-methods/${methodId}`, paymentMethodData);
      return response.data;
    } catch (error) {
      console.error('Error updating payment method:', error);
      throw error;
    }
  },

  deletePaymentMethod: async (userId, methodId) => {
    try {
      const response = await apiClient.delete(`/users/${userId}/payment-methods/${methodId}`);
      return response.data;
    } catch (error) {
      console.error('Error deleting payment method:', error);
      throw error;
    }
  },

  // Payment History and Analytics
  getPaymentHistory: async (userId, params = {}) => {
    try {
      const response = await apiClient.get(`/users/${userId}/payment-history`, { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching payment history:', error);
      throw error;
    }
  },

  getPaymentAnalytics: async (userId, params = {}) => {
    try {
      const response = await apiClient.get(`/users/${userId}/payment-analytics`, { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching payment analytics:', error);
      throw error;
    }
  },

  // Transaction Management
  getTransactionDetails: async (transactionId) => {
    try {
      const response = await apiClient.get(`/payments/transactions/${transactionId}`);
      return response.data;
    } catch (error) {
      console.error('Error fetching transaction details:', error);
      throw error;
    }
  },

  getTransactionStatus: async (transactionId) => {
    try {
      const response = await apiClient.get(`/payments/transactions/${transactionId}/status`);
      return response.data;
    } catch (error) {
      console.error('Error fetching transaction status:', error);
      throw error;
    }
  },

  retryPayment: async (transactionId) => {
    try {
      const response = await apiClient.post(`/payments/transactions/${transactionId}/retry`);
      return response.data;
    } catch (error) {
      console.error('Error retrying payment:', error);
      throw error;
    }
  },

  // Cross-border Payment Support
  getSupportedCountries: async () => {
    try {
      const response = await apiClient.get('/payments/supported-countries');
      return response.data;
    } catch (error) {
      console.error('Error fetching supported countries:', error);
      throw error;
    }
  },

  getExchangeRates: async (params = {}) => {
    try {
      const response = await apiClient.get('/payments/exchange-rates', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching exchange rates:', error);
      throw error;
    }
  },

  convertCurrency: async (amount, fromCurrency, toCurrency) => {
    try {
      const response = await apiClient.post('/payments/currency-convert', {
        amount,
        from_currency: fromCurrency,
        to_currency: toCurrency
      });
      return response.data;
    } catch (error) {
      console.error('Error converting currency:', error);
      throw error;
    }
  },

  // Payment Security and Fraud Prevention
  getFraudRiskAssessment: async (transactionData) => {
    try {
      const response = await apiClient.post('/payments/fraud-risk-assessment', transactionData);
      return response.data;
    } catch (error) {
      console.error('Error performing fraud risk assessment:', error);
      throw error;
    }
  },

  enablePaymentSecurity: async (userId, securitySettings) => {
    try {
      const response = await apiClient.post(`/users/${userId}/payment-security`, securitySettings);
      return response.data;
    } catch (error) {
      console.error('Error enabling payment security:', error);
      throw error;
    }
  }
};

export default paymentService;