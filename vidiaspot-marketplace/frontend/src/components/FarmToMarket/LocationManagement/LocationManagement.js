import React, { useState, useEffect } from 'react';
import { GoogleMap, LoadScript, Marker, Circle } from '@react-google-maps/api';

const LocationManagement = ({ farmData }) => {
  const [farmLocation, setFarmLocation] = useState({
    lat: farmData?.latitude || 6.456,
    lng: farmData?.longitude || 3.387
  });
  const [deliveryRadius, setDeliveryRadius] = useState(farmData?.deliveryRadius || 10);
  const [isEditing, setIsEditing] = useState(false);
  const [loading, setLoading] = useState(false);

  const mapStyles = {
    height: '400px',
    width: '100%',
    borderRadius: '8px',
    border: '1px solid #e2e8f0'
  };

  const defaultCenter = {
    lat: farmLocation.lat,
    lng: farmLocation.lng
  };

  const handleSaveLocation = async () => {
    setLoading(true);
    try {
      // In a real app, this would update the location in the backend
      await new Promise(resolve => setTimeout(resolve, 800)); // Simulate API call
      
      // Update farm location in parent component
      // This would be passed as props from parent
      setIsEditing(false);
      alert('Farm location updated successfully!');
    } catch (error) {
      console.error('Error updating farm location:', error);
      alert('Failed to update farm location');
    } finally {
      setLoading(false);
    }
  };

  const handleMapClick = (event) => {
    if (isEditing) {
      setFarmLocation({
        lat: event.latLng.lat(),
        lng: event.latLng.lng()
      });
    }
  };

  return (
    <div className="location-management">
      <div className="header">
        <h2>Location Management</h2>
        <p>Set your farm location and delivery radius</p>
      </div>

      <div className="location-content">
        <div className="location-form">
          <div className="form-row">
            <div className="form-group">
              <label htmlFor="latitude">Latitude</label>
              <input
                id="latitude"
                type="number"
                value={farmLocation.lat}
                onChange={(e) => setFarmLocation({...farmLocation, lat: parseFloat(e.target.value)})}
                disabled={!isEditing}
                step="any"
              />
            </div>
            
            <div className="form-group">
              <label htmlFor="longitude">Longitude</label>
              <input
                id="longitude"
                type="number"
                value={farmLocation.lng}
                onChange={(e) => setFarmLocation({...farmLocation, lng: parseFloat(e.target.value)})}
                disabled={!isEditing}
                step="any"
              />
            </div>
          </div>
          
          <div className="form-group">
            <label htmlFor="radius">Delivery Radius (km)</label>
            <input
              id="radius"
              type="number"
              value={deliveryRadius}
              onChange={(e) => setDeliveryRadius(parseInt(e.target.value))}
              disabled={!isEditing}
              min="1"
              max="100"
            />
          </div>
          
          <div className="form-group">
            <label htmlFor="address">Farm Address</label>
            <textarea
              id="address"
              value={farmData?.address || ''}
              disabled={!isEditing}
              rows="3"
            />
          </div>
          
          <div className="form-actions">
            {isEditing ? (
              <>
                <button 
                  className="btn btn-primary"
                  onClick={handleSaveLocation}
                  disabled={loading}
                >
                  {loading ? 'Saving...' : 'Save Location'}
                </button>
                <button 
                  className="btn btn-secondary"
                  onClick={() => setIsEditing(false)}
                  disabled={loading}
                >
                  Cancel
                </button>
              </>
            ) : (
              <button 
                className="btn btn-primary"
                onClick={() => setIsEditing(true)}
              >
                Edit Location
              </button>
            )}
          </div>
        </div>

        <div className="map-container">
          <LoadScript googleMapsApiKey={process.env.REACT_APP_GOOGLE_MAPS_API_KEY || 'YOUR_API_KEY'}>
            <GoogleMap
              mapContainerStyle={mapStyles}
              center={defaultCenter}
              zoom={12}
              onClick={handleMapClick}
            >
              <Marker 
                position={{ lat: farmLocation.lat, lng: farmLocation.lng }} 
                draggable={isEditing}
                onDragEnd={(position) => {
                  setFarmLocation({
                    lat: position.latLng.lat(),
                    lng: position.latLng.lng()
                  });
                }}
              />
              <Circle
                center={{ lat: farmLocation.lat, lng: farmLocation.lng }}
                radius={deliveryRadius * 1000} // Convert km to meters
                options={{
                  fillColor: '#4ade80',
                  fillOpacity: 0.2,
                  strokeColor: '#4ade80',
                  strokeOpacity: 0.8,
                  strokeWeight: 2,
                }}
              />
            </GoogleMap>
          </LoadScript>
          
          <div className="map-legend">
            <div className="legend-item">
              <div className="marker-icon green"></div>
              <span>Farm Location</span>
            </div>
            <div className="legend-item">
              <div className="area-indicator green"></div>
              <span>Delivery Area ({deliveryRadius} km radius)</span>
            </div>
          </div>
        </div>
      </div>

      <div className="delivery-info">
        <h3>Delivery Information</h3>
        <div className="info-grid">
          <div className="info-card">
            <h4>Estimated Coverage</h4>
            <p>About {Math.round(Math.PI * deliveryRadius * deliveryRadius)} km² covered</p>
          </div>
          <div className="info-card">
            <h4>Estimated Customers</h4>
            <p>About {Math.round(Math.PI * deliveryRadius * deliveryRadius * 50)} potential customers</p>
          </div>
          <div className="info-card">
            <h4>Delivery Time</h4>
            <p>Within {Math.round(deliveryRadius * 3)} minutes</p>
          </div>
        </div>
      </div>

      <div className="delivery-areas-section">
        <h3>Delivery Areas</h3>
        <div className="delivery-areas-list">
          {['Lagos Island', 'Ikoyi', 'Victoria Island', 'Surulere', 'Yaba', 'Ajah'].map((area, index) => (
            <div key={index} className="delivery-area-item">
              <div className="area-info">
                <h4>{area}</h4>
                <p>Distance: {Math.abs(5 - index * 1.2).toFixed(1)} km from farm</p>
              </div>
              <div className="area-status">
                <span className={`status ${index % 2 === 0 ? 'active' : 'inactive'}`}>
                  {index % 2 === 0 ? 'Active' : 'Inactive'}
                </span>
              </div>
            </div>
          ))}
        </div>
      </div>
    </div>
  );
};

export default LocationManagement;