import React, { useState, useEffect } from 'react';
import { withAuth } from '../utils/withAuth';
import foodVendorService from '../services/foodVendorService';
import cartService from '../services/cartService';

const FoodOrderingSystem = () => {
  const [vendors, setVendors] = useState([]);
  const [selectedVendor, setSelectedVendor] = useState(null);
  const [vendorMenu, setVendorMenu] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [searchQuery, setSearchQuery] = useState('');
  const [cuisineFilter, setCuisineFilter] = useState('');
  const [cartItems, setCartItems] = useState([]);
  const [showCart, setShowCart] = useState(false);
  const [activeTab, setActiveTab] = useState('vendors'); // vendors, menu, cart
  const [orderNotes, setOrderNotes] = useState('');
  const [deliveryAddress, setDeliveryAddress] = useState('');
  const [deliveryTime, setDeliveryTime] = useState('asap');
  const [cartLoading, setCartLoading] = useState({});

  // Fetch vendors
  useEffect(() => {
    fetchVendors();
  }, [searchQuery, cuisineFilter]);

  const fetchVendors = async () => {
    try {
      setLoading(true);
      const params = {
        search: searchQuery,
        cuisine: cuisineFilter
      };
      
      const response = await foodVendorService.getVendors(params);
      setVendors(response.data || response.vendors || []);
      setError(null);
    } catch (err) {
      setError('Failed to fetch vendors. Please try again later.');
      console.error('Error fetching vendors:', err);
    } finally {
      setLoading(false);
    }
  };

  // Fetch cart items
  const fetchCartItems = async () => {
    try {
      const cartData = await cartService.getCart();
      setCartItems(cartData.items || cartData || []);
    } catch (err) {
      console.error('Error fetching cart:', err);
    }
  };

  useEffect(() => {
    fetchCartItems();
  }, []);

  // Fetch menu when vendor is selected
  useEffect(() => {
    if (selectedVendor) {
      fetchVendorMenu(selectedVendor.id);
    }
  }, [selectedVendor]);

  const fetchVendorMenu = async (vendorId) => {
    try {
      const menu = await foodVendorService.getVendorMenu(vendorId);
      setVendorMenu(menu.items || menu.menu || []);
    } catch (err) {
      setError('Failed to fetch menu. Please try again.');
      console.error('Error fetching menu:', err);
    }
  };

  const handleAddToCart = async (item, quantity = 1) => {
    try {
      setCartLoading(prev => ({ ...prev, [item.id]: true }));
      await cartService.addToCart(item.id, quantity);
      fetchCartItems(); // Refresh cart
      setActiveTab('cart');
    } catch (err) {
      setError('Failed to add item to cart. Please try again.');
      console.error('Error adding to cart:', err);
    } finally {
      setCartLoading(prev => ({ ...prev, [item.id]: false }));
    }
  };

  const handleVendorSelect = (vendor) => {
    setSelectedVendor(vendor);
    setActiveTab('menu');
  };

  const handlePlaceOrder = async () => {
    try {
      setLoading(true);
      const orderData = {
        items: cartItems.map(item => ({
          id: item.id,
          quantity: item.quantity,
          variant_id: item.variant_id
        })),
        delivery_address: deliveryAddress,
        delivery_time: deliveryTime,
        notes: orderNotes,
        order_type: 'food'
      };

      await foodVendorService.placeOrder(orderData);
      
      // Clear cart after order
      await cartService.clearCart();
      fetchCartItems();
      
      alert('Order placed successfully!');
      setActiveTab('vendors');
      setOrderNotes('');
      setDeliveryAddress('');
      setDeliveryTime('asap');
    } catch (err) {
      setError('Failed to place order. Please try again.');
      console.error('Error placing order:', err);
    } finally {
      setLoading(false);
    }
  };

  const getTotalCartItems = () => {
    return cartItems.reduce((total, item) => total + (item.quantity || 1), 0);
  };

  const getCartTotal = () => {
    return cartItems.reduce((total, item) => {
      const price = item.price || item.product?.price || 0;
      return total + (price * (item.quantity || 1));
    }, 0);
  };

  const getStatusColor = (status) => {
    switch (status) {
      case 'open': return 'bg-green-100 text-green-800';
      case 'closed': return 'bg-red-100 text-red-800';
      case 'busy': return 'bg-yellow-100 text-yellow-800';
      default: return 'bg-gray-100 text-gray-800';
    }
  };

  if (loading && vendors.length === 0) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <div className="animate-spin rounded-full h-32 w-32 border-b-2 border-blue-600"></div>
      </div>
    );
  }

  return (
    <div className="max-w-7xl mx-auto p-6">
      <div className="mb-8">
        <h1 className="text-3xl font-bold text-gray-900">Food Delivery & Ordering System</h1>
        <p className="text-gray-600">Order from restaurants, food vendors, and delivery hubs near you</p>
      </div>

      {error && (
        <div className="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
          <strong className="font-bold">Error: </strong>
          <span className="block sm:inline">{error}</span>
        </div>
      )}

      {/* Tab Navigation */}
      <div className="border-b border-gray-200 mb-6">
        <nav className="-mb-px flex space-x-8">
          <button
            onClick={() => setActiveTab('vendors')}
            className={`whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm ${
              activeTab === 'vendors'
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            }`}
          >
            Restaurants
          </button>
          <button
            onClick={() => setActiveTab('menu')}
            disabled={!selectedVendor}
            className={`whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm ${
              activeTab === 'menu' && selectedVendor
                ? 'border-blue-500 text-blue-600'
                : selectedVendor
                  ? 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                  : 'border-transparent text-gray-300 cursor-not-allowed'
            }`}
          >
            {selectedVendor?.name || 'Menu'}
          </button>
          <button
            onClick={() => setActiveTab('cart')}
            className={`whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm ${
              activeTab === 'cart'
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            }`}
          >
            Cart ({getTotalCartItems()})
          </button>
          <button
            onClick={() => setActiveTab('orders')}
            className={`whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm ${
              activeTab === 'orders'
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            }`}
          >
            My Orders
          </button>
        </nav>
      </div>

      {/* Search and Filter Bar */}
      {activeTab === 'vendors' && (
        <div className="mb-6 flex flex-col md:flex-row gap-4 items-center justify-between">
          <div className="flex flex-col sm:flex-row gap-4 w-full md:w-auto">
            <input
              type="text"
              placeholder="Search restaurants, cuisines, or dishes..."
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
              className="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
            <select
              value={cuisineFilter}
              onChange={(e) => setCuisineFilter(e.target.value)}
              className="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <option value="">All Cuisines</option>
              <option value="italian">Italian</option>
              <option value="mexican">Mexican</option>
              <option value="chinese">Chinese</option>
              <option value="indian">Indian</option>
              <option value="american">American</option>
              <option value="thai">Thai</option>
              <option value="pizza">Pizza</option>
              <option value="burgers">Burgers</option>
              <option value="healthy">Healthy</option>
              <option value="fast_food">Fast Food</option>
              <option value="desserts">Desserts</option>
            </select>
          </div>
        </div>
      )}

      {/* Vendors Tab */}
      {activeTab === 'vendors' && (
        <div>
          {loading ? (
            <div className="flex justify-center items-center h-64">
              <div className="animate-spin rounded-full h-32 w-32 border-b-2 border-blue-600"></div>
            </div>
          ) : (
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              {vendors.map((vendor) => (
                <div key={vendor.id} className="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                  {vendor.image && (
                    <img
                      src={vendor.image}
                      alt={vendor.name}
                      className="w-full h-48 object-cover"
                      onError={(e) => {
                        e.target.src = 'https://via.placeholder.com/400x200?text=Restaurant+Image';
                      }}
                    />
                  )}
                  <div className="p-6">
                    <div className="flex justify-between items-start mb-2">
                      <h3 className="text-xl font-semibold text-gray-900">{vendor.name}</h3>
                      <span className={`inline-flex items-center px-3 py-1 rounded-full text-xs font-medium ${getStatusColor(vendor.status)}`}>
                        {vendor.status?.replace('_', ' ') || 'open'}
                      </span>
                    </div>
                    <p className="text-gray-600 mb-2">{vendor.cuisine} • {vendor.category}</p>
                    <p className="text-gray-500 text-sm mb-4">{vendor.description}</p>
                    <div className="flex justify-between items-center text-sm text-gray-600 mb-4">
                      <span>⭐ {vendor.rating?.toFixed(1) || '4.5'}</span>
                      <span>{vendor.delivery_time} mins</span>
                      <span>{vendor.delivery_fee ? `$${vendor.delivery_fee}` : 'Free delivery'}</span>
                    </div>
                    <button
                      onClick={() => handleVendorSelect(vendor)}
                      className="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md font-medium"
                    >
                      View Menu
                    </button>
                  </div>
                </div>
              ))}
            </div>
          )}
        </div>
      )}

      {/* Menu Tab */}
      {activeTab === 'menu' && selectedVendor && (
        <div>
          <div className="mb-6 flex items-center justify-between">
            <h2 className="text-2xl font-bold text-gray-900">{selectedVendor.name} Menu</h2>
            <button
              onClick={() => setActiveTab('vendors')}
              className="text-blue-600 hover:text-blue-800"
            >
              ← Back to Vendors
            </button>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            {vendorMenu.map((item) => (
              <div key={item.id} className="bg-white rounded-lg shadow-md p-4">
                <div className="flex">
                  {item.image && (
                    <img
                      src={item.image}
                      alt={item.name}
                      className="w-20 h-20 object-cover rounded-md mr-4"
                      onError={(e) => {
                        e.target.src = 'https://via.placeholder.com/80x80?text=Item';
                      }}
                    />
                  )}
                  <div className="flex-1">
                    <h3 className="text-lg font-semibold text-gray-900">{item.name}</h3>
                    <p className="text-gray-600 text-sm mb-2">{item.description}</p>
                    <p className="text-lg font-bold text-gray-900">${item.price?.toFixed(2)}</p>
                    {item.ingredients && (
                      <p className="text-xs text-gray-500">Ingredients: {item.ingredients}</p>
                    )}
                    {item.dietary_info && (
                      <p className="text-xs text-gray-500">Dietary: {item.dietary_info}</p>
                    )}
                    <button
                      onClick={() => handleAddToCart(item, 1)}
                      disabled={cartLoading[item.id]}
                      className={`mt-2 w-full py-2 px-4 rounded-md font-medium ${
                        cartLoading[item.id]
                          ? 'bg-gray-400 text-white cursor-not-allowed'
                          : 'bg-blue-600 text-white hover:bg-blue-700'
                      }`}
                    >
                      {cartLoading[item.id] ? 'Adding...' : 'Add to Cart'}
                    </button>
                  </div>
                </div>
              </div>
            ))}
          </div>
        </div>
      )}

      {/* Cart Tab */}
      {activeTab === 'cart' && (
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <div className="lg:col-span-2">
            <h2 className="text-2xl font-bold text-gray-900 mb-4">Your Order</h2>
            
            {cartItems.length === 0 ? (
              <div className="bg-white rounded-lg shadow-md p-8 text-center">
                <p className="text-gray-500 text-lg">Your cart is empty</p>
                <button
                  onClick={() => setActiveTab('vendors')}
                  className="mt-4 bg-blue-600 text-white py-2 px-6 rounded-md hover:bg-blue-700"
                >
                  Browse Restaurants
                </button>
              </div>
            ) : (
              <div className="space-y-4">
                {cartItems.map((item) => (
                  <div key={item.id} className="bg-white rounded-lg shadow-md p-4 flex justify-between items-center">
                    <div>
                      <h3 className="font-medium">{item.name || item.product?.name}</h3>
                      <p className="text-gray-600">${(item.price || item.product?.price)?.toFixed(2)} x {item.quantity}</p>
                    </div>
                    <p className="font-semibold">${((item.price || item.product?.price) * item.quantity).toFixed(2)}</p>
                  </div>
                ))}
                
                <div className="bg-white rounded-lg shadow-md p-4">
                  <h3 className="text-lg font-semibold mb-4">Delivery Information</h3>
                  <div className="space-y-4">
                    <div>
                      <label className="block text-sm font-medium text-gray-700 mb-1">Delivery Address</label>
                      <input
                        type="text"
                        value={deliveryAddress}
                        onChange={(e) => setDeliveryAddress(e.target.value)}
                        className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Enter delivery address"
                      />
                    </div>
                    
                    <div>
                      <label className="block text-sm font-medium text-gray-700 mb-1">Delivery Time</label>
                      <select
                        value={deliveryTime}
                        onChange={(e) => setDeliveryTime(e.target.value)}
                        className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                      >
                        <option value="asap">As soon as possible</option>
                        <option value="30">In 30 minutes</option>
                        <option value="45">In 45 minutes</option>
                        <option value="60">In 1 hour</option>
                        <option value="later">Schedule for later</option>
                      </select>
                    </div>
                    
                    <div>
                      <label className="block text-sm font-medium text-gray-700 mb-1">Order Notes</label>
                      <textarea
                        value={orderNotes}
                        onChange={(e) => setOrderNotes(e.target.value)}
                        className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Any special instructions?"
                        rows="3"
                      />
                    </div>
                  </div>
                </div>
              </div>
            )}
          </div>
          
          <div>
            <div className="bg-white rounded-lg shadow-md p-6 sticky top-6">
              <h3 className="text-xl font-semibold mb-4">Order Summary</h3>
              
              <div className="space-y-2 mb-4">
                <div className="flex justify-between">
                  <span>Subtotal</span>
                  <span>${getCartTotal().toFixed(2)}</span>
                </div>
                <div className="flex justify-between">
                  <span>Delivery Fee</span>
                  <span>${selectedVendor?.delivery_fee || 0}</span>
                </div>
                <div className="flex justify-between">
                  <span>Tax</span>
                  <span>${(getCartTotal() * 0.08).toFixed(2)}</span>
                </div>
                <hr />
                <div className="flex justify-between font-bold">
                  <span>Total</span>
                  <span>${(getCartTotal() + (selectedVendor?.delivery_fee || 0) + (getCartTotal() * 0.08)).toFixed(2)}</span>
                </div>
              </div>
              
              <button
                onClick={handlePlaceOrder}
                disabled={cartItems.length === 0 || !deliveryAddress || loading}
                className={`w-full py-3 px-4 rounded-md font-medium text-white ${
                  cartItems.length === 0 || !deliveryAddress
                    ? 'bg-gray-400 cursor-not-allowed'
                    : 'bg-green-600 hover:bg-green-700'
                }`}
              >
                {loading ? 'Placing Order...' : `Place Order - ${(getCartTotal() + (selectedVendor?.delivery_fee || 0) + (getCartTotal() * 0.08)).toFixed(2)}`}
              </button>
            </div>
          </div>
        </div>
      )}

      {/* Orders Tab */}
      {activeTab === 'orders' && (
        <div className="bg-white rounded-lg shadow-md p-6">
          <h2 className="text-2xl font-bold text-gray-900 mb-4">My Order History</h2>
          <div className="space-y-6">
            {cartItems.length === 0 ? (
              <div className="text-center py-10">
                <p className="text-gray-500 text-lg">You haven't placed any orders yet.</p>
                <button
                  onClick={() => setActiveTab('vendors')}
                  className="mt-4 bg-blue-600 text-white py-2 px-6 rounded-md hover:bg-blue-700"
                >
                  Browse Restaurants
                </button>
              </div>
            ) : (
              <div className="space-y-4">
                {cartItems.map((item, index) => (
                  <div key={index} className="border rounded-lg p-4">
                    <div className="flex justify-between items-center mb-2">
                      <h3 className="font-medium">{item.name || item.product?.name}</h3>
                      <span className="font-bold">${(item.price || item.product?.price)?.toFixed(2)}</span>
                    </div>
                    <div className="flex justify-between text-sm text-gray-600">
                      <span>Qty: {item.quantity}</span>
                      <span>Total: ${(item.price || item.product?.price) * item.quantity?.toFixed(2)}</span>
                    </div>
                  </div>
                ))}
              </div>
            )}
          </div>
        </div>
      )}
    </div>
  );
};

export default withAuth(FoodOrderingSystem, ['customer', 'admin']);