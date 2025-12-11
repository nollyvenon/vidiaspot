import React, { useState } from 'react';

const DeliveryPackageDetail = ({ package: pkg, onClose, onUpdateStatus }) => {
  const [signature, setSignature] = useState('');
  const [notes, setNotes] = useState('');
  const [image, setImage] = useState(null);
  const [imagePreview, setImagePreview] = useState('');

  const handleMarkDelivered = () => {
    // In a real app, this would send the data to the backend
    onUpdateStatus(pkg.id, 'delivered');
    onClose();
  };

  const handleImageChange = (e) => {
    const file = e.target.files[0];
    if (file) {
      setImage(file);
      const reader = new FileReader();
      reader.onloadend = () => {
        setImagePreview(reader.result);
      };
      reader.readAsDataURL(file);
    }
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

  return (
    <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
      <div className="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div className="p-6">
          <div className="flex justify-between items-center mb-4">
            <h2 className="text-xl font-bold">Package Detail</h2>
            <button 
              onClick={onClose}
              className="text-gray-500 hover:text-gray-700"
            >
              <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <div className="mb-6">
            <div className="flex justify-between items-start">
              <div>
                <h3 className="font-bold text-lg">{pkg.recipientName}</h3>
                <p className="text-gray-600">{pkg.trackingNumber}</p>
              </div>
              <span className={`px-3 py-1 rounded-full text-sm font-medium ${getStatusColor(pkg.status)}`}>
                {pkg.status.replace(/([A-Z])/g, ' $1').replace(/^./, str => str.toUpperCase())}
              </span>
            </div>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
              <h4 className="font-semibold mb-2">Recipient Information</h4>
              <p><span className="font-medium">Name:</span> {pkg.recipientName}</p>
              <p><span className="font-medium">Phone:</span> {pkg.recipientPhone}</p>
              <p><span className="font-medium">Address:</span> {pkg.deliveryAddress}</p>
            </div>
            
            <div>
              <h4 className="font-semibold mb-2">Package Information</h4>
              <p><span className="font-medium">Details:</span> {pkg.packageDetails}</p>
              <p><span className="font-medium">Weight:</span> {pkg.weight} kg</p>
              <p><span className="font-medium">Volume:</span> {pkg.volume} m³</p>
              <p><span className="font-medium">Priority:</span> {getPriorityString(pkg.priority)}</p>
            </div>
          </div>

          <div className="mb-6">
            <h4 className="font-semibold mb-2">Delivery Requirements</h4>
            <div className="flex space-x-4">
              <div className="flex items-center">
                <input 
                  type="checkbox" 
                  id="signature" 
                  checked={pkg.requiresSignature}
                  readOnly
                  className="mr-2"
                />
                <label htmlFor="signature">Signature Required</label>
              </div>
              <div className="flex items-center">
                <input 
                  type="checkbox" 
                  id="photo" 
                  checked={pkg.requiresPhoto}
                  readOnly
                  className="mr-2"
                />
                <label htmlFor="photo">Photo Required</label>
              </div>
              <div className="flex items-center">
                <input 
                  type="checkbox" 
                  id="idcheck" 
                  checked={pkg.requiresIdCheck}
                  readOnly
                  className="mr-2"
                />
                <label htmlFor="idcheck">ID Check Required</label>
              </div>
            </div>
          </div>

          {pkg.requiresPhoto && (
            <div className="mb-6">
              <h4 className="font-semibold mb-2">Delivery Photo</h4>
              {imagePreview ? (
                <div className="mb-4">
                  <img src={imagePreview} alt="Delivery proof" className="max-w-full h-auto rounded" />
                </div>
              ) : (
                <div className="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                  <input 
                    type="file" 
                    id="photo-upload"
                    accept="image/*"
                    onChange={handleImageChange}
                    className="hidden"
                  />
                  <label htmlFor="photo-upload" className="cursor-pointer">
                    <div className="flex flex-col items-center justify-center">
                      <svg xmlns="http://www.w3.org/2000/svg" className="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                      </svg>
                      <p className="mt-2 text-sm text-gray-600">Click to upload photo</p>
                    </div>
                  </label>
                </div>
              )}
            </div>
          )}

          {pkg.requiresSignature && (
            <div className="mb-6">
              <h4 className="font-semibold mb-2">Signature</h4>
              <div className="border border-gray-300 rounded-lg p-4 h-40 flex items-center justify-center">
                {signature ? (
                  <p className="text-center">{signature}</p>
                ) : (
                  <div className="text-center">
                    <p className="text-gray-500 mb-2">Signature area</p>
                    <button 
                      className="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700"
                      onClick={() => setSignature('Signature captured')}
                    >
                      Capture Signature
                    </button>
                  </div>
                )}
              </div>
            </div>
          )}

          <div className="mb-6">
            <label htmlFor="notes" className="block text-sm font-medium text-gray-700 mb-1">
              Delivery Notes
            </label>
            <textarea
              id="notes"
              rows={3}
              value={notes}
              onChange={(e) => setNotes(e.target.value)}
              className="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
              placeholder="Any special delivery instructions or notes..."
            />
          </div>

          <div className="flex justify-end space-x-3">
            <button
              onClick={onClose}
              className="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50"
            >
              Cancel
            </button>
            <button
              onClick={handleMarkDelivered}
              disabled={pkg.requiresPhoto && !imagePreview}
              className={`px-4 py-2 rounded-md shadow-sm text-sm font-medium text-white ${
                (pkg.requiresPhoto && !imagePreview) 
                  ? 'bg-gray-400 cursor-not-allowed' 
                  : 'bg-green-600 hover:bg-green-700'
              }`}
            >
              Mark as Delivered
            </button>
          </div>
        </div>
      </div>
    </div>
  );
};

export default DeliveryPackageDetail;