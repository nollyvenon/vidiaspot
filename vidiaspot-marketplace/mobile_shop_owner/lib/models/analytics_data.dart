class AnalyticsData {
  final double totalRevenue;
  final int totalOrders;
  final int totalCustomers;
  final double avgOrderValue;
  final int newCustomers;
  final double conversionRate;
  final double monthlyGrowth;
  final List<SalesDataPoint> salesOverTime;

  AnalyticsData({
    required this.totalRevenue,
    required this.totalOrders,
    required this.totalCustomers,
    required this.avgOrderValue,
    required this.newCustomers,
    required this.conversionRate,
    required this.monthlyGrowth,
    required this.salesOverTime,
  });

  factory AnalyticsData.fromJson(Map<String, dynamic> json) {
    List<SalesDataPoint> salesData = [];
    if (json['sales_over_time'] != null) {
      salesData = (json['sales_over_time'] as List)
          .map((item) => SalesDataPoint.fromJson(item))
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