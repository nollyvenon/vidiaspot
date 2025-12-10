import React, { useState, useEffect } from 'react';
import { withAuth } from '../utils/withAuth';
import adminService from '../services/adminService';

const AdminPanel = () => {
  const [activeTab, setActiveTab] = useState('dashboard');
  const [dashboardData, setDashboardData] = useState(null);
  const [ads, setAds] = useState([]);
  const [users, setUsers] = useState([]);
  const [categories, setCategories] = useState([]);
  const [payments, setPayments] = useState([]);
  const [vendors, setVendors] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [stats, setStats] = useState({});

  useEffect(() => {
    fetchData();
  }, [activeTab]);

  const fetchData = async () => {
    try {
      setLoading(true);
      setError(null);

      switch (activeTab) {
        case 'dashboard':
          const dashboard = await adminService.getDashboardStats();
          setDashboardData(dashboard);
          break;
        case 'ads':
          const adsData = await adminService.getAds();
          setAds(adsData.data || adsData.ads || []);
          break;
        case 'users':
          const usersData = await adminService.getUsers();
          setUsers(usersData.data || usersData.users || []);
          break;
        case 'categories':
          const categoriesData = await adminService.getCategories();
          setCategories(categoriesData.data || categoriesData.categories || []);
          break;
        case 'payments':
          const paymentsData = await adminService.getPayments();
          setPayments(paymentsData.data || paymentsData.payments || []);
          break;
        case 'vendors':
          const vendorsData = await adminService.getVendors();
          setVendors(vendorsData.data || vendorsData.vendors || []);
          break;
        default:
          break;
      }
    } catch (err) {
      setError(`Failed to load ${activeTab} data. Please try again.`);
      console.error(`Error fetching ${activeTab} data:`, err);
    } finally {
      setLoading(false);
    }
  };

  const handleUpdateAdStatus = async (adId, newStatus) => {
    try {
      await adminService.updateAdStatus(adId, newStatus);
      // Refresh ads list
      const adsData = await adminService.getAds();
      setAds(adsData.data || adsData.ads || []);
    } catch (err) {
      setError('Failed to update ad status.');
      console.error('Error updating ad status:', err);
    }
  };

  const handleUpdateUserRole = async (userId, newRole) => {
    try {
      await adminService.updateUserRole(userId, newRole);
      // Refresh users list
      const usersData = await adminService.getUsers();
      setUsers(usersData.data || usersData.users || []);
    } catch (err) {
      setError('Failed to update user role.');
      console.error('Error updating user role:', err);
    }
  };

  const handleSuspendUser = async (userId) => {
    try {
      await adminService.suspendUser(userId);
      // Refresh users list
      const usersData = await adminService.getUsers();
      setUsers(usersData.data || usersData.users || []);
    } catch (err) {
      setError('Failed to suspend user.');
      console.error('Error suspending user:', err);
    }
  };

  const handleApproveVendor = async (vendorId) => {
    try {
      await adminService.approveVendor(vendorId);
      // Refresh vendors list
      const vendorsData = await adminService.getVendors();
      setVendors(vendorsData.data || vendorsData.vendors || []);
    } catch (err) {
      setError('Failed to approve vendor.');
      console.error('Error approving vendor:', err);
    }
  };

  const handleSuspendVendor = async (vendorId) => {
    try {
      await adminService.suspendVendor(vendorId);
      // Refresh vendors list
      const vendorsData = await adminService.getVendors();
      setVendors(vendorsData.data || vendorsData.vendors || []);
    } catch (err) {
      setError('Failed to suspend vendor.');
      console.error('Error suspending vendor:', err);
    }
  };

  const getStatusColor = (status) => {
    switch (status) {
      case 'active': case 'approved': case 'completed':
        return 'bg-green-100 text-green-800';
      case 'pending': case 'processing':
        return 'bg-yellow-100 text-yellow-800';
      case 'inactive': case 'suspended': case 'cancelled': case 'failed':
        return 'bg-red-100 text-red-800';
      default:
        return 'bg-gray-100 text-gray-800';
    }
  };

  if (loading && !dashboardData && ads.length === 0 && users.length === 0) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <div className="animate-spin rounded-full h-32 w-32 border-b-2 border-blue-600"></div>
      </div>
    );
  }

  return (
    <div className="max-w-7xl mx-auto p-6">
      <div className="mb-8">
        <h1 className="text-3xl font-bold text-gray-900">Admin Dashboard</h1>
        <p className="text-gray-600">Manage the SaaS platform and monitor activities</p>
      </div>

      {error && (
        <div className="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
          <strong className="font-bold">Error: </strong>
          <span className="block sm:inline">{error}</span>
        </div>
      )}

      {/* Tab Navigation */}
      <div className="border-b border-gray-200 mb-6">
        <nav className="-mb-px flex flex-wrap space-x-8">
          <button
            onClick={() => setActiveTab('dashboard')}
            className={`whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm ${
              activeTab === 'dashboard'
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            }`}
          >
            Dashboard
          </button>
          <button
            onClick={() => setActiveTab('ads')}
            className={`whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm ${
              activeTab === 'ads'
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            }`}
          >
            Ads Management
          </button>
          <button
            onClick={() => setActiveTab('users')}
            className={`whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm ${
              activeTab === 'users'
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            }`}
          >
            Users
          </button>
          <button
            onClick={() => setActiveTab('categories')}
            className={`whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm ${
              activeTab === 'categories'
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            }`}
          >
            Categories
          </button>
          <button
            onClick={() => setActiveTab('payments')}
            className={`whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm ${
              activeTab === 'payments'
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            }`}
          >
            Payments
          </button>
          <button
            onClick={() => setActiveTab('vendors')}
            className={`whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm ${
              activeTab === 'vendors'
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            }`}
          >
            Vendors
          </button>
        </nav>
      </div>

      {/* Dashboard Tab */}
      {activeTab === 'dashboard' && dashboardData && (
        <div>
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div className="bg-white rounded-lg shadow-md p-6">
              <h3 className="text-lg font-medium text-gray-900">Total Users</h3>
              <p className="text-3xl font-bold text-blue-600">{dashboardData.total_users || 0}</p>
            </div>
            <div className="bg-white rounded-lg shadow-md p-6">
              <h3 className="text-lg font-medium text-gray-900">Total Ads</h3>
              <p className="text-3xl font-bold text-green-600">{dashboardData.total_ads || 0}</p>
            </div>
            <div className="bg-white rounded-lg shadow-md p-6">
              <h3 className="text-lg font-medium text-gray-900">Total Revenue</h3>
              <p className="text-3xl font-bold text-purple-600">${dashboardData.total_revenue?.toFixed(2) || '0.00'}</p>
            </div>
            <div className="bg-white rounded-lg shadow-md p-6">
              <h3 className="text-lg font-medium text-gray-900">Pending Reviews</h3>
              <p className="text-3xl font-bold text-yellow-600">{dashboardData.pending_reviews || 0}</p>
            </div>
          </div>

          <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div className="bg-white rounded-lg shadow-md p-6">
              <h3 className="text-lg font-semibold mb-4">Recent Activity</h3>
              <div className="space-y-4">
                {dashboardData.recent_activity?.slice(0, 5).map((activity, index) => (
                  <div key={index} className="flex justify-between items-center border-b pb-2">
                    <div>
                      <p className="font-medium">{activity.action}</p>
                      <p className="text-sm text-gray-500">{activity.user} • {new Date(activity.timestamp).toLocaleString()}</p>
                    </div>
                    <span className={`px-2 py-1 rounded-full text-xs ${getStatusColor(activity.status)}`}>
                      {activity.status}
                    </span>
                  </div>
                )) || <p className="text-gray-500">No recent activity</p>}
              </div>
            </div>

            <div className="bg-white rounded-lg shadow-md p-6">
              <h3 className="text-lg font-semibold mb-4">Top Categories</h3>
              <div className="space-y-3">
                {dashboardData.top_categories?.slice(0, 5).map((category, index) => (
                  <div key={index} className="flex justify-between items-center">
                    <span>{category.name}</span>
                    <span className="font-medium">{category.count} ads</span>
                  </div>
                )) || <p className="text-gray-500">No category data</p>}
              </div>
            </div>
          </div>
        </div>
      )}

      {/* Ads Management Tab */}
      {activeTab === 'ads' && (
        <div className="bg-white rounded-lg shadow-md overflow-hidden">
          <div className="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h2 className="text-lg font-medium text-gray-900">Ads Management</h2>
          </div>
          <div className="overflow-x-auto">
            <table className="min-w-full divide-y divide-gray-200">
              <thead className="bg-gray-50">
                <tr>
                  <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    ID
                  </th>
                  <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Title
                  </th>
                  <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    User
                  </th>
                  <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Status
                  </th>
                  <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Created
                  </th>
                  <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody className="bg-white divide-y divide-gray-200">
                {ads.map((ad) => (
                  <tr key={ad.id}>
                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {ad.id}
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                      {ad.title}
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {ad.user?.name || ad.user_name || 'N/A'}
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap">
                      <span className={`inline-flex items-center px-3 py-1 rounded-full text-xs font-medium ${getStatusColor(ad.status)}`}>
                        {ad.status}
                      </span>
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {new Date(ad.created_at).toLocaleDateString()}
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap text-sm font-medium">
                      <select
                        value={ad.status}
                        onChange={(e) => handleUpdateAdStatus(ad.id, e.target.value)}
                        className="text-sm border border-gray-300 rounded px-2 py-1 mr-2"
                      >
                        <option value="active">Active</option>
                        <option value="pending">Pending</option>
                        <option value="inactive">Inactive</option>
                        <option value="suspended">Suspended</option>
                      </select>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      )}

      {/* Users Management Tab */}
      {activeTab === 'users' && (
        <div className="bg-white rounded-lg shadow-md overflow-hidden">
          <div className="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h2 className="text-lg font-medium text-gray-900">Users Management</h2>
          </div>
          <div className="overflow-x-auto">
            <table className="min-w-full divide-y divide-gray-200">
              <thead className="bg-gray-50">
                <tr>
                  <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Name
                  </th>
                  <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Email
                  </th>
                  <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Role
                  </th>
                  <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Status
                  </th>
                  <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody className="bg-white divide-y divide-gray-200">
                {users.map((user) => (
                  <tr key={user.id}>
                    <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                      {user.name}
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {user.email}
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      <select
                        value={user.role}
                        onChange={(e) => handleUpdateUserRole(user.id, e.target.value)}
                        className="text-sm border border-gray-300 rounded px-2 py-1"
                      >
                        <option value="customer">Customer</option>
                        <option value="seller">Seller</option>
                        <option value="store_owner">Store Owner</option>
                        <option value="admin">Admin</option>
                      </select>
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap">
                      <span className={`inline-flex items-center px-3 py-1 rounded-full text-xs font-medium ${getStatusColor(user.status)}`}>
                        {user.status}
                      </span>
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap text-sm font-medium">
                      <button
                        onClick={() => handleSuspendUser(user.id)}
                        className="text-red-600 hover:text-red-900 mr-3"
                      >
                        {user.status === 'suspended' ? 'Activate' : 'Suspend'}
                      </button>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      )}

      {/* Vendors Management Tab */}
      {activeTab === 'vendors' && (
        <div className="bg-white rounded-lg shadow-md overflow-hidden">
          <div className="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h2 className="text-lg font-medium text-gray-900">Vendors Management</h2>
          </div>
          <div className="overflow-x-auto">
            <table className="min-w-full divide-y divide-gray-200">
              <thead className="bg-gray-50">
                <tr>
                  <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Name
                  </th>
                  <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Type
                  </th>
                  <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Status
                  </th>
                  <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Rating
                  </th>
                  <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody className="bg-white divide-y divide-gray-200">
                {vendors.map((vendor) => (
                  <tr key={vendor.id}>
                    <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                      {vendor.name}
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {vendor.type}
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap">
                      <span className={`inline-flex items-center px-3 py-1 rounded-full text-xs font-medium ${getStatusColor(vendor.status)}`}>
                        {vendor.status}
                      </span>
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      ⭐ {vendor.rating?.toFixed(1) || 'N/A'}
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap text-sm font-medium">
                      {vendor.status === 'pending' && (
                        <button
                          onClick={() => handleApproveVendor(vendor.id)}
                          className="text-green-600 hover:text-green-900 mr-3"
                        >
                          Approve
                        </button>
                      )}
                      <button
                        onClick={() => handleSuspendVendor(vendor.id)}
                        className="text-red-600 hover:text-red-900"
                      >
                        {vendor.status === 'suspended' ? 'Activate' : 'Suspend'}
                      </button>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      )}
    </div>
  );
};

export default withAuth(AdminPanel, ['admin']);