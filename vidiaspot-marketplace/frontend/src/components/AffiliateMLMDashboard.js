import React, { useState, useEffect } from 'react';
import { withAuth } from '../utils/withAuth';
import affiliateService from '../services/affiliateService';
import { BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, Legend, ResponsiveContainer, PieChart, Pie, Cell, LineChart, Line, AreaChart, Area } from 'recharts';

const AffiliateMLMDashboard = () => {
  const [activeTab, setActiveTab] = useState('dashboard');
  const [subTab, setSubTab] = useState('overview'); // for nested tabs
  const [affiliateData, setAffiliateData] = useState(null);
  const [commissions, setCommissions] = useState([]);
  const [referrals, setReferrals] = useState([]);
  const [payouts, setPayouts] = useState([]);
  const [downline, setDownline] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [mlmSettings, setMlmSettings] = useState(null);
  const [commissionStructure, setCommissionStructure] = useState([]);
  const [newTier, setNewTier] = useState({
    name: '',
    level: 1,
    commission_percentage: 0,
    min_referrals: 0,
    min_commissions: 0
  });

  const COLORS = ['#0088FE', '#00C49F', '#FFBB28', '#FF8042', '#8884D8', '#82CA9D', '#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4'];

  useEffect(() => {
    loadAffiliateData();
  }, [activeTab, subTab]);

  const loadAffiliateData = async () => {
    try {
      setLoading(true);
      setError(null);

      switch (activeTab) {
        case 'dashboard':
          const dashboardData = await affiliateService.getAffiliateDashboard(currentUser.id);
          setAffiliateData(dashboardData);
          break;
          
        case 'referrals':
          const referralsData = await affiliateService.getAffiliateReferrals(currentUser.id);
          setReferrals(referralsData);
          break;
          
        case 'commissions':
          const commissionsData = await affiliateService.getAffiliateCommissions(currentUser.id);
          setCommissions(commissionsData);
          break;
          
        case 'payouts':
          const payoutsData = await affiliateService.getAffiliatePayouts(currentUser.id);
          setPayouts(payoutsData);
          break;
          
        case 'downline':
          const downlineData = await affiliateService.getUserDownline(currentUser.id);
          setDownline(downlineData);
          break;
          
        case 'mlm':
          if (subTab === 'settings') {
            const settings = await affiliateService.getMLMSettings();
            setMlmSettings(settings);
            const structure = await affiliateService.getCommissionStructure();
            setCommissionStructure(structure);
          } else {
            const downlineData = await affiliateService.getUserDownline(currentUser.id);
            setDownline(downlineData);
          }
          break;
          
        case 'marketing':
          // Marketing materials will be loaded separately if needed
          break;
      }
    } catch (err) {
      setError('Failed to load affiliate data. Please try again later.');
      console.error('Error loading affiliate data:', err);
    } finally {
      setLoading(false);
    }
  };

  const handleApplyForAffiliate = async () => {
    try {
      setLoading(true);
      const applicationData = {
        user_id: currentUser.id,
        business_type: 'online_store',
        marketing_channels: ['social_media', 'content_marketing', 'email'],
        expected_monthly_traffic: 1000
      };
      
      await affiliateService.applyForAffiliate(applicationData);
      alert('Affiliate application submitted successfully!');
      loadAffiliateData();
    } catch (err) {
      setError('Failed to submit affiliate application. Please try again.');
      console.error('Error applying for affiliate:', err);
    } finally {
      setLoading(false);
    }
  };

  const handleRequestPayout = async () => {
    try {
      setLoading(true);
      const payoutData = {
        user_id: currentUser.id,
        amount: parseFloat(affiliateData?.available_balance || 0),
        method: 'paypal', // Default to PayPal
        request_date: new Date().toISOString()
      };
      
      await affiliateService.requestPayout(payoutData);
      alert('Payout request submitted successfully!');
      loadAffiliateData();
    } catch (err) {
      setError('Failed to request payout. Please try again.');
      console.error('Error requesting payout:', err);
    } finally {
      setLoading(false);
    }
  };

  const handleGenerateReferralCode = async () => {
    try {
      setLoading(true);
      const newCode = await affiliateService.generateReferralCode(currentUser.id);
      setAffiliateData({...affiliateData, referral_code: newCode.code});
      alert('New referral code generated successfully!');
    } catch (err) {
      setError('Failed to generate referral code. Please try again.');
      console.error('Error generating referral code:', err);
    } finally {
      setLoading(false);
    }
  };

  const handleUpdateCommissionStructure = async (e) => {
    e.preventDefault();
    try {
      await affiliateService.updateCommissionStructure(commissionStructure);
      alert('Commission structure updated successfully!');
    } catch (err) {
      setError('Failed to update commission structure. Please try again.');
      console.error('Error updating commission structure:', err);
    }
  };

  const handleCreateTier = async (e) => {
    e.preventDefault();
    try {
      const newStructure = [...commissionStructure, newTier];
      await affiliateService.updateCommissionStructure(newStructure);
      setCommissionStructure(newStructure);
      setNewTier({
        name: '',
        level: commissionStructure.length + 1,
        commission_percentage: 0,
        min_referrals: 0,
        min_commissions: 0
      });
      alert('Tier added successfully!');
    } catch (err) {
      setError('Failed to create tier. Please try again.');
      console.error('Error creating tier:', err);
    }
  };

  const toggleMLMFeature = async (enabled) => {
    try {
      await affiliateService.updateMLMSettings({ enabled });
      setMlmSettings({...mlmSettings, enabled});
      alert(`MLM feature ${enabled ? 'enabled' : 'disabled'} successfully!`);
    } catch (err) {
      setError(`Failed to ${enabled ? 'enable' : 'disable'} MLM feature. Please try again.`);
      console.error(`Error ${enabled ? 'enabling' : 'disabling'} MLM feature:`, err);
    }
  };

  const getStatusColor = (status) => {
    switch (status) {
      case 'approved': return 'bg-green-100 text-green-800';
      case 'pending': return 'bg-yellow-100 text-yellow-800';
      case 'rejected': return 'bg-red-100 text-red-800';
      case 'active': return 'bg-green-100 text-green-800';
      case 'inactive': return 'bg-gray-100 text-gray-800';
      default: return 'bg-gray-100 text-gray-800';
    }
  };

  const renderDashboard = () => (
    <div className="space-y-6">
      {affiliateData ? (
        <>
          {/* Stats Overview */}
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div className="bg-blue-50 rounded-lg p-6">
              <h3 className="text-lg font-medium text-blue-900 mb-2">Total Earnings</h3>
              <p className="text-3xl font-bold text-blue-600">${affiliateData.total_earnings?.toFixed(2) || '0.00'}</p>
              <p className="text-sm text-blue-700">All time</p>
            </div>
            <div className="bg-green-50 rounded-lg p-6">
              <h3 className="text-lg font-medium text-green-900 mb-2">Available Balance</h3>
              <p className="text-3xl font-bold text-green-600">${affiliateData.available_balance?.toFixed(2) || '0.00'}</p>
              <p className="text-sm text-green-700">Ready to withdraw</p>
            </div>
            <div className="bg-purple-50 rounded-lg p-6">
              <h3 className="text-lg font-medium text-purple-900 mb-2">Total Referrals</h3>
              <p className="text-3xl font-bold text-purple-600">{affiliateData.total_referrals || 0}</p>
              <p className="text-sm text-purple-700">Unique signups</p>
            </div>
            <div className="bg-yellow-50 rounded-lg p-6">
              <h3 className="text-lg font-medium text-yellow-900 mb-2">Conversion Rate</h3>
              <p className="text-3xl font-bold text-yellow-600">{affiliateData.conversion_rate ? `${affiliateData.conversion_rate}%` : '0%'}</p>
              <p className="text-sm text-yellow-700">From referrals</p>
            </div>
          </div>

          {/* Referral Code Section */}
          <div className="bg-white rounded-lg shadow-md p-6">
            <div className="flex flex-col md:flex-row justify-between items-start md:items-center mb-4">
              <h2 className="text-xl font-semibold text-gray-900">Your Referral Code</h2>
              <button
                onClick={handleGenerateReferralCode}
                className="mt-2 md:mt-0 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md"
              >
                Generate New Code
              </button>
            </div>
            <div className="p-4 bg-gray-100 rounded-md">
              <div className="flex items-center justify-between">
                <code className="text-lg font-mono">{affiliateData.referral_code}</code>
                <button
                  onClick={() => navigator.clipboard.writeText(affiliateData.referral_code)}
                  className="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm"
                >
                  Copy
                </button>
              </div>
              <p className="mt-2 text-sm text-gray-600">Share this code to earn commissions</p>
            </div>
          </div>

          {/* Performance Chart */}
          {affiliateData.performance_chart_data && (
            <div className="bg-white rounded-lg shadow-md p-6">
              <h2 className="text-xl font-semibold text-gray-900 mb-4">Performance Over Time</h2>
              <div className="h-80">
                <ResponsiveContainer width="100%" height="100%">
                  <AreaChart
                    data={affiliateData.performance_chart_data}
                    margin={{ top: 10, right: 30, left: 0, bottom: 0 }}
                  >
                    <defs>
                      <linearGradient id="colorEarnings" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="5%" stopColor="#8884d8" stopOpacity={0.8} />
                        <stop offset="95%" stopColor="#8884d8" stopOpacity={0} />
                      </linearGradient>
                    </defs>
                    <XAxis dataKey="date" />
                    <YAxis />
                    <CartesianGrid strokeDasharray="3 3" />
                    <Tooltip />
                    <Area type="monotone" dataKey="earnings" stroke="#8884d8" fillOpacity={1} fill="url(#colorEarnings)" name="Earnings" />
                  </AreaChart>
                </ResponsiveContainer>
              </div>
            </div>
          )}

          {/* Quick Actions */}
          <div className="bg-white rounded-lg shadow-md p-6">
            <h2 className="text-xl font-semibold text-gray-900 mb-4">Quick Actions</h2>
            <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
              <button className="bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 rounded-md">
                Share Referral Link
              </button>
              <button className="bg-green-600 hover:bg-green-700 text-white py-3 px-4 rounded-md">
                Download Marketing Materials
              </button>
              <button 
                onClick={handleRequestPayout}
                className="bg-purple-600 hover:bg-purple-700 text-white py-3 px-4 rounded-md"
              >
                Request Payout
              </button>
            </div>
          </div>
        </>
      ) : (
        <div className="bg-white rounded-lg shadow-md p-12 text-center">
          <h2 className="text-2xl font-semibold text-gray-900 mb-4">Join the Affiliate Program</h2>
          <p className="text-gray-600 mb-6">Earn commissions by referring new users to our platform</p>
          <button
            onClick={handleApplyForAffiliate}
            className="bg-blue-600 hover:bg-blue-700 text-white py-3 px-6 rounded-md text-lg"
          >
            Apply Now
          </button>
        </div>
      )}
    </div>
  );

  const renderReferrals = () => (
    <div className="bg-white rounded-lg shadow-md p-6">
      <h2 className="text-xl font-semibold text-gray-900 mb-4">Your Referrals</h2>
      
      <div className="overflow-x-auto">
        <table className="min-w-full divide-y divide-gray-200">
          <thead className="bg-gray-50">
            <tr>
              <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                User
              </th>
              <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Sign Up Date
              </th>
              <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Status
              </th>
              <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Total Value
              </th>
              <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Commission Earned
              </th>
            </tr>
          </thead>
          <tbody className="bg-white divide-y divide-gray-200">
            {referrals.map((referral) => (
              <tr key={referral.id}>
                <td className="px-6 py-4 whitespace-nowrap">
                  <div className="text-sm font-medium text-gray-900">{referral.user_name}</div>
                  <div className="text-sm text-gray-500">{referral.email}</div>
                </td>
                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {new Date(referral.sign_up_date).toLocaleDateString()}
                </td>
                <td className="px-6 py-4 whitespace-nowrap">
                  <span className={`px-2 py-1 rounded-full text-xs font-medium ${getStatusColor(referral.status)}`}>
                    {referral.status}
                  </span>
                </td>
                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  ${referral.total_value?.toFixed(2) || '0.00'}
                </td>
                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  ${referral.commission_earned?.toFixed(2) || '0.00'}
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );

  const renderCommissions = () => (
    <div className="space-y-6">
      <div className="bg-white rounded-lg shadow-md p-6">
        <h2 className="text-xl font-semibold text-gray-900 mb-4">Commission History</h2>
        
        <div className="overflow-x-auto">
          <table className="min-w-full divide-y divide-gray-200">
            <thead className="bg-gray-50">
              <tr>
                <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Date
                </th>
                <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Type
                </th>
                <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Description
                </th>
                <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Amount
                </th>
                <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Status
                </th>
              </tr>
            </thead>
            <tbody className="bg-white divide-y divide-gray-200">
              {commissions.map((commission) => (
                <tr key={commission.id}>
                  <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {new Date(commission.date).toLocaleDateString()}
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {commission.type}
                  </td>
                  <td className="px-6 py-4 text-sm text-gray-900">
                    {commission.description}
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    ${commission.amount?.toFixed(2)}
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap">
                    <span className={`px-2 py-1 rounded-full text-xs font-medium ${getStatusColor(commission.status)}`}>
                      {commission.status}
                    </span>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>

      {/* Commission Chart */}
      {commissions.length > 0 && (
        <div className="bg-white rounded-lg shadow-md p-6">
          <h2 className="text-xl font-semibold text-gray-900 mb-4">Commission Trends</h2>
          <div className="h-80">
            <ResponsiveContainer width="100%" height="100%">
              <LineChart
                data={commissions.map(c => ({ date: c.date, amount: c.amount }))}
                margin={{ top: 5, right: 30, left: 20, bottom: 5 }}
              >
                <CartesianGrid strokeDasharray="3 3" />
                <XAxis dataKey="date" />
                <YAxis />
                <Tooltip />
                <Legend />
                <Line type="monotone" dataKey="amount" stroke="#8884d8" activeDot={{ r: 8 }} name="Commission Amount" />
              </LineChart>
            </ResponsiveContainer>
          </div>
        </div>
      )}
    </div>
  );

  const renderPayouts = () => (
    <div className="space-y-6">
      <div className="bg-white rounded-lg shadow-md p-6">
        <div className="flex justify-between items-center mb-4">
          <h2 className="text-xl font-semibold text-gray-900">Payout History</h2>
          <button
            onClick={handleRequestPayout}
            className="bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-md"
          >
            Request Payout
          </button>
        </div>
        
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
                  Method
                </th>
                <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Status
                </th>
                <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Transaction ID
                </th>
              </tr>
            </thead>
            <tbody className="bg-white divide-y divide-gray-200">
              {payouts.map((payout) => (
                <tr key={payout.id}>
                  <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {new Date(payout.date).toLocaleDateString()}
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    ${payout.amount?.toFixed(2)}
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {payout.method}
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap">
                    <span className={`px-2 py-1 rounded-full text-xs font-medium ${getStatusColor(payout.status)}`}>
                      {payout.status}
                    </span>
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {payout.transaction_id || 'N/A'}
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>

      <div className="bg-white rounded-lg shadow-md p-6">
        <h2 className="text-xl font-semibold text-gray-900 mb-4">Payout Information</h2>
        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <h3 className="font-medium text-gray-900 mb-2">Available Balance</h3>
            <p className="text-2xl font-bold text-green-600">${affiliateData?.available_balance?.toFixed(2) || '0.00'}</p>
          </div>
          <div>
            <h3 className="font-medium text-gray-900 mb-2">Minimum Withdrawal</h3>
            <p className="text-lg font-medium text-gray-900">$50.00</p>
          </div>
          <div>
            <h3 className="font-medium text-gray-900 mb-2">Payment Methods</h3>
            <ul className="text-gray-700 space-y-1">
              <li>PayPal</li>
              <li>Bank Transfer</li>
              <li>Debit Card</li>
            </ul>
          </div>
          <div>
            <h3 className="font-medium text-gray-900 mb-2">Withdrawal Processing</h3>
            <p className="text-gray-700">2-5 business days</p>
          </div>
        </div>
      </div>
    </div>
  );

  const renderDownline = () => (
    <div className="bg-white rounded-lg shadow-md p-6">
      <h2 className="text-xl font-semibold text-gray-900 mb-4">Your Downline</h2>
      
      <div className="overflow-x-auto">
        <table className="min-w-full divide-y divide-gray-200">
          <thead className="bg-gray-50">
            <tr>
              <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                User
              </th>
              <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Join Date
              </th>
              <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Level
              </th>
              <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Direct Referrals
              </th>
              <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Total Commission
              </th>
            </tr>
          </thead>
          <tbody className="bg-white divide-y divide-gray-200">
            {downline.map((member) => (
              <tr key={member.id}>
                <td className="px-6 py-4 whitespace-nowrap">
                  <div className="text-sm font-medium text-gray-900">{member.name}</div>
                  <div className="text-sm text-gray-500">{member.email}</div>
                </td>
                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {new Date(member.join_date).toLocaleDateString()}
                </td>
                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  Level {member.level}
                </td>
                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {member.direct_referrals || 0}
                </td>
                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  ${member.commission_earned?.toFixed(2) || '0.00'}
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );

  const renderMLMSettings = () => (
    <div className="space-y-6">
      <div className="bg-white rounded-lg shadow-md p-6">
        <div className="flex justify-between items-center mb-4">
          <h2 className="text-xl font-semibold text-gray-900">MLM Settings</h2>
          <button
            onClick={() => toggleMLMFeature(!mlmSettings?.enabled)}
            className={`px-4 py-2 rounded-md text-white ${
              mlmSettings?.enabled ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700'
            }`}
          >
            {mlmSettings?.enabled ? 'Disable MLM' : 'Enable MLM'}
          </button>
        </div>
        
        <div className="mb-6">
          <h3 className="text-lg font-medium text-gray-900 mb-2">Feature Status</h3>
          <p className={`text-lg font-medium ${mlmSettings?.enabled ? 'text-green-600' : 'text-red-600'}`}>
            {mlmSettings?.enabled ? 'ENABLED' : 'DISABLED'}
          </p>
          <p className="text-gray-600 mt-2">
            When enabled, you'll receive commissions from your direct referrals and downline generations.
          </p>
        </div>

        <div className="border-t border-gray-200 pt-6">
          <h3 className="text-lg font-medium text-gray-900 mb-4">Commission Structure</h3>
          
          <form onSubmit={handleUpdateCommissionStructure}>
            <div className="space-y-4 mb-6">
              {commissionStructure.map((tier, index) => (
                <div key={index} className="flex items-center space-x-4 p-4 border rounded-md">
                  <div className="flex-1">
                    <label className="block text-sm font-medium text-gray-700 mb-1">Tier Name</label>
                    <input
                      type="text"
                      value={tier.name}
                      onChange={(e) => {
                        const updated = [...commissionStructure];
                        updated[index].name = e.target.value;
                        setCommissionStructure(updated);
                      }}
                      className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                  </div>
                  <div className="w-32">
                    <label className="block text-sm font-medium text-gray-700 mb-1">Level</label>
                    <input
                      type="number"
                      value={tier.level}
                      onChange={(e) => {
                        const updated = [...commissionStructure];
                        updated[index].level = parseInt(e.target.value);
                        setCommissionStructure(updated);
                      }}
                      className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                  </div>
                  <div className="w-40">
                    <label className="block text-sm font-medium text-gray-700 mb-1">Commission %</label>
                    <input
                      type="number"
                      step="0.1"
                      value={tier.commission_percentage}
                      onChange={(e) => {
                        const updated = [...commissionStructure];
                        updated[index].commission_percentage = parseFloat(e.target.value);
                        setCommissionStructure(updated);
                      }}
                      className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                  </div>
                  <button
                    type="button"
                    onClick={() => {
                      const updated = commissionStructure.filter((_, i) => i !== index);
                      setCommissionStructure(updated);
                    }}
                    className="text-red-600 hover:text-red-900 mt-6"
                  >
                    Remove
                  </button>
                </div>
              ))}
            </div>
            
            <div className="flex space-x-4">
              <button
                type="submit"
                className="bg-blue-600 hover:bg-blue-700 text-white py-2 px-6 rounded-md"
              >
                Save Structure
              </button>
            </div>
          </form>

          <div className="mt-8">
            <h4 className="text-md font-medium text-gray-900 mb-4">Add New Tier</h4>
            <form onSubmit={handleCreateTier} className="flex flex-wrap gap-4">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Tier Name</label>
                <input
                  type="text"
                  value={newTier.name}
                  onChange={(e) => setNewTier({...newTier, name: e.target.value})}
                  className="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  required
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Level</label>
                <input
                  type="number"
                  value={newTier.level}
                  onChange={(e) => setNewTier({...newTier, level: parseInt(e.target.value)})}
                  className="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  required
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Commission %</label>
                <input
                  type="number"
                  step="0.1"
                  value={newTier.commission_percentage}
                  onChange={(e) => setNewTier({...newTier, commission_percentage: parseFloat(e.target.value)})}
                  className="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  required
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Min Referrals</label>
                <input
                  type="number"
                  value={newTier.min_referrals}
                  onChange={(e) => setNewTier({...newTier, min_referrals: parseInt(e.target.value)})}
                  className="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Min Commissions ($)</label>
                <input
                  type="number"
                  step="0.01"
                  value={newTier.min_commissions}
                  onChange={(e) => setNewTier({...newTier, min_commissions: parseFloat(e.target.value)})}
                  className="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
              </div>
              <button
                type="submit"
                className="self-end bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-md"
              >
                Add Tier
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  );

  if (loading && !affiliateData && !referrals.length && !commissions.length) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <div className="animate-spin rounded-full h-32 w-32 border-b-2 border-blue-600"></div>
      </div>
    );
  }

  return (
    <div className="max-w-7xl mx-auto p-6">
      <div className="mb-8">
        <h1 className="text-3xl font-bold text-gray-900">Affiliate & MLM Dashboard</h1>
        <p className="text-gray-600">Manage your affiliate program and multi-level marketing commissions</p>
      </div>

      {error && (
        <div className="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
          <strong className="font-bold">Error: </strong>
          <span className="block sm:inline">{error}</span>
        </div>
      )}

      {/* Tab Navigation */}
      <div className="border-b border-gray-200 mb-6">
        <nav className="-mb-px flex space-x-8">
          <button
            onClick={() => setActiveTab('dashboard')}
            className={`whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm ${
              activeTab === 'dashboard'
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            }`}
          >
            Dashboard
          </button>
          <button
            onClick={() => setActiveTab('referrals')}
            className={`whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm ${
              activeTab === 'referrals'
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            }`}
          >
            Referrals
          </button>
          <button
            onClick={() => setActiveTab('commissions')}
            className={`whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm ${
              activeTab === 'commissions'
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            }`}
          >
            Commissions
          </button>
          <button
            onClick={() => setActiveTab('payouts')}
            className={`whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm ${
              activeTab === 'payouts'
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            }`}
          >
            Payouts
          </button>
          <button
            onClick={() => setActiveTab('downline')}
            className={`whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm ${
              activeTab === 'downline'
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            }`}
          >
            My Downline
          </button>
          <button
            onClick={() => setActiveTab('mlm')}
            className={`whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm ${
              activeTab === 'mlm'
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            }`}
          >
            MLM Settings
          </button>
        </nav>
      </div>

      {/* Tab Content */}
      {activeTab === 'dashboard' && renderDashboard()}
      {activeTab === 'referrals' && renderReferrals()}
      {activeTab === 'commissions' && renderCommissions()}
      {activeTab === 'payouts' && renderPayouts()}
      {activeTab === 'downline' && renderDownline()}
      {activeTab === 'mlm' && subTab === 'settings' && renderMLMSettings()}
    </div>
  );
};

export default withAuth(AffiliateMLMDashboard, ['user', 'affiliate', 'admin']);