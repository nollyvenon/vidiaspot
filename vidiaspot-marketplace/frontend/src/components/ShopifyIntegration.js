import React, { useState, useEffect } from 'react';
import { withAuth } from '../utils/withAuth';
import shopifyService from '../services/shopifyService';

const ShopifyIntegration = () => {
  const [shopifyStores, setShopifyStores] = useState([]);
  const [products, setProducts] = useState([]);
  const [orders, setOrders] = useState([]);
  const [loading, setLoading] = useState(true);
  const [activeTab, setActiveTab] = useState('stores');
  const [newStore, setNewStore] = useState({ name: '', domain: '', apiKey: '', password: '' });
  const [error, setError] = useState(null);
  const [currentStoreId, setCurrentStoreId] = useState(null);

  // Fetch Shopify data
  useEffect(() => {
    const fetchData = async () => {
      try {
        setLoading(true);
        const stores = await shopifyService.getStores();
        setShopifyStores(stores);
      } catch (err) {
        setError('Failed to fetch Shopify stores. Please try again later.');
        console.error('Error fetching Shopify stores:', err);
      } finally {
        setLoading(false);
      }
    };

    fetchData();
  }, []);

  // Fetch products/orders when currentStoreId changes and activeTab is products or orders
  useEffect(() => {
    const fetchStoreData = async () => {
      if (!currentStoreId) return;

      try {
        setLoading(true);
        setError(null);

        if (activeTab === 'products') {
          const storeProducts = await shopifyService.getStoreProducts(currentStoreId);
          setProducts(storeProducts);
        } else if (activeTab === 'orders') {
          const storeOrders = await shopifyService.getStoreOrders(currentStoreId);
          setOrders(storeOrders);
        }
      } catch (err) {
        setError(`Failed to fetch ${activeTab}. Please try again later.`);
        console.error(`Error fetching ${activeTab}:`, err);
      } finally {
        setLoading(false);
      }
    };

    fetchStoreData();
  }, [activeTab, currentStoreId]);

  const handleAddStore = async (e) => {
    e.preventDefault();

    try {
      setLoading(true);
      const storeData = {
        name: newStore.name,
        domain: newStore.domain,
        apiKey: newStore.apiKey,
        password: newStore.password,
      };

      const newStoreResult = await shopifyService.connectStore(storeData);
      setShopifyStores([...shopifyStores, newStoreResult]);
      setNewStore({ name: '', domain: '', apiKey: '', password: '' });
      setError(null);
    } catch (err) {
      setError('Failed to connect Shopify store. Please check your credentials and try again.');
      console.error('Error connecting Shopify store:', err);
    } finally {
      setLoading(false);
    }
  };

  const handleSyncStore = async (storeId) => {
    try {
      setLoading(true);
      // Sync both products and orders for the store
      await shopifyService.syncProducts(storeId);
      await shopifyService.syncOrders(storeId);
      // Refresh the store list to show updated product/order counts
      const stores = await shopifyService.getStores();
      setShopifyStores(stores);
      setError(null);
    } catch (err) {
      setError('Failed to sync store. Please try again.');
      console.error('Error syncing store:', err);
    } finally {
      setLoading(false);
    }
  };

  const handleDisconnectStore = async (storeId) => {
    try {
      setLoading(true);
      await shopifyService.disconnectStore(storeId);
      // Remove the store from the local state
      setShopifyStores(shopifyStores.filter(store => store.id !== storeId));
      setError(null);
    } catch (err) {
      setError('Failed to disconnect store. Please try again.');
      console.error('Error disconnecting store:', err);
    } finally {
      setLoading(false);
    }
  };

  const handleStoreSelect = (storeId) => {
    setCurrentStoreId(storeId);
  };

  const getStatusColor = (status) => {
    switch (status) {
      case 'connected': return 'bg-green-100 text-green-800';
      case 'disconnected': return 'bg-red-100 text-red-800';
      case 'pending_connection': return 'bg-yellow-100 text-yellow-800';
      default: return 'bg-gray-100 text-gray-800';
    }
  };

  const getStatusText = (status) => {
    switch (status) {
      case 'connected': return 'Connected';
      case 'disconnected': return 'Disconnected';
      case 'pending_connection': return 'Pending Connection';
      default: return status;
    }
  };

  const getProductStatusColor = (status) => {
    switch (status) {
      case 'active': return 'bg-green-100 text-green-800';
      case 'low_stock': return 'bg-yellow-100 text-yellow-800';
      case 'out_of_stock': return 'bg-red-100 text-red-800';
      default: return 'bg-gray-100 text-gray-800';
    }
  };

  return (
    <div className="max-w-7xl mx-auto p-6">
      <div className="mb-8">
        <h1 className="text-3xl font-bold text-gray-900">Shopify Integration</h1>
        <p className="text-gray-600 mt-2">Connect and manage your Shopify stores</p>
      </div>

      {error && (
        <div className="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
          <strong className="font-bold">Error: </strong>
          <span className="block sm:inline">{error}</span>
        </div>
      )}

      <div className="border-b border-gray-200 mb-6">
        <nav className="-mb-px flex space-x-8">
          <button
            onClick={() => setActiveTab('stores')}
            className={`whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm ${
              activeTab === 'stores'
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            }`}
          >
            Connected Stores
          </button>
          <button
            onClick={() => setActiveTab('products')}
            className={`whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm ${
              activeTab === 'products'
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            }`}
          >
            Products
          </button>
          <button
            onClick={() => setActiveTab('orders')}
            className={`whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm ${
              activeTab === 'orders'
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            }`}
          >
            Orders
          </button>
          <button
            onClick={() => setActiveTab('sync')}
            className={`whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm ${
              activeTab === 'sync'
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            }`}
          >
            Sync Settings
          </button>
        </nav>
      </div>

      {activeTab === 'stores' && (
        <div>
          <div className="mb-6">
            <h2 className="text-xl font-semibold text-gray-900 mb-4">Connect New Shopify Store</h2>
            <form onSubmit={handleAddStore} className="bg-white rounded-lg shadow-md p-6">
              <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label htmlFor="name" className="block text-sm font-medium text-gray-700 mb-1">Store Name</label>
                  <input
                    type="text"
                    id="name"
                    value={newStore.name}
                    onChange={(e) => setNewStore({...newStore, name: e.target.value})}
                    className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required
                  />
                </div>
                <div>
                  <label htmlFor="domain" className="block text-sm font-medium text-gray-700 mb-1">Store Domain</label>
                  <input
                    type="text"
                    id="domain"
                    value={newStore.domain}
                    onChange={(e) => setNewStore({...newStore, domain: e.target.value})}
                    className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="your-store.myshopify.com"
                    required
                  />
                </div>
                <div>
                  <label htmlFor="apiKey" className="block text-sm font-medium text-gray-700 mb-1">API Key</label>
                  <input
                    type="password"
                    id="apiKey"
                    value={newStore.apiKey}
                    onChange={(e) => setNewStore({...newStore, apiKey: e.target.value})}
                    className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required
                  />
                </div>
                <div>
                  <label htmlFor="password" className="block text-sm font-medium text-gray-700 mb-1">API Password</label>
                  <input
                    type="password"
                    id="password"
                    value={newStore.password}
                    onChange={(e) => setNewStore({...newStore, password: e.target.value})}
                    className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required
                  />
                </div>
              </div>
              <div className="mt-6">
                <button
                  type="submit"
                  className="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md"
                  disabled={loading}
                >
                  {loading ? 'Connecting...' : 'Connect Store'}
                </button>
              </div>
            </form>
          </div>

          <div className="bg-white rounded-lg shadow-md overflow-hidden">
            <div className="px-6 py-4 border-b border-gray-200">
              <h2 className="text-lg font-medium text-gray-900">Connected Stores</h2>
            </div>
            <div className="overflow-x-auto">
              <table className="min-w-full divide-y divide-gray-200">
                <thead className="bg-gray-50">
                  <tr>
                    <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Store Name
                    </th>
                    <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Domain
                    </th>
                    <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Status
                    </th>
                    <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Products
                    </th>
                    <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Orders
                    </th>
                    <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Actions
                    </th>
                  </tr>
                </thead>
                <tbody className="bg-white divide-y divide-gray-200">
                  {shopifyStores.map(store => (
                    <tr key={store.id}>
                      <td className="px-6 py-4 whitespace-nowrap">
                        <div className="text-sm font-medium text-gray-900">{store.name}</div>
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {store.domain}
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap">
                        <span className={`inline-flex items-center px-3 py-1 rounded-full text-xs font-medium ${getStatusColor(store.status)}`}>
                          {getStatusText(store.status)}
                        </span>
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {store.products}
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {store.orders}
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <button
                          onClick={() => handleSyncStore(store.id)}
                          className="text-blue-600 hover:text-blue-900 mr-3"
                          disabled={loading}
                        >
                          {loading ? 'Syncing...' : 'Sync'}
                        </button>
                        <button
                          onClick={() => handleDisconnectStore(store.id)}
                          className="text-red-600 hover:text-red-900"
                          disabled={loading}
                        >
                          {loading ? 'Processing...' : 'Disconnect'}
                        </button>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </div>
        </div>
      )}

      {activeTab === 'products' && (
        <div>
          {shopifyStores.length > 0 && (
            <div className="mb-6">
              <label htmlFor="storeSelect" className="block text-sm font-medium text-gray-700 mb-2">Select Store</label>
              <select
                id="storeSelect"
                value={currentStoreId || ''}
                onChange={(e) => setCurrentStoreId(Number(e.target.value))}
                className="w-full md:w-1/3 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
                <option value="">Select a store to view products</option>
                {shopifyStores.map(store => (
                  <option key={store.id} value={store.id}>{store.name}</option>
                ))}
              </select>
            </div>
          )}
          {currentStoreId && (
            <div className="bg-white rounded-lg shadow-md overflow-hidden">
              <div className="px-6 py-4 border-b border-gray-200">
                <h2 className="text-lg font-medium text-gray-900">Products</h2>
              </div>
              <div className="overflow-x-auto">
                <table className="min-w-full divide-y divide-gray-200">
                  <thead className="bg-gray-50">
                    <tr>
                      <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Product
                      </th>
                      <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Price
                      </th>
                      <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Inventory
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
                    {products.map(product => (
                      <tr key={product.id}>
                        <td className="px-6 py-4 whitespace-nowrap">
                          <div className="text-sm font-medium text-gray-900">{product.title}</div>
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                          ${product.price}
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                          {product.inventory}
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap">
                          <span className={`inline-flex items-center px-3 py-1 rounded-full text-xs font-medium ${getProductStatusColor(product.status)}`}>
                            {product.status.replace('_', ' ').toUpperCase()}
                          </span>
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap text-sm font-medium">
                          <button className="text-blue-600 hover:text-blue-900 mr-3">Edit</button>
                          <button className="text-green-600 hover:text-green-900">Sync</button>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            </div>
          )}
          {!currentStoreId && (
            <div className="bg-white rounded-lg shadow-md p-6 text-center">
              <p className="text-gray-600">Please select a store to view its products.</p>
            </div>
          )}
        </div>
      )}

      {activeTab === 'orders' && (
        <div>
          {shopifyStores.length > 0 && (
            <div className="mb-6">
              <label htmlFor="orderStoreSelect" className="block text-sm font-medium text-gray-700 mb-2">Select Store</label>
              <select
                id="orderStoreSelect"
                value={currentStoreId || ''}
                onChange={(e) => setCurrentStoreId(Number(e.target.value))}
                className="w-full md:w-1/3 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
                <option value="">Select a store to view orders</option>
                {shopifyStores.map(store => (
                  <option key={store.id} value={store.id}>{store.name}</option>
                ))}
              </select>
            </div>
          )}
          {currentStoreId && (
            <div className="bg-white rounded-lg shadow-md overflow-hidden">
              <div className="px-6 py-4 border-b border-gray-200">
                <h2 className="text-lg font-medium text-gray-900">Orders</h2>
              </div>
              <div className="overflow-x-auto">
                <table className="min-w-full divide-y divide-gray-200">
                  <thead className="bg-gray-50">
                    <tr>
                      <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Order #
                      </th>
                      <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Customer
                      </th>
                      <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Total
                      </th>
                      <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                      </th>
                      <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Date
                      </th>
                      <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Actions
                      </th>
                    </tr>
                  </thead>
                  <tbody className="bg-white divide-y divide-gray-200">
                    {orders.map(order => (
                      <tr key={order.id}>
                        <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                          #{order.id.toString().padStart(6, '0')}
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                          {order.customer}
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                          ${order.total}
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap">
                          <span className={`inline-flex items-center px-3 py-1 rounded-full text-xs font-medium ${
                            order.status === 'fulfilled' ? 'bg-green-100 text-green-800' :
                            order.status === 'processing' ? 'bg-yellow-100 text-yellow-800' :
                            order.status === 'pending' ? 'bg-blue-100 text-blue-800' :
                            'bg-red-100 text-red-800'
                          }`}>
                            {order.status.replace('_', ' ').toUpperCase()}
                          </span>
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                          {order.date}
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap text-sm font-medium">
                          <button className="text-blue-600 hover:text-blue-900 mr-3">View</button>
                          <button className="text-green-600 hover:text-green-900">Fulfill</button>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            </div>
          )}
          {!currentStoreId && (
            <div className="bg-white rounded-lg shadow-md p-6 text-center">
              <p className="text-gray-600">Please select a store to view its orders.</p>
            </div>
          )}
        </div>
      )}

      {activeTab === 'sync' && (
        <div className="bg-white rounded-lg shadow-md p-6">
          <h2 className="text-xl font-semibold text-gray-900 mb-6">Sync Settings</h2>
          
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div className="border rounded-lg p-6">
              <h3 className="text-lg font-medium text-gray-900 mb-4">Product Sync</h3>
              <div className="space-y-4">
                <div className="flex items-center justify-between">
                  <span>Auto-sync products</span>
                  <button className="relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none bg-blue-600">
                    <span className="sr-only">Use setting</span>
                    <span className="pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200 translate-x-5"></span>
                  </button>
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Sync frequency</label>
                  <select className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option>Every 15 minutes</option>
                    <option>Every 30 minutes</option>
                    <option>Every hour</option>
                    <option>Every 6 hours</option>
                    <option>Every 12 hours</option>
                    <option>Daily</option>
                  </select>
                </div>
              </div>
            </div>
            
            <div className="border rounded-lg p-6">
              <h3 className="text-lg font-medium text-gray-900 mb-4">Order Sync</h3>
              <div className="space-y-4">
                <div className="flex items-center justify-between">
                  <span>Auto-sync orders</span>
                  <button className="relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none bg-blue-600">
                    <span className="sr-only">Use setting</span>
                    <span className="pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200 translate-x-5"></span>
                  </button>
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Default fulfillment status</label>
                  <select className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option>Unfulfilled</option>
                    <option>Fulfilled</option>
                    <option>Partially Fulfilled</option>
                  </select>
                </div>
              </div>
            </div>
          </div>
          
          <div className="mt-6 pt-6 border-t border-gray-200">
            <h3 className="text-lg font-medium text-gray-900 mb-4">Manual Sync</h3>
            <div className="flex flex-wrap gap-4">
              {shopifyStores.length > 0 ? (
                shopifyStores.map(store => (
                  <div key={store.id} className="w-full md:w-auto">
                    <p className="mb-2 font-medium">{store.name}</p>
                    <div className="flex gap-2">
                      <button
                        onClick={() => handleSyncStore(store.id)}
                        className="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md"
                        disabled={loading}
                      >
                        {loading ? 'Syncing...' : 'Sync Products'}
                      </button>
                      <button
                        onClick={() => {
                          setCurrentStoreId(store.id);
                          setActiveTab('orders');
                        }}
                        className="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md"
                      >
                        View Orders
                      </button>
                    </div>
                  </div>
                ))
              ) : (
                <p className="text-gray-600">Connect a store to enable manual sync.</p>
              )}
            </div>
          </div>
        </div>
      )}
    </div>
  );
};

export default withAuth(ShopifyIntegration, ['store_owner', 'seller', 'admin']);