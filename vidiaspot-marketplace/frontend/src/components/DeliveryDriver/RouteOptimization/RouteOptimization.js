import React, { useState, useEffect } from 'react';

const RouteOptimization = () => {
  const [deliveryStops, setDeliveryStops] = useState([
    {
      id: 'stop-1',
      address: '123 Main Street, Lagos',
      customerName: 'John Doe',
      packageDetails: 'Electronics - Phone',
      priority: 2,
      weight: 2.5,
      volume: 0.01,
      timeWindowStart: '09:00',
      timeWindowEnd: '11:00',
      isCompleted: false,
      latitude: 6.456,
      longitude: 3.387
    },
    {
      id: 'stop-2',
      address: '456 Business Avenue, Lagos',
      customerName: 'Jane Smith',
      packageDetails: 'Documents',
      priority: 1,
      weight: 0.5,
      volume: 0.002,
      timeWindowStart: '10:00',
      timeWindowEnd: '12:00',
      isCompleted: false,
      latitude: 6.432,
      longitude: 3.398
    },
    {
      id: 'stop-3',
      address: '789 Innovation Hub, Lagos',
      customerName: 'Bob Johnson',
      packageDetails: 'Clothing items',
      priority: 3,
      weight: 1.2,
      volume: 0.008,
      timeWindowStart: '11:00',
      timeWindowEnd: '13:00',
      isCompleted: true,
      latitude: 6.445,
      longitude: 3.376
    }
  ]);

  const [optimizedRoute, setOptimizedRoute] = useState([]);
  const [totalDistance, setTotalDistance] = useState(0);
  const [totalTime, setTotalTime] = useState(0);
  const [driverLocation, setDriverLocation] = useState({ latitude: 6.4433, longitude: 3.3922 }); // Lagos coordinates
  const [isSharingLocation, setIsSharingLocation] = useState(false);

  // Simulate route optimization
  useEffect(() => {
    optimizeRoute();
    startLocationTracking();
  }, [deliveryStops]);

  const optimizeRoute = () => {
    // Simple nearest neighbor optimization (simplified)
    const unvisited = deliveryStops.filter(stop => !stop.isCompleted);

    if (unvisited.length === 0) {
      setOptimizedRoute([]);
      setTotalDistance(0);
      setTotalTime(0);
      return;
    }

    // For demo purposes, just sort by priority
    const route = [...unvisited].sort((a, b) => a.priority - b.priority);

    setOptimizedRoute(route);

    // Calculate approximate distance and time
    const distance = route.reduce((sum, stop) => sum + stop.weight, 0) * 5; // Simplified calculation
    setTotalDistance(distance);

    // Calculate estimated time (in minutes)
    const time = route.reduce((sum, stop) => sum + stop.weight * 10, 0) + route.length * 15; // 15 min per stop
    setTotalTime(time);
  };

  // Simulate location tracking
  const startLocationTracking = () => {
    // In a real app, this would use the Geolocation API
    const locationInterval = setInterval(() => {
      // Simulate slight location changes
      setDriverLocation(prev => ({
        latitude: prev.latitude + (Math.random() - 0.5) * 0.001,
        longitude: prev.longitude + (Math.random() - 0.5) * 0.001
      }));
    }, 5000); // Update every 5 seconds

    return () => clearInterval(locationInterval);
  };

  const toggleLocationSharing = () => {
    setIsSharingLocation(!isSharingLocation);

    // In a real app, this would send location to backend
    if (!isSharingLocation) {
      alert('Location sharing activated. Your location is now visible to customers.');
    } else {
      alert('Location sharing deactivated.');
    }
  };

  const updateStopStatus = (stopId, status) => {
    setDeliveryStops(prev => prev.map(stop =>
      stop.id === stopId ? {...stop, isCompleted: status} : stop
    ));
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

  const getPriorityColor = (priority) => {
    switch (priority) {
      case 1: return 'bg-red-100 text-red-800';
      case 2: return 'bg-orange-100 text-orange-800';
      case 3: return 'bg-yellow-100 text-yellow-800';
      case 4: return 'bg-blue-100 text-blue-800';
      case 5: return 'bg-green-100 text-green-800';
      default: return 'bg-gray-100 text-gray-800';
    }
  };

  return (
    <div>
      <div className="flex justify-between items-center mb-6">
        <h2 className="text-2xl font-bold">Optimized Delivery Route</h2>
        <div className="flex space-x-3">
          <button
            onClick={optimizeRoute}
            className="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 flex items-center"
          >
            <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
              <path fillRule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clipRule="evenodd" />
            </svg>
            Optimize Route
          </button>

          <button
            onClick={toggleLocationSharing}
            className={`px-4 py-2 rounded-md flex items-center ${
              isSharingLocation
                ? 'bg-green-600 text-white hover:bg-green-700'
                : 'bg-red-600 text-white hover:bg-red-700'
            }`}
          >
            <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
              <path fillRule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clipRule="evenodd" />
            </svg>
            {isSharingLocation ? 'Stop Sharing Location' : 'Share Location'}
          </button>
        </div>
      </div>

      {/* Route Summary */}
      <div className="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div className="bg-white rounded-lg shadow p-6">
          <h3 className="text-lg font-semibold text-gray-700">Total Stops</h3>
          <p className="text-3xl font-bold text-blue-600">{optimizedRoute.length}</p>
        </div>
        <div className="bg-white rounded-lg shadow p-6">
          <h3 className="text-lg font-semibold text-gray-700">Total Distance</h3>
          <p className="text-3xl font-bold text-blue-600">{totalDistance.toFixed(1)} km</p>
        </div>
        <div className="bg-white rounded-lg shadow p-6">
          <h3 className="text-lg font-semibold text-gray-700">Estimated Time</h3>
          <p className="text-3xl font-bold text-blue-600">{Math.floor(totalTime/60)}h {totalTime%60}m</p>
        </div>
        <div className="bg-white rounded-lg shadow p-6">
          <h3 className="text-lg font-semibold text-gray-700">Location Sharing</h3>
          <p className={`text-3xl font-bold ${isSharingLocation ? 'text-green-600' : 'text-red-600'}`}>
            {isSharingLocation ? 'Active' : 'Inactive'}
          </p>
        </div>
      </div>

      {/* Route Visualization */}
      <div className="bg-white rounded-lg shadow p-6 mb-6">
        <h3 className="text-lg font-semibold mb-4">Route Visualization</h3>
        <div className="bg-gray-100 border-2 border-dashed border-gray-300 rounded-xl h-64 flex items-center justify-center">
          <div className="text-center">
            <svg xmlns="http://www.w3.org/2000/svg" className="h-12 w-12 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
            </svg>
            <p className="mt-2 text-gray-500">Interactive Map View</p>
            <p className="text-sm text-gray-400">Optimized route with driver location sharing</p>
          </div>
        </div>
      </div>

      {/* Delivery Stops List */}
      <div className="bg-white rounded-lg shadow overflow-hidden">
        <div className="px-6 py-4 border-b border-gray-200">
          <h3 className="text-lg font-semibold">Delivery Stops</h3>
        </div>

        <ul className="divide-y divide-gray-200">
          {optimizedRoute.map((stop, index) => (
            <li key={stop.id} className="p-6 hover:bg-gray-50">
              <div className="flex items-start">
                <div className="flex-shrink-0 w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-4">
                  <span className="text-blue-800 font-semibold">{index + 1}</span>
                </div>

                <div className="flex-1 min-w-0">
                  <div className="flex items-center justify-between">
                    <h4 className="text-sm font-medium text-gray-900">{stop.customerName}</h4>
                    <span className={`inline-flex items-center px-3 py-0.5 rounded-full text-xs font-medium ${getPriorityColor(stop.priority)}`}>
                      {getPriorityString(stop.priority)}
                    </span>
                  </div>

                  <p className="text-sm text-gray-500 mt-1">{stop.address}</p>

                  <div className="mt-2 grid grid-cols-2 gap-2 text-xs">
                    <div><span className="font-medium">Package:</span> {stop.packageDetails}</div>
                    <div><span className="font-medium">Time Window:</span> {stop.timeWindowStart} - {stop.timeWindowEnd}</div>
                    <div><span className="font-medium">Weight:</span> {stop.weight} kg</div>
                    <div><span className="font-medium">Volume:</span> {stop.volume} m³</div>
                  </div>
                </div>

                <div className="ml-4 flex flex-col items-end space-y-2">
                  <button
                    onClick={() => updateStopStatus(stop.id, !stop.isCompleted)}
                    className={`px-3 py-1 rounded text-sm font-medium ${
                      stop.isCompleted
                        ? 'bg-green-100 text-green-800'
                        : 'bg-yellow-100 text-yellow-800'
                    }`}
                  >
                    {stop.isCompleted ? 'Completed' : 'Mark Complete'}
                  </button>
                  <button className="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    Navigate
                  </button>
                </div>
              </div>
            </li>
          ))}
        </ul>
      </div>
    </div>
  );
};

export default RouteOptimization;