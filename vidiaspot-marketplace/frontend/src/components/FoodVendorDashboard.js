import React, { useState, useEffect } from 'react';
import { withAuth } from '../utils/withAuth';
import foodVendorService from '../services/foodVendorService';
import { BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, Legend, ResponsiveContainer, PieChart, Pie, Cell, LineChart, Line } from 'recharts';

const FoodVendorDashboard = () => {
  const [activeTab, setActiveTab] = useState('menu-management');
  const [vendor, setVendor] = useState(null);
  const [menuItems, setMenuItems] = useState([]);
  const [categories, setCategories] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [newMenuItem, setNewItem] = useState({
    name: '',
    description: '',
    price: '',
    category_id: '',
    image: null,
    availability: 'available'
  });

  const [newCategory, setNewCategory] = useState({
    name: '',
    description: ''
  });

  useEffect(() => {
    loadVendorData();
  }, [activeTab]);

  const loadVendorData = async () => {
    try {
      setLoading(true);
      const vendorData = await foodVendorService.getVendorProfile();
      setVendor(vendorData);

      if (activeTab === 'menu-management') {
        const menuData = await foodVendorService.getVendorMenuItems(vendorData.id);
        setMenuItems(menuData);
        
        const categoryData = await foodVendorService.getVendorCategories(vendorData.id);
        setCategories(categoryData);
      }
      
    } catch (err) {
      setError('Failed to load vendor data. Please try again.');
      console.error('Error loading vendor data:', err);
    } finally {
      setLoading(false);
    }
  };

  const handleAddMenuItem = async (e) => {
    e.preventDefault();
    
    try {
      const menuItemData = {
        ...newMenuItem,
        price: parseFloat(newMenuItem.price),
        vendor_id: vendor.id
      };
      
      const createdItem = await foodVendorService.addMenuItem(vendor.id, menuItemData);
      setMenuItems([...menuItems, createdItem]);
      setNewItem({
        name: '',
        description: '',
        price: '',
        category_id: '',
        image: null,
        availability: 'available'
      });
    } catch (err) {
      setError('Failed to add menu item. Please try again.');
      console.error('Error adding menu item:', err);
    }
  };

  const handleUpdateMenuItem = async (itemId, updatedData) => {
    try {
      const updatedItem = await foodVendorService.updateMenuItem(vendor.id, itemId, updatedData);
      setMenuItems(menuItems.map(item => item.id === itemId ? updatedItem : item));
    } catch (err) {
      setError('Failed to update menu item. Please try again.');
      console.error('Error updating menu item:', err);
    }
  };

  const handleDeleteMenuItem = async (itemId) => {
    try {
      await foodVendorService.deleteMenuItem(vendor.id, itemId);
      setMenuItems(menuItems.filter(item => item.id !== itemId));
    } catch (err) {
      setError('Failed to delete menu item. Please try again.');
      console.error('Error deleting menu item:', err);
    }
  };

  const handleAddCategory = async (e) => {
    e.preventDefault();
    
    try {
      const categoryData = {
        ...newCategory,
        vendor_id: vendor.id
      };
      
      const createdCategory = await foodVendorService.addCategory(vendor.id, categoryData);
      setCategories([...categories, createdCategory]);
      setNewCategory({ name: '', description: '' });
    } catch (err) {
      setError('Failed to add category. Please try again.');
      console.error('Error adding category:', err);
    }
  };

  const toggleMenuItemAvailability = async (itemId, currentAvailability) => {
    const newAvailability = currentAvailability === 'available' ? 'unavailable' : 'available';
    try {
      await foodVendorService.updateMenuItemAvailability(vendor.id, itemId, newAvailability);
      setMenuItems(items => items.map(item => 
        item.id === itemId ? { ...item, availability: newAvailability } : item
      ));
    } catch (err) {
      setError('Failed to update item availability. Please try again.');
      console.error('Error updating item availability:', err);
    }
  };

  const COLORS = ['#0088FE', '#00C49F', '#FFBB28', '#FF8042', '#8884D8', '#82CA9D', '#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4'];

  if (loading && !vendor) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <div className="animate-spin rounded-full h-32 w-32 border-b-2 border-blue-600"></div>
      </div>
    );
  }

  return (
    <div className="max-w-7xl mx-auto p-6">
      <div className="mb-8">
        <h1 className="text-3xl font-bold text-gray-900">Food Vendor Management Dashboard</h1>
        <p className="text-gray-600">Manage your restaurant menu, orders, and business analytics</p>
      </div>

      {error && (
        <div className="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
          <strong className="font-bold">Error: </strong>
          <span className="block sm:inline">{error}</span>
        </div>
      )}

      {/* Vendor Info Header */}
      {vendor && (
        <div className="bg-blue-50 rounded-lg p-6 mb-6">
          <h2 className="text-2xl font-bold text-blue-900">{vendor.name}</h2>
          <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
            <div>
              <p className="text-sm text-blue-700">Email</p>
              <p className="font-medium">{vendor.email}</p>
            </div>
            <div>
              <p className="text-sm text-blue-700">Phone</p>
              <p className="font-medium">{vendor.phone || 'Not provided'}</p>
            </div>
            <div>
              <p className="text-sm text-blue-700">Location</p>
              <p className="font-medium">{vendor.address || 'Not provided'}</p>
            </div>
          </div>
        </div>
      )}

      {/* Tab Navigation */}
      <div className="border-b border-gray-200 mb-6">
        <nav className="-mb-px flex flex-wrap">
          <button
            onClick={() => setActiveTab('menu-management')}
            className={`whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm ${
              activeTab === 'menu-management'
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            }`}
          >
            Menu Management
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

      {/* Menu Management Tab */}
      {activeTab === 'menu-management' && (
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <div className="lg:col-span-1">
            <div className="bg-white rounded-lg shadow-md p-6 mb-6">
              <h2 className="text-xl font-semibold text-gray-900 mb-4">Add New Menu Item</h2>
              <form onSubmit={handleAddMenuItem} className="space-y-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Item Name *</label>
                  <input
                    type="text"
                    value={newMenuItem.name}
                    onChange={(e) => setNewItem({...newMenuItem, name: e.target.value})}
                    className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Description</label>
                  <textarea
                    value={newMenuItem.description}
                    onChange={(e) => setNewItem({...newMenuItem, description: e.target.value})}
                    className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    rows="3"
                  ></textarea>
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Price *</label>
                  <input
                    type="number"
                    step="0.01"
                    value={newMenuItem.price}
                    onChange={(e) => setNewItem({...newMenuItem, price: e.target.value})}
                    className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Category</label>
                  <select
                    value={newMenuItem.category_id}
                    onChange={(e) => setNewItem({...newMenuItem, category_id: e.target.value})}
                    className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  >
                    <option value="">Select Category</option>
                    {categories.map(category => (
                      <option key={category.id} value={category.id}>{category.name}</option>
                    ))}
                  </select>
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Availability</label>
                  <select
                    value={newMenuItem.availability}
                    onChange={(e) => setNewItem({...newMenuItem, availability: e.target.value})}
                    className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  >
                    <option value="available">Available</option>
                    <option value="unavailable">Unavailable</option>
                  </select>
                </div>
                <button
                  type="submit"
                  className="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md"
                >
                  Add Menu Item
                </button>
              </form>
            </div>

            <div className="bg-white rounded-lg shadow-md p-6">
              <h2 className="text-xl font-semibold text-gray-900 mb-4">Add New Category</h2>
              <form onSubmit={handleAddCategory} className="space-y-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Category Name *</label>
                  <input
                    type="text"
                    value={newCategory.name}
                    onChange={(e) => setNewCategory({...newCategory, name: e.target.value})}
                    className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Description</label>
                  <input
                    type="text"
                    value={newCategory.description}
                    onChange={(e) => setNewCategory({...newCategory, description: e.target.value})}
                    className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  />
                </div>
                <button
                  type="submit"
                  className="w-full bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-md"
                >
                  Add Category
                </button>
              </form>
            </div>
          </div>

          <div className="lg:col-span-2">
            <div className="bg-white rounded-lg shadow-md p-6">
              <h2 className="text-xl font-semibold text-gray-900 mb-4">Menu Items</h2>
              <div className="overflow-x-auto">
                <table className="min-w-full divide-y divide-gray-200">
                  <thead className="bg-gray-50">
                    <tr>
                      <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Item
                      </th>
                      <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Category
                      </th>
                      <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Price
                      </th>
                      <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Availability
                      </th>
                      <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Actions
                      </th>
                    </tr>
                  </thead>
                  <tbody className="bg-white divide-y divide-gray-200">
                    {menuItems.map(item => (
                      <tr key={item.id}>
                        <td className="px-6 py-4 whitespace-nowrap">
                          <div className="text-sm font-medium text-gray-900">{item.name}</div>
                          <div className="text-sm text-gray-500">{item.description}</div>
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                          {categories.find(cat => cat.id === item.category_id)?.name || 'Uncategorized'}
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                          ${item.price?.toFixed(2)}
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap">
                          <span className={`px-2 py-1 rounded-full text-xs font-medium ${
                            item.availability === 'available' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                          }`}>
                            {item.availability?.replace('_', ' ').toUpperCase()}
                          </span>
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap text-sm font-medium">
                          <button
                            onClick={() => toggleMenuItemAvailability(item.id, item.availability)}
                            className={`mr-3 ${
                              item.availability === 'available' 
                                ? 'text-red-600 hover:text-red-900' 
                                : 'text-green-600 hover:text-green-900'
                            }`}
                          >
                            {item.availability === 'available' ? 'Hide' : 'Show'}
                          </button>
                          <button
                            onClick={() => {
                              // Implement edit functionality
                              setNewItem({
                                name: item.name,
                                description: item.description,
                                price: item.price,
                                category_id: item.category_id,
                                availability: item.availability
                              });
                              // Also need to handle updating the item
                              const updatedItem = {...newMenuItem, id: item.id};
                              handleUpdateMenuItem(item.id, updatedItem);
                            }}
                            className="text-blue-600 hover:text-blue-900 mr-3"
                          >
                            Edit
                          </button>
                          <button
                            onClick={() => handleDeleteMenuItem(item.id)}
                            className="text-red-600 hover:text-red-900"
                          >
                            Delete
                          </button>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      )}

      {/* Orders Tab */}
      {activeTab === 'orders' && (
        <div className="bg-white rounded-lg shadow-md p-6">
          <h2 className="text-xl font-semibold text-gray-900 mb-4">Order Management</h2>
          <div className="text-gray-500 text-center py-10">Order management functionality coming soon...</div>
        </div>
      )}

      {/* Analytics Tab */}
      {activeTab === 'analytics' && (
        <div className="bg-white rounded-lg shadow-md p-6">
          <h2 className="text-xl font-semibold text-gray-900 mb-4">Business Analytics</h2>
          <div className="text-gray-500 text-center py-10">Analytics dashboard coming soon...</div>
        </div>
      )}
    </div>
  );
};

export default withAuth(FoodVendorDashboard, ['food_vendor', 'admin']);