import React from 'react';
import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import Header from './components/Header';
import Footer from './components/Footer';
import Home from './components/Home';
import AdList from './components/AdList';
import AdDetail from './components/AdDetail';
import CreateAd from './components/CreateAd';
import MyAds from './components/MyAds';
import Login from './components/Login';
import Register from './components/Register';
import Profile from './components/Profile';
import Messages from './components/Messages';
import CategoryList from './components/CategoryList';

function App() {
  return (
    <Router>
      <div className="flex flex-col min-h-screen">
        <Header />
        <main className="flex-grow py-6">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <Routes>
              <Route path="/" element={<Home />} />
              <Route path="/ads" element={<AdList />} />
              <Route path="/ads/:id" element={<AdDetail />} />
              <Route path="/create-ad" element={<CreateAd />} />
              <Route path="/my-ads" element={<MyAds />} />
              <Route path="/login" element={<Login />} />
              <Route path="/register" element={<Register />} />
              <Route path="/profile" element={<Profile />} />
              <Route path="/messages" element={<Messages />} />
              <Route path="/categories" element={<CategoryList />} />
            </Routes>
          </div>
        </main>
        <Footer />
      </div>
    </Router>
  );
}

export default App;
