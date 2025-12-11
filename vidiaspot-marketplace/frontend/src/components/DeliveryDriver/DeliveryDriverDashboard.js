import React, { useState } from 'react';
import DeliveryManagement from './DeliveryManagement/DeliveryManagement';
import RouteOptimization from './RouteOptimization/RouteOptimization';
import Communication from './Communication/Communication';
import { FaTruck, FaRoute, FaComments, FaUser } from 'react-icons/fa';

const DeliveryDriverDashboard = () => {
  const [activeTab, setActiveTab] = useState('packages');

  const renderTabContent = () => {
    switch (activeTab) {
      case 'packages':
        return <DeliveryManagement />;
      case 'route':
        return <RouteOptimization />;
      case 'messages':
        return <Communication />;
      case 'profile':
        return <ProfileSection />;
      default:
        return <DeliveryManagement />;
    }
  };

  return (
    <div className="min-h-screen bg-gray-50">
      <header className="bg-blue-600 text-white shadow-md">
        <div className="container mx-auto px-4 py-3 flex justify-between items-center">
          <h1 className="text-xl font-bold">Delivery Driver Dashboard</h1>
          <div className="flex items-center space-x-4">
            <span className="bg-green-500 px-3 py-1 rounded-full text-sm">Online</span>
            <div className="flex items-center space-x-2">
              <div className="w-8 h-8 bg-blue-400 rounded-full flex items-center justify-center">
                <FaUser />
              </div>
              <span>Driver Name</span>
            </div>
          </div>
        </div>
      </header>

      <div className="flex flex-col md:flex-row">
        {/* Sidebar Navigation */}
        <nav className="bg-white shadow-md md:w-64">
          <ul className="py-4">
            <li>
              <button
                className={`w-full text-left px-6 py-3 flex items-center space-x-3 ${
                  activeTab === 'packages' ? 'bg-blue-100 text-blue-600 border-l-4 border-blue-600' : 'hover:bg-gray-100'
                }`}
                onClick={() => setActiveTab('packages')}
              >
                <FaTruck className="text-lg" />
                <span>Packages</span>
              </button>
            </li>
            <li>
              <button
                className={`w-full text-left px-6 py-3 flex items-center space-x-3 ${
                  activeTab === 'route' ? 'bg-blue-100 text-blue-600 border-l-4 border-blue-600' : 'hover:bg-gray-100'
                }`}
                onClick={() => setActiveTab('route')}
              >
                <FaRoute className="text-lg" />
                <span>Route</span>
              </button>
            </li>
            <li>
              <button
                className={`w-full text-left px-6 py-3 flex items-center space-x-3 ${
                  activeTab === 'messages' ? 'bg-blue-100 text-blue-600 border-l-4 border-blue-600' : 'hover:bg-gray-100'
                }`}
                onClick={() => setActiveTab('messages')}
              >
                <FaComments className="text-lg" />
                <span>Messages</span>
              </button>
            </li>
            <li>
              <button
                className={`w-full text-left px-6 py-3 flex items-center space-x-3 ${
                  activeTab === 'profile' ? 'bg-blue-100 text-blue-600 border-l-4 border-blue-600' : 'hover:bg-gray-100'
                }`}
                onClick={() => setActiveTab('profile')}
              >
                <FaUser className="text-lg" />
                <span>Profile</span>
              </button>
            </li>
          </ul>
        </nav>

        {/* Main Content */}
        <main className="flex-1 p-6">
          {renderTabContent()}
        </main>
      </div>
    </div>
  );
};

const ProfileSection = () => {
  return (
    <div className="bg-white rounded-lg shadow p-6">
      <h2 className="text-2xl font-bold mb-6">Driver Profile</h2>
      
      <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div className="bg-gray-50 p-6 rounded-lg text-center">
          <div className="w-24 h-24 bg-blue-200 rounded-full mx-auto flex items-center justify-center mb-4">
            <FaUser className="text-4xl text-blue-600" />
          </div>
          <h3 className="font-bold">Driver Name</h3>
          <p className="text-gray-600">Driver ID: DRV-12345</p>
        </div>
        
        <div className="md:col-span-2">
          <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div className="bg-blue-50 p-4 rounded-lg text-center">
              <p className="text-2xl font-bold text-blue-600">12</p>
              <p className="text-gray-600">Delivered</p>
            </div>
            <div className="bg-yellow-50 p-4 rounded-lg text-center">
              <p className="text-2xl font-bold text-yellow-600">5</p>
              <p className="text-gray-600">Pending</p>
            </div>
            <div className="bg-green-50 p-4 rounded-lg text-center">
              <p className="text-2xl font-bold text-green-600">$120</p>
              <p className="text-gray-600">Earnings</p>
            </div>
          </div>
          
          <div className="bg-gray-50 p-4 rounded-lg">
            <h3 className="font-bold mb-2">Status</h3>
            <div className="flex items-center">
              <span className="mr-3">Accepting Deliveries:</span>
              <label className="switch">
                <input type="checkbox" defaultChecked />
                <span className="slider round"></span>
              </label>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default DeliveryDriverDashboard;