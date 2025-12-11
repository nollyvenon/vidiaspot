import 'package:flutter/material.dart';
import 'package:syncfusion_flutter_charts/charts.dart';
import 'package:provider/provider.dart';
import '../ui/providers/food_seller_provider.dart';
import '../models/analytics_data.dart';

class AnalyticsScreen extends StatefulWidget {
  @override
  _AnalyticsScreenState createState() => _AnalyticsScreenState();
}

class _AnalyticsScreenState extends State<AnalyticsScreen> {
  DateTime _startDate = DateTime.now().subtract(Duration(days: 30));
  DateTime _endDate = DateTime.now();
  String _timeRange = '30d'; // 7d, 30d, 90d, 365d

  @override
  Widget build(BuildContext context) {
    final foodSellerProvider = Provider.of<FoodSellerProvider>(context);
    final analyticsData = foodSellerProvider.analyticsData;

    return Scaffold(
      appBar: AppBar(
        title: Text('Analytics'),
        backgroundColor: Colors.orange[400],
        foregroundColor: Colors.white,
        bottom: PreferredSize(
          preferredSize: Size.fromHeight(50),
          child: Container(
            padding: EdgeInsets.symmetric(horizontal: 16, vertical: 8),
            child: Row(
              children: [
                Text(
                  '${_startDate.toString().split(' ')[0]} - ${_endDate.toString().split(' ')[0]}',
                  style: TextStyle(color: Colors.white),
                ),
                Spacer(),
                PopupMenuButton<String>(
                  icon: Icon(Icons.date_range, color: Colors.white),
                  itemBuilder: (context) => [
                    PopupMenuItem(value: '7d', child: Text('Last 7 days')),
                    PopupMenuItem(value: '30d', child: Text('Last 30 days')),
                    PopupMenuItem(value: '90d', child: Text('Last 90 days')),
                    PopupMenuItem(value: '365d', child: Text('Last 365 days')),
                    PopupMenuItem(value: 'custom', child: Text('Custom range')),
                  ],
                  onSelected: (value) {
                    setState(() {
                      _timeRange = value;
                      // Update dates based on selected range
                      switch (value) {
                        case '7d':
                          _startDate = DateTime.now().subtract(Duration(days: 7));
                          break;
                        case '30d':
                          _startDate = DateTime.now().subtract(Duration(days: 30));
                          break;
                        case '90d':
                          _startDate = DateTime.now().subtract(Duration(days: 90));
                          break;
                        case '365d':
                          _startDate = DateTime.now().subtract(Duration(days: 365));
                          break;
                      }
                      _endDate = DateTime.now();
                    });
                  },
                ),
              ],
            ),
          ),
        ),
      ),
      body: foodSellerProvider.isLoading
          ? Center(child: CircularProgressIndicator())
          : RefreshIndicator(
              onRefresh: () async {
                // Refresh analytics data
              },
              child: SingleChildScrollView(
                child: Padding(
                  padding: EdgeInsets.all(16),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      // Key metrics
                      Text(
                        'Overview',
                        style: TextStyle(
                          fontSize: 20,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                      SizedBox(height: 10),
                      Wrap(
                        spacing: 10,
                        runSpacing: 10,
                        children: [
                          _buildMetricCard(
                            'Total Revenue',
                            analyticsData != null
                                ? '\$${analyticsData.totalRevenue.toStringAsFixed(2)}'
                                : '\$0.00',
                            Icons.attach_money,
                            Colors.green,
                          ),
                          _buildMetricCard(
                            'Total Orders',
                            analyticsData != null
                                ? analyticsData.totalOrders.toString()
                                : '0',
                            Icons.shopping_bag,
                            Colors.blue,
                          ),
                          _buildMetricCard(
                            'Total Customers',
                            analyticsData != null
                                ? analyticsData.totalCustomers.toString()
                                : '0',
                            Icons.people,
                            Colors.orange,
                          ),
                          _buildMetricCard(
                            'Avg. Order Value',
                            analyticsData != null
                                ? '\$${analyticsData.avgOrderValue.toStringAsFixed(2)}'
                                : '\$0.00',
                            Icons.monetization_on,
                            Colors.purple,
                          ),
                        ],
                      ),
                      SizedBox(height: 20),
                      
                      // Sales chart
                      Container(
                        width: double.infinity,
                        height: 300,
                        child: Card(
                          elevation: 4,
                          child: Padding(
                            padding: EdgeInsets.all(16),
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(
                                  'Sales Trend',
                                  style: TextStyle(
                                    fontSize: 16,
                                    fontWeight: FontWeight.bold,
                                  ),
                                ),
                                SizedBox(height: 10),
                                if (analyticsData != null && analyticsData.salesOverTime.isNotEmpty)
                                  Expanded(
                                    child: SfCartesianChart(
                                      primaryXAxis: CategoryAxis(),
                                      primaryYAxis: NumericAxis(),
                                      series: <CartesianSeries>[
                                        SplineSeries<SalesDataPoint, String>(
                                          dataSource: analyticsData.salesOverTime,
                                          xValueMapper: (SalesDataPoint sales, _) => sales.date,
                                          yValueMapper: (SalesDataPoint sales, _) => sales.revenue,
                                          color: Colors.orange[400],
                                          width: 3,
                                        )
                                      ],
                                    ),
                                  )
                                else
                                  Expanded(
                                    child: Center(
                                      child: Column(
                                        mainAxisAlignment: MainAxisAlignment.center,
                                        children: [
                                          Icon(
                                            Icons.show_chart,
                                            size: 80,
                                            color: Colors.grey[400],
                                          ),
                                          SizedBox(height: 10),
                                          Text(
                                            'No sales data available',
                                            style: TextStyle(color: Colors.grey[600]),
                                          ),
                                        ],
                                      ),
                                    ),
                                  ),
                              ],
                            ),
                          ),
                        ),
                      ),
                      SizedBox(height: 20),
                      
                      // Top/Least selling items
                      if (analyticsData != null && 
                          (analyticsData.topSellingItems.isNotEmpty || 
                           analyticsData.leastSellingItems.isNotEmpty))
                        Row(
                          children: [
                            Expanded(
                              child: _buildItemsList(
                                'Top Selling Items',
                                analyticsData.topSellingItems.take(3).toList(),
                                Colors.green,
                              ),
                            ),
                            SizedBox(width: 10),
                            Expanded(
                              child: _buildItemsList(
                                'Least Selling Items',
                                analyticsData.leastSellingItems.take(3).toList(),
                                Colors.red,
                              ),
                            ),
                          ],
                        ),
                    ],
                  ),
                ),
              ),
            ),
    );
  }

  Widget _buildMetricCard(String title, String value, IconData icon, Color color) {
    return Expanded(
      child: Card(
        elevation: 4,
        child: Padding(
          padding: EdgeInsets.all(16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                children: [
                  Container(
                    padding: EdgeInsets.all(8),
                    decoration: BoxDecoration(
                      color: color.withOpacity(0.2),
                      borderRadius: BorderRadius.circular(10),
                    ),
                    child: Icon(icon, color: color),
                  ),
                  SizedBox(width: 10),
                  Expanded(
                    child: Text(
                      title,
                      style: TextStyle(
                        fontSize: 14,
                        color: Colors.grey[600],
                      ),
                    ),
                  ),
                ],
              ),
              SizedBox(height: 10),
              Text(
                value,
                style: TextStyle(
                  fontSize: 24,
                  fontWeight: FontWeight.bold,
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildItemsList(String title, List<FoodItemAnalytics> items, Color color) {
    return Card(
      elevation: 4,
      child: Padding(
        padding: EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              title,
              style: TextStyle(
                fontSize: 16,
                fontWeight: FontWeight.bold,
              ),
            ),
            SizedBox(height: 10),
            if (items.isNotEmpty)
              ListView.builder(
                shrinkWrap: true,
                physics: NeverScrollableScrollPhysics(),
                itemCount: items.length,
                itemBuilder: (context, index) {
                  final item = items[index];
                  return ListTile(
                    contentPadding: EdgeInsets.symmetric(vertical: 4),
                    leading: Container(
                      padding: EdgeInsets.all(4),
                      decoration: BoxDecoration(
                        color: color.withOpacity(0.2),
                        borderRadius: BorderRadius.circular(5),
                      ),
                      child: Text(
                        '${index + 1}',
                        style: TextStyle(color: color, fontWeight: FontWeight.bold),
                      ),
                    ),
                    title: Text(
                      item.name,
                      style: TextStyle(fontSize: 14),
                    ),
                    subtitle: Text(
                      '${item.unitsSold} sold',
                      style: TextStyle(fontSize: 12, color: Colors.grey[600]),
                    ),
                    trailing: Text(
                      '\$${item.revenueGenerated.toStringAsFixed(2)}',
                      style: TextStyle(fontWeight: FontWeight.bold),
                    ),
                  );
                },
              ),
            if (items.isEmpty)
              Padding(
                padding: EdgeInsets.symmetric(vertical: 20),
                child: Center(
                  child: Text(
                    'No data',
                    style: TextStyle(color: Colors.grey[600]),
                  ),
                ),
              ),
          ],
        ),
      ),
    );
  }
}