import React, { useState, useEffect } from 'react';
import { withAuth } from '../utils/withAuth';
import storeService from '../services/storeService';

const StoreManagement = () => {
  const [activeTab, setActiveTab] = useState('setup');
  const [store, setStore] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [themes, setThemes] = useState([]);
  
  const [storeFormData, setStoreFormData] = useState({
    name: '',
    slug: '',
    description: '',
    logo: null,
    cover_image: null,
    contact_email: '',
    contact_phone: '',
    address: '',
    timezone: '',
    currency: 'USD',
    theme_id: null,
    settings: {}
  });
  
  const [domainData, setDomainData] = useState({
    custom_domain: '',
    ssl_enabled: true,
    cname_records: []
  });

  useEffect(() => {
    loadStoreData();
  }, []);

  const loadStoreData = async () => {
    try {
      setLoading(true);
      // Try to get user's store
      const storeData = await storeService.getMyStore();
      if (storeData) {
        setStore(storeData);
        setStoreFormData({
          name: storeData.name,
          slug: storeData.slug,
          description: storeData.description,
          contact_email: storeData.contact_email,
          contact_phone: storeData.contact_phone,
          address: storeData.address,
          timezone: storeData.timezone || Intl.DateTimeFormat().resolvedOptions().timeZone,
          currency: storeData.currency || 'USD',
          theme_id: storeData.theme_id
        });
      }
      
      // Load available themes
      const themeList = await storeService.getStoreThemes();
      setThemes(themeList);
    } catch (err) {
      setError('No store found. You can create one below.');
      console.error('Error loading store data:', err);
    } finally {
      setLoading(false);
    }
  };

  const handleStoreSubmit = async (e) => {
    e.preventDefault();
    
    try {
      if (store) {
        // Update existing store
        const updatedStore = await storeService.updateStore(store.id, storeFormData);
        setStore(updatedStore);
        alert('Store updated successfully!');
      } else {
        // Create new store
        const newStore = await storeService.createStore(storeFormData);
        setStore(newStore);
        alert('Store created successfully!');
      }
    } catch (err) {
      setError('Failed to save store. Please try again.');
      console.error('Error saving store:', err);
    }
  };

  const handleDomainSubmit = async (e) => {
    e.preventDefault();
    
    try {
      if (!store) {
        setError('Please create your store first before adding a domain.');
        return;
      }
      
      const result = await storeService.addCustomDomain(store.id, domainData);
      alert('Domain added successfully! Please update your DNS settings as instructed.');
    } catch (err) {
      setError('Failed to add domain. Please check the domain and try again.');
      console.error('Error adding domain:', err);
    }
  };

  const handleVerifyDomain = async () => {
    try {
      if (!store || !domainData.custom_domain) {
        setError('Please enter a domain to verify.');
        return;
      }
      
      const result = await storeService.verifyDomain(store.id, domainData.custom_domain);
      alert(result.message || 'Domain verification initiated!');
    } catch (err) {
      setError('Domain verification failed. Please check your DNS settings.');
      console.error('Error verifying domain:', err);
    }
  };

  if (loading && !store) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <div className="animate-spin rounded-full h-32 w-32 border-b-2 border-blue-600"></div>
      </div>
    );
  }

  return (
    <div className="max-w-7xl mx-auto p-6">
      <div className="mb-8">
        <h1 className="text-3xl font-bold text-gray-900">Store Management</h1>
        <p className="text-gray-600">Create and manage your e-commerce store</p>
      </div>

      {error && (
        <div className="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
          <strong className="font-bold">Error: </strong>
          <span className="block sm:inline">{error}</span>
        </div>
      )}

      {/* Tab Navigation */}
      <div className="border-b border-gray-200 mb-6">
        <nav className="-mb-px flex flex-wrap">
          <button
            onClick={() => setActiveTab('setup')}
            className={`whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm ${
              activeTab === 'setup'
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            }`}
          >
            Store Setup
          </button>
          <button
            onClick={() => setActiveTab('domain')}
            className={`whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm ${
              activeTab === 'domain'
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            }`}
          >
            Domain Management
          </button>
          <button
            onClick={() => setActiveTab('design')}
            className={`whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm ${
              activeTab === 'design'
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            }`}
          >
            Design & Themes
          </button>
          <button
            onClick={() => setActiveTab('analytics')}
            className={`whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm ${
              activeTab === 'analytics'
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            }`}
          >
            Analytics
          </button>
        </nav>
      </div>

      {/* Store Setup Tab */}
      {activeTab === 'setup' && (
        <div className="bg-white rounded-lg shadow-md p-6">
          <h2 className="text-xl font-semibold text-gray-900 mb-4">
            {store ? 'Update Your Store' : 'Create Your Store'}
          </h2>
          
          <form onSubmit={handleStoreSubmit} className="space-y-6">
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Store Name *</label>
                <input
                  type="text"
                  value={storeFormData.name}
                  onChange={(e) => setStoreFormData({...storeFormData, name: e.target.value})}
                  className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  required
                />
              </div>
              
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Store Slug *</label>
                <input
                  type="text"
                  value={storeFormData.slug}
                  onChange={(e) => setStoreFormData({...storeFormData, slug: e.target.value.toLowerCase().replace(/\s+/g, '-')})}
                  className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="your-store-name"
                  required
                />
                <p className="mt-1 text-sm text-gray-500">This will be part of your store URL</p>
              </div>
              
              <div className="md:col-span-2">
                <label className="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea
                  value={storeFormData.description}
                  onChange={(e) => setStoreFormData({...storeFormData, description: e.target.value})}
                  className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  rows="3"
                ></textarea>
              </div>
              
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Contact Email *</label>
                <input
                  type="email"
                  value={storeFormData.contact_email}
                  onChange={(e) => setStoreFormData({...storeFormData, contact_email: e.target.value})}
                  className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  required
                />
              </div>
              
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Contact Phone</label>
                <input
                  type="tel"
                  value={storeFormData.contact_phone}
                  onChange={(e) => setStoreFormData({...storeFormData, contact_phone: e.target.value})}
                  className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
              </div>
              
              <div className="md:col-span-2">
                <label className="block text-sm font-medium text-gray-700 mb-1">Address</label>
                <textarea
                  value={storeFormData.address}
                  onChange={(e) => setStoreFormData({...storeFormData, address: e.target.value})}
                  className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  rows="2"
                ></textarea>
              </div>
              
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Timezone</label>
                <select
                  value={storeFormData.timezone}
                  onChange={(e) => setStoreFormData({...storeFormData, timezone: e.target.value})}
                  className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                  <option value="UTC">UTC</option>
                  <option value="America/New_York">Eastern Time (ET)</option>
                  <option value="America/Chicago">Central Time (CT)</option>
                  <option value="America/Denver">Mountain Time (MT)</option>
                  <option value="America/Los_Angeles">Pacific Time (PT)</option>
                  <option value="Europe/London">London (GMT)</option>
                  <option value="Europe/Berlin">Berlin (CET)</option>
                  <option value="Asia/Tokyo">Tokyo</option>
                  <option value="Asia/Shanghai">Shanghai</option>
                </select>
              </div>
              
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Currency</label>
                <select
                  value={storeFormData.currency}
                  onChange={(e) => setStoreFormData({...storeFormData, currency: e.target.value})}
                  className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                  <option value="USD">USD ($)</option>
                  <option value="EUR">EUR (€)</option>
                  <option value="GBP">GBP (£)</option>
                  <option value="NGN">NGN (₦)</option>
                  <option value="CAD">CAD ($)</option>
                  <option value="AUD">AUD ($)</option>
                </select>
              </div>
            </div>
            
            <div className="flex justify-end">
              <button
                type="submit"
                className="bg-blue-600 hover:bg-blue-700 text-white py-2 px-6 rounded-md"
              >
                {store ? 'Update Store' : 'Create Store'}
              </button>
            </div>
          </form>
        </div>
      )}

      {/* Domain Management Tab */}
      {activeTab === 'domain' && (
        <div className="bg-white rounded-lg shadow-md p-6">
          <h2 className="text-xl font-semibold text-gray-900 mb-4">Domain Management</h2>
          <p className="text-gray-600 mb-6">Connect your own domain to your store</p>
          
          {!store ? (
            <div className="bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded-md">
              <p>Please create your store first before connecting a custom domain.</p>
            </div>
          ) : (
            <div className="space-y-6">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Custom Domain *</label>
                <input
                  type="text"
                  value={domainData.custom_domain}
                  onChange={(e) => setDomainData({...domainData, custom_domain: e.target.value})}
                  className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="mystore.com"
                />
                <p className="mt-1 text-sm text-gray-500">Enter your custom domain to connect to your store</p>
              </div>
              
              <div>
                <label className="flex items-center">
                  <input
                    type="checkbox"
                    checked={domainData.ssl_enabled}
                    onChange={(e) => setDomainData({...domainData, ssl_enabled: e.target.checked})}
                    className="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                  />
                  <span className="ml-2 text-sm text-gray-700">Enable SSL Certificate</span>
                </label>
              </div>
              
              <div className="bg-gray-50 p-4 rounded-md">
                <h3 className="font-medium text-gray-900 mb-2">DNS Setup Instructions</h3>
                <p className="text-sm text-gray-600 mb-3">To connect your domain, update your DNS settings with these records:</p>
                <div className="space-y-2 text-sm">
                  <div className="font-mono bg-white p-3 rounded-sm">
                    <div>Type: A Record</div>
                    <div>Name/Host: @</div>
                    <div>Value: {window.location.hostname}</div>
                  </div>
                  <div className="font-mono bg-white p-3 rounded-sm">
                    <div>Type: A Record</div>
                    <div>Name/Host: www</div>
                    <div>Value: {window.location.hostname}</div>
                  </div>
                </div>
                <p className="mt-3 text-xs text-gray-500">
                  Note: Propagation may take up to 48 hours. Change your nameservers if your domain registrar supports sub-domain settings.
                </p>
              </div>
              
              <div className="flex space-x-4">
                <button
                  onClick={handleDomainSubmit}
                  className="bg-green-600 hover:bg-green-700 text-white py-2 px-6 rounded-md"
                >
                  Add Domain
                </button>
                <button
                  onClick={handleVerifyDomain}
                  className="bg-blue-600 hover:bg-blue-700 text-white py-2 px-6 rounded-md"
                >
                  Verify Domain
                </button>
              </div>
            </div>
          )}
        </div>
      )}

      {/* Design & Themes Tab */}
      {activeTab === 'design' && (
        <div className="bg-white rounded-lg shadow-md p-6">
          <h2 className="text-xl font-semibold text-gray-900 mb-4">Store Design & Themes</h2>
          <p className="text-gray-600 mb-6">Customize the appearance of your store</p>
          
          {!store ? (
            <div className="bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded-md">
              <p>Please create your store first before customizing the design.</p>
            </div>
          ) : (
            <div className="space-y-6">
              <div>
                <h3 className="text-lg font-medium text-gray-900 mb-4">Choose a Theme</h3>
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                  {themes.map((theme) => (
                    <div key={theme.id} className="border rounded-lg p-4 hover:shadow-lg transition-shadow">
                      <div className="bg-gray-200 border-2 border-dashed rounded-xl w-full h-48 mb-4"></div>
                      <h4 className="font-medium text-gray-900">{theme.name}</h4>
                      <p className="text-sm text-gray-600 mb-4">{theme.description}</p>
                      <button
                        onClick={() => {
                          setStoreFormData({...storeFormData, theme_id: theme.id});
                          storeService.applyStoreTheme(store.id, theme.id);
                        }}
                        className={`w-full py-2 rounded-md ${
                          storeFormData.theme_id === theme.id
                            ? 'bg-blue-600 text-white'
                            : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                        }`}
                      >
                        {storeFormData.theme_id === theme.id ? 'Active' : 'Activate'}
                      </button>
                    </div>
                  ))}
                </div>
              </div>
              
              <div>
                <h3 className="text-lg font-medium text-gray-900 mb-4">Customize Colors</h3>
                <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-1">Primary Color</label>
                    <input
                      type="color"
                      value={storeFormData.settings.primary_color || '#3B82F6'}
                      onChange={(e) => setStoreFormData({
                        ...storeFormData,
                        settings: {...storeFormData.settings, primary_color: e.target.value}
                      })}
                      className="w-12 h-12 border border-gray-300 rounded-md"
                    />
                  </div>
                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-1">Secondary Color</label>
                    <input
                      type="color"
                      value={storeFormData.settings.secondary_color || '#10B981'}
                      onChange={(e) => setStoreFormData({
                        ...storeFormData,
                        settings: {...storeFormData.settings, secondary_color: e.target.value}
                      })}
                      className="w-12 h-12 border border-gray-300 rounded-md"
                    />
                  </div>
                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-1">Background Color</label>
                    <input
                      type="color"
                      value={storeFormData.settings.background_color || '#FFFFFF'}
                      onChange={(e) => setStoreFormData({
                        ...storeFormData,
                        settings: {...storeFormData.settings, background_color: e.target.value}
                      })}
                      className="w-12 h-12 border border-gray-300 rounded-md"
                    />
                  </div>
                </div>
              </div>
              
              <div className="flex justify-end">
                <button
                  onClick={() => {
                    storeService.updateStoreSettings(store.id, storeFormData.settings);
                    alert('Store design updated successfully!');
                  }}
                  className="bg-blue-600 hover:bg-blue-700 text-white py-2 px-6 rounded-md"
                >
                  Save Design
                </button>
              </div>
            </div>
          )}
        </div>
      )}

      {/* Analytics Tab */}
      {activeTab === 'analytics' && (
        <div className="bg-white rounded-lg shadow-md p-6">
          <h2 className="text-xl font-semibold text-gray-900 mb-4">Store Analytics</h2>
          <p className="text-gray-600 mb-6">Track your store performance and sales</p>
          
          {!store ? (
            <div className="bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded-md">
              <p>Please create your store first to view analytics.</p>
            </div>
          ) : (
            <div>
              <div className="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div className="bg-blue-50 rounded-lg p-4">
                  <h3 className="font-medium text-blue-900">Total Revenue</h3>
                  <p className="text-2xl font-bold text-blue-600">$0</p>
                  <p className="text-sm text-blue-500">Last 30 days</p>
                </div>
                <div className="bg-green-50 rounded-lg p-4">
                  <h3 className="font-medium text-green-900">Total Orders</h3>
                  <p className="text-2xl font-bold text-green-600">0</p>
                  <p className="text-sm text-green-500">Last 30 days</p>
                </div>
                <div className="bg-purple-50 rounded-lg p-4">
                  <h3 className="font-medium text-purple-900">Visitors</h3>
                  <p className="text-2xl font-bold text-purple-600">0</p>
                  <p className="text-sm text-purple-500">Last 30 days</p>
                </div>
                <div className="bg-yellow-50 rounded-lg p-4">
                  <h3 className="font-medium text-yellow-900">Conversion Rate</h3>
                  <p className="text-2xl font-bold text-yellow-600">0%</p>
                  <p className="text-sm text-yellow-500">Last 30 days</p>
                </div>
              </div>
              
              <div className="bg-gray-50 p-6 rounded-lg">
                <h3 className="font-medium text-gray-900 mb-4">Quick Actions</h3>
                <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <button 
                    className="bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 rounded-md"
                    onClick={() => window.open(`${window.location.origin}/store/${store.slug}`, '_blank')}
                  >
                    Visit Your Store
                  </button>
                  <button 
                    className="bg-green-600 hover:bg-green-700 text-white py-3 px-4 rounded-md"
                    onClick={() => setActiveTab('setup')}
                  >
                    Edit Store Details
                  </button>
                  <button 
                    className="bg-purple-600 hover:bg-purple-700 text-white py-3 px-4 rounded-md"
                    onClick={() => setActiveTab('domain')}
                  >
                    Manage Domain
                  </button>
                </div>
              </div>
            </div>
          )}
        </div>
      )}
    </div>
  );
};

export default withAuth(StoreManagement, ['seller', 'store_owner']);