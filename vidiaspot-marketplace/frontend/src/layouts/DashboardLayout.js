import React, { useState } from 'react';
import { Link, useLocation, useNavigate } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';
import { useUserRoles } from '../utils/withAuth';
import { useTenant } from '../context/TenantContext';

const DashboardLayout = ({ children }) => {
  const [sidebarOpen, setSidebarOpen] = useState(false);
  const location = useLocation();
  const navigate = useNavigate();
  const { user, logout } = useAuth();
  const {
    isBuyer,
    isSeller,
    isStoreOwner,
    isDeliverySubscriber,
    isCryptoUser,
    isAdmin,
    isSuperAdmin
  } = useUserRoles();
  const { currentTenant, tenants, switchTenant } = useTenant();

  const getNavigation = () => {
    let navigation = [];

    // Common navigation items
    navigation.push(
      { name: 'Dashboard', href: '/', current: location.pathname === '/' }
    );

    // Navigation based on user roles
    if (isSeller || isStoreOwner) {
      navigation.push(
        { name: 'My Listings', href: '/my-ads', current: location.pathname === '/my-ads' },
        { name: 'Create Listing', href: '/create-ad', current: location.pathname === '/create-ad' }
      );
    }

    if (isBuyer) {
      navigation.push(
        { name: 'Browse Listings', href: '/ads', current: location.pathname === '/ads' },
        { name: 'Categories', href: '/categories', current: location.pathname === '/categories' }
      );
    }

    if (isDeliverySubscriber) {
      navigation.push(
        { name: 'Delivery Dashboard', href: '/delivery', current: location.pathname === '/delivery' }
      );
    }

    if (isSeller || isStoreOwner) {
      navigation.push(
        { name: 'Orders', href: '/orders', current: location.pathname === '/orders' },
        { name: 'Inventory', href: '/inventory', current: location.pathname === '/inventory' }
      );
    }

    if (isCryptoUser) {
      navigation.push(
        { name: 'Crypto Wallet', href: '/wallet', current: location.pathname === '/wallet' },
        { name: 'Crypto Payments', href: '/crypto-payments', current: location.pathname === '/crypto-payments' }
      );
    }

    if (isAdmin || isSuperAdmin) {
      navigation.push(
        { name: 'Admin Dashboard', href: '/admin', current: location.pathname === '/admin' },
        { name: 'User Management', href: '/admin/users', current: location.pathname === '/admin/users' },
        { name: 'Reports', href: '/reports', current: location.pathname === '/reports' }
      );
    }

    navigation.push(
      { name: 'Messages', href: '/messages', current: location.pathname === '/messages' },
      { name: 'Profile', href: '/profile', current: location.pathname === '/profile' }
    );

    return navigation;
  };

  const navigation = getNavigation();

  const handleLogout = () => {
    logout();
    navigate('/login');
  };

  return (
    <>
      <div>
        {/* Mobile sidebar */}
        {sidebarOpen && (
          <div className="fixed inset-0 z-40 flex md:hidden">
            <div className="fixed inset-0 bg-gray-600 bg-opacity-75" onClick={() => setSidebarOpen(false)}></div>
            <div className="relative flex-1 flex flex-col max-w-xs w-full bg-white">
              <div className="absolute top-0 right-0 -mr-12 pt-2">
                <button
                  type="button"
                  className="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
                  onClick={() => setSidebarOpen(false)}
                >
                  <span className="sr-only">Close sidebar</span>
                  <svg className="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
              <div className="flex-1 h-0 pt-5 pb-4 overflow-y-auto">
                <div className="flex-shrink-0 flex items-center px-4">
                  <span className="text-xl font-bold text-blue-600">VidiaSpot</span>
                </div>

                {/* Tenant switcher in mobile */}
                {tenants && tenants.length > 1 && (
                  <div className="px-2 mt-4">
                    <label className="block text-sm font-medium text-gray-700 mb-1">Switch Tenant</label>
                    <select
                      value={currentTenant?.id || ''}
                      onChange={(e) => switchTenant(e.target.value)}
                      className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                      {tenants.map(tenant => (
                        <option key={tenant.id} value={tenant.id}>
                          {tenant.name}
                        </option>
                      ))}
                    </select>
                  </div>
                )}

                <nav className="mt-5 px-2 space-y-1">
                  {navigation.map((item) => (
                    <Link
                      key={item.name}
                      to={item.href}
                      className={`${
                        item.current
                          ? 'bg-blue-100 text-blue-600'
                          : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                      } group flex items-center px-2 py-2 text-base font-medium rounded-md`}
                      onClick={() => setSidebarOpen(false)}
                    >
                      {item.name}
                    </Link>
                  ))}
                </nav>

                <div className="mt-6 px-2">
                  <button
                    onClick={handleLogout}
                    className="w-full text-left px-2 py-2 text-base font-medium text-red-600 hover:bg-red-50 rounded-md"
                  >
                    Logout
                  </button>
                </div>
              </div>
            </div>
          </div>
        )}

        {/* Static sidebar for desktop */}
        <div className="hidden md:flex md:w-64 md:flex-col md:fixed md:inset-y-0">
          <div className="flex-1 flex flex-col min-h-0 border-r border-gray-200 bg-white">
            <div className="flex-1 flex flex-col pt-5 pb-4 overflow-y-auto">
              <div className="flex items-center flex-shrink-0 px-4">
                <span className="text-xl font-bold text-blue-600">VidiaSpot</span>
              </div>

              {/* Tenant switcher in desktop */}
              {tenants && tenants.length > 1 && (
                <div className="px-4 mt-4">
                  <label className="block text-sm font-medium text-gray-700 mb-1">Switch Tenant</label>
                  <select
                    value={currentTenant?.id || ''}
                    onChange={(e) => switchTenant(e.target.value)}
                    className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  >
                    {tenants.map(tenant => (
                      <option key={tenant.id} value={tenant.id}>
                        {tenant.name}
                      </option>
                    ))}
                  </select>
                </div>
              )}

              <nav className="mt-5 flex-1 px-2 bg-white space-y-1">
                {navigation.map((item) => (
                  <Link
                    key={item.name}
                    to={item.href}
                    className={`${
                      item.current
                        ? 'bg-blue-100 text-blue-600'
                        : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                    } group flex items-center px-2 py-2 text-sm font-medium rounded-md`}
                  >
                    {item.name}
                  </Link>
                ))}
              </nav>

              <div className="mt-auto px-2">
                <div className="text-xs text-gray-500 mb-1">
                  {currentTenant ? `Tenant: ${currentTenant.name}` : 'No tenant selected'}
                </div>
                <button
                  onClick={handleLogout}
                  className="w-full text-left px-2 py-2 text-sm font-medium text-red-600 hover:bg-red-50 rounded-md"
                >
                  Logout
                </button>
              </div>
            </div>
          </div>
        </div>

        <div className="md:pl-64 flex flex-col flex-1">
          <div className="sticky top-0 z-10 md:hidden pl-1 pt-1 sm:pl-3 sm:pt-3 bg-white">
            <button
              type="button"
              className="-ml-0.5 -mt-0.5 h-12 w-12 inline-flex items-center justify-center rounded-md text-gray-500 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500"
              onClick={() => setSidebarOpen(true)}
            >
              <span className="sr-only">Open sidebar</span>
              <svg className="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 6h16M4 12h16M4 18h16" />
              </svg>
            </button>
          </div>

          <main className="flex-1">
            {children}
          </main>
        </div>
      </div>
    </>
  );
};

export default DashboardLayout;