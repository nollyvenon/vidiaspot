import React, { useState, useEffect } from 'react';

const OrderManagement = () => {
  const [orders, setOrders] = useState([]);
  const [filteredOrders, setFilteredOrders] = useState([]);
  const [selectedStatus, setSelectedStatus] = useState('all');
  const [loading, setLoading] = useState(true);
  const [searchTerm, setSearchTerm] = useState('');

  useEffect(() => {
    loadOrders();
  }, []);

  useEffect(() => {
    filterOrders();
  }, [orders, selectedStatus, searchTerm]);

  const loadOrders = () => {
    // Simulate API call to fetch orders
    setTimeout(() => {
      setOrders([
        {
          id: 'ord-1',
          orderId: 'VID-FTM-2023-001',
          customerId: 'cust-1',
          customerName: 'John Doe',
          customerEmail: 'john@example.com',
          customerPhone: '+1234567890',
          status: 'pending',
          items: [
            {
              id: 'item-1',
              productId: 'prod-1',
              productName: 'Fresh Tomatoes',
              quantity: 2,
              unitPrice: 2.99,
              total: 5.98,
              specialInstructions: '',
              addons: [],
            },
            {
              id: 'item-2', 
              productId: 'prod-2',
              productName: 'Organic Lettuce',
              quantity: 1,
              unitPrice: 1.99,
              total: 1.99,
              specialInstructions: 'Please wash before packaging',
              addons: [],
            }
          ],
          subtotal: 7.97,
          tax: 0.40,
          deliveryFee: 3.00,
          tipAmount: 1.00,
          totalAmount: 12.37,
          currency: 'USD',
          orderDate: new Date('2023-11-18T10:30:00'),
          estimatedDeliveryTime: new Date('2023-11-18T12:00:00'),
          deliveryAddress: '123 Main Street, City, State 12345',
          deliveryInstructions: 'Gate code 1234',
          paymentMethod: 'Credit Card',
          paymentStatus: 'paid',
          deliveryPersonId: 'del-1',
          deliveryPersonName: 'Mike Johnson',
          deliveryPersonPhone: '+19876543210',
          specialRequests: 'Handle with care',
          contactlessDelivery: true,
          farmId: 'farm-1',
        },
        {
          id: 'ord-2',
          orderId: 'VID-FTM-2023-002',
          customerId: 'cust-2',
          customerName: 'Jane Smith',
          customerEmail: 'jane@example.com',
          customerPhone: '+13245678902',
          status: 'confirmed',
          items: [
            {
              id: 'item-3',
              productId: 'prod-3',
              productName: 'Farm Fresh Eggs',
              quantity: 1,
              unitPrice: 4.99,
              total: 4.99,
              specialInstructions: '',
              addons: [],
            }
          ],
          subtotal: 4.99,
          tax: 0.25,
          deliveryFee: 3.00,
          tipAmount: 0.00,
          totalAmount: 8.24,
          currency: 'USD',
          orderDate: new Date('2023-11-18T11:15:00'),
          estimatedDeliveryTime: new Date('2023-11-18T13:30:00'),
          deliveryAddress: '456 Oak Avenue, City, State 12345',
          deliveryInstructions: 'Leave at door',
          paymentMethod: 'Cash',
          paymentStatus: 'pending',
          deliveryPersonId: 'del-2',
          deliveryPersonName: 'Sarah Williams',
          deliveryPersonPhone: '+18765432109',
          specialRequests: '',
          contactlessDelivery: false,
          farmId: 'farm-1',
        },
        {
          id: 'ord-3',
          orderId: 'VID-FTM-2023-003',
          customerId: 'cust-3',
          customerName: 'Bob Johnson',
          customerEmail: 'bob@example.com',
          customerPhone: '+14325678903',
          status: 'delivered',
          items: [
            {
              id: 'item-1',
              productId: 'prod-1',
              productName: 'Fresh Tomatoes',
              quantity: 3,
              unitPrice: 2.99,
              total: 8.97,
              specialInstructions: '',
              addons: [],
            },
            {
              id: 'item-4',
              productId: 'prod-3',
              productName: 'Organic Lettuce',
              quantity: 2,
              unitPrice: 1.99,
              total: 3.98,
              specialInstructions: 'Extra fresh if possible',
              addons: [],
            }
          ],
          subtotal: 12.95,
          tax: 0.65,
          deliveryFee: 3.00,
          tipAmount: 2.00,
          totalAmount: 18.60,
          currency: 'USD',
          orderDate: new Date('2023-11-17T09:45:00'),
          estimatedDeliveryTime: new Date('2023-11-17T11:15:00'),
          deliveryAddress: '789 Pine Street, City, State 12345',
          deliveryInstructions: 'Ring bell twice',
          paymentMethod: 'Mobile Money',
          paymentStatus: 'paid',
          deliveryPersonId: 'del-1',
          deliveryPersonName: 'Mike Johnson',
          deliveryPersonPhone: '+19876543210',
          specialRequests: 'Include receipt',
          contactlessDelivery: true,
          farmId: 'farm-1',
        }
      ]);
      setLoading(false);
    }, 1000);
  };

  const filterOrders = () => {
    let result = orders;

    // Filter by status
    if (selectedStatus !== 'all') {
      result = result.filter(order => order.status === selectedStatus);
    }

    // Filter by search term
    if (searchTerm) {
      result = result.filter(order => 
        order.customerName.toLowerCase().includes(searchTerm.toLowerCase()) ||
        order.orderId.toLowerCase().includes(searchTerm.toLowerCase()) ||
        order.items.some(item => 
          item.productName.toLowerCase().includes(searchTerm.toLowerCase())
        )
      );
    }

    setFilteredOrders(result);
  };

  const updateOrderStatus = (orderId, newStatus) => {
    setOrders(prevOrders => 
      prevOrders.map(order => 
        order.id === orderId 
          ? { ...order, status: newStatus, updatedAt: new Date() } 
          : order
      )
    );
  };

  const getStatusColor = (status) => {
    switch (status) {
      case 'pending': return 'orange';
      case 'confirmed': return 'blue';
      case 'preparing': return 'yellow';
      case 'out_for_delivery': return 'purple';
      case 'delivered': return 'green';
      case 'cancelled': return 'red';
      case 'refunded': return 'gray';
      default: return 'gray';
    }
  };

  const getStatusString = (status) => {
    switch (status) {
      case 'pending': return 'Pending';
      case 'confirmed': return 'Confirmed';
      case 'preparing': return 'Preparing';
      case 'out_for_delivery': return 'Out for Delivery';
      case 'delivered': return 'Delivered';
      case 'cancelled': return 'Cancelled';
      case 'refunded': return 'Refunded';
      default: return status;
    }
  };

  if (loading) {
    return (
      <div className="loading-container">
        <div className="spinner"></div>
        <p>Loading orders...</p>
      </div>
    );
  }

  return (
    <div className="order-management">
      <div className="header">
        <h2>Order Management</h2>
      </div>

      <div className="controls">
        <div className="filters">
          <select 
            value={selectedStatus} 
            onChange={(e) => setSelectedStatus(e.target.value)}
            className="filter-select"
          >
            <option value="all">All Orders</option>
            <option value="pending">Pending</option>
            <option value="confirmed">Confirmed</option>
            <option value="preparing">Preparing</option>
            <option value="out_for_delivery">Out for Delivery</option>
            <option value="delivered">Delivered</option>
            <option value="cancelled">Cancelled</option>
          </select>
        </div>
        
        <div className="search-box">
          <input
            type="text"
            placeholder="Search orders..."
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
            className="search-input"
          />
        </div>
      </div>

      <div className="order-stats">
        <div className="stat-card">
          <h3>Total Orders</h3>
          <p>{orders.length}</p>
        </div>
        <div className="stat-card">
          <h3>Pending Orders</h3>
          <p>{orders.filter(o => o.status === 'pending').length}</p>
        </div>
        <div className="stat-card">
          <h3>Today's Revenue</h3>
          <p>${orders.filter(o => o.status === 'delivered' && 
                 new Date(o.orderDate).toDateString() === new Date().toDateString())
                 .reduce((sum, order) => sum + order.totalAmount, 0).toFixed(2)}</p>
        </div>
      </div>

      <div className="orders-list">
        {filteredOrders.map((order) => (
          <div key={order.id} className="order-card">
            <div className="order-header">
              <div>
                <h3>Order #{order.orderId}</h3>
                <p>Customer: {order.customerName}</p>
              </div>
              <div className="status-badge" style={{ backgroundColor: `${getStatusColor(order.status)}20`, color: getStatusColor(order.status) }}>
                {getStatusString(order.status)}
              </div>
            </div>

            <div className="order-details">
              <div className="customer-info">
                <h4>Customer Information</h4>
                <p><strong>Email:</strong> {order.customerEmail}</p>
                <p><strong>Phone:</strong> {order.customerPhone}</p>
                <p><strong>Address:</strong> {order.deliveryAddress}</p>
                <p><strong>Delivery Instructions:</strong> {order.deliveryInstructions}</p>
              </div>

              <div className="order-summary">
                <h4>Order Summary</h4>
                <div className="items-list">
                  {order.items.map((item) => (
                    <div key={item.id} className="item-row">
                      <span>{item.quantity}x {item.productName}</span>
                      <span>${item.total.toFixed(2)}</span>
                    </div>
                  ))}
                </div>
                
                <div className="order-totals">
                  <div className="total-row">
                    <span>Subtotal:</span>
                    <span>${order.subtotal.toFixed(2)}</span>
                  </div>
                  <div className="total-row">
                    <span>Tax:</span>
                    <span>${order.tax.toFixed(2)}</span>
                  </div>
                  <div className="total-row">
                    <span>Delivery Fee:</span>
                    <span>${order.deliveryFee.toFixed(2)}</span>
                  </div>
                  <div className="total-row">
                    <span>Tip:</span>
                    <span>${order.tipAmount.toFixed(2)}</span>
                  </div>
                  <div className="total-row total">
                    <span>Total:</span>
                    <span>${order.totalAmount.toFixed(2)}</span>
                  </div>
                </div>
              </div>
            </div>

            <div className="order-actions">
              <div className="action-buttons">
                {order.status === 'pending' && (
                  <>
                    <button 
                      className="btn btn-success"
                      onClick={() => updateOrderStatus(order.id, 'confirmed')}
                    >
                      Confirm Order
                    </button>
                    <button 
                      className="btn btn-danger"
                      onClick={() => updateOrderStatus(order.id, 'cancelled')}
                    >
                      Cancel Order
                    </button>
                  </>
                )}
                
                {order.status === 'confirmed' && (
                  <button 
                    className="btn btn-warning"
                    onClick={() => updateOrderStatus(order.id, 'preparing')}
                  >
                    Start Preparing
                  </button>
                )}
                
                {order.status === 'preparing' && (
                  <button 
                    className="btn btn-primary"
                    onClick={() => updateOrderStatus(order.id, 'out_for_delivery')}
                  >
                    Mark as Out for Delivery
                  </button>
                )}
                
                {order.status === 'out_for_delivery' && (
                  <button 
                    className="btn btn-success"
                    onClick={() => updateOrderStatus(order.id, 'delivered')}
                  >
                    Mark as Delivered
                  </button>
                )}
                
                {(order.status === 'delivered' || order.status === 'cancelled') && (
                  <span className="status-message">
                    {order.status === 'delivered' 
                      ? 'Order Completed' 
                      : 'Order Cancelled'}
                  </span>
                )}
              </div>
              
              <div className="additional-info">
                <p><strong>Order Date:</strong> {new Date(order.orderDate).toLocaleString()}</p>
                <p><strong>Estimated Delivery:</strong> {new Date(order.estimatedDeliveryTime).toLocaleString()}</p>
                <p><strong>Payment:</strong> {order.paymentMethod} - {order.paymentStatus}</p>
                <p><strong>Special Requests:</strong> {order.specialRequests}</p>
                <p><strong>Contactless:</strong> {order.contactlessDelivery ? 'Yes' : 'No'}</p>
              </div>
            </div>
          </div>
        ))}
      </div>

      {filteredOrders.length === 0 && (
        <div className="empty-state">
          <div className="empty-icon">📦</div>
          <h3>No Orders Found</h3>
          <p>{searchTerm ? 'No orders match your search criteria' : 'No orders available'}</p>
        </div>
      )}
    </div>
  );
};

export default OrderManagement;