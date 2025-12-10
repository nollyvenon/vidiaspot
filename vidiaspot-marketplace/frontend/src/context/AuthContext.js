import React, { createContext, useContext, useState, useEffect } from 'react';
import axios from 'axios';

const AuthContext = createContext();

export const useAuth = () => {
  const context = useContext(AuthContext);
  if (!context) {
    throw new Error('useAuth must be used within an AuthProvider');
  }
  return context;
};

export const AuthProvider = ({ children }) => {
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);
  const [tenant, setTenant] = useState(null);

  useEffect(() => {
    // Check if user is already logged in
    const token = localStorage.getItem('token');
    const userData = localStorage.getItem('user');
    const tenantData = localStorage.getItem('tenant');

    if (token && userData) {
      setUser(JSON.parse(userData));
      if (tenantData) {
        setTenant(JSON.parse(tenantData));
      }
      // Set default authorization header
      axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
    }
    setLoading(false);
  }, []);

  const login = async (email, password) => {
    try {
      const response = await axios.post('http://localhost:8000/api/login', {
        email,
        password
      });

      if (response.data.token) {
        const { token, user: userData, tenant: tenantData } = response.data;
        
        localStorage.setItem('token', token);
        localStorage.setItem('user', JSON.stringify(userData));
        if (tenantData) {
          localStorage.setItem('tenant', JSON.stringify(tenantData));
          setTenant(tenantData);
        }
        
        // Set authorization header
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
        setUser(userData);
        
        return { success: true, user: userData };
      } else {
        return { success: false, message: response.data.message || 'Login failed' };
      }
    } catch (error) {
      console.error('Login error:', error);
      return { 
        success: false, 
        message: error.response?.data?.message || 'Login failed. Please try again.' 
      };
    }
  };

  const register = async (userData) => {
    try {
      const response = await axios.post('http://localhost:8000/api/register', userData);
      
      if (response.data.token) {
        const { token, user: userResponse, tenant: tenantData } = response.data;
        
        localStorage.setItem('token', token);
        localStorage.setItem('user', JSON.stringify(userResponse));
        if (tenantData) {
          localStorage.setItem('tenant', JSON.stringify(tenantData));
          setTenant(tenantData);
        }
        
        // Set authorization header
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
        setUser(userResponse);
        
        return { success: true, user: userResponse };
      } else {
        return { success: false, message: response.data.message || 'Registration failed' };
      }
    } catch (error) {
      console.error('Registration error:', error);
      return { 
        success: false, 
        message: error.response?.data?.message || 'Registration failed. Please try again.' 
      };
    }
  };

  const logout = () => {
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    localStorage.removeItem('tenant');
    delete axios.defaults.headers.common['Authorization'];
    setUser(null);
    setTenant(null);
  };

  const updateUser = (updatedUser) => {
    setUser(updatedUser);
    localStorage.setItem('user', JSON.stringify(updatedUser));
  };

  const updateTenant = (updatedTenant) => {
    setTenant(updatedTenant);
    localStorage.setItem('tenant', JSON.stringify(updatedTenant));
  };

  const value = {
    user,
    tenant,
    login,
    register,
    logout,
    updateUser,
    updateTenant,
    loading,
    isAuthenticated: !!user,
    hasRole: (roles) => {
      if (!user) return false;
      const userRoles = Array.isArray(user.role) ? user.role : [user.role];
      if (Array.isArray(roles)) {
        return userRoles.some(role => roles.includes(role));
      }
      return userRoles.includes(roles);
    },
    hasTenant: !!tenant
  };

  return (
    <AuthContext.Provider value={value}>
      {children}
    </AuthContext.Provider>
  );
};