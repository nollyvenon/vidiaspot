import React, { useState, useEffect } from 'react';
import { withAuth } from '../utils/withAuth';

const CryptoPayments = () => {
  const [activeTab, setActiveTab] = useState('wallet');
  const [wallets, setWallets] = useState([]);
  const [transactions, setTransactions] = useState([]);
  const [loading, setLoading] = useState(true);
  const [newTransaction, setNewTransaction] = useState({
    to: '',
    amount: '',
    currency: 'BTC'
  });

  // Mock data for crypto payments
  useEffect(() => {
    // In a real app, this would fetch from the API
    setTimeout(() => {
      const mockWallets = [
        { id: 1, name: 'Bitcoin Wallet', currency: 'BTC', balance: 0.456789, value: 32456.78, address: '1A1zP1eP5QGefi2DMPTfTL5SLmv7DivfNa' },
        { id: 2, name: 'Ethereum Wallet', currency: 'ETH', balance: 12.345678, value: 36789.12, address: '0x742d35Cc6634C0532925a3b844Bc454e4438f44e' },
        { id: 3, name: 'Litecoin Wallet', currency: 'LTC', balance: 56.789012, value: 4567.89, address: 'LcbqgYJ8a1XH3b6b6X7gX5zX8zX5zX8zX5zX8zX5zX8' },
      ];
      
      const mockTransactions = [
        { id: 1, type: 'received', amount: 0.123456, currency: 'BTC', from: '1A1zP1eP5QGefi2DMPTfTL5SLmv7DivfNa', date: '2023-06-15', status: 'confirmed' },
        { id: 2, type: 'sent', amount: 2.5, currency: 'ETH', to: '0x742d35Cc6634C0532925a3b844Bc454e4438f44e', date: '2023-06-14', status: 'confirmed' },
        { id: 3, type: 'received', amount: 5.6789, currency: 'LTC', from: 'LcbqgYJ8a1XH3b6b6X7gX5zX8zX5zX8zX5zX8zX5zX8', date: '2023-06-13', status: 'confirmed' },
        { id: 4, type: 'sent', amount: 0.05, currency: 'BTC', to: '1BvBMSEYstWetqTFn5Au4m4GFg7xJaNVN2', date: '2023-06-12', status: 'pending' },
      ];
      
      setWallets(mockWallets);
      setTransactions(mockTransactions);
      setLoading(false);
    }, 1000);
  }, []);

  const handleSendTransaction = (e) => {
    e.preventDefault();
    // In a real app, this would make an API call to send cryptocurrency
    const transaction = {
      id: transactions.length + 1,
      type: 'sent',
      ...newTransaction,
      date: new Date().toISOString().split('T')[0],
      status: 'pending'
    };
    setTransactions([transaction, ...transactions]);
    setNewTransaction({ to: '', amount: '', currency: 'BTC' });
  };

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <div className="animate-spin rounded-full h-32 w-32 border-b-2 border-blue-600"></div>
      </div>
    );
  }

  return (
    <div className="max-w-7xl mx-auto p-6">
      <div className="mb-8">
        <h1 className="text-3xl font-bold text-gray-900">Crypto Payments</h1>
        <p className="text-gray-600 mt-2">Manage your cryptocurrency wallets and transactions</p>
      </div>

      <div className="border-b border-gray-200 mb-6">
        <nav className="-mb-px flex space-x-8">
          <button
            onClick={() => setActiveTab('wallet')}
            className={`whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm ${
              activeTab === 'wallet'
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            }`}
          >
            Wallet
          </button>
          <button
            onClick={() => setActiveTab('transactions')}
            className={`whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm ${
              activeTab === 'transactions'
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            }`}
          >
            Transactions
          </button>
          <button
            onClick={() => setActiveTab('send')}
            className={`whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm ${
              activeTab === 'send'
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            }`}
          >
            Send
          </button>
          <button
            onClick={() => setActiveTab('receive')}
            className={`whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm ${
              activeTab === 'receive'
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            }`}
          >
            Receive
          </button>
        </nav>
      </div>

      {activeTab === 'wallet' && (
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {wallets.map(wallet => (
            <div key={wallet.id} className="bg-white rounded-lg shadow-md p-6">
              <div className="flex justify-between items-start mb-4">
                <div>
                  <h3 className="text-lg font-semibold text-gray-900">{wallet.name}</h3>
                  <p className="text-sm text-gray-500">{wallet.currency}</p>
                </div>
                <div className="text-right">
                  <p className="text-2xl font-bold text-gray-900">{wallet.balance}</p>
                  <p className="text-sm text-gray-500">${wallet.value.toLocaleString()}</p>
                </div>
              </div>
              
              <div className="mb-4">
                <p className="text-xs text-gray-500 mb-1">Address:</p>
                <p className="text-sm text-gray-700 break-all">{wallet.address}</p>
              </div>
              
              <div className="flex space-x-2">
                <button className="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                  Send
                </button>
                <button className="flex-1 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                  Receive
                </button>
              </div>
            </div>
          ))}
          
          <div className="bg-white rounded-lg shadow-md p-6 flex items-center justify-center border-2 border-dashed border-gray-300">
            <button className="text-blue-600 hover:text-blue-800 text-lg font-medium">
              + Add New Wallet
            </button>
          </div>
        </div>
      )}

      {activeTab === 'transactions' && (
        <div className="bg-white rounded-lg shadow-md overflow-hidden">
          <div className="px-6 py-4 border-b border-gray-200">
            <h2 className="text-lg font-medium text-gray-900">Transaction History</h2>
          </div>
          <div className="overflow-x-auto">
            <table className="min-w-full divide-y divide-gray-200">
              <thead className="bg-gray-50">
                <tr>
                  <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Type
                  </th>
                  <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Amount
                  </th>
                  <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Currency
                  </th>
                  <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    From/To
                  </th>
                  <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Date
                  </th>
                  <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Status
                  </th>
                </tr>
              </thead>
              <tbody className="bg-white divide-y divide-gray-200">
                {transactions.map(transaction => (
                  <tr key={transaction.id}>
                    <td className="px-6 py-4 whitespace-nowrap">
                      <span className={`px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${
                        transaction.type === 'received' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'
                      }`}>
                        {transaction.type.toUpperCase()}
                      </span>
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                      {transaction.type === 'received' ? '+' : '-'}{transaction.amount}
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {transaction.currency}
                    </td>
                    <td className="px-6 py-4 text-sm text-gray-500">
                      {transaction.type === 'received' ? transaction.from : transaction.to}
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {transaction.date}
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap">
                      <span className={`px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${
                        transaction.status === 'confirmed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'
                      }`}>
                        {transaction.status.toUpperCase()}
                      </span>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      )}

      {activeTab === 'send' && (
        <div className="bg-white rounded-lg shadow-md p-6 max-w-2xl mx-auto">
          <h2 className="text-xl font-semibold text-gray-900 mb-6">Send Cryptocurrency</h2>
          
          <form onSubmit={handleSendTransaction}>
            <div className="grid grid-cols-1 gap-6">
              <div>
                <label htmlFor="to" className="block text-sm font-medium text-gray-700 mb-1">To Address</label>
                <input
                  type="text"
                  id="to"
                  value={newTransaction.to}
                  onChange={(e) => setNewTransaction({...newTransaction, to: e.target.value})}
                  className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="Enter recipient address"
                  required
                />
              </div>
              
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label htmlFor="amount" className="block text-sm font-medium text-gray-700 mb-1">Amount</label>
                  <input
                    type="number"
                    id="amount"
                    value={newTransaction.amount}
                    onChange={(e) => setNewTransaction({...newTransaction, amount: e.target.value})}
                    className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="0.00"
                    step="any"
                    required
                  />
                </div>
                
                <div>
                  <label htmlFor="currency" className="block text-sm font-medium text-gray-700 mb-1">Currency</label>
                  <select
                    id="currency"
                    value={newTransaction.currency}
                    onChange={(e) => setNewTransaction({...newTransaction, currency: e.target.value})}
                    className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  >
                    <option value="BTC">Bitcoin (BTC)</option>
                    <option value="ETH">Ethereum (ETH)</option>
                    <option value="LTC">Litecoin (LTC)</option>
                    <option value="USDT">Tether (USDT)</option>
                    <option value="USDC">USD Coin (USDC)</option>
                  </select>
                </div>
              </div>
              
              <div className="bg-gray-50 p-4 rounded-md">
                <div className="flex justify-between mb-2">
                  <span className="text-sm text-gray-600">Estimated Network Fee</span>
                  <span className="text-sm font-medium">0.0002 BTC</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-sm text-gray-600">Total</span>
                  <span className="text-sm font-medium">0.1002 BTC</span>
                </div>
              </div>
            </div>
            
            <div className="mt-6">
              <button
                type="submit"
                className="w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-md"
              >
                Send Transaction
              </button>
            </div>
          </form>
        </div>
      )}

      {activeTab === 'receive' && (
        <div className="bg-white rounded-lg shadow-md p-6 max-w-2xl mx-auto">
          <h2 className="text-xl font-semibold text-gray-900 mb-6">Receive Cryptocurrency</h2>
          
          <div className="text-center mb-6">
            <div className="bg-gray-200 border-2 border-dashed rounded-xl w-48 h-48 mx-auto flex items-center justify-center">
              <span className="text-gray-500">QR Code</span>
            </div>
            <p className="mt-4 text-gray-600">Scan this QR code with your wallet app</p>
          </div>
          
          <div className="space-y-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Your Address</label>
              <div className="flex">
                <input
                  type="text"
                  readOnly
                  value="1A1zP1eP5QGefi2DMPTfTL5SLmv7DivfNa"
                  className="flex-1 px-3 py-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
                <button className="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-r-md">
                  Copy
                </button>
              </div>
            </div>
            
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Currency</label>
              <select className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option>Bitcoin (BTC)</option>
                <option>Ethereum (ETH)</option>
                <option>Litecoin (LTC)</option>
                <option>Tether (USDT)</option>
                <option>USD Coin (USDC)</option>
              </select>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};

export default withAuth(CryptoPayments, ['crypto_user', 'admin']);