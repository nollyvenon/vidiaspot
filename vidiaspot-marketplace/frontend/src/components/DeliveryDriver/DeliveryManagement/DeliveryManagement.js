import React, { useState } from 'react';
import DeliveryPackageDetail from './DeliveryManagement/DeliveryPackageDetail';

const DeliveryManagement = () => {
  const [activeTab, setActiveTab] = useState('pending');
  const [selectedPackage, setSelectedPackage] = useState(null);
  const [packages, setPackages] = useState([
    {
      id: 'pkg-1',
      trackingNumber: 'VID001234567',
      recipientName: 'John Doe',
      recipientPhone: '+2348012345678',
      deliveryAddress: '123 Main Street, Lagos',
      packageDetails: 'Electronics - Phone',
      weight: 2.5,
      volume: 0.01,
      requiresSignature: true,
      requiresPhoto: true,
      requiresIdCheck: false,
      priority: 2,
      scheduledDeliveryTime: new Date(),
      deliveredAt: null,
      eta: new Date(Date.now() + 30 * 60000), // 30 minutes from now
      status: 'outForDelivery', // pending, inTransit, outForDelivery, delivered, failed, returned
      signature: null,
      photo: null,
      notes: ''
    },
    {
      id: 'pkg-2',
      trackingNumber: 'VID001234568',
      recipientName: 'Jane Smith',
      recipientPhone: '+2348012345679',
      deliveryAddress: '456 Business Avenue, Lagos',
      packageDetails: 'Documents',
      weight: 0.5,
      volume: 0.002,
      requiresSignature: true,
      requiresPhoto: false,
      requiresIdCheck: true,
      priority: 1,
      scheduledDeliveryTime: new Date(),
      deliveredAt: null,
      eta: new Date(Date.now() + 45 * 60000), // 45 minutes from now
      status: 'pending',
      signature: null,
      photo: null,
      notes: ''
    },
    {
      id: 'pkg-3',
      trackingNumber: 'VID001234569',
      recipientName: 'Bob Johnson',
      recipientPhone: '+2348012345670',
      deliveryAddress: '789 Innovation Hub, Lagos',
      packageDetails: 'Clothing items',
      weight: 1.2,
      volume: 0.008,
      requiresSignature: false,
      requiresPhoto: true,
      requiresIdCheck: false,
      priority: 3,
      scheduledDeliveryTime: new Date(),
      deliveredAt: null,
      eta: new Date(Date.now() + 60 * 60000), // 60 minutes from now
      status: 'inTransit',
      signature: null,
      photo: null,
      notes: ''
    }
  ]);

  const handleUpdateStatus = (packageId, newStatus) => {
    setPackages(prev => prev.map(pkg => 
      pkg.id === packageId ? {...pkg, status: newStatus} : pkg
    ));
  };

  const getStatusColor = (status) => {
    switch (status) {
      case 'pending': return 'bg-gray-200 text-gray-800';
      case 'inTransit': return 'bg-blue-200 text-blue-800';
      case 'outForDelivery': return 'bg-orange-200 text-orange-800';
      case 'delivered': return 'bg-green-200 text-green-800';
      case 'failed': return 'bg-red-200 text-red-800';
      case 'returned': return 'bg-purple-200 text-purple-800';
      default: return 'bg-gray-200 text-gray-800';
    }
  };

  const getStatusString = (status) => {
    switch (status) {
      case 'pending': return 'Pending';
      case 'inTransit': return 'In Transit';
      case 'outForDelivery': return 'Out for Delivery';
      case 'delivered': return 'Delivered';
      case 'failed': return 'Failed';
      case 'returned': return 'Returned';
      default: return status;
    }
  };

  const getPriorityString = (priority) => {
    switch (priority) {
      case 1: return 'High';
      case 2: return 'Medium-High';
      case 3: return 'Medium';
      case 4: return 'Medium-Low';
      case 5: return 'Low';
      default: return 'Medium';
    }
  };

  const filteredPackages = packages.filter(pkg => pkg.status === activeTab);

  return (
    <div>
      <div className="flex justify-between items-center mb-6">
        <h2 className="text-2xl font-bold">Delivery Packages</h2>
        <div className="flex space-x-4">
          <button className="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
            Scan Package
          </button>
          <button className="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
            Add Manual Package
          </button>
        </div>
      </div>

      {/* Tab Navigation */}
      <div className="border-b border-gray-200 mb-6">
        <nav className="flex space-x-8">
          {['pending', 'inTransit', 'outForDelivery'].map((tab) => (
            <button
              key={tab}
              className={`py-4 px-1 border-b-2 font-medium text-sm ${
                activeTab === tab
                  ? 'border-blue-500 text-blue-600'
                  : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
              }`}
              onClick={() => setActiveTab(tab)}
            >
              {getStatusString(tab)}
              <span className="ml-2 bg-gray-100 text-gray-800 text-xs font-medium px-2 py-0.5 rounded-full">
                {packages.filter(pkg => pkg.status === tab).length}
              </span>
            </button>
          ))}
        </nav>
      </div>

      {/* Package List */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {filteredPackages.map((pkg) => (
          <div key={pkg.id} className="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
            <div className="flex justify-between items-start">
              <div>
                <h3 className="font-bold text-lg">{pkg.recipientName}</h3>
                <p className="text-gray-600 text-sm">{pkg.trackingNumber}</p>
              </div>
              <span className={`px-3 py-1 rounded-full text-xs font-medium ${getStatusColor(pkg.status)}`}>
                {getStatusString(pkg.status)}
              </span>
            </div>
            
            <p className="mt-3 text-gray-700">{pkg.deliveryAddress}</p>
            
            <div className="mt-4 grid grid-cols-2 gap-2 text-sm">
              <div><span className="font-semibold">Priority:</span> {getPriorityString(pkg.priority)}</div>
              <div><span className="font-semibold">Weight:</span> {pkg.weight} kg</div>
              <div><span className="font-semibold">ETA:</span> {pkg.eta ? pkg.eta.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) : 'N/A'}</div>
              <div><span className="font-semibold">Phone:</span> {pkg.recipientPhone}</div>
            </div>
            
            <div className="mt-6 flex space-x-2">
              <button 
                className="bg-blue-600 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-700 flex-1"
                onClick={() => setSelectedPackage(pkg)}
              >
                View Details
              </button>
              {pkg.status !== 'delivered' && (
                <button 
                  className="bg-green-600 text-white px-4 py-2 rounded-md text-sm hover:bg-green-700 flex-1"
                  onClick={() => handleUpdateStatus(pkg.id, 'delivered')}
                >
                  Mark Delivered
                </button>
              )}
            </div>
          </div>
        ))}
      </div>

      {/* Package Detail Modal */}
      {selectedPackage && (
        <DeliveryPackageDetail 
          package={selectedPackage} 
          onClose={() => setSelectedPackage(null)}
          onUpdateStatus={handleUpdateStatus}
        />
      )}
    </div>
  );
};

export default DeliveryManagement;