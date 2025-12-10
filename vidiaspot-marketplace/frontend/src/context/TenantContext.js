import React, { createContext, useContext, useState, useEffect } from 'react';
import axios from 'axios';
import { useAuth } from './context/AuthContext';

const TenantContext = createContext();

export const useTenant = () => {
  const context = useContext(TenantContext);
  if (!context) {
    throw new Error('useTenant must be used within a TenantProvider');
  }
  return context;
};

export const TenantProvider = ({ children }) => {
  const [tenants, setTenants] = useState([]);
  const [currentTenant, setCurrentTenant] = useState(null);
  const [loading, setLoading] = useState(true);
  const { user } = useAuth();

  useEffect(() => {
    fetchUserTenants();
  }, [user]);

  const fetchUserTenants = async () => {
    if (!user) return;
    
    try {
      const response = await axios.get('http://localhost:8000/api/tenants/user');
      setTenants(response.data.tenants || []);
      
      // Set current tenant if available in local storage
      const storedTenant = localStorage.getItem('tenant');
      if (storedTenant) {
        setCurrentTenant(JSON.parse(storedTenant));
      } else if (response.data.tenants && response.data.tenants.length > 0) {
        // Set first tenant as current if none is set
        setCurrentTenant(response.data.tenants[0]);
      }
    } catch (error) {
      console.error('Error fetching tenants:', error);
      setTenants([]);
    } finally {
      setLoading(false);
    }
  };

  const createTenant = async (tenantData) => {
    try {
      const response = await axios.post('http://localhost:8000/api/tenants', tenantData);
      const newTenant = response.data.tenant;
      
      setTenants(prev => [...prev, newTenant]);
      setCurrentTenant(newTenant);
      
      // Store in localStorage
      localStorage.setItem('tenant', JSON.stringify(newTenant));
      
      return { success: true, tenant: newTenant };
    } catch (error) {
      console.error('Error creating tenant:', error);
      return { 
        success: false, 
        message: error.response?.data?.message || 'Failed to create tenant' 
      };
    }
  };

  const switchTenant = async (tenantId) => {
    try {
      const response = await axios.post(`http://localhost:8000/api/tenants/${tenantId}/switch`);
      const tenant = response.data.tenant;
      
      setCurrentTenant(tenant);
      localStorage.setItem('tenant', JSON.stringify(tenant));
      
      return { success: true, tenant };
    } catch (error) {
      console.error('Error switching tenant:', error);
      return { 
        success: false, 
        message: error.response?.data?.message || 'Failed to switch tenant' 
      };
    }
  };

  const updateTenant = async (tenantId, updateData) => {
    try {
      const response = await axios.put(`http://localhost:8000/api/tenants/${tenantId}`, updateData);
      const updatedTenant = response.data.tenant;
      
      setTenants(prev => prev.map(t => t.id === tenantId ? updatedTenant : t));
      if (currentTenant?.id === tenantId) {
        setCurrentTenant(updatedTenant);
        localStorage.setItem('tenant', JSON.stringify(updatedTenant));
      }
      
      return { success: true, tenant: updatedTenant };
    } catch (error) {
      console.error('Error updating tenant:', error);
      return { 
        success: false, 
        message: error.response?.data?.message || 'Failed to update tenant' 
      };
    }
  };

  const value = {
    tenants,
    currentTenant,
    createTenant,
    switchTenant,
    updateTenant,
    loading,
    hasTenant: !!currentTenant
  };

  return (
    <TenantContext.Provider value={value}>
      {children}
    </TenantContext.Provider>
  );
};