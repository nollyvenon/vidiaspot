class FarmAnalytics {
  final double totalRevenue;
  final int totalOrders;
  final int totalCustomers;
  final double avgOrderValue;
  final int newCustomers;
  final double conversionRate;
  final double monthlyGrowth;
  final List<SalesDataPoint> salesOverTime;
  final List<ProductAnalytics> topSellingProducts;
  final List<ProductAnalytics> leastSellingProducts;

  FarmAnalytics({
    required this.totalRevenue,
    required this.totalOrders,
    required this.totalCustomers,
    required this.avgOrderValue,
    required this.newCustomers,
    required this.conversionRate,
    required this.monthlyGrowth,
    required this.salesOverTime,
    required this.topSellingProducts,
    required this.leastSellingProducts,
  });

  factory FarmAnalytics.fromJson(Map<String, dynamic> json) {
    List<SalesDataPoint> salesData = [];
    if (json['sales_over_time'] != null) {
      salesData = (json['sales_over_time'] as List)
          .map((item) => SalesDataPoint.fromJson(item))
          .toList();
    }

    List<ProductAnalytics> topProducts = [];
    if (json['top_selling_products'] != null) {
      topProducts = (json['top_selling_products'] as List)
          .map((item) => ProductAnalytics.fromJson(item))
          .toList();
    }

    List<ProductAnalytics> leastProducts = [];
    if (json['least_selling_products'] != null) {
      leastProducts = (json['least_selling_products'] as List)
          .map((item) => ProductAnalytics.fromJson(item))
          .toList();
    }

    return FarmAnalytics(
      totalRevenue: (json['total_revenue'] as num?)?.toDouble() ?? 0.0,
      totalOrders: json['total_orders'] ?? 0,
      totalCustomers: json['total_customers'] ?? 0,
      avgOrderValue: (json['avg_order_value'] as num?)?.toDouble() ?? 0.0,
      newCustomers: json['new_customers'] ?? 0,
      conversionRate: (json['conversion_rate'] as num?)?.toDouble() ?? 0.0,
      monthlyGrowth: (json['monthly_growth'] as num?)?.toDouble() ?? 0.0,
      salesOverTime: salesData,
      topSellingProducts: topProducts,
      leastSellingProducts: leastProducts,
    );
  }
}

class SalesDataPoint {
  final String date;
  final double revenue;

  SalesDataPoint({
    required this.date,
    required this.revenue,
  });

  factory SalesDataPoint.fromJson(Map<String, dynamic> json) {
    return SalesDataPoint(
      date: json['date'] ?? '',
      revenue: (json['revenue'] as num?)?.toDouble() ?? 0.0,
    );
  }
}

class ProductAnalytics {
  final String id;
  final String name;
  final int unitsSold;
  final double revenueGenerated;
  final double percentageOfTotal;

  ProductAnalytics({
    required this.id,
    required this.name,
    required this.unitsSold,
    required this.revenueGenerated,
    required this.percentageOfTotal,
  });

  factory ProductAnalytics.fromJson(Map<String, dynamic> json) {
    return ProductAnalytics(
      id: json['id'] ?? '',
      name: json['name'] ?? '',
      unitsSold: json['units_sold'] ?? 0,
      revenueGenerated: (json['revenue_generated'] as num?)?.toDouble() ?? 0.0,
      percentageOfTotal: (json['percentage_of_total'] as num?)?.toDouble() ?? 0.0,
    );
  }
}