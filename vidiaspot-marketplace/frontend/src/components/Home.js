import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import axios from 'axios';
import DashboardLayout from '../layouts/DashboardLayout';

const Home = () => {
  const [featuredAds, setFeaturedAds] = useState([]);
  const [categories, setCategories] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    // Fetch featured ads and categories
    const fetchHomeData = async () => {
      try {
        // Fetch latest ads as featured ads
        const adsResponse = await axios.get('http://localhost:8000/api/ads?per_page=6');
        setFeaturedAds(adsResponse.data.data.data || adsResponse.data.data);

        // Fetch categories
        const categoriesResponse = await axios.get('http://localhost:8000/api/categories');
        setCategories(categoriesResponse.data.data || []);

        setLoading(false);
      } catch (error) {
        console.error('Error fetching home data:', error);
        setLoading(false);
      }
    };

    fetchHomeData();
  }, []);

  return (
    <DashboardLayout>
      <div className="py-6">
        {/* Hero Section */}
        <div className="bg-gradient-to-r from-blue-600 to-indigo-700 text-white py-12 mb-8 rounded-lg">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div className="lg:text-center">
              <h1 className="text-4xl font-extrabold tracking-tight sm:text-5xl lg:text-6xl">
                Buy and Sell Locally
              </h1>
              <p className="mt-6 text-xl max-w-3xl mx-auto">
                Find great deals or sell your items to people in your community.
              </p>
              <div className="mt-10">
                <Link
                  to="/create-ad"
                  className="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-blue-700 bg-white hover:bg-blue-50"
                >
                  Post Your Ad
                </Link>
              </div>
            </div>
          </div>
        </div>

        {/* Categories Section */}
        <section className="mb-12">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 className="text-3xl font-extrabold text-gray-900 mb-8">Popular Categories</h2>
            {loading ? (
              <p className="text-center">Loading categories...</p>
            ) : (
              <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-4">
                {categories.slice(0, 6).map((category) => (
                  <div 
                    key={category.id} 
                    className="bg-white rounded-lg shadow p-6 text-center cursor-pointer hover:shadow-md transition-shadow duration-300"
                    onClick={() => window.location.href = `/ads?category_id=${category.id}`}
                  >
                    <div className="mx-auto mb-4 text-blue-500">
                      <svg xmlns="http://www.w3.org/2000/svg" className="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                      </svg>
                    </div>
                    <h3 className="text-lg font-medium text-gray-900">{category.name}</h3>
                  </div>
                ))}
              </div>
            )}
          </div>
        </section>

        {/* Featured Ads Section */}
        <section className="mb-12">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div className="flex justify-between items-center mb-8">
              <h2 className="text-3xl font-extrabold text-gray-900">Featured Ads</h2>
              <Link to="/ads" className="text-blue-600 hover:text-blue-800 font-medium">
                View All →
              </Link>
            </div>
            {loading ? (
              <p className="text-center">Loading featured ads...</p>
            ) : (
              <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
                {featuredAds.slice(0, 6).map((ad) => (
                  <div key={ad.id} className="bg-white rounded-lg shadow overflow-hidden hover:shadow-md transition-shadow duration-300">
                    <div className="h-40 bg-gray-200 flex items-center justify-center">
                      {ad.images && ad.images.length > 0 ? (
                        <img
                          src={ad.images[0]?.image_url || 'https://via.placeholder.com/300x200'}
                          alt={ad.title}
                          className="w-full h-full object-cover"
                        />
                      ) : (
                        <div className="text-gray-500">No Image</div>
                      )}
                    </div>
                    <div className="p-4">
                      <h3 className="text-lg font-medium text-gray-900 truncate">{ad.title}</h3>
                      <p className="text-blue-600 font-semibold mt-2">
                        {ad.price ? `₦${parseFloat(ad.price).toLocaleString()}` : 'Price not specified'}
                      </p>
                      <p className="text-gray-500 text-sm mt-1 truncate">{ad.location}</p>
                      <p className="text-gray-400 text-xs mt-1">
                        {new Date(ad.created_at).toLocaleDateString()}
                      </p>
                    </div>
                  </div>
                ))}
              </div>
            )}
          </div>
        </section>

        {/* How it Works Section */}
        <section className="bg-gray-50 py-12 mb-8">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 className="text-3xl font-extrabold text-gray-900 text-center mb-12">How VidiAspot Works</h2>
            <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
              <div className="text-center">
                <div className="mx-auto mb-6 text-blue-500">
                  <svg xmlns="http://www.w3.org/2000/svg" className="h-16 w-16 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                  </svg>
                </div>
                <h3 className="text-xl font-semibold text-gray-900 mb-3">Create Account</h3>
                <p className="text-gray-600">
                  Sign up for free to start buying or selling items in your area.
                </p>
              </div>
              <div className="text-center">
                <div className="mx-auto mb-6 text-blue-500">
                  <svg xmlns="http://www.w3.org/2000/svg" className="h-16 w-16 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                  </svg>
                </div>
                <h3 className="text-xl font-semibold text-gray-900 mb-3">Post Your Ad</h3>
                <p className="text-gray-600">
                  Create a listing with photos, description, and price for your item.
                </p>
              </div>
              <div className="text-center">
                <div className="mx-auto mb-6 text-blue-500">
                  <svg xmlns="http://www.w3.org/2000/svg" className="h-16 w-16 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
                <h3 className="text-xl font-semibold text-gray-900 mb-3">Connect & Trade</h3>
                <p className="text-gray-600">
                  Connect with buyers/sellers and make secure transactions.
                </p>
              </div>
            </div>
          </div>
        </section>
      </div>
    </DashboardLayout>
  );
};

export default Home;