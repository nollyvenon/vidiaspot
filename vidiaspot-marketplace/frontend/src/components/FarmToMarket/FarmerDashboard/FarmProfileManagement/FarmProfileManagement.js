import React, { useState, useEffect } from 'react';

const FarmProfileManagement = () => {
  const [farmData, setFarmData] = useState({
    id: 'farm-1',
    name: 'Green Valley Farms',
    description: 'Sustainable organic farming producing fresh vegetables and fruits',
    ownerName: 'John Doe',
    email: 'contact@greenvalleyfarms.com',
    phone: '+1234567890',
    address: '123 Farm Road, Agriculture District',
    latitude: 37.7749,
    longitude: -122.4194,
    categories: ['Vegetables', 'Fruits', 'Organic'],
    rating: 4.8,
    numRatings: 120,
    currency: 'USD',
    isActive: true,
    acceptsOnlineOrders: true,
    offersDelivery: true,
    offersPickup: true,
    deliveryRadius: 15, // in km
    operatingHours: [
      { day: 'Monday', open: '08:00', close: '18:00', open24: false },
      { day: 'Tuesday', open: '08:00', close: '18:00', open24: false },
      { day: 'Wednesday', open: '08:00', close: '18:00', open24: false },
      { day: 'Thursday', open: '08:00', close: '18:00', open24: false },
      { day: 'Friday', open: '08:00', close: '18:00', open24: false },
      { day: 'Saturday', open: '09:00', close: '16:00', open24: false },
      { day: 'Sunday', open: '10:00', close: '14:00', open24: false },
    ],
    logoUrl: '',
    bannerImage: '',
    paymentMethods: ['Cash', 'Card', 'Mobile Money'],
    certifications: ['Organic Certified', 'Sustainable Farming'],
    yearsInBusiness: 5,
    deliveryFee: 5.00,
    minOrderAmount: 10.00,
    avgPreparationTime: 30, // in minutes
    deliveryTimeEstimate: 45, // in minutes
    isVerified: true,
    dateJoined: new Date('2023-01-15'),
    languages: ['English', 'Local Language'],
    taxRate: 0.08, // 8%
    businessLicense: 'BUS-LIC-2023-001',
  });

  const [editedData, setEditedData] = useState({ ...farmData });
  const [activeTab, setActiveTab] = useState('basic');
  const [isEditing, setIsEditing] = useState(false);
  const [loading, setLoading] = useState(false);

  const handleInputChange = (field, value) => {
    setEditedData(prev => ({
      ...prev,
      [field]: value
    }));
  };

  const handleOperatingHourChange = (dayIndex, field, value) => {
    const updatedHours = [...editedData.operatingHours];
    updatedHours[dayIndex][field] = value;
    setEditedData(prev => ({
      ...prev,
      operatingHours: updatedHours
    }));
  };

  const toggleOperatingDay = (dayIndex) => {
    const updatedHours = [...editedData.operatingHours];
    updatedHours[dayIndex].open24 = !updatedHours[dayIndex].open24;
    if (updatedHours[dayIndex].open24) {
      updatedHours[dayIndex].open = '00:00';
      updatedHours[dayIndex].close = '23:59';
    }
    setEditedData(prev => ({
      ...prev,
      operatingHours: updatedHours
    }));
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);
    
    // In a real app, this would send data to the API
    setTimeout(() => {
      setFarmData({ ...editedData });
      setIsEditing(false);
      setLoading(false);
      alert('Farm profile updated successfully!');
    }, 1000);
  };

  const toggleFarmStatus = () => {
    setEditedData(prev => ({
      ...prev,
      isActive: !prev.isActive
    }));
  };

  return (
    <div className="farm-profile-management">
      <div className="header">
        <h2>Farm Profile Management</h2>
        <button 
          className={`status-toggle ${farmData.isActive ? 'active' : 'inactive'}`}
          onClick={toggleFarmStatus}
        >
          {farmData.isActive ? 'Farm Active' : 'Farm Inactive'}
        </button>
      </div>

      <div className="tabs">
        <button 
          className={activeTab === 'basic' ? 'active' : ''}
          onClick={() => setActiveTab('basic')}
        >
          Basic Info
        </button>
        <button 
          className={activeTab === 'operations' ? 'active' : ''}
          onClick={() => setActiveTab('operations')}
        >
          Operations
        </button>
        <button 
          className={activeTab === 'business' ? 'active' : ''}
          onClick={() => setActiveTab('business')}
        >
          Business Info
        </button>
        <button 
          className={activeTab === 'images' ? 'active' : ''}
          onClick={() => setActiveTab('images')}
        >
          Images
        </button>
      </div>

      <div className="tab-content">
        {activeTab === 'basic' && (
          <div className="tab-panel">
            <div className="farm-header-section">
              <div className="farm-images">
                <div className="logo-preview">
                  {editedData.logoUrl ? (
                    <img src={editedData.logoUrl} alt="Farm Logo" className="logo-img" />
                  ) : (
                    <div className="logo-placeholder">GV</div>
                  )}
                  <button 
                    className="change-btn"
                    onClick={() => document.getElementById('logo-upload').click()}
                  >
                    Change Logo
                  </button>
                  <input 
                    id="logo-upload" 
                    type="file" 
                    accept="image/*"
                    className="upload-input"
                    onChange={(e) => {
                      if (e.target.files && e.target.files[0]) {
                        // Handle logo upload
                        const url = URL.createObjectURL(e.target.files[0]);
                        setEditedData(prev => ({ ...prev, logoUrl: url }));
                      }
                    }}
                  />
                </div>
                
                <div className="banner-preview">
                  {editedData.bannerImage ? (
                    <img src={editedData.bannerImage} alt="Farm Banner" className="banner-img" />
                  ) : (
                    <div className="banner-placeholder">Farm Banner Image</div>
                  )}
                  <button 
                    className="change-btn"
                    onClick={() => document.getElementById('banner-upload').click()}
                  >
                    Change Banner
                  </button>
                  <input 
                    id="banner-upload" 
                    type="file" 
                    accept="image/*"
                    className="upload-input"
                    onChange={(e) => {
                      if (e.target.files && e.target.files[0]) {
                        // Handle banner upload
                        const url = URL.createObjectURL(e.target.files[0]);
                        setEditedData(prev => ({ ...prev, bannerImage: url }));
                      }
                    }}
                  />
                </div>
              </div>
            </div>

            <form onSubmit={handleSubmit} className="profile-form">
              <div className="form-grid">
                <div className="form-group">
                  <label>Farm Name *</label>
                  <input
                    type="text"
                    value={editedData.name}
                    onChange={(e) => handleInputChange('name', e.target.value)}
                    disabled={!isEditing}
                    required
                  />
                </div>
                
                <div className="form-group">
                  <label>Owner Name *</label>
                  <input
                    type="text"
                    value={editedData.ownerName}
                    onChange={(e) => handleInputChange('ownerName', e.target.value)}
                    disabled={!isEditing}
                    required
                  />
                </div>
                
                <div className="form-group">
                  <label>Email *</label>
                  <input
                    type="email"
                    value={editedData.email}
                    onChange={(e) => handleInputChange('email', e.target.value)}
                    disabled={!isEditing}
                    required
                  />
                </div>
                
                <div className="form-group">
                  <label>Phone *</label>
                  <input
                    type="tel"
                    value={editedData.phone}
                    onChange={(e) => handleInputChange('phone', e.target.value)}
                    disabled={!isEditing}
                    required
                  />
                </div>
                
                <div className="form-group">
                  <label>Address *</label>
                  <textarea
                    value={editedData.address}
                    onChange={(e) => handleInputChange('address', e.target.value)}
                    disabled={!isEditing}
                    required
                    rows="3"
                  />
                </div>
                
                <div className="form-group">
                  <label>Description *</label>
                  <textarea
                    value={editedData.description}
                    onChange={(e) => handleInputChange('description', e.target.value)}
                    disabled={!isEditing}
                    required
                    rows="4"
                  />
                </div>
                
                <div className="form-group">
                  <label>Categories *</label>
                  <div className="checkbox-group">
                    {['Vegetables', 'Fruits', 'Dairy', 'Eggs', 'Meat', 'Poultry', 'Grains', 'Other'].map((category) => (
                      <label key={category} className="checkbox-label">
                        <input
                          type="checkbox"
                          checked={editedData.categories.includes(category)}
                          onChange={(e) => {
                            const categories = editedData.categories;
                            if (e.target.checked) {
                              categories.push(category);
                            } else {
                              categories.splice(categories.indexOf(category), 1);
                            }
                            handleInputChange('categories', [...categories]);
                          }}
                          disabled={!isEditing}
                        />
                        {category}
                      </label>
                    ))}
                  </div>
                </div>
                
                <div className="form-group">
                  <label>Currency</label>
                  <select
                    value={editedData.currency}
                    onChange={(e) => handleInputChange('currency', e.target.value)}
                    disabled={!isEditing}
                  >
                    <option value="USD">USD - US Dollar</option>
                    <option value="EUR">EUR - Euro</option>
                    <option value="GBP">GBP - British Pound</option>
                    <option value="NGN">NGN - Nigerian Naira</option>
                    <option value="KES">KES - Kenyan Shilling</option>
                  </select>
                </div>
              </div>

              <div className="form-actions">
                {isEditing ? (
                  <>
                    <button type="submit" className="btn btn-primary" disabled={loading}>
                      {loading ? 'Saving...' : 'Save Changes'}
                    </button>
                    <button 
                      type="button" 
                      className="btn btn-secondary"
                      onClick={() => {
                        setEditedData({ ...farmData });
                        setIsEditing(false);
                      }}
                    >
                      Cancel
                    </button>
                  </>
                ) : (
                  <button 
                    type="button" 
                    className="btn btn-primary"
                    onClick={() => setIsEditing(true)}
                  >
                    Edit Profile
                  </button>
                )}
              </div>
            </form>
          </div>
        )}

        {activeTab === 'operations' && (
          <div className="tab-panel">
            <form onSubmit={handleSubmit} className="profile-form">
              <div className="form-grid">
                <div className="form-group">
                  <label>Operating Hours</label>
                  <div className="operating-hours">
                    {editedData.operatingHours.map((day, index) => (
                      <div key={index} className="day-hours">
                        <div className="day-name">{day.day}</div>
                        <div className="hours-inputs">
                          {day.open24 ? (
                            <span className="open-24">Open 24 Hours</span>
                          ) : (
                            <>
                              <input
                                type="time"
                                value={day.open}
                                onChange={(e) => handleOperatingHourChange(index, 'open', e.target.value)}
                                disabled={!isEditing}
                              />
                              <span>to</span>
                              <input
                                type="time"
                                value={day.close}
                                onChange={(e) => handleOperatingHourChange(index, 'close', e.target.value)}
                                disabled={!isEditing}
                              />
                            </>
                          )}
                          <label className="open24-checkbox">
                            <input
                              type="checkbox"
                              checked={day.open24}
                              onChange={() => toggleOperatingDay(index)}
                              disabled={!isEditing}
                            />
                            Open 24 Hours
                          </label>
                        </div>
                      </div>
                    ))}
                  </div>
                </div>
                
                <div className="form-row">
                  <div className="form-group">
                    <label>Delivery Radius (km)</label>
                    <input
                      type="number"
                      value={editedData.deliveryRadius}
                      onChange={(e) => handleInputChange('deliveryRadius', parseInt(e.target.value))}
                      disabled={!isEditing}
                    />
                  </div>
                  
                  <div className="form-group">
                    <label>Delivery Fee ($)</label>
                    <input
                      type="number"
                      step="0.01"
                      value={editedData.deliveryFee}
                      onChange={(e) => handleInputChange('deliveryFee', parseFloat(e.target.value))}
                      disabled={!isEditing}
                    />
                  </div>
                </div>
                
                <div className="form-row">
                  <div className="form-group">
                    <label>Minimum Order Amount ($)</label>
                    <input
                      type="number"
                      step="0.01"
                      value={editedData.minOrderAmount}
                      onChange={(e) => handleInputChange('minOrderAmount', parseFloat(e.target.value))}
                      disabled={!isEditing}
                    />
                  </div>
                  
                  <div className="form-group">
                    <label>Average Preparation Time (minutes)</label>
                    <input
                      type="number"
                      value={editedData.avgPreparationTime}
                      onChange={(e) => handleInputChange('avgPreparationTime', parseInt(e.target.value))}
                      disabled={!isEditing}
                    />
                  </div>
                </div>
                
                <div className="form-row">
                  <div className="form-group checkbox-group">
                    <label>
                      <input
                        type="checkbox"
                        checked={editedData.acceptsOnlineOrders}
                        onChange={(e) => handleInputChange('acceptsOnlineOrders', e.target.checked)}
                        disabled={!isEditing}
                      />
                      Accepts Online Orders
                    </label>
                  </div>
                  
                  <div className="form-group checkbox-group">
                    <label>
                      <input
                        type="checkbox"
                        checked={editedData.offersDelivery}
                        onChange={(e) => handleInputChange('offersDelivery', e.target.checked)}
                        disabled={!isEditing}
                      />
                      Offers Delivery
                    </label>
                  </div>
                  
                  <div className="form-group checkbox-group">
                    <label>
                      <input
                        type="checkbox"
                        checked={editedData.offersPickup}
                        onChange={(e) => handleInputChange('offersPickup', e.target.checked)}
                        disabled={!isEditing}
                      />
                      Offers Pickup
                    </label>
                  </div>
                  
                  <div className="form-group checkbox-group">
                    <label>
                      <input
                        type="checkbox"
                        checked={editedData.isVerified}
                        onChange={(e) => handleInputChange('isVerified', e.target.checked)}
                        disabled={!isEditing}
                      />
                      Verified Account
                    </label>
                  </div>
                </div>
              </div>

              <div className="form-actions">
                {isEditing ? (
                  <>
                    <button type="submit" className="btn btn-primary" disabled={loading}>
                      {loading ? 'Saving...' : 'Save Changes'}
                    </button>
                    <button 
                      type="button" 
                      className="btn btn-secondary"
                      onClick={() => {
                        setEditedData({ ...farmData });
                        setIsEditing(false);
                      }}
                    >
                      Cancel
                    </button>
                  </>
                ) : (
                  <button 
                    type="button" 
                    className="btn btn-primary"
                    onClick={() => setIsEditing(true)}
                  >
                    Edit Operations
                  </button>
                )}
              </div>
            </form>
          </div>
        )}

        {activeTab === 'business' && (
          <div className="tab-panel">
            <form onSubmit={handleSubmit} className="profile-form">
              <div className="form-grid">
                <div className="form-row">
                  <div className="form-group">
                    <label>Years in Business</label>
                    <input
                      type="number"
                      value={editedData.yearsInBusiness}
                      onChange={(e) => handleInputChange('yearsInBusiness', parseInt(e.target.value))}
                      disabled={!isEditing}
                    />
                  </div>
                  
                  <div className="form-group">
                    <label>Tax Rate (%)</label>
                    <input
                      type="number"
                      step="0.01"
                      value={editedData.taxRate * 100}
                      onChange={(e) => handleInputChange('taxRate', parseFloat(e.target.value) / 100)}
                      disabled={!isEditing}
                    />
                  </div>
                </div>
                
                <div className="form-group">
                  <label>Payment Methods Accepted</label>
                  <div className="checkbox-group">
                    {['Cash', 'Card', 'Mobile Money', 'Bank Transfer', 'Other'].map((method) => (
                      <label key={method} className="checkbox-label">
                        <input
                          type="checkbox"
                          checked={editedData.paymentMethods.includes(method)}
                          onChange={(e) => {
                            const methods = editedData.paymentMethods;
                            if (e.target.checked) {
                              methods.push(method);
                            } else {
                              methods.splice(methods.indexOf(method), 1);
                            }
                            handleInputChange('paymentMethods', [...methods]);
                          }}
                          disabled={!isEditing}
                        />
                        {method}
                      </label>
                    ))}
                  </div>
                </div>
                
                <div className="form-group">
                  <label>Certifications</label>
                  <div className="certifications-list">
                    {editedData.certifications.map((cert, index) => (
                      <div key={index} className="cert-item">
                        <span>{cert}</span>
                        {isEditing && (
                          <button 
                            type="button" 
                            className="remove-cert"
                            onClick={() => {
                              const certs = [...editedData.certifications];
                              certs.splice(index, 1);
                              handleInputChange('certifications', certs);
                            }}
                          >
                            ×
                          </button>
                        )}
                      </div>
                    ))}
                    {isEditing && (
                      <div className="add-cert">
                        <input
                          type="text"
                          placeholder="Add certification"
                          onKeyDown={(e) => {
                            if (e.key === 'Enter' && e.target.value.trim()) {
                              e.preventDefault();
                              const certs = [...editedData.certifications, e.target.value.trim()];
                              handleInputChange('certifications', certs);
                              e.target.value = '';
                            }
                          }}
                        />
                      </div>
                    )}
                  </div>
                </div>
                
                <div className="form-group">
                  <label>Business License Number</label>
                  <input
                    type="text"
                    value={editedData.businessLicense}
                    onChange={(e) => handleInputChange('businessLicense', e.target.value)}
                    disabled={!isEditing}
                  />
                </div>
                
                <div className="form-group">
                  <label>Languages Supported</label>
                  <div className="checkbox-group">
                    {['English', 'Spanish', 'French', 'Local Languages', 'Other'].map((lang) => (
                      <label key={lang} className="checkbox-label">
                        <input
                          type="checkbox"
                          checked={editedData.languages.includes(lang)}
                          onChange={(e) => {
                            const langs = editedData.languages;
                            if (e.target.checked) {
                              langs.push(lang);
                            } else {
                              langs.splice(langs.indexOf(lang), 1);
                            }
                            handleInputChange('languages', [...langs]);
                          }}
                          disabled={!isEditing}
                        />
                        {lang}
                      </label>
                    ))}
                  </div>
                </div>
              </div>

              <div className="form-actions">
                {isEditing ? (
                  <>
                    <button type="submit" className="btn btn-primary" disabled={loading}>
                      {loading ? 'Saving...' : 'Save Changes'}
                    </button>
                    <button 
                      type="button" 
                      className="btn btn-secondary"
                      onClick={() => {
                        setEditedData({ ...farmData });
                        setIsEditing(false);
                      }}
                    >
                      Cancel
                    </button>
                  </>
                ) : (
                  <button 
                    type="button" 
                    className="btn btn-primary"
                    onClick={() => setIsEditing(true)}
                  >
                    Edit Business Info
                  </button>
                )}
              </div>
            </form>
          </div>
        )}

        {activeTab === 'images' && (
          <div className="tab-panel">
            <div className="image-management">
              <div className="image-section">
                <h3>Gallery Images</h3>
                <div className="image-grid">
                  {[1, 2, 3, 4].map((index) => (
                    <div key={index} className="image-card">
                      {editedData[`galleryImage${index}`] ? (
                        <img src={editedData[`galleryImage${index}`]} alt={`Gallery ${index}`} />
                      ) : (
                        <div className="image-placeholder">Image {index}</div>
                      )}
                      <div className="image-actions">
                        <button 
                          className="btn btn-sm btn-secondary"
                          onClick={() => document.getElementById(`gallery-upload-${index}`).click()}
                        >
                          Upload
                        </button>
                        <input 
                          id={`gallery-upload-${index}`} 
                          type="file" 
                          accept="image/*"
                          className="upload-input"
                          onChange={(e) => {
                            if (e.target.files && e.target.files[0]) {
                              const url = URL.createObjectURL(e.target.files[0]);
                              setEditedData(prev => ({ ...prev, [`galleryImage${index}`]: url }));
                            }
                          }}
                        />
                        {editedData[`galleryImage${index}`] && (
                          <button 
                            className="btn btn-sm btn-danger"
                            onClick={() => setEditedData(prev => ({ ...prev, [`galleryImage${index}`]: '' }))}
                          >
                            Remove
                          </button>
                        )}
                      </div>
                    </div>
                  ))}
                </div>
              </div>
            </div>

            <div className="form-actions">
              {isEditing ? (
                <>
                  <button type="submit" className="btn btn-primary" disabled={loading}>
                    {loading ? 'Saving...' : 'Save Changes'}
                  </button>
                  <button 
                    type="button" 
                    className="btn btn-secondary"
                    onClick={() => {
                      setEditedData({ ...farmData });
                      setIsEditing(false);
                    }}
                  >
                    Cancel
                  </button>
                </>
              ) : (
                <button 
                  type="button" 
                  className="btn btn-primary"
                  onClick={() => setIsEditing(true)}
                >
                  Edit Images
                </button>
              )}
            </div>
          </div>
        )}
      </div>
    </div>
  );
};

export default FarmProfileManagement;