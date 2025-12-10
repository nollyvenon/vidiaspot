import apiClient from './api';

const securityService = {
  // SSL Certificate Management
  getSSLStatus: async (storeId) => {
    try {
      const response = await apiClient.get(`/security/${storeId}/ssl-status`);
      return response.data;
    } catch (error) {
      console.error('Error fetching SSL status:', error);
      throw error;
    }
  },

  requestSSLCertificate: async (storeId, domainData) => {
    try {
      const response = await apiClient.post(`/security/${storeId}/ssl-request`, domainData);
      return response.data;
    } catch (error) {
      console.error('Error requesting SSL certificate:', error);
      throw error;
    }
  },

  // PCI Compliance
  getPCIComplianceStatus: async (storeId) => {
    try {
      const response = await apiClient.get(`/security/${storeId}/pci-compliance`);
      return response.data;
    } catch (error) {
      console.error('Error fetching PCI compliance status:', error);
      throw error;
    }
  },

  validatePCICompliance: async (storeId, complianceData) => {
    try {
      const response = await apiClient.post(`/security/${storeId}/pci-validation`, complianceData);
      return response.data;
    } catch (error) {
      console.error('Error validating PCI compliance:', error);
      throw error;
    }
  },

  // Fraud Protection
  getFraudAnalytics: async (params = {}) => {
    try {
      const response = await apiClient.get('/security/fraud-analytics', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching fraud analytics:', error);
      throw error;
    }
  },

  getRiskScore: async (transactionData) => {
    try {
      const response = await apiClient.post('/security/risk-score', transactionData);
      return response.data;
    } catch (error) {
      console.error('Error calculating risk score:', error);
      throw error;
    }
  },

  // Audit & Compliance
  getAuditTrail: async (params = {}) => {
    try {
      const response = await apiClient.get('/security/audit-trail', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching audit trail:', error);
      throw error;
    }
  },

  getComplianceReport: async (params = {}) => {
    try {
      const response = await apiClient.get('/security/compliance-report', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching compliance report:', error);
      throw error;
    }
  },

  // Data Privacy (GDPR/CCPA)
  getPrivacySettings: async (storeId) => {
    try {
      const response = await apiClient.get(`/security/${storeId}/privacy-settings`);
      return response.data;
    } catch (error) {
      console.error('Error fetching privacy settings:', error);
      throw error;
    }
  },

  updatePrivacySettings: async (storeId, privacyData) => {
    try {
      const response = await apiClient.put(`/security/${storeId}/privacy-settings`, privacyData);
      return response.data;
    } catch (error) {
      console.error('Error updating privacy settings:', error);
      throw error;
    }
  },

  getUserDataRightsRequests: async (params = {}) => {
    try {
      const response = await apiClient.get('/security/data-rights-requests', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching data rights requests:', error);
      throw error;
    }
  },

  processUserDataRightRequest: async (requestId, action) => {
    try {
      const response = await apiClient.post(`/security/data-rights-requests/${requestId}/${action}`);
      return response.data;
    } catch (error) {
      console.error('Error processing data rights request:', error);
      throw error;
    }
  },

  // Authentication & Authorization
  getTwoFactorAuthSettings: async (userId) => {
    try {
      const response = await apiClient.get(`/security/${userId}/2fa-settings`);
      return response.data;
    } catch (error) {
      console.error('Error fetching 2FA settings:', error);
      throw error;
    }
  },

  configureTwoFactorAuth: async (userId, configData) => {
    try {
      const response = await apiClient.post(`/security/${userId}/2fa-configure`, configData);
      return response.data;
    } catch (error) {
      console.error('Error configuring 2FA:', error);
      throw error;
    }
  },

  getRolePermissions: async (storeId) => {
    try {
      const response = await apiClient.get(`/security/${storeId}/role-permissions`);
      return response.data;
    } catch (error) {
      console.error('Error fetching role permissions:', error);
      throw error;
    }
  },

  updateRolePermissions: async (storeId, roleData) => {
    try {
      const response = await apiClient.put(`/security/${storeId}/role-permissions`, roleData);
      return response.data;
    } catch (error) {
      console.error('Error updating role permissions:', error);
      throw error;
    }
  },

  // Threat Monitoring
  getSecurityAlerts: async (params = {}) => {
    try {
      const response = await apiClient.get('/security/alerts', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching security alerts:', error);
      throw error;
    }
  },

  getThreatDetectionReport: async (params = {}) => {
    try {
      const response = await apiClient.get('/security/threat-detection', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching threat detection report:', error);
      throw error;
    }
  },

  // Vulnerability Scanning
  getVulnerabilityScan: async (storeId) => {
    try {
      const response = await apiClient.get(`/security/${storeId}/vulnerability-scan`);
      return response.data;
    } catch (error) {
      console.error('Error fetching vulnerability scan:', error);
      throw error;
    }
  },

  requestVulnerabilityScan: async (storeId) => {
    try {
      const response = await apiClient.post(`/security/${storeId}/vulnerability-scan`);
      return response.data;
    } catch (error) {
      console.error('Error requesting vulnerability scan:', error);
      throw error;
    }
  },

  // Data Backup & Recovery
  getBackupStatus: async (storeId) => {
    try {
      const response = await apiClient.get(`/security/${storeId}/backup-status`);
      return response.data;
    } catch (error) {
      console.error('Error fetching backup status:', error);
      throw error;
    }
  },

  initiateBackup: async (storeId) => {
    try {
      const response = await apiClient.post(`/security/${storeId}/initiate-backup`);
      return response.data;
    } catch (error) {
      console.error('Error initiating backup:', error);
      throw error;
    }
  },

  getRecoveryOptions: async (storeId) => {
    try {
      const response = await apiClient.get(`/security/${storeId}/recovery-options`);
      return response.data;
    } catch (error) {
      console.error('Error fetching recovery options:', error);
      throw error;
    }
  },

  // Security Policies
  getSecurityPolicies: async (storeId) => {
    try {
      const response = await apiClient.get(`/security/${storeId}/policies`);
      return response.data;
    } catch (error) {
      console.error('Error fetching security policies:', error);
      throw error;
    }
  },

  updateSecurityPolicies: async (storeId, policyData) => {
    try {
      const response = await apiClient.put(`/security/${storeId}/policies`, policyData);
      return response.data;
    } catch (error) {
      console.error('Error updating security policies:', error);
      throw error;
    }
  },

  // Session Management
  getSessionActivity: async (userId, params = {}) => {
    try {
      const response = await apiClient.get(`/security/${userId}/session-activity`, { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching session activity:', error);
      throw error;
    }
  },

  terminateSession: async (sessionId) => {
    try {
      const response = await apiClient.delete(`/security/sessions/${sessionId}/terminate`);
      return response.data;
    } catch (error) {
      console.error('Error terminating session:', error);
      throw error;
    }
  },

  // IP Whitelisting
  getIPWhitelist: async (storeId) => {
    try {
      const response = await apiClient.get(`/security/${storeId}/ip-whitelist`);
      return response.data;
    } catch (error) {
      console.error('Error fetching IP whitelist:', error);
      throw error;
    }
  },

  addIPToWhitelist: async (storeId, ipData) => {
    try {
      const response = await apiClient.post(`/security/${storeId}/ip-whitelist`, ipData);
      return response.data;
    } catch (error) {
      console.error('Error adding IP to whitelist:', error);
      throw error;
    }
  },

  removeIPFromWhitelist: async (storeId, ipAddress) => {
    try {
      const response = await apiClient.delete(`/security/${storeId}/ip-whitelist/${encodeURIComponent(ipAddress)}`);
      return response.data;
    } catch (error) {
      console.error('Error removing IP from whitelist:', error);
      throw error;
    }
  },

  // Security Health Checks
  getSecurityHealth: async (storeId) => {
    try {
      const response = await apiClient.get(`/security/${storeId}/health-check`);
      return response.data;
    } catch (error) {
      console.error('Error fetching security health:', error);
      throw error;
    }
  },

  runSecurityHealthCheck: async (storeId) => {
    try {
      const response = await apiClient.post(`/security/${storeId}/health-check`);
      return response.data;
    } catch (error) {
      console.error('Error running security health check:', error);
      throw error;
    }
  }
};

export default securityService;