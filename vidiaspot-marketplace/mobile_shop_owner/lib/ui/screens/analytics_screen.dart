import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../providers/shop_owner_provider.dart';
import '../../models/analytics_data.dart';
import 'package:syncfusion_flutter_charts/charts.dart';

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
    final shopOwnerProvider = Provider.of<ShopOwnerProvider>(context);
    final analyticsData = shopOwnerProvider.analyticsData;

    return Scaffold(
      appBar: AppBar(
        title: Text('Analytics'),
        backgroundColor: Colors.blue,
        foregroundColor: Colors.white,
      ),
      body: shopOwnerProvider.isLoading
          ? Center(child: CircularProgressIndicator())
          : RefreshIndicator(
              onRefresh: () async {
                // Refresh analytics
              },
              child: SingleChildScrollView(
                child: Padding(
                  padding: EdgeInsets.all(16),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      // Time range selector
                      _buildTimeRangeSelector(),
                      SizedBox(height: 20),
                      
                      // Key metrics
                      _buildMetricsGrid(analyticsData),
                      SizedBox(height: 20),
                      
                      // Sales chart
                      _buildSalesChart(analyticsData),
                      SizedBox(height: 20),
                      
                      // Performance metrics
                      _buildPerformanceMetrics(analyticsData),
                    ],
                  ),
                ),
              ),
            ),
    );
  }

  Widget _buildTimeRangeSelector() {
    return Container(
      padding: EdgeInsets.all(10),
      decoration: BoxDecoration(
        border: Border.all(color: Colors.grey[300]!),
        borderRadius: BorderRadius.circular(10),
      ),
      child: Row(
        children: [
          Text(
            '${_startDate.toString().split(' ')[0]} - ${_endDate.toString().split(' ')[0]}',
            style: TextStyle(fontWeight: FontWeight.w500),
          ),
          Spacer(),
          PopupMenuButton<String>(
            initialValue: _timeRange,
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
            child: Container(
              padding: EdgeInsets.symmetric(horizontal: 10, vertical: 5),
              decoration: BoxDecoration(
                border: Border.all(color: Colors.blue),
                borderRadius: BorderRadius.circular(5),
              ),
              child: Row(
                children: [
                  Text(
                    _timeRange == 'custom' ? 'Custom' : '$_timeRange',
                    style: TextStyle(color: Colors.blue),
                  ),
                  Icon(Icons.arrow_drop_down, color: Colors.blue),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildMetricsGrid(AnalyticsData? analyticsData) {
    return GridView.count(
      crossAxisCount: 2,
      crossAxisSpacing: 10,
      mainAxisSpacing: 10,
      shrinkWrap: true,
      physics: NeverScrollableScrollPhysics(),
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
          'Avg. Order Value',
          analyticsData != null 
              ? '\$${analyticsData.avgOrderValue.toStringAsFixed(2)}' 
              : '\$0.00',
          Icons.monetization_on,
          Colors.purple,
        ),
        _buildMetricCard(
          'Conversion Rate',
          analyticsData != null 
              ? '${analyticsData.conversionRate.toStringAsFixed(2)}%' 
              : '0.00%',
          Icons.trending_up,
          Colors.orange,
        ),
      ],
    );
  }

  Widget _buildMetricCard(String title, String value, IconData icon, Color color) {
    return Card(
      elevation: 3,
      child: Padding(
        padding: EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Row(
              children: [
                Container(
                  padding: EdgeInsets.all(8),
                  decoration: BoxDecoration(
                    color: color.withOpacity(0.1),
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
                      fontWeight: FontWeight.w500,
                    ),
                    maxLines: 2,
                  ),
                ),
              ],
            ),
            SizedBox(height: 10),
            Text(
              value,
              style: TextStyle(
                fontSize: 20,
                fontWeight: FontWeight.bold,
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildSalesChart(AnalyticsData? analyticsData) {
    return Card(
      elevation: 3,
      child: Padding(
        padding: EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              'Sales Over Time',
              style: TextStyle(
                fontSize: 18,
                fontWeight: FontWeight.bold,
              ),
            ),
            SizedBox(height: 10),
            Container(
              height: 300,
              child: SfCartesianChart(
                primaryXAxis: CategoryAxis(),
                primaryYAxis: NumericAxis(),
                series: <ChartSeries>[
                  LineSeries<SalesDataPoint, String>(
                    dataSource: analyticsData != null && analyticsData.salesOverTime.isNotEmpty
                        ? analyticsData.salesOverTime
                        : [
                            SalesDataPoint(date: 'Jan', revenue: 0),
                            SalesDataPoint(date: 'Feb', revenue: 0),
                            SalesDataPoint(date: 'Mar', revenue: 0),
                            SalesDataPoint(date: 'Apr', revenue: 0),
                            SalesDataPoint(date: 'May', revenue: 0),
                            SalesDataPoint(date: 'Jun', revenue: 0),
                          ],
                    xValueMapper: (SalesDataPoint sales, _) => sales.date,
                    yValueMapper: (SalesDataPoint sales, _) => sales.revenue,
                    color: Colors.blue,
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildPerformanceMetrics(AnalyticsData? analyticsData) {
    return Card(
      elevation: 3,
      child: Padding(
        padding: EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              'Performance Metrics',
              style: TextStyle(
                fontSize: 18,
                fontWeight: FontWeight.bold,
              ),
            ),
            SizedBox(height: 15),
            _buildPerformanceRow(
              'New Customers',
              analyticsData != null 
                  ? analyticsData.newCustomers.toString() 
                  : '0',
              Icons.person_add,
              Colors.green,
            ),
            Divider(),
            _buildPerformanceRow(
              'Total Customers',
              analyticsData != null 
                  ? analyticsData.totalCustomers.toString() 
                  : '0',
              Icons.people,
              Colors.blue,
            ),
            Divider(),
            _buildPerformanceRow(
              'Monthly Growth',
              analyticsData != null 
                  ? '${analyticsData.monthlyGrowth.toStringAsFixed(2)}%' 
                  : '0.00%',
              Icons.trending_up,
              Colors.orange,
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildPerformanceRow(String title, String value, IconData icon, Color color) {
    return Padding(
      padding: EdgeInsets.symmetric(vertical: 10),
      child: Row(
        children: [
          Container(
            padding: EdgeInsets.all(8),
            decoration: BoxDecoration(
              color: color.withOpacity(0.1),
              borderRadius: BorderRadius.circular(10),
            ),
            child: Icon(icon, color: color),
          ),
          SizedBox(width: 15),
          Expanded(
            child: Text(
              title,
              style: TextStyle(
                fontSize: 16,
                fontWeight: FontWeight.w500,
              ),
            ),
          ),
          Text(
            value,
            style: TextStyle(
              fontSize: 16,
              fontWeight: FontWeight.bold,
            ),
          ),
        ],
      ),
    );
  }
}