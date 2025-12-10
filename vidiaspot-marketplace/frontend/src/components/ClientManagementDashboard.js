import React, { useState, useEffect } from 'react';
import { withAuth } from '../utils/withAuth';
import clientService from '../services/clientService';
import { BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, Legend, ResponsiveContainer, PieChart, Pie, Cell, LineChart, Line } from 'recharts';

const ClientManagementDashboard = () => {
  const [activeTab, setActiveTab] = useState('client-profile');
  const [selectedClient, setSelectedClient] = useState(null);
  const [clients, setClients] = useState([]);
  const [deliveryLogs, setDeliveryLogs] = useState([]);
  const [pickups, setPickups] = useState([]);
  const [routes, setRoutes] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  
  const [newClient, setNewClient] = useState({
    name: '',
    business_type: '',
    contact_person: '',
    email: '',
    phone: '',
    address: '',
    delivery_preferences: '',
    special_instructions: ''
  });

  const [newDelivery, setNewDelivery] = useState({
    client_id: '',
    item_description: '',
    pickup_location: '',
    delivery_location: '',
    scheduled_time: '',
    priority: 'normal'
  });

  const COLORS = ['#0088FE', '#00C49F', '#FFBB28', '#FF8042', '#8884D8', '#82CA9D'];

  useEffect(() => {
    loadData();
  }, []);

  const loadData = async () => {
    try {
      setLoading(true);
      const [clientsData, deliveryData] = await Promise.all([
        clientService.getClients(),
        clientService.getDeliveryLogs()
      ]);
      
      setClients(clientsData);
      setDeliveryLogs(deliveryData);
    } catch (err) {
      setError('Failed to load data. Please try again later.');
      console.error('Error loading data:', err);
    } finally {
      setLoading(false);
    }
  };

  const handleCreateClient = async (e) => {
    e.preventDefault();
    try {
      const result = await clientService.createClient(newClient);
      setClients([...clients, result]);
      setNewClient({
        name: '',
        business_type: '',
        contact_person: '',
        email: '',
        phone: '',
        address: '',
        delivery_preferences: '',
        special_instructions: ''
      });
    } catch (err) {
      setError('Failed to create client. Please try again.');
      console.error('Error creating client:', err);
    }
  };

  const handleCreateDelivery = async (e) => {
    e.preventDefault();
    try {
      const result = await clientService.logDelivery(newDelivery);
      setDeliveryLogs([...deliveryLogs, result]);
      setNewDelivery({
        client_id: '',
        item_description: '',
        pickup_location: '',
        delivery_location: '',
        scheduled_time: '',
        priority: 'normal'
      });
    } catch (err) {
      setError('Failed to log delivery. Please try again.');
      console.error('Error logging delivery:', err);
    }
  };

  const handleUpdateClient = async (clientId, updatedData) => {
    try {
      const result = await clientService.updateClient(clientId, updatedData);
      setClients(clients.map(c => c.id === clientId ? result : c));
    } catch (err) {
      setError('Failed to update client. Please try again.');
      console.error('Error updating client:', err);
    }
  };

  const handleDeleteClient = async (clientId) => {
    try {
      await clientService.deleteClient(clientId);
      setClients(clients.filter(c => c.id !== clientId));
    } catch (err) {
      setError('Failed to delete client. Please try again.');
      console.error('Error deleting client:', err);
    }
  };

  const getStatusColor = (status) => {
    switch (status) {
      case 'delivered': return 'bg-green-100 text-green-800';
      case 'in_transit': return 'bg-blue-100 text-blue-800';
      case 'pending': return 'bg-yellow-100 text-yellow-800';
      case 'failed': return 'bg-red-100 text-red-800';
      default: return 'bg-gray-100 text-gray-800';
    }
  };

  if (loading && clients.length === 0) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <div className="animate-spin rounded-full h-32 w-32 border-b-2 border-blue-600"></div>
      </div>
    );
  }

  return (
    <div className="max-w-7xl mx-auto p-6">
      <div className="mb-8">
        <h1 className="text-3xl font-bold text-gray-900">Enhanced Client Management System</h1>
        <p className="text-gray-600">Comprehensive client management, delivery tracking, and route optimization</p>
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
            onClick={() => setActiveTab('client-profile')}
            className={`whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm ${
              activeTab === 'client-profile'
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            }`}
          >
            Client Profiles
          </button>
          <button
            onClick={() => setActiveTab('delivery-logs')}
            className={`whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm ${
              activeTab === 'delivery-logs'
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            }`}
          >
            Delivery Logs
          </button>
          <button
            onClick={() => setActiveTab('route-optimization')}
            className={`whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm ${
              activeTab === 'route-optimization'
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            }`}
          >
            Route Optimization
          </button>
          <button
            onClick={() => setActiveTab('pickup-coordination')}
            className={`whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm ${
              activeTab === 'pickup-coordination'
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            }`}
          >
            Pickup Coordination
          </button>
        </nav>
      </div>

      {/* Client Profile Management */}
      {activeTab === 'client-profile' && (
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <div className="lg:col-span-1">
            <div className="bg-white rounded-lg shadow-md p-6">
              <h2 className="text-xl font-semibold text-gray-900 mb-4">Create New Client</h2>
              <form onSubmit={handleCreateClient} className="space-y-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Client Name</label>
                  <input
                    type="text"
                    value={newClient.name}
                    onChange={(e) => setNewClient({...newClient, name: e.target.value})}
                    className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Business Type</label>
                  <input
                    type="text"
                    value={newClient.business_type}
                    onChange={(e) => setNewClient({...newClient, business_type: e.target.value})}
                    className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Contact Person</label>
                  <input
                    type="text"
                    value={newClient.contact_person}
                    onChange={(e) => setNewClient({...newClient, contact_person: e.target.value})}
                    className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Email</label>
                  <input
                    type="email"
                    value={newClient.email}
                    onChange={(e) => setNewClient({...newClient, email: e.target.value})}
                    className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                  <input
                    type="tel"
                    value={newClient.phone}
                    onChange={(e) => setNewClient({...newClient, phone: e.target.value})}
                    className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Address</label>
                  <textarea
                    value={newClient.address}
                    onChange={(e) => setNewClient({...newClient, address: e.target.value})}
                    className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    rows="2"
                  ></textarea>
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Delivery Preferences</label>
                  <textarea
                    value={newClient.delivery_preferences}
                    onChange={(e) => setNewClient({...newClient, delivery_preferences: e.target.value})}
                    className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    rows="2"
                  ></textarea>
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Special Instructions</label>
                  <textarea
                    value={newClient.special_instructions}
                    onChange={(e) => setNewClient({...newClient, special_instructions: e.target.value})}
                    className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    rows="2"
                  ></textarea>
                </div>
                <button
                  type="submit"
                  className="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md"
                >
                  Create Client
                </button>
              </form>
            </div>
          </div>

          <div className="lg:col-span-2">
            <div className="bg-white rounded-lg shadow-md p-6">
              <h2 className="text-xl font-semibold text-gray-900 mb-4">Client List</h2>
              <div className="overflow-x-auto">
                <table className="min-w-full divide-y divide-gray-200">
                  <thead className="bg-gray-50">
                    <tr>
                      <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Client Name
                      </th>
                      <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Business Type
                      </th>
                      <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Contact
                      </th>
                      <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Delivery Pref.
                      </th>
                      <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Actions
                      </th>
                    </tr>
                  </thead>
                  <tbody className="bg-white divide-y divide-gray-200">
                    {clients.map((client) => (
                      <tr key={client.id}>
                        <td className="px-6 py-4 whitespace-nowrap">
                          <div className="text-sm font-medium text-gray-900">{client.name}</div>
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap">
                          <div className="text-sm text-gray-500">{client.business_type}</div>
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap">
                          <div className="text-sm text-gray-500">{client.contact_person}</div>
                          <div className="text-sm text-gray-500">{client.email}</div>
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap">
                          <div className="text-sm text-gray-500">{client.delivery_preferences}</div>
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap text-sm font-medium">
                          <button 
                            onClick={() => setSelectedClient(client)}
                            className="text-blue-600 hover:text-blue-900 mr-3"
                          >
                            View/Update
                          </button>
                          <button 
                            onClick={() => handleDeleteClient(client.id)}
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

      {/* Delivery Logging System */}
      {activeTab === 'delivery-logs' && (
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <div className="lg:col-span-1">
            <div className="bg-white rounded-lg shadow-md p-6">
              <h2 className="text-xl font-semibold text-gray-900 mb-4">Log New Delivery</h2>
              <form onSubmit={handleCreateDelivery} className="space-y-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Client</label>
                  <select
                    value={newDelivery.client_id}
                    onChange={(e) => setNewDelivery({...newDelivery, client_id: e.target.value})}
                    className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required
                  >
                    <option value="">Select Client</option>
                    {clients.map(client => (
                      <option key={client.id} value={client.id}>{client.name}</option>
                    ))}
                  </select>
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Item Description</label>
                  <input
                    type="text"
                    value={newDelivery.item_description}
                    onChange={(e) => setNewDelivery({...newDelivery, item_description: e.target.value})}
                    className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Pickup Location</label>
                  <input
                    type="text"
                    value={newDelivery.pickup_location}
                    onChange={(e) => setNewDelivery({...newDelivery, pickup_location: e.target.value})}
                    className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Delivery Location</label>
                  <input
                    type="text"
                    value={newDelivery.delivery_location}
                    onChange={(e) => setNewDelivery({...newDelivery, delivery_location: e.target.value})}
                    className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Scheduled Time</label>
                  <input
                    type="datetime-local"
                    value={newDelivery.scheduled_time}
                    onChange={(e) => setNewDelivery({...newDelivery, scheduled_time: e.target.value})}
                    className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                  <select
                    value={newDelivery.priority}
                    onChange={(e) => setNewDelivery({...newDelivery, priority: e.target.value})}
                    className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  >
                    <option value="low">Low</option>
                    <option value="normal">Normal</option>
                    <option value="high">High</option>
                    <option value="urgent">Urgent</option>
                  </select>
                </div>
                <button
                  type="submit"
                  className="w-full bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-md"
                >
                  Log Delivery
                </button>
              </form>
            </div>
          </div>

          <div className="lg:col-span-2">
            <div className="bg-white rounded-lg shadow-md p-6">
              <h2 className="text-xl font-semibold text-gray-900 mb-4">Delivery Logs</h2>
              <div className="overflow-x-auto">
                <table className="min-w-full divide-y divide-gray-200">
                  <thead className="bg-gray-50">
                    <tr>
                      <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Client
                      </th>
                      <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Item
                      </th>
                      <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Locations
                      </th>
                      <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Scheduled
                      </th>
                      <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                      </th>
                      <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Priority
                      </th>
                    </tr>
                  </thead>
                  <tbody className="bg-white divide-y divide-gray-200">
                    {deliveryLogs.map((delivery) => (
                      <tr key={delivery.id}>
                        <td className="px-6 py-4 whitespace-nowrap">
                          <div className="text-sm font-medium text-gray-900">
                            {clients.find(c => c.id === delivery.client_id)?.name || delivery.client_name}
                          </div>
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap">
                          <div className="text-sm text-gray-500">{delivery.item_description}</div>
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap">
                          <div className="text-sm text-gray-500">P: {delivery.pickup_location}</div>
                          <div className="text-sm text-gray-500">D: {delivery.delivery_location}</div>
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap">
                          <div className="text-sm text-gray-500">{new Date(delivery.scheduled_time).toLocaleString()}</div>
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap">
                          <span className={`px-2 py-1 rounded-full text-xs font-medium ${getStatusColor(delivery.status)}`}>
                            {delivery.status?.replace('_', ' ') || 'pending'}
                          </span>
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap">
                          <span className={`px-2 py-1 rounded-full text-xs font-medium ${
                            delivery.priority === 'urgent' ? 'bg-red-100 text-red-800' :
                            delivery.priority === 'high' ? 'bg-orange-100 text-orange-800' :
                            delivery.priority === 'normal' ? 'bg-blue-100 text-blue-800' :
                            'bg-gray-100 text-gray-800'
                          }`}>
                            {delivery.priority?.toUpperCase()}
                          </span>
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

      {/* Route Optimization */}
      {activeTab === 'route-optimization' && (
        <div className="bg-white rounded-lg shadow-md p-6">
          <h2 className="text-xl font-semibold text-gray-900 mb-4">Route Optimization</h2>
          <p className="text-gray-600 mb-6">Advanced route planning and optimization tools for efficient delivery operations</p>
          
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <h3 className="text-lg font-medium text-gray-900 mb-3">Optimize Multiple Stops</h3>
              <div className="bg-gray-50 p-4 rounded-md">
                <p className="text-gray-700">Use advanced algorithms to optimize delivery routes based on:</p>
                <ul className="mt-2 list-disc pl-5 text-sm text-gray-600 space-y-1">
                  <li>Geographic proximity and clustering</li>
                  <li>Time window constraints</li>
                  <li>Vehicle capacity and weight distribution</li>
                  <li>Priority-based sorting</li>
                  <li>Real-time traffic conditions</li>
                  <li>Historical performance data</li>
                </ul>
              </div>
            </div>
            
            <div>
              <h3 className="text-lg font-medium text-gray-900 mb-3">Delivery Performance Analytics</h3>
              {deliveryLogs.length > 0 ? (
                <ResponsiveContainer width="100%" height={300}>
                  <BarChart data={deliveryLogs.slice(0, 10)}>
                    <CartesianGrid strokeDasharray="3 3" />
                    <XAxis dataKey="id" />
                    <YAxis />
                    <Tooltip />
                    <Legend />
                    <Bar dataKey="distance" name="Distance (km)" fill="#8884d8" />
                  </BarChart>
                </ResponsiveContainer>
              ) : (
                <div className="text-center py-8 text-gray-500">No delivery data available for analytics</div>
              )}
            </div>
          </div>
        </div>
      )}

      {/* Pickup Coordination */}
      {activeTab === 'pickup-coordination' && (
        <div className="bg-white rounded-lg shadow-md p-6">
          <h2 className="text-xl font-semibold text-gray-900 mb-4">Multi-Client Pickup Coordination</h2>
          <p className="text-gray-600 mb-6">Batch processing and coordination for efficient pickup operations</p>
          
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <h3 className="text-lg font-medium text-gray-900 mb-3">Pickup Requests</h3>
              <div className="overflow-x-auto">
                <table className="min-w-full divide-y divide-gray-200">
                  <thead className="bg-gray-50">
                    <tr>
                      <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Client
                      </th>
                      <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Item
                      </th>
                      <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Location
                      </th>
                      <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Requested Time
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
                    {deliveryLogs.filter(d => d.type === 'pickup').map((pickup) => (
                      <tr key={pickup.id}>
                        <td className="px-6 py-4 whitespace-nowrap">
                          <div className="text-sm font-medium text-gray-900">
                            {clients.find(c => c.id === pickup.client_id)?.name || pickup.client_name}
                          </div>
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap">
                          <div className="text-sm text-gray-500">{pickup.item_description}</div>
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap">
                          <div className="text-sm text-gray-500">{pickup.pickup_location}</div>
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap">
                          <div className="text-sm text-gray-500">{new Date(pickup.scheduled_time).toLocaleString()}</div>
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap">
                          <span className={`px-2 py-1 rounded-full text-xs font-medium ${getStatusColor(pickup.status)}`}>
                            {pickup.status?.replace('_', ' ') || 'pending'}
                          </span>
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap text-sm font-medium">
                          <button className="text-blue-600 hover:text-blue-900 mr-3">Assign Vehicle</button>
                          <button className="text-green-600 hover:text-green-900">Group</button>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            </div>
            
            <div>
              <h3 className="text-lg font-medium text-gray-900 mb-3">Batch Processing</h3>
              <div className="bg-gray-50 p-4 rounded-md">
                <p className="text-gray-700">Coordinate and group multiple pickup requests by:</p>
                <ul className="mt-2 list-disc pl-5 text-sm text-gray-600 space-y-1">
                  <li>Geographic proximity</li>
                  <li>Scheduled time windows</li>
                  <li>Vehicle capacity and availability</li>
                  <li>Client priority levels</li>
                  <li>Item type and handling requirements</li>
                </ul>
                <button className="mt-4 bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md">
                  Generate Pickup Batches
                </button>
              </div>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};

export default withAuth(ClientManagementDashboard, ['admin', 'logistics_manager', 'dispatcher']);