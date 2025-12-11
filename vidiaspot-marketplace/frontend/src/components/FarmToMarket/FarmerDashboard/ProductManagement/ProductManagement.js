import React, { useState, useEffect } from 'react';
import ProductForm from './ProductForm';

const ProductManagement = () => {
  const [products, setProducts] = useState([]);
  const [loading, setLoading] = useState(true);
  const [showForm, setShowForm] = useState(false);
  const [editingProduct, setEditingProduct] = useState(null);

  useEffect(() => {
    loadProducts();
  }, []);

  const loadProducts = async () => {
    // Simulate API call
    setTimeout(() => {
      setProducts([
        {
          id: 'prod-1',
          name: 'Fresh Tomatoes',
          description: 'Organic, vine-ripened tomatoes',
          category: 'Vegetables',
          subcategory: 'Tomatoes',
          price: 2.99,
          compareAtPrice: 3.49,
          currency: 'USD',
          inventoryQuantity: 50,
          unit: 'kg',
          isAvailable: true,
          isVisible: true,
          images: ['https://example.com/tomatoes.jpg'],
          mainImage: 'https://example.com/tomatoes.jpg',
          sku: 'FT-TOM-001',
          barcode: '1234567890123',
          weight: 1.0,
          dimensions: 'N/A',
          ingredients: 'Tomatoes',
          allergens: [],
          isOrganic: true,
          isFresh: true,
          isSeasonal: false,
          isLocal: true,
          certification: 'Organic Certified',
          productionDate: '2023-11-15',
          expiryDate: '2023-11-22',
          nutritionalInfo: 'Calories: 18, Vitamin C: 23%',
          tags: ['fresh', 'organic', 'healthy'],
          createdAt: new Date(),
          updatedAt: new Date(),
          farmId: 'farm-1',
          viewCount: 120,
          orderCount: 45,
          avgRating: 4.8,
          numRatings: 24,
        },
        {
          id: 'prod-2',
          name: 'Organic Lettuce',
          description: 'Crisp, fresh organic lettuce',
          category: 'Vegetables',
          subcategory: 'Leafy Greens',
          price: 1.99,
          compareAtPrice: 2.49,
          currency: 'USD',
          inventoryQuantity: 30,
          unit: 'kg',
          isAvailable: true,
          isVisible: true,
          images: ['https://example.com/lettuce.jpg'],
          mainImage: 'https://example.com/lettuce.jpg',
          sku: 'OL-LET-002',
          barcode: '1234567890124',
          weight: 0.5,
          dimensions: 'N/A',
          ingredients: 'Lettuce',
          allergens: [],
          isOrganic: true,
          isFresh: true,
          isSeasonal: false,
          isLocal: true,
          certification: 'Organic Certified',
          productionDate: '2023-11-16',
          expiryDate: '2023-11-23',
          nutritionalInfo: 'Calories: 5, Vitamin K: 126%',
          tags: ['fresh', 'organic', 'healthy'],
          createdAt: new Date(),
          updatedAt: new Date(),
          farmId: 'farm-1',
          viewCount: 98,
          orderCount: 32,
          avgRating: 4.7,
          numRatings: 18,
        },
        {
          id: 'prod-3',
          name: 'Farm Fresh Eggs',
          description: 'Free-range chicken eggs',
          category: 'Dairy & Eggs',
          subcategory: 'Eggs',
          price: 4.99,
          compareAtPrice: 5.49,
          currency: 'USD',
          inventoryQuantity: 100,
          unit: 'dozen',
          isAvailable: true,
          isVisible: true,
          images: ['https://example.com/eggs.jpg'],
          mainImage: 'https://example.com/eggs.jpg',
          sku: 'FFE-EGG-003',
          barcode: '1234567890125',
          weight: 0.6,
          dimensions: 'N/A',
          ingredients: 'Eggs',
          allergens: ['Eggs'],
          isOrganic: true,
          isFresh: true,
          isSeasonal: false,
          isLocal: true,
          certification: 'Organic Certified, Free Range',
          productionDate: '2023-11-17',
          expiryDate: '2023-12-15',
          nutritionalInfo: 'Calories: 70/egg, Protein: 6g/egg',
          tags: ['fresh', 'organic', 'free-range'],
          createdAt: new Date(),
          updatedAt: new Date(),
          farmId: 'farm-1',
          viewCount: 156,
          orderCount: 67,
          avgRating: 4.9,
          numRatings: 31,
        },
      ]);
      setLoading(false);
    }, 1000);
  };

  const handleAddProduct = () => {
    setEditingProduct(null);
    setShowForm(true);
  };

  const handleEditProduct = (product) => {
    setEditingProduct(product);
    setShowForm(true);
  };

  const handleDeleteProduct = (productId) => {
    if (window.confirm('Are you sure you want to delete this product?')) {
      setProducts(prev => prev.filter(product => product.id !== productId));
    }
  };

  const handleToggleAvailability = (productId) => {
    setProducts(prev => prev.map(product => 
      product.id === productId 
        ? { ...product, isAvailable: !product.isAvailable } 
        : product
    ));
  };

  const handleToggleVisibility = (productId) => {
    setProducts(prev => prev.map(product => 
      product.id === productId 
        ? { ...product, isVisible: !product.isVisible } 
        : product
    ));
  };

  if (loading) {
    return (
      <div className="loading-container">
        <div className="spinner"></div>
        <p>Loading products...</p>
      </div>
    );
  }

  if (showForm) {
    return (
      <ProductForm
        product={editingProduct}
        onSave={async (productData) => {
          if (editingProduct) {
            // Update existing product
            setProducts(prev => prev.map(p => 
              p.id === productData.id ? { ...productData, updatedAt: new Date() } : p
            ));
          } else {
            // Add new product
            const newProduct = {
              ...productData,
              id: `prod-${Date.now()}`,
              createdAt: new Date(),
              updatedAt: new Date(),
              viewCount: 0,
              orderCount: 0,
              avgRating: 0,
              numRatings: 0,
            };
            setProducts(prev => [newProduct, ...prev]);
          }
          setShowForm(false);
        }}
        onCancel={() => setShowForm(false)}
      />
    );
  }

  return (
    <div className="product-management">
      <div className="header">
        <h2>Product Management</h2>
        <button className="btn btn-primary" onClick={handleAddProduct}>
          Add Product
        </button>
      </div>

      <div className="filter-bar">
        <select className="filter-select">
          <option>All Categories</option>
          <option>Vegetables</option>
          <option>Fruits</option>
          <option>Dairy & Eggs</option>
          <option>Grains</option>
          <option>Livestock</option>
        </select>
        <select className="filter-select">
          <option>All Status</option>
          <option>In Stock</option>
          <option>Out of Stock</option>
          <option>Low Stock</option>
        </select>
        <input 
          type="text" 
          placeholder="Search products..." 
          className="search-input"
        />
      </div>

      <div className="products-grid">
        {products.map((product) => (
          <div key={product.id} className="product-card">
            <div className="product-image">
              {product.mainImage ? (
                <img src={product.mainImage} alt={product.name} />
              ) : (
                <div className="placeholder-image">🌱</div>
              )}
            </div>
            
            <div className="product-details">
              <h3>{product.name}</h3>
              <p className="description">{product.description}</p>
              
              <div className="product-info">
                <div className="price">
                  <span className="current-price">${product.price.toFixed(2)}</span>
                  {product.compareAtPrice > product.price && (
                    <span className="original-price">${product.compareAtPrice.toFixed(2)}</span>
                  )}
                </div>
                
                <div className="inventory">
                  <span className={`stock-status ${product.inventoryQuantity > 10 ? 'good' : product.inventoryQuantity > 0 ? 'low' : 'out'}`}>
                    {product.inventoryQuantity > 10 ? 'In Stock' : product.inventoryQuantity > 0 ? 'Low Stock' : 'Out of Stock'} ({product.inventoryQuantity} {product.unit})
                  </span>
                </div>
              </div>
              
              <div className="product-meta">
                <span className="category">{product.category}</span>
                <span className="sku">SKU: {product.sku}</span>
              </div>
              
              <div className="product-stats">
                <span>Views: {product.viewCount}</span>
                <span>Orders: {product.orderCount}</span>
                <span>Rating: {product.avgRating}</span>
              </div>
            </div>
            
            <div className="product-actions">
              <div className="status-switches">
                <label className="switch">
                  <input
                    type="checkbox"
                    checked={product.isAvailable}
                    onChange={() => handleToggleAvailability(product.id)}
                  />
                  <span className="slider round"></span>
                  <span className="label">Available</span>
                </label>
                
                <label className="switch">
                  <input
                    type="checkbox"
                    checked={product.isVisible}
                    onChange={() => handleToggleVisibility(product.id)}
                  />
                  <span className="slider round"></span>
                  <span className="label">Visible</span>
                </label>
              </div>
              
              <div className="action-buttons">
                <button 
                  className="btn btn-outline"
                  onClick={() => handleEditProduct(product)}
                >
                  Edit
                </button>
                <button 
                  className="btn btn-danger"
                  onClick={() => handleDeleteProduct(product.id)}
                >
                  Delete
                </button>
              </div>
            </div>
          </div>
        ))}
      </div>

      {products.length === 0 && (
        <div className="empty-state">
          <div className="empty-icon">🌱</div>
          <h3>No Products Yet</h3>
          <p>Add your first product to start selling on the marketplace</p>
          <button className="btn btn-primary" onClick={handleAddProduct}>
            Add Your First Product
          </button>
        </div>
      )}
    </div>
  );
};

export default ProductManagement;