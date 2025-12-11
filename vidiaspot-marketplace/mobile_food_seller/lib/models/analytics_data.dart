class AnalyticsData {
  final double totalRevenue;
  final int totalOrders;
  final int totalCustomers;
  final double avgOrderValue;
  final int newCustomers;
  final double conversionRate;
  final double monthlyGrowth;
  final List<SalesDataPoint> salesOverTime;
  final List<FoodItemAnalytics> topSellingItems;
  final List<FoodItemAnalytics> leastSellingItems;

  AnalyticsData({
    required this.totalRevenue,
    required this.totalOrders,
    required this.totalCustomers,
    required this.avgOrderValue,
    required this.newCustomers,
    required this.conversionRate,
    required this.monthlyGrowth,
    required this.salesOverTime,
    required this.topSellingItems,
    required this.leastSellingItems,
  });

  factory AnalyticsData.fromJson(Map<String, dynamic> json) {
    List<SalesDataPoint> salesData = [];
    if (json['sales_over_time'] != null) {
      salesData = (json['sales_over_time'] as List)
          .map((item) => SalesDataPoint.fromJson(item))
          .toList();
    }

    List<FoodItemAnalytics> topItems = [];
    if (json['top_selling_items'] != null) {
      topItems = (json['top_selling_items'] as List)
          .map((item) => FoodItemAnalytics.fromJson(item))
          .toList();
    }

    List<FoodItemAnalytics> leastItems = [];
    if (json['least_selling_items'] != null) {
      leastItems = (json['least_selling_items'] as List)
          .map((item) => FoodItemAnalytics.fromJson(item))
          .toList();
    }

    return AnalyticsData(
      totalRevenue: (json['total_revenue'] as num?)?.toDouble() ?? 0.0,
      totalOrders: json['total_orders'] ?? 0,
      totalCustomers: json['total_customers'] ?? 0,
      avgOrderValue: (json['avg_order_value'] as num?)?.toDouble() ?? 0.0,
      newCustomers: json['new_customers'] ?? 0,
      conversionRate: (json['conversion_rate'] as num?)?.toDouble() ?? 0.0,
      monthlyGrowth: (json['monthly_growth'] as num?)?.toDouble() ?? 0.0,
      salesOverTime: salesData,
      topSellingItems: topItems,
      leastSellingItems: leastItems,
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

class FoodItemAnalytics {
  final String id;
  final String name;
  final int unitsSold;
  final double revenueGenerated;
  final double percentageOfTotal;

  FoodItemAnalytics({
    required this.id,
    required this.name,
    required this.unitsSold,
    required this.revenueGenerated,
    required this.percentageOfTotal,
  });

  factory FoodItemAnalytics.fromJson(Map<String, dynamic> json) {
    return FoodItemAnalytics(
      id: json['id'] ?? '',
      name: json['name'] ?? '',
      unitsSold: json['units_sold'] ?? 0,
      revenueGenerated: (json['revenue_generated'] as num?)?.toDouble() ?? 0.0,
      percentageOfTotal: (json['percentage_of_total'] as num?)?.toDouble() ?? 0.0,
    );
  }
}