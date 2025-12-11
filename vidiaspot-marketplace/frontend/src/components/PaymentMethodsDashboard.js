import React, { useState, useEffect } from 'react';
import { withAuth } from '../utils/withAuth';
import paymentService from '../services/paymentService';
import { BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, Legend, ResponsiveContainer, PieChart, Pie, Cell, LineChart, Line } from 'recharts';

const PaymentMethodsDashboard = () => {
  const [activeTab, setActiveTab] = useState('crypto');
  const [cryptoProviders, setCryptoProviders] = useState([]);
  const [bnplProviders, setBnplProviders] = useState([]);
  const [mobileMoneyProviders, setMobileMoneyProviders] = useState([]);
  const [currentUser, setCurrentUser] = useState(null);
  const [paymentMethods, setPaymentMethods] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [selectedPaymentMethod, setSelectedPaymentMethod] = useState(null);

  // States for different payment features
  const [bnplApplication, setBnplApplication] = useState({
    provider: 'klarna',
    income: '',
    employment_type: '',
    credit_score: '',
    country: 'us',
    supporting_documents: []
  });
  
  const [qrCodePayment, setQrCodePayment] = useState({
    amount: '',
    recipient: '',
    note: ''
  });

  const [splitPayment, setSplitPayment] = useState({
    total_amount: '',
    participants: [],
    note: ''
  });

  const [insuranceOptions, setInsuranceOptions] = useState([]);
  const [selectedInsurance, setSelectedInsurance] = useState(null);

  const COLORS = ['#0088FE', '#00C49F', '#FFBB28', '#FF8042', '#8884D8', '#82CA9D', '#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4'];

  useEffect(() => {
    loadPaymentData();
  }, [activeTab]);

  const loadPaymentData = async () => {
    try {
      setLoading(true);
      setError(null);

      const promises = [];

      if (activeTab === 'crypto') {
        promises.push(paymentService.getSupportedCryptocurrencies().then(setCryptoProviders));
      } else if (activeTab === 'bnpl') {
        promises.push(paymentService.getBNPLProviders().then(setBnplProviders));
      } else if (activeTab === 'mobile-money') {
        promises.push(paymentService.getMobileMoneyProviders().then(setMobileMoneyProviders));
      }

      // Always load payment methods
      promises.push(paymentService.getPaymentMethods(currentUser?.id).then(setPaymentMethods));

      await Promise.all(promises);
    } catch (err) {
      setError(`Failed to load ${activeTab} data. Please try again.`);
      console.error(`Error loading ${activeTab} data:`, err);
    } finally {
      setLoading(false);
    }
  };

  const handleCryptoPayment = async (e) => {
    e.preventDefault();
    
    try {
      setLoading(true);
      const paymentData = {
        ...qrCodePayment, // Using qrCodePayment as base since it has amount
        payment_method: 'cryptocurrency',
        currency: 'bitcoin', // This would come from user selection
        user_id: currentUser.id
      };
      
      const result = await paymentService.createCryptoPayment(paymentData);
      alert('Crypto payment created successfully! Please complete the payment using your wallet.');
    } catch (err) {
      setError('Failed to process crypto payment. Please try again.');
      console.error('Error creating crypto payment:', err);
    } finally {
      setLoading(false);
    }
  };

  const handleBNPLApplication = async (e) => {
    e.preventDefault();
    
    try {
      setLoading(true);
      const applicationResult = await paymentService.applyForBNPL(bnplApplication);
      alert('Buy-now-pay-later application submitted successfully! You will be notified of the decision shortly.');
      setBnplApplication({
        provider: 'klarna',
        income: '',
        employment_type: '',
        credit_score: '',
        country: 'us',
        supporting_documents: []
      });
    } catch (err) {
      setError('Failed to submit BNPL application. Please try again.');
      console.error('Error submitting BNPL application:', err);
    } finally {
      setLoading(false);
    }
  };

  const handleMobileMoneyPayment = async (e) => {
    e.preventDefault();
    
    try {
      setLoading(true);
      const paymentData = {
        ...qrCodePayment,
        payment_method: 'mobile_money',
        provider: 'mpesa', // This would come from user selection
        user_id: currentUser.id
      };
      
      const result = await paymentService.createMobileMoneyPayment(paymentData);
      alert('Mobile money payment initiated successfully! Check your phone for confirmation.');
    } catch (err) {
      setError('Failed to process mobile money payment. Please try again.');
      console.error('Error creating mobile money payment:', err);
    } finally {
      setLoading(false);
    }
  };

  const handleQRCodePayment = async (e) => {
    e.preventDefault();
    
    try {
      setLoading(true);
      const paymentData = {
        ...qrCodePayment,
        payment_method: 'qr_code',
        user_id: currentUser.id
      };
      
      const result = await paymentService.generateQRCodePayment(paymentData);
      alert('QR code payment generated successfully! Scan with your mobile payment app to complete payment.');
    } catch (err) {
      setError('Failed to generate QR code payment. Please try again.');
      console.error('Error generating QR code payment:', err);
    } finally {
      setLoading(false);
    }
  };

  const handleSplitPayment = async (e) => {
    e.preventDefault();
    
    try {
      setLoading(true);
      const splitData = {
        ...splitPayment,
        initiated_by: currentUser.id
      };
      
      const result = await paymentService.createSplitPayment(splitData);
      alert('Split payment created successfully! Share the payment link with your friends to contribute.');
    } catch (err) {
      setError('Failed to create split payment. Please try again.');
      console.error('Error creating split payment:', err);
    } finally {
      setLoading(false);
    }
  };

  const handleInsurancePurchase = async (e) => {
    e.preventDefault();
    
    try {
      setLoading(true);
      const insuranceData = {
        ...selectedInsurance,
        user_id: currentUser.id
      };
      
      const result = await paymentService.purchaseInsurance(insuranceData);
      alert('Insurance purchased successfully! Your item is now protected.');
    } catch (err) {
      setError('Failed to purchase insurance. Please try again.');
      console.error('Error purchasing insurance:', err);
    } finally {
      setLoading(false);
    }
  };

  const renderCryptoPayment = () => (
    <div className="space-y-6">
      <div className="bg-white rounded-lg shadow-md p-6">
        <h2 className="text-xl font-semibold text-gray-900 mb-4">Cryptocurrency Payments</h2>
        
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
          {cryptoProviders.map((crypto) => (
            <div key={crypto.id} className="border rounded-lg p-4 hover:shadow-md transition-shadow">
              <div className="flex items-center">
                <div className="bg-gray-200 border-2 border-dashed rounded-xl w-16 h-16" />
                <div className="ml-4">
                  <h3 className="font-medium text-gray-900">{crypto.name}</h3>
                  <p className="text-sm text-gray-600">{crypto.symbol}</p>
                  <p className="text-sm text-green-600">✓ Enabled</p>
                </div>
              </div>
            </div>
          ))}
        </div>

        <form onSubmit={handleCryptoPayment} className="bg-gray-50 rounded-lg p-6">
          <h3 className="text-lg font-medium text-gray-900 mb-4">Make Crypto Payment</h3>
          
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Amount</label>
              <input
                type="number"
                step="0.01"
                value={qrCodePayment.amount}
                onChange={(e) => setQrCodePayment({...qrCodePayment, amount: e.target.value})}
                className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                required
              />
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Cryptocurrency</label>
              <select
                className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
                <option value="bitcoin">Bitcoin (BTC)</option>
                <option value="ethereum">Ethereum (ETH)</option>
                <option value="litecoin">Litecoin (LTC)</option>
                <option value="usdc">USD Coin (USDC)</option>
                <option value="usdt">Tether (USDT)</option>
              </select>
            </div>
          </div>
          
          <div className="mt-4">
            <label className="block text-sm font-medium text-gray-700 mb-1">Recipient</label>
            <input
              type="text"
              value={qrCodePayment.recipient}
              onChange={(e) => setQrCodePayment({...qrCodePayment, recipient: e.target.value})}
              className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="Wallet address or recipient identifier"
              required
            />
          </div>
          
          <div className="mt-4">
            <label className="block text-sm font-medium text-gray-700 mb-1">Note (Optional)</label>
            <textarea
              value={qrCodePayment.note}
              onChange={(e) => setQrCodePayment({...qrCodePayment, note: e.target.value})}
              className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              rows="2"
              placeholder="Add a note for the payment"
            ></textarea>
          </div>
          
          <div className="mt-6">
            <button
              type="submit"
              className="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 rounded-md"
            >
              Initiate Crypto Payment
            </button>
          </div>
        </form>
      </div>

      <div className="bg-white rounded-lg shadow-md p-6">
        <h3 className="text-lg font-medium text-gray-900 mb-4">Crypto Wallet Addresses</h3>
        <div className="overflow-x-auto">
          <table className="min-w-full divide-y divide-gray-200">
            <thead className="bg-gray-50">
              <tr>
                <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Cryptocurrency
                </th>
                <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Address
                </th>
                <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Status
                </th>
                <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Actions
                </th>
              </tr>
            </thead>
            <tbody className="bg-white divide-y divide-gray-200">
              {paymentMethods.filter(method => method.type === 'cryptocurrency').map((method) => (
                <tr key={method.id}>
                  <td className="px-6 py-4 whitespace-nowrap">
                    <div className="text-sm font-medium text-gray-900">{method.crypto_type}</div>
                  </td>
                  <td className="px-6 py-4">
                    <div className="text-sm text-gray-900 font-mono">{method.address}</div>
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap">
                    <span className={`px-2 py-1 rounded-full text-xs font-medium ${
                      method.is_verified ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'
                    }`}>
                      {method.is_verified ? 'Verified' : 'Unverified'}
                    </span>
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <button className="text-blue-600 hover:text-blue-900 mr-3">Send</button>
                    <button className="text-green-600 hover:text-green-900 mr-3">Receive</button>
                    <button className="text-gray-600 hover:text-gray-900">Details</button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );

  const renderBNPL = () => (
    <div className="space-y-6">
      <div className="bg-white rounded-lg shadow-md p-6">
        <h2 className="text-xl font-semibold text-gray-900 mb-4">Buy-Now-Pay-Later Options</h2>
        
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
          {bnplProviders.map((provider) => (
            <div key={provider.id} className="border rounded-lg p-6 hover:shadow-md transition-shadow">
              <div className="flex items-center justify-between mb-4">
                <h3 className="text-lg font-medium text-gray-900">{provider.name}</h3>
                <img src={provider.logo_url} alt={provider.name} className="h-8 w-8" />
              </div>
              <p className="text-gray-600 mb-4">{provider.description}</p>
              <ul className="text-sm text-gray-600 space-y-1 mb-4">
                <li>✓ Interest-free periods: {provider.interest_free_period}</li>
                <li>✓ Available in: {provider.available_countries.join(', ')}</li>
                <li>✓ Credit check required: {provider.credit_check_required ? 'Yes' : 'No'}</li>
              </ul>
              <button 
                className="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md"
                onClick={() => {
                  setBnplApplication({...bnplApplication, provider: provider.name.toLowerCase()});
                }}
              >
                Apply Now
              </button>
            </div>
          ))}
        </div>

        <form onSubmit={handleBNPLApplication} className="bg-gray-50 rounded-lg p-6">
          <h3 className="text-lg font-medium text-gray-900 mb-4">Apply for Buy-Now-Pay-Later</h3>
          
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">BNPL Provider</label>
              <select
                value={bnplApplication.provider}
                onChange={(e) => setBnplApplication({...bnplApplication, provider: e.target.value})}
                className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
                <option value="klarna">Klarna</option>
                <option value="afterpay">Afterpay</option>
                <option value="sezzle">Sezzle</option>
                <option value="affirm">Affirm</option>
                <option value="zip">Zip Co</option>
              </select>
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Monthly Income ($)</label>
              <input
                type="number"
                step="0.01"
                value={bnplApplication.income}
                onChange={(e) => setBnplApplication({...bnplApplication, income: e.target.value})}
                className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Enter your monthly income"
                required
              />
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Employment Type</label>
              <select
                value={bnplApplication.employment_type}
                onChange={(e) => setBnplApplication({...bnplApplication, employment_type: e.target.value})}
                className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
                <option value="full_time">Full-time Employee</option>
                <option value="part_time">Part-time Employee</option>
                <option value="contractor">Contractor/Freelancer</option>
                <option value="government">Government Employee</option>
                <option value="retired">Retired</option>
                <option value="student">Student</option>
              </select>
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Credit Score</label>
              <input
                type="number"
                min="300"
                max="850"
                value={bnplApplication.credit_score}
                onChange={(e) => setBnplApplication({...bnplApplication, credit_score: e.target.value})}
                className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Enter your credit score (300-850)"
                required
              />
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Country</label>
              <select
                value={bnplApplication.country}
                onChange={(e) => setBnplApplication({...bnplApplication, country: e.target.value})}
                className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
                <option value="us">United States</option>
                <option value="ca">Canada</option>
                <option value="uk">United Kingdom</option>
                <option value="au">Australia</option>
                <option value="de">Germany</option>
                <option value="fr">France</option>
                <option value="nl">Netherlands</option>
              </select>
            </div>
          </div>
          
          <div className="mt-4">
            <label className="block text-sm font-medium text-gray-700 mb-1">Supporting Documents</label>
            <div className="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
              <input
                type="file"
                multiple
                accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                className="hidden"
                id="document-upload"
              />
              <label htmlFor="document-upload" className="cursor-pointer">
                <svg className="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                  <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-12H9.88a2 2 0 00-1.995 1.858L8 14.25v4.5l.879.66a2 2 0 001.995.34H20m0 0v6m0-6h6m6 0v6m0-6h-6m-6-10H8a2 2 0 00-2 2v10a2 2 0 002 2h20a2 2 0 002-2V8a2 2 0 00-2-2z" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                </svg>
                <p className="mt-2 text-sm text-gray-600">Upload supporting documents (salary slips, bank statements, tax returns)</p>
                <p className="text-xs text-gray-500">PDF, DOC, JPG, PNG up to 10MB</p>
              </label>
            </div>
          </div>
          
          <div className="mt-6">
            <button
              type="submit"
              className="w-full bg-green-600 hover:bg-green-700 text-white py-3 px-4 rounded-md"
            >
              Submit Application
            </button>
          </div>
        </form>
      </div>

      <div className="bg-white rounded-lg shadow-md p-6">
        <h3 className="text-lg font-medium text-gray-900 mb-4">Your BNPL Status</h3>
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div className="bg-blue-50 p-4 rounded-lg">
            <h4 className="font-medium text-blue-900">Available Limit</h4>
            <p className="text-2xl font-bold text-blue-600">$2,500</p>
          </div>
          <div className="bg-green-50 p-4 rounded-lg">
            <h4 className="font-medium text-green-900">Active Plans</h4>
            <p className="text-2xl font-bold text-green-600">2</p>
          </div>
          <div className="bg-purple-50 p-4 rounded-lg">
            <h4 className="font-medium text-purple-900">Payment Due</h4>
            <p className="text-2xl font-bold text-purple-600">$450</p>
          </div>
        </div>
      </div>
    </div>
  );

  const renderMobileMoney = () => (
    <div className="space-y-6">
      <div className="bg-white rounded-lg shadow-md p-6">
        <h2 className="text-xl font-semibold text-gray-900 mb-4">Mobile Money Payments</h2>
        
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
          {mobileMoneyProviders.map((provider) => (
            <div key={provider.id} className="border rounded-lg p-4 hover:shadow-md transition-shadow text-center">
              <div className="bg-gray-200 border-2 border-dashed rounded-xl w-16 h-16 mx-auto" />
              <h3 className="font-medium text-gray-900 mt-2">{provider.name}</h3>
              <p className="text-sm text-gray-600">{provider.region}</p>
              <button 
                className="mt-2 bg-green-600 hover:bg-green-700 text-white py-1 px-3 rounded text-sm"
                onClick={() => {
                  setQrCodePayment({...qrCodePayment, recipient: provider.default_number});
                }}
              >
                Pay Now
              </button>
            </div>
          ))}
        </div>

        <form onSubmit={handleMobileMoneyPayment} className="bg-gray-50 rounded-lg p-6">
          <h3 className="text-lg font-medium text-gray-900 mb-4">Make Mobile Money Payment</h3>
          
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Amount</label>
              <input
                type="number"
                step="0.01"
                value={qrCodePayment.amount}
                onChange={(e) => setQrCodePayment({...qrCodePayment, amount: e.target.value})}
                className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                required
              />
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Provider</label>
              <select
                className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
                <option value="mpesa">M-Pesa</option>
                <option value="mtn">MTN Mobile Money</option>
                <option value="airtel">Airtel Money</option>
                <option value="tigo">Tigo Pesa</option>
              </select>
            </div>
          </div>
          
          <div className="mt-4">
            <label className="block text-sm font-medium text-gray-700 mb-1">Mobile Number</label>
            <input
              type="tel"
              value={qrCodePayment.recipient}
              onChange={(e) => setQrCodePayment({...qrCodePayment, recipient: e.target.value})}
              className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="Enter mobile number (e.g., +254712345678)"
              required
            />
          </div>
          
          <div className="mt-4">
            <label className="block text-sm font-medium text-gray-700 mb-1">Reason for Payment (Optional)</label>
            <input
              type="text"
              value={qrCodePayment.note}
              onChange={(e) => setQrCodePayment({...qrCodePayment, note: e.target.value})}
              className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="What is this payment for?"
            />
          </div>
          
          <div className="mt-6">
            <button
              type="submit"
              className="w-full bg-green-600 hover:bg-green-700 text-white py-3 px-4 rounded-md"
            >
              Initiate Mobile Payment
            </button>
          </div>
        </form>
      </div>

      <div className="bg-white rounded-lg shadow-md p-6">
        <h3 className="text-lg font-medium text-gray-900 mb-4">Saved Mobile Money Accounts</h3>
        <div className="overflow-x-auto">
          <table className="min-w-full divide-y divide-gray-200">
            <thead className="bg-gray-50">
              <tr>
                <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Provider
                </th>
                <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Mobile Number
                </th>
                <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Status
                </th>
                <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Actions
                </th>
              </tr>
            </thead>
            <tbody className="bg-white divide-y divide-gray-200">
              {paymentMethods.filter(method => method.type === 'mobile_money').map((method) => (
                <tr key={method.id}>
                  <td className="px-6 py-4 whitespace-nowrap">
                    <div className="text-sm font-medium text-gray-900">{method.provider}</div>
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap">
                    <div className="text-sm text-gray-900">{method.mobile_number}</div>
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap">
                    <span className={`px-2 py-1 rounded-full text-xs font-medium ${
                      method.is_verified ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'
                    }`}>
                      {method.is_verified ? 'Verified' : 'Pending'}
                    </span>
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <button className="text-blue-600 hover:text-blue-900 mr-3">Pay</button>
                    <button className="text-gray-600 hover:text-gray-900">Details</button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );

  const renderQRCode = () => (
    <div className="space-y-6">
      <div className="bg-white rounded-lg shadow-md p-6">
        <h2 className="text-xl font-semibold text-gray-900 mb-4">QR Code Payments</h2>
        
        <div className="bg-gray-50 rounded-lg p-8 text-center mb-6">
          <div className="bg-white p-4 inline-block rounded-lg">
            <div className="bg-gray-200 border-2 border-dashed rounded-xl w-48 h-48 mx-auto flex items-center justify-center">
              <span className="text-gray-500">QR Code</span>
            </div>
          </div>
          <p className="mt-4 text-gray-600">Scan this QR code with your mobile payment app to make a payment</p>
        </div>

        <form onSubmit={handleQRCodePayment} className="bg-gray-50 rounded-lg p-6">
          <h3 className="text-lg font-medium text-gray-900 mb-4">Create QR Payment</h3>
          
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Amount</label>
              <input
                type="number"
                step="0.01"
                value={qrCodePayment.amount}
                onChange={(e) => setQrCodePayment({...qrCodePayment, amount: e.target.value})}
                className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="0.00"
                required
              />
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Currency</label>
              <select
                className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
                <option value="USD">USD ($)</option>
                <option value="EUR">EUR (€)</option>
                <option value="GBP">GBP (£)</option>
                <option value="NGN">NGN (₦)</option>
                <option value="KES">KES (KSh)</option>
                <option value="GHS">GHS (GH₵)</option>
              </select>
            </div>
          </div>
          
          <div className="mt-4">
            <label className="block text-sm font-medium text-gray-700 mb-1">Recipient</label>
            <input
              type="text"
              value={qrCodePayment.recipient}
              onChange={(e) => setQrCodePayment({...qrCodePayment, recipient: e.target.value})}
              className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="Name or identifier of recipient"
              required
            />
          </div>
          
          <div className="mt-4">
            <label className="block text-sm font-medium text-gray-700 mb-1">Description</label>
            <textarea
              value={qrCodePayment.note}
              onChange={(e) => setQrCodePayment({...qrCodePayment, note: e.target.value})}
              className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              rows="2"
              placeholder="What is this payment for?"
            ></textarea>
          </div>
          
          <div className="mt-6">
            <button
              type="submit"
              className="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 rounded-md"
            >
              Generate QR Code
            </button>
          </div>
        </form>
      </div>

      <div className="bg-white rounded-lg shadow-md p-6">
        <h3 className="text-lg font-medium text-gray-900 mb-4">Recent QR Code Transactions</h3>
        <div className="overflow-x-auto">
          <table className="min-w-full divide-y divide-gray-200">
            <thead className="bg-gray-50">
              <tr>
              <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Date
              </th>
              <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Amount
              </th>
              <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Type
              </th>
              <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Status
              </th>
              <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Reference
              </th>
            </tr>
          </thead>
          <tbody className="bg-white divide-y divide-gray-200">
            {paymentMethods.filter(method => method.type === 'qr_code').map((method, index) => (
              <tr key={index}>
                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {new Date().toLocaleDateString()}
                </td>
                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  ${method.amount?.toFixed(2) || '0.00'}
                </td>
                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  QR Code Payment
                </td>
                <td className="px-6 py-4 whitespace-nowrap">
                  <span className={`px-2 py-1 rounded-full text-xs font-medium ${
                    method.status === 'completed' ? 'bg-green-100 text-green-800' :
                    method.status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                    'bg-red-100 text-red-800'
                  }`}>
                    {method.status?.toUpperCase() || 'COMPLETED'}
                  </span>
                </td>
                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {method.reference || 'N/A'}
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  </div>
);

const renderSplitPayment = () => (
  <div className="space-y-6">
    <div className="bg-white rounded-lg shadow-md p-6">
      <h2 className="text-xl font-semibold text-gray-900 mb-4">Split Payment for Group Purchases</h2>
      
      <form onSubmit={handleSplitPayment} className="bg-gray-50 rounded-lg p-6">
        <h3 className="text-lg font-medium text-gray-900 mb-4">Create Split Payment</h3>
        
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Total Amount</label>
            <input
              type="number"
              step="0.01"
              value={splitPayment.total_amount}
              onChange={(e) => setSplitPayment({...splitPayment, total_amount: e.target.value})}
              className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="0.00"
              required
            />
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Number of People</label>
            <input
              type="number"
              min="2"
              value={splitPayment.participants.length}
              onChange={(e) => {
                const num = parseInt(e.target.value);
                const newParticipants = Array.from({ length: num }, () => ({ email: '', amount: '' }));
                setSplitPayment({...splitPayment, participants: newParticipants});
              }}
              className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              required
            />
          </div>
        </div>
        
        <div className="mt-4">
          <label className="block text-sm font-medium text-gray-700 mb-2">Participants</label>
          {splitPayment.participants.map((participant, index) => (
            <div key={index} className="grid grid-cols-1 md:grid-cols-2 gap-4 mb-2">
              <input
                type="email"
                value={participant.email}
                onChange={(e) => {
                  const updated = [...splitPayment.participants];
                  updated[index].email = e.target.value;
                  setSplitPayment({...splitPayment, participants: updated});
                }}
                className="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Email address"
              />
              <input
                type="number"
                step="0.01"
                value={participant.amount}
                onChange={(e) => {
                  const updated = [...splitPayment.participants];
                  updated[index].amount = e.target.value;
                  setSplitPayment({...splitPayment, participants: updated});
                }}
                className="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Amount each should pay"
              />
            </div>
          ))}
        </div>
        
        <div className="mt-4">
          <label className="block text-sm font-medium text-gray-700 mb-1">Description (Optional)</label>
          <textarea
            value={splitPayment.note}
            onChange={(e) => setSplitPayment({...splitPayment, note: e.target.value})}
            className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            rows="2"
            placeholder="What is this payment for? (e.g., dinner, rent share, gift)"
          ></textarea>
        </div>
        
        <div className="mt-6">
          <button
            type="submit"
            className="w-full bg-purple-600 hover:bg-purple-700 text-white py-3 px-4 rounded-md"
          >
            Create Split Payment
          </button>
        </div>
      </form>
    </div>

    <div className="bg-white rounded-lg shadow-md p-6">
      <h3 className="text-lg font-medium text-gray-900 mb-4">Your Split Payment Groups</h3>
      <div className="overflow-x-auto">
        <table className="min-w-full divide-y divide-gray-200">
          <thead className="bg-gray-50">
            <tr>
              <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Description
              </th>
              <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Total Amount
              </th>
              <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                People
              </th>
              <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Status
              </th>
              <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Actions
              </th>
            </tr>
          </thead>
          <tbody className="bg-white divide-y divide-gray-200">
            {paymentMethods.filter(method => method.type === 'split_payment').map((payment, index) => (
              <tr key={index}>
                <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                  {payment.description || 'Group Payment'}
                </td>
                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  ${payment.total_amount?.toFixed(2) || '0.00'}
                </td>
                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {payment.participants?.length || 0} people
                </td>
                <td className="px-6 py-4 whitespace-nowrap">
                  <span className={`px-2 py-1 rounded-full text-xs font-medium ${
                    payment.status === 'completed' ? 'bg-green-100 text-green-800' :
                    payment.status === 'partial' ? 'bg-yellow-100 text-yellow-800' :
                    'bg-red-100 text-red-800'
                  }`}>
                    {payment.status?.toUpperCase() || 'PENDING'}
                  </span>
                </td>
                <td className="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <button className="text-blue-600 hover:text-blue-900 mr-3">View</button>
                  <button className="text-green-600 hover:text-green-900">Share Link</button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  </div>
);

const renderInsurance = () => (
  <div className="space-y-6">
    <div className="bg-white rounded-lg shadow-md p-6">
      <h2 className="text-xl font-semibold text-gray-900 mb-4">Insurance Integration for High-Value Items</h2>
      
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        {insuranceOptions.map((option) => (
          <div key={option.id} className="border rounded-lg p-6 hover:shadow-md transition-shadow">
            <h3 className="text-lg font-medium text-gray-900">{option.name}</h3>
            <p className="text-gray-600 my-2">{option.description}</p>
            <div className="text-2xl font-bold text-blue-600 mb-4">{option.cost}</div>
            <ul className="text-sm text-gray-600 space-y-1 mb-4">
              {option.coverage_points.map((point, idx) => (
                <li key={idx} className="flex items-center">
                  <svg className="h-4 w-4 text-green-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M5 13l4 4L19 7" />
                  </svg>
                  {point}
                </li>
              ))}
            </ul>
            <button 
              onClick={() => setSelectedInsurance(option)}
              className="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md"
            >
              Select Coverage
            </button>
          </div>
        ))}
      </div>

      {selectedInsurance && (
        <form onSubmit={handleInsurancePurchase} className="bg-gray-50 rounded-lg p-6">
          <h3 className="text-lg font-medium text-gray-900 mb-4">Purchase Insurance for Order</h3>
          
          <div className="mb-4 p-4 bg-blue-50 rounded-md">
            <h4 className="font-medium text-blue-900">{selectedInsurance.name}</h4>
            <p className="text-blue-800">{selectedInsurance.description}</p>
            <p className="text-lg font-bold text-blue-700">{selectedInsurance.cost}</p>
          </div>
          
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Order ID</label>
            <input
              type="text"
              placeholder="Enter the order ID to insure"
              className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>
          
          <div className="mt-4">
            <label className="block text-sm font-medium text-gray-700 mb-1">Item Description</label>
            <textarea
              placeholder="Describe the high-value item being insured"
              className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              rows="2"
            ></textarea>
          </div>
          
          <div className="mt-4">
            <label className="block text-sm font-medium text-gray-700 mb-1">Estimated Value</label>
            <input
              type="number"
              step="0.01"
              placeholder="0.00"
              className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>
          
          <div className="mt-6">
            <button
              type="submit"
              className="w-full bg-green-600 hover:bg-green-700 text-white py-3 px-4 rounded-md"
            >
              Purchase Insurance
            </button>
          </div>
        </form>
      )}
    </div>

    <div className="bg-white rounded-lg shadow-md p-6">
      <h3 className="text-lg font-medium text-gray-900 mb-4">Your Insurance Policies</h3>
      <div className="overflow-x-auto">
        <table className="min-w-full divide-y divide-gray-200">
          <thead className="bg-gray-50">
            <tr>
              <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Policy ID
              </th>
              <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Item
              </th>
              <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Coverage Amount
              </th>
              <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Premium
              </th>
              <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Status
              </th>
              <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Actions
              </th>
            </tr>
          </thead>
          <tbody className="bg-white divide-y divide-gray-200">
            {paymentMethods.filter(method => method.type === 'insurance').map((policy, index) => (
              <tr key={index}>
                <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                  {policy.policy_id || `POL-${index + 1}`}
                </td>
                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {policy.item_description || 'High-value purchase'}
                </td>
                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  ${policy.coverage_amount?.toFixed(2) || '0.00'}
                </td>
                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  ${policy.premium?.toFixed(2) || '0.00'}
                </td>
                <td className="px-6 py-4 whitespace-nowrap">
                  <span className={`px-2 py-1 rounded-full text-xs font-medium ${
                    policy.status === 'active' ? 'bg-green-100 text-green-800' :
                    policy.status === 'claimed' ? 'bg-yellow-100 text-yellow-800' :
                    'bg-red-100 text-red-800'
                  }`}>
                    {policy.status?.toUpperCase() || 'ACTIVE'}
                  </span>
                </td>
                <td className="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <button className="text-blue-600 hover:text-blue-900 mr-3">Details</button>
                  <button className="text-green-600 hover:text-green-900">File Claim</button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  </div>
);

if (loading && activeTab !== 'split-payment' && !paymentMethods.length) {
  return (
    <div className="min-h-screen flex items-center justify-center">
      <div className="animate-spin rounded-full h-32 w-32 border-b-2 border-blue-600"></div>
    </div>
  );
}

return (
  <div className="max-w-7xl mx-auto p-6">
    <div className="mb-8">
      <h1 className="text-3xl font-bold text-gray-900">Advanced Payment Options</h1>
      <p className="text-gray-600">Multiple payment methods including crypto, BNPL, mobile money, and more</p>
    </div>

    {error && (
      <div className="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
        <strong className="font-bold">Error: </strong>
        <span className="block sm:inline">{error}</span>
      </div>
    )}

    {/* Tab Navigation */}
    <div className="border-b border-gray-200 mb-6">
      <nav className="-mb-px flex flex-wrap">
        <button
          onClick={() => setActiveTab('crypto')}
          className={`whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm ${
            activeTab === 'crypto'
              ? 'border-blue-500 text-blue-600'
              : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
          }`}
        >
          Cryptocurrency
        </button>
        <button
          onClick={() => setActiveTab('bnpl')}
          className={`whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm ${
            activeTab === 'bnpl'
              ? 'border-blue-500 text-blue-600'
              : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
          }`}
        >
          Buy-Now-Pay-Later
        </button>
        <button
          onClick={() => setActiveTab('mobile-money')}
          className={`whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm ${
            activeTab === 'mobile-money'
              ? 'border-blue-500 text-blue-600'
              : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
          }`}
        >
          Mobile Money
        </button>
        <button
          onClick={() => setActiveTab('qr-code')}
          className={`whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm ${
            activeTab === 'qr-code'
              ? 'border-blue-500 text-blue-600'
              : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
          }`}
        >
          QR Code Payments
        </button>
        <button
          onClick={() => setActiveTab('split-payment')}
          className={`whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm ${
            activeTab === 'split-payment'
              ? 'border-blue-500 text-blue-600'
              : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
          }`}
        >
          Split Payments
        </button>
        <button
          onClick={() => setActiveTab('insurance')}
          className={`whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm ${
            activeTab === 'insurance'
              ? 'border-blue-500 text-blue-600'
              : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
          }`}
        >
          Insurance
        </button>
      </nav>
    </div>

    {/* Tab Content */}
    {activeTab === 'crypto' && renderCryptoPayment()}
    {activeTab === 'bnpl' && renderBNPL()}
    {activeTab === 'mobile-money' && renderMobileMoney()}
    {activeTab === 'qr-code' && renderQRCode()}
    {activeTab === 'split-payment' && renderSplitPayment()}
    {activeTab === 'insurance' && renderInsurance()}
  </div>
);
};

export default withAuth(PaymentMethodsDashboard, ['customer', 'seller', 'store_owner']);