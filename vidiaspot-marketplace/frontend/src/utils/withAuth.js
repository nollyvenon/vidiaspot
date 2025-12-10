import React from 'react';
import { useAuth } from '../context/AuthContext';
import { Navigate } from 'react-router-dom';

// Higher-order component for role-based access control
export const withAuth = (WrappedComponent, allowedRoles = null) => {
  const AuthenticatedComponent = (props) => {
    const { isAuthenticated, hasRole, loading } = useAuth();

    if (loading) {
      return (
        <div className="min-h-screen flex items-center justify-center">
          <div className="animate-spin rounded-full h-32 w-32 border-b-2 border-blue-600"></div>
        </div>
      );
    }

    if (!isAuthenticated) {
      return <Navigate to="/login" replace />;
    }

    if (allowedRoles && !hasRole(allowedRoles)) {
      return (
        <div className="min-h-screen flex items-center justify-center">
          <div className="text-center">
            <h2 className="text-2xl font-bold text-red-600">Access Denied</h2>
            <p className="text-gray-600">You don't have permission to view this page.</p>
          </div>
        </div>
      );
    }

    return <WrappedComponent {...props} />;
  };

  return AuthenticatedComponent;
};

// Component for role-based rendering
export const RoleBasedRoute = ({ allowedRoles, children }) => {
  const { hasRole, loading } = useAuth();

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <div className="animate-spin rounded-full h-32 w-32 border-b-2 border-blue-600"></div>
      </div>
    );
  }

  if (!hasRole(allowedRoles)) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <div className="text-center">
          <h2 className="text-2xl font-bold text-red-600">Access Denied</h2>
          <p className="text-gray-600">You don't have permission to view this page.</p>
        </div>
      </div>
    );
  }

  return children;
};

// Custom hook to get user roles
export const useUserRoles = () => {
  const { user, hasRole, hasTenant } = useAuth();
  
  return {
    user,
    hasRole,
    hasTenant,
    isBuyer: hasRole('buyer'),
    isSeller: hasRole('seller'),
    isStoreOwner: hasRole('store_owner'),
    isDeliverySubscriber: hasRole('delivery_subscriber'),
    isCryptoUser: hasRole('crypto_user'),
    isAdmin: hasRole('admin'),
    isSuperAdmin: hasRole('super_admin'),
  };
};