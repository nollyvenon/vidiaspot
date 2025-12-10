import React, { useState, useEffect } from 'react';
import { withAuth } from '../utils/withAuth';
import foodVendingService from '../services/foodVendingService';

const FoodVendingManagement = () => {
  const [vendingMachines, setVendingMachines] = useState([]);
  const [selectedMachine, setSelectedMachine] = useState(null);
  const [inventory, setInventory] = useState([]);
  const [loading, setLoading] = useState(true);
  const [activeTab, setActiveTab] = useState('machines');
  const [error, setError] = useState(null);
  const [selectedMachineId, setSelectedMachineId] = useState(null);

  // Fetch data for food vending management
  useEffect(() => {
    const fetchData = async () => {
      try {
        setLoading(true);
        const machines = await foodVendingService.getVendingMachines();
        setVendingMachines(machines);
        setError(null);
      } catch (err) {
        setError('Failed to fetch vending machines. Please try again later.');
        console.error('Error fetching vending machines:', err);
      } finally {
        setLoading(false);
      }
    };

    fetchData();
  }, []);

  // Fetch inventory when selectedMachineId changes and activeTab is inventory
  useEffect(() => {
    const fetchInventory = async () => {
      if (!selectedMachineId || activeTab !== 'inventory') return;

      try {
        setLoading(true);
        const machineInventory = await foodVendingService.getMachineInventory(selectedMachineId);
        setInventory(machineInventory);
        setError(null);
      } catch (err) {
        setError('Failed to fetch inventory. Please try again later.');
        console.error('Error fetching inventory:', err);
      } finally {
        setLoading(false);
      }
    };

    fetchInventory();
  }, [selectedMachineId, activeTab]);

  const handleMachineSelect = (machine) => {
    setSelectedMachine(machine);
    setSelectedMachineId(machine.id);
    setActiveTab('inventory');
  };

  const handleAddMachine = async (e) => {
    e.preventDefault();
    // Get form data (we'd need to add form fields to the component)
    // For now, this is a placeholder
  };

  const handleUpdateMachine = async (machineId, machineData) => {
    try {
      setLoading(true);
      const updatedMachine = await foodVendingService.updateVendingMachine(machineId, machineData);
      // Update the local state
      setVendingMachines(vendingMachines.map(m => m.id === machineId ? updatedMachine : m));
      setError(null);
    } catch (err) {
      setError('Failed to update vending machine. Please try again later.');
      console.error('Error updating vending machine:', err);
    } finally {
      setLoading(false);
    }
  };

  const handleDeleteMachine = async (machineId) => {
    try {
      setLoading(true);
      await foodVendingService.deleteVendingMachine(machineId);
      setVendingMachines(vendingMachines.filter(m => m.id !== machineId));
      setError(null);
    } catch (err) {
      setError('Failed to delete vending machine. Please try again later.');
      console.error('Error deleting vending machine:', err);
    } finally {
      setLoading(false);
    }
  };

  const handleUpdateMachineStatus = async (machineId, status) => {
    try {
      setLoading(true);
      const updatedMachine = await foodVendingService.updateMachineStatus(machineId, status);
      // Update the local state
      setVendingMachines(vendingMachines.map(m => m.id === machineId ? updatedMachine : m));
      setError(null);
    } catch (err) {
      setError('Failed to update machine status. Please try again later.');
      console.error('Error updating machine status:', err);
    } finally {
      setLoading(false);
    }
  };

  const handleUpdateInventory = async (machineId, inventoryData) => {
    try {
      setLoading(true);
      await foodVendingService.updateMachineInventory(machineId, inventoryData);
      // Refresh inventory if this is the currently selected machine
      if (selectedMachineId === machineId) {
        const updatedInventory = await foodVendingService.getMachineInventory(machineId);
        setInventory(updatedInventory);
      }
      setError(null);
    } catch (err) {
      setError('Failed to update inventory. Please try again later.');
      console.error('Error updating inventory:', err);
    } finally {
      setLoading(false);
    }
  };

  const getStatusColor = (status) => {
    switch (status) {
      case 'active': return 'bg-green-100 text-green-800';
      case 'maintenance': return 'bg-yellow-100 text-yellow-800';
      case 'out_of_order': return 'bg-red-100 text-red-800';
      default: return 'bg-gray-100 text-gray-800';
    }
  };

  return (
    <div className="max-w-7xl mx-auto p-6">
      <div className="mb-8">
        <h1 className="text-3xl font-bold text-gray-900">Food Vending Management</h1>
        <p className="text-gray-600 mt-2">Manage your food vending operations across all locations</p>
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
            onClick={() => setActiveTab('machines')}
            className={`whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm ${
              activeTab === 'machines'
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            }`}
          >
            Vending Machines
          </button>
          <button
            onClick={() => setActiveTab('inventory')}
            className={`whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm ${
              activeTab === 'inventory'
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            }`}
          >
            Inventory
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

      {activeTab === 'machines' && (
        <div>
          {loading && vendingMachines.length === 0 ? (
            <div className="flex justify-center items-center h-64">
              <div className="animate-spin rounded-full h-32 w-32 border-b-2 border-blue-600"></div>
            </div>
          ) : (
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              {vendingMachines.map(machine => (
                <div key={machine.id} className="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
                  <div className="flex justify-between items-start mb-4">
                    <div>
                      <h3 className="text-lg font-semibold text-gray-900">{machine.name}</h3>
                      <p className="text-sm text-gray-500">{machine.location}</p>
                    </div>
                    <span className={`inline-flex items-center px-3 py-1 rounded-full text-xs font-medium ${getStatusColor(machine.status)}`}>
                      {machine.status.replace('_', ' ').toUpperCase()}
                    </span>
                  </div>

                  <div className="grid grid-cols-2 gap-4 mb-4">
                    <div>
                      <p className="text-sm text-gray-500">Items</p>
                      <p className="text-2xl font-bold text-gray-900">{machine.items}</p>
                    </div>
                    <div>
                      <p className="text-sm text-gray-500">Revenue</p>
                      <p className="text-2xl font-bold text-green-600">${machine.revenue}</p>
                    </div>
                  </div>

                  <div className="flex space-x-2">
                    <button
                      onClick={() => handleMachineSelect(machine)}
                      className="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium"
                    >
                      Manage
                    </button>
                    <button
                      onClick={() => handleUpdateMachineStatus(machine.id,
                        machine.status === 'active' ? 'maintenance' : 'active'
                      )}
                      className="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50"
                      disabled={loading}
                    >
                      {loading ? '...' : machine.status === 'active' ? 'Service' : 'Activate'}
                    </button>
                  </div>
                </div>
              ))}
            </div>
          )}
        </div>
      )}

      {activeTab === 'inventory' && (
        <div>
          {loading && selectedMachineId && activeTab === 'inventory' ? (
            <div className="flex justify-center items-center h-64">
              <div className="animate-spin rounded-full h-32 w-32 border-b-2 border-blue-600"></div>
            </div>
          ) : (
            <div className="bg-white rounded-lg shadow-md p-6">
              <h2 className="text-xl font-semibold text-gray-900 mb-4">Inventory Management</h2>

              {selectedMachine && (
                <div className="mb-6 p-4 bg-blue-50 rounded-md">
                  <h3 className="font-medium text-blue-900">Managing Inventory for: {selectedMachine.name}</h3>
                  <p className="text-sm text-blue-700">{selectedMachine.location}</p>
                </div>
              )}

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
                        Quantity
                      </th>
                      <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Actions
                      </th>
                    </tr>
                  </thead>
                  <tbody className="bg-white divide-y divide-gray-200">
                    {inventory.map(item => (
                      <tr key={item.id}>
                        <td className="px-6 py-4 whitespace-nowrap">
                          <div className="text-sm font-medium text-gray-900">{item.name}</div>
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap">
                          <div className="text-sm text-gray-500">{item.category}</div>
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap">
                          <div className="text-sm text-gray-900">${item.price}</div>
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap">
                          <div className="text-sm text-gray-900">{item.quantity}</div>
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap text-sm font-medium">
                          <button className="text-blue-600 hover:text-blue-900 mr-3">Edit</button>
                          <button
                            onClick={() => handleUpdateInventory(selectedMachineId, {
                              items: [
                                { ...item, quantity: item.quantity + 10 } // Example: restock 10 items
                              ]
                            })}
                            className="text-red-600 hover:text-red-900"
                            disabled={loading}
                          >
                            {loading ? 'Restocking...' : 'Restock'}
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
      )}

      {activeTab === 'analytics' && (
        <div>
          {loading && activeTab === 'analytics' ? (
            <div className="flex justify-center items-center h-64">
              <div className="animate-spin rounded-full h-32 w-32 border-b-2 border-blue-600"></div>
            </div>
          ) : (
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
              <div className="bg-white rounded-lg shadow-md p-6">
                <h2 className="text-xl font-semibold text-gray-900 mb-4">Revenue Overview</h2>
                <div className="space-y-4">
                  <div>
                    <p className="text-sm text-gray-500">Daily Revenue</p>
                    <p className="text-3xl font-bold text-green-600">$1,245.75</p>
                  </div>
                  <div>
                    <p className="text-sm text-gray-500">Weekly Revenue</p>
                    <p className="text-3xl font-bold text-green-600">$8,720.25</p>
                  </div>
                  <div>
                    <p className="text-sm text-gray-500">Monthly Revenue</p>
                    <p className="text-3xl font-bold text-green-600">$34,890.50</p>
                  </div>
                </div>
              </div>

              <div className="bg-white rounded-lg shadow-md p-6">
                <h2 className="text-xl font-semibold text-gray-900 mb-4">Top Selling Items</h2>
                <div className="space-y-3">
                  <div className="flex justify-between items-center">
                    <span>Soda Can</span>
                    <span className="font-medium">124 sold</span>
                  </div>
                  <div className="flex justify-between items-center">
                    <span>Chips</span>
                    <span className="font-medium">98 sold</span>
                  </div>
                  <div className="flex justify-between items-center">
                    <span>Chocolate Bar</span>
                    <span className="font-medium">87 sold</span>
                  </div>
                  <div className="flex justify-between items-center">
                    <span>Energy Drink</span>
                    <span className="font-medium">76 sold</span>
                  </div>
                </div>
              </div>
            </div>
          )}
        </div>
      )}
    </div>
  );
};

export default withAuth(FoodVendingManagement, ['seller', 'store_owner']);