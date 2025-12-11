// Farm Management Service for web app
class FarmManagementService {
  static BASE_URL = '/api/farm-management'; // Replace with actual API base URL

  // Get farm data
  async getFarmData(farmId) {
    try {
      // Simulate API call
      await new Promise(resolve => setTimeout(resolve, 800));
      
      return {
        id: 'farm-1',
        name: 'Green Valley Farms',
        description: 'Sustainable organic farming producing fresh vegetables and fruits',
        ownerName: 'John Doe',
        email: 'contact@greenvalleyfarms.com',
        phone: '+1234567890',
        address: '123 Farm Road, Agriculture District',
        latitude: 37.7749,
        longitude: -122.4194,
        categories: ['Vegetables', 'Fruits', 'Organic'],
        rating: 4.8,
        numRatings: 120,
        currency: 'USD',
        isActive: true,
        acceptsOnlineOrders: true,
        offersDelivery: true,
        offersPickup: true,
        deliveryRadius: 15, // in km
        operatingHours: [
          { day: 'Monday', open: '08:00', close: '18:00', open24: false },
          { day: 'Tuesday', open: '08:00', close: '18:00', open24: false },
          { day: 'Wednesday', open: '08:00', close: '18:00', open24: false },
          { day: 'Thursday', open: '08:00', close: '18:00', open24: false },
          { day: 'Friday', open: '08:00', close: '18:00', open24: false },
          { day: 'Saturday', open: '09:00', close: '16:00', open24: false },
          { day: 'Sunday', open: '10:00', close: '14:00', open24: false },
        ],
        logoUrl: '',
        bannerImage: '',
        paymentMethods: ['Cash', 'Card', 'Mobile Money'],
        certifications: ['Organic Certified', 'Sustainable Farming'],
        yearsInBusiness: 5,
        deliveryFee: 5.00,
        minOrderAmount: 10.00,
        avgPreparationTime: 30, // in minutes
        deliveryTimeEstimate: 45, // in minutes
        isVerified: true,
        dateJoined: new Date('2023-01-15'),
        languages: ['English', 'Local Language'],
        taxRate: 0.08, // 8%
        businessLicense: 'BUS-LIC-2023-001',
      };
    } catch (error) {
      console.error('Error fetching farm data:', error);
      throw error;
    }
  }

  // Update farm profile
  async updateFarmProfile(farmId, formData) {
    try {
      // Simulate API call
      await new Promise(resolve => setTimeout(resolve, 1000));
      
      // In a real app, this would send the form data to the backend
      return {
        success: true,
        message: 'Farm profile updated successfully',
        updatedFarmData: formData
      };
    } catch (error) {
      console.error('Error updating farm profile:', error);
      throw error;
    }
  }

  // Get farm products
  async getFarmProducts(farmId) {
    try {
      // Simulate API call
      await new Promise(resolve => setTimeout(resolve, 800));
      
      return [
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
      ];
    } catch (error) {
      console.error('Error fetching farm products:', error);
      throw error;
    }
  }

  // Add new product
  async addProduct(farmId, productData) {
    try {
      // Simulate API call
      await new Promise(resolve => setTimeout(resolve, 1000));
      
      // In a real app, this would send product data to the backend
      return {
        success: true,
        message: 'Product added successfully',
        product: {
          id: `prod-${Date.now()}`,
          ...productData,
          createdAt: new Date(),
          updatedAt: new Date(),
          viewCount: 0,
          orderCount: 0,
          avgRating: 0,
          numRatings: 0,
        }
      };
    } catch (error) {
      console.error('Error adding product:', error);
      throw error;
    }
  }

  // Update product
  async updateProduct(farmId, productId, productData) {
    try {
      // Simulate API call
      await new Promise(resolve => setTimeout(resolve, 1000));
      
      // In a real app, this would update product data on the backend
      return {
        success: true,
        message: 'Product updated successfully',
        product: {
          id: productId,
          ...productData,
          updatedAt: new Date(),
        }
      };
    } catch (error) {
      console.error('Error updating product:', error);
      throw error;
    }
  }

  // Delete product
  async deleteProduct(farmId, productId) {
    try {
      // Simulate API call
      await new Promise(resolve => setTimeout(resolve, 800));
      
      // In a real app, this would delete the product on the backend
      return {
        success: true,
        message: 'Product deleted successfully'
      };
    } catch (error) {
      console.error('Error deleting product:', error);
      throw error;
    }
  }

  // Get farm orders
  async getFarmOrders(farmId, status = 'all') {
    try {
      // Simulate API call
      await new Promise(resolve => setTimeout(resolve, 800));
      
      let orders = [
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
              id: 'item-4',
              productId: 'prod-1',
              productName: 'Fresh Tomatoes',
              quantity: 3,
              unitPrice: 2.99,
              total: 8.97,
              specialInstructions: '',
              addons: [],
            },
            {
              id: 'item-5',
              productId: 'prod-2',
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
          deliveryPersonId: 'del-3',
          deliveryPersonName: 'David Brown',
          deliveryPersonPhone: '+17654321098',
          specialRequests: 'Include receipt',
          contactlessDelivery: true,
          farmId: 'farm-1',
        }
      ];

      if (status !== 'all') {
        orders = orders.filter(order => order.status === status);
      }

      return orders;
    } catch (error) {
      console.error('Error fetching farm orders:', error);
      throw error;
    }
  }

  // Update order status
  async updateOrderStatus(orderId, newStatus) {
    try {
      // Simulate API call
      await new Promise(resolve => setTimeout(resolve, 500));
      
      // In a real app, this would update the order status on the backend
      return {
        success: true,
        message: 'Order status updated successfully',
        orderId,
        status: newStatus
      };
    } catch (error) {
      console.error('Error updating order status:', error);
      throw error;
    }
  }

  // Get farm analytics
  async getFarmAnalytics(farmId, period = 'month') {
    try {
      // Simulate API call
      await new Promise(resolve => setTimeout(resolve, 800));
      
      return {
        totalRevenue: 12500.75,
        totalOrders: 342,
        totalCustomers: 120,
        avgOrderValue: 36.56,
        newCustomers: 24,
        conversionRate: 2.4,
        monthlyGrowth: 8.5,
        salesOverTime: [
          { date: '2023-01', revenue: 8500.00 },
          { date: '2023-02', revenue: 9200.50 },
          { date: '2023-03', revenue: 10100.75 },
          { date: '2023-04', revenue: 11500.25 },
          { date: '2023-05', revenue: 12300.80 },
          { date: '2023-06', revenue: 12900.60 },
          { date: '2023-07', revenue: 13500.90 },
          { date: '2023-08', revenue: 14200.30 },
          { date: '2023-09', revenue: 13800.75 },
          { date: '2023-10', revenue: 14100.20 },
          { date: '2023-11', revenue: 14500.50 },
          { date: '2023-12', revenue: 15200.75 },
        ],
        topSellingProducts: [
          { id: 'prod-1', name: 'Fresh Tomatoes', unitsSold: 245, revenueGenerated: 732.55, percentageOfTotal: 15.8 },
          { id: 'prod-2', name: 'Organic Lettuce', unitsSold: 189, revenueGenerated: 376.11, percentageOfTotal: 8.1 },
          { id: 'prod-3', name: 'Farm Fresh Eggs', unitsSold: 156, revenueGenerated: 778.44, percentageOfTotal: 16.8 },
        ],
        leastSellingProducts: [
          { id: 'prod-4', name: 'Organic Potatoes', unitsSold: 23, revenueGenerated: 45.77, percentageOfTotal: 0.99 },
          { id: 'prod-5', name: 'Fresh Herbs', unitsSold: 34, revenueGenerated: 67.66, percentageOfTotal: 1.46 },
        ],
      };
    } catch (error) {
      console.error('Error fetching farm analytics:', error);
      throw error;
    }
  }

  // Add/update farm location
  async updateFarmLocation(farmId, locationData) {
    try {
      // Simulate API call
      await new Promise(resolve => setTimeout(resolve, 1000));
      
      // In a real app, this would update the farm location on the backend
      return {
        success: true,
        message: 'Farm location updated successfully',
        location: locationData
      };
    } catch (error) {
      console.error('Error updating farm location:', error);
      throw error;
    }
  }

  // Get farms near location
  async getFarmsNearLocation(lat, lng, radius = 10) {
    try {
      // Simulate API call
      await new Promise(resolve => setTimeout(resolve, 800));
      
      return [
        {
          id: 'farm-1',
          name: 'Green Valley Farms',
          description: 'Sustainable organic farming',
          logoUrl: 'https://example.com/logo1.jpg',
          bannerImage: 'https://example.com/banner1.jpg',
          ownerName: 'John Doe',
          email: 'contact@greenvalleyfarms.com',
          phone: '+1234567890',
          address: '123 Farm Road, Agriculture District',
          latitude: 37.7749,
          longitude: -122.4194,
          categories: ['Vegetables', 'Fruits', 'Organic'],
          rating: 4.8,
          numRatings: 120,
          currency: 'USD',
          isActive: true,
          acceptsOnlineOrders: true,
          offersDelivery: true,
          offersPickup: true,
          deliveryRadius: 15,
          operatingHours: ['Mon-Fri: 8AM-6PM', 'Sat: 9AM-4PM'],
          deliveryFee: 5.00,
          minOrderAmount: 10.00,
          avgPreparationTime: 30,
          deliveryTimeEstimate: 45,
          isVerified: true,
          distance: 2.5, // Distance from user in km
          featured: true,
        },
        {
          id: 'farm-2',
          name: 'Organic Harvest Co',
          description: 'Premium organic produce',
          logoUrl: 'https://example.com/logo2.jpg',
          bannerImage: 'https://example.com/banner2.jpg',
          ownerName: 'Jane Smith',
          email: 'contact@organicharvest.com',
          phone: '+1987654321',
          address: '456 Orchard Lane, Agriculture District',
          latitude: 37.7649,
          longitude: -122.4094,
          categories: ['Vegetables', 'Fruits', 'Herbs'],
          rating: 4.6,
          numRatings: 98,
          currency: 'USD',
          isActive: true,
          acceptsOnlineOrders: true,
          offersDelivery: true,
          offersPickup: false,
          deliveryRadius: 10,
          operatingHours: ['Tue-Sun: 9AM-5PM'],
          deliveryFee: 4.50,
          minOrderAmount: 15.00,
          avgPreparationTime: 45,
          deliveryTimeEstimate: 50,
          isVerified: true,
          distance: 5.2,
          featured: false,
        },
      ];
    } catch (error) {
      console.error('Error fetching farms near location:', error);
      throw error;
    }
  }
}

export default new FarmManagementService();