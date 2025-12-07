import React, { useState, useEffect } from 'react';
import { useParams, Link } from 'react-router-dom';
import axios from 'axios';
import DashboardLayout from '../layouts/DashboardLayout';

const AdDetail = () => {
  const [ad, setAd] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [selectedImage, setSelectedImage] = useState(null);
  const { id } = useParams(); // Get ad ID from URL params

  useEffect(() => {
    fetchAdDetail();
  }, [id]);

  const fetchAdDetail = async () => {
    try {
      setLoading(true);
      const response = await axios.get(`http://localhost:8000/api/ads/${id}`);
      setAd(response.data.data);
      setSelectedImage(response.data.data.images && response.data.data.images.length > 0
        ? response.data.data.images[0].image_url
        : null);
      setLoading(false);
    } catch (err) {
      setError('Failed to fetch ad details. Please try again.');
      setLoading(false);
    }
  };

  const handleContactSeller = () => {
    const token = localStorage.getItem('token');
    if (!token) {
      alert('Please log in to contact the seller');
      window.location.href = '/login';
      return;
    }

    // Navigate to messages page with seller info
    window.location.href = `/messages?seller_id=${ad?.user.id}&ad_id=${ad?.id}`;
  };

  if (loading) {
    return (
      <DashboardLayout>
        <div className="py-6">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div className="flex justify-center items-center py-12">
              <div className="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>
            </div>
          </div>
        </div>
      </DashboardLayout>
    );
  }

  if (error) {
    return (
      <DashboardLayout>
        <div className="py-6">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div className="rounded-md bg-red-50 p-4 mb-6">
              <div className="flex">
                <div className="flex-shrink-0">
                  <svg className="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clipRule="evenodd" />
                  </svg>
                </div>
                <div className="ml-3">
                  <h3 className="text-sm font-medium text-red-800">{error}</h3>
                </div>
              </div>
            </div>
          </div>
        </div>
      </DashboardLayout>
    );
  }

  if (!ad) {
    return (
      <DashboardLayout>
        <div className="py-6">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div className="rounded-md bg-yellow-50 p-4 mb-6">
              <div className="flex">
                <div className="flex-shrink-0">
                  <svg className="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fillRule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clipRule="evenodd" />
                  </svg>
                </div>
                <div className="ml-3">
                  <h3 className="text-sm font-medium text-yellow-800">Ad not found</h3>
                </div>
              </div>
            </div>
          </div>
        </div>
      </DashboardLayout>
    );
  }

  return (
    <DashboardLayout>
      <div className="py-6">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="lg:grid lg:grid-cols-3 lg:gap-x-8 lg:items-start">
            {/* Image Gallery */}
            <div className="lg:col-span-2">
              <div className="lg:aspect-w-3 lg:aspect-h-2 h-96 rounded-lg overflow-hidden">
                {selectedImage ? (
                  <img
                    src={selectedImage}
                    alt={ad.title}
                    className="w-full h-full object-scale-down sm:object-cover"
                  />
                ) : (
                  <div className="w-full h-full bg-gray-200 flex items-center justify-center">
                    <span className="text-gray-500">No Image Available</span>
                  </div>
                )}
              </div>

              {/* Thumbnails */}
              {ad.images && ad.images.length > 1 && (
                <div className="mt-4 grid grid-cols-4 gap-4">
                  {ad.images.map((image, index) => (
                    <button
                      key={index}
                      className={`relative h-24 bg-white rounded-md flex items-center justify-center text-sm font-medium uppercase text-gray-900 cursor-pointer hover:bg-gray-50 focus:outline-none focus:ring focus:ring-blue-500 focus:ring-opacity-50 overflow-hidden ${
                        selectedImage === image.image_url ? 'ring-2 ring-blue-500' : ''
                      }`}
                      onClick={() => setSelectedImage(image.image_url)}
                    >
                      <img
                        src={image.image_url}
                        alt={`Ad image ${index + 1}`}
                        className="w-full h-full object-cover"
                      />
                    </button>
                  ))}
                </div>
              )}

              {/* Ad Details Card */}
              <div className="mt-8 bg-white shadow rounded-lg p-6">
                <h1 className="text-2xl font-bold text-gray-900 mb-2">{ad.title}</h1>
                <div className="flex items-baseline mb-4">
                  <p className="text-3xl font-extrabold text-gray-900">
                    ₦{parseFloat(ad.price).toLocaleString()}
                  </p>
                  {ad.negotiable && (
                    <p className="ml-2 text-sm font-medium text-gray-500">Negotiable</p>
                  )}
                </div>

                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                  <div>
                    <h3 className="text-sm font-medium text-gray-500">Condition</h3>
                    <p className="text-lg text-gray-900 capitalize">{ad.condition.replace('_', ' ')}</p>
                  </div>
                  <div>
                    <h3 className="text-sm font-medium text-gray-500">Location</h3>
                    <p className="text-lg text-gray-900">{ad.location}</p>
                  </div>
                  <div>
                    <h3 className="text-sm font-medium text-gray-500">Posted</h3>
                    <p className="text-lg text-gray-900">{new Date(ad.created_at).toLocaleDateString()}</p>
                  </div>
                  <div>
                    <h3 className="text-sm font-medium text-gray-500">Views</h3>
                    <p className="text-lg text-gray-900">{ad.view_count}</p>
                  </div>
                </div>

                <div className="border-t border-gray-200 pt-6">
                  <h3 className="text-lg font-medium text-gray-900 mb-4">Description</h3>
                  <p className="text-gray-600 whitespace-pre-line">{ad.description}</p>
                </div>
              </div>
            </div>

            {/* Seller Information and Actions */}
            <div className="mt-8 lg:mt-0 lg:col-span-1">
              {/* Seller Card */}
              <div className="bg-white shadow rounded-lg p-6">
                <h2 className="text-lg font-medium text-gray-900 mb-4">Seller Information</h2>
                
                <div className="flex items-center mb-4">
                  <div className="h-12 w-12 rounded-full bg-gray-200 flex items-center justify-center">
                    <span className="text-gray-700 font-medium">
                      {ad.user?.name?.charAt(0)?.toUpperCase() || 'U'}
                    </span>
                  </div>
                  <div className="ml-4">
                    <h3 className="text-base font-medium text-gray-900">{ad.user?.name || 'Unknown'}</h3>
                    <p className="text-sm text-gray-500">
                      Member since {new Date(ad.user?.created_at).getFullYear()}
                    </p>
                  </div>
                </div>

                {ad.user?.is_verified && (
                  <div className="mb-4 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                    <svg className="-ml-1 mr-1.5 h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                      <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd" />
                    </svg>
                    Verified Seller
                  </div>
                )}

                <button
                  onClick={handleContactSeller}
                  className="w-full bg-blue-600 text-white py-3 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                  Contact Seller
                </button>

                {ad.contact_phone && (
                  <div className="mt-4 text-center">
                    <p className="text-sm text-gray-500">Or call directly</p>
                    <a
                      href={`tel:${ad.contact_phone}`}
                      className="text-blue-600 hover:text-blue-800 font-medium"
                    >
                      {ad.contact_phone}
                    </a>
                  </div>
                )}
              </div>

              {/* Ad Actions */}
              <div className="mt-6 bg-white shadow rounded-lg p-6">
                <h2 className="text-lg font-medium text-gray-900 mb-4">Ad Actions</h2>
                <div className="space-y-3">
                  <button className="w-full bg-gray-100 text-gray-800 py-2 px-4 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    Save Ad
                  </button>
                  <button className="w-full bg-gray-100 text-gray-800 py-2 px-4 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    Report Ad
                  </button>
                  <button className="w-full bg-gray-100 text-gray-800 py-2 px-4 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    Share Ad
                  </button>
                </div>
              </div>

              {/* Return to Ads */}
              <div className="mt-6">
                <Link
                  to="/ads"
                  className="w-full bg-white text-gray-700 py-2 px-4 rounded-md border border-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 flex items-center justify-center"
                >
                  <svg className="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                  </svg>
                  Back to Ads
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </DashboardLayout>
  );
};

export default AdDetail;