import React from 'react';
import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import { Provider } from 'react-redux';
import { createStore } from 'redux';
import { GoogleMap, LoadScript } from 'react-google-maps-api';
import './App.css';
import FarmDashboard from './components/FarmDashboard/FarmDashboard';
import ProductManagement from './components/ProductManagement/ProductManagement';
import OrderManagement from './components/OrderManagement/OrderManagement';
import FarmProfile from './components/FarmProfile/FarmProfile';
import AnalyticsDashboard from './components/AnalyticsDashboard/AnalyticsDashboard';
import LocationManagement from './components/LocationManagement/LocationManagement';

// Simple reducer for state management
const initialState = {
  farmData: null,
  products: [],
  orders: [],
  currentUser: null,
};

function appReducer(state = initialState, action) {
  switch (action.type) {
    case 'SET_FARM_DATA':
      return { ...state, farmData: action.payload };
    case 'SET_PRODUCTS':
      return { ...state, products: action.payload };
    case 'SET_ORDERS':
      return { ...state, orders: action.payload };
    case 'SET_CURRENT_USER':
      return { ...state, currentUser: action.payload };
    default:
      return state;
  }
}

const store = createStore(appReducer);

function App() {
  return (
    <Provider store={store}>
      <Router>
        <div className="App">
          <Routes>
            <Route path="/" element={<FarmDashboard />} />
            <Route path="/farm/dashboard" element={<FarmDashboard />} />
            <Route path="/farm/products" element={<ProductManagement />} />
            <Route path="/farm/orders" element={<OrderManagement />} />
            <Route path="/farm/profile" element={<FarmProfile />} />
            <Route path="/farm/analytics" element={<AnalyticsDashboard />} />
            <Route path="/farm/location" element={<LocationManagement />} />
          </Routes>
        </div>
      </Router>
    </Provider>
  );
}

export default App;