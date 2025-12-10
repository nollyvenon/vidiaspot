import React, { useState } from 'react';
import ShopifyIntegration from './components/ShopifyIntegration';
import FoodVendingManagement from './components/FoodVendingManagement';

const TestComponent = () => {
  const [activeApp, setActiveApp] = useState('shopify'); // 'shopify' or 'food'

  return (
    <div className="p-6">
      <h1 className="text-3xl font-bold text-gray-900 mb-6">VidiaSpot Marketplace - Feature Testing</h1>
      
      <div className="mb-6">
        <button
          className={`mr-4 px-4 py-2 rounded-md ${
            activeApp === 'shopify' 
              ? 'bg-blue-600 text-white' 
              : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
          }`}
          onClick={() => setActiveApp('shopify')}
        >
          Shopify Integration
        </button>
        <button
          className={`px-4 py-2 rounded-md ${
            activeApp === 'food'
              ? 'bg-blue-600 text-white'
              : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
          }`}
          onClick={() => setActiveApp('food')}
        >
          Food Vending Management
        </button>
      </div>

      {activeApp === 'shopify' && (
        <div>
          <h2 className="text-2xl font-semibold mb-4">Shopify Integration Test</h2>
          <p className="mb-4 text-gray-600">Testing the Shopify integration functionality with API service</p>
          <ShopifyIntegration />
        </div>
      )}

      {activeApp === 'food' && (
        <div>
          <h2 className="text-2xl font-semibold mb-4">Food Vending Management Test</h2>
          <p className="mb-4 text-gray-600">Testing the food vending management functionality with API service</p>
          <FoodVendingManagement />
        </div>
      )}
    </div>
  );
};

export default TestComponent;