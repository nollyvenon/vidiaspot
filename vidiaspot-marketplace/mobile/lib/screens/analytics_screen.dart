// lib/screens/analytics_screen.dart
import 'package:flutter/material.dart';
import 'package:syncfusion_flutter_charts/charts.dart';
import '../services/analytics_service.dart';
import '../services/crypto_p2p_service.dart';

class AnalyticsScreen extends StatefulWidget {
  const AnalyticsScreen({Key? key}) : super(key: key);

  @override
  _AnalyticsScreenState createState() => _AnalyticsScreenState();
}

class _AnalyticsScreenState extends State<AnalyticsScreen> {
  final AnalyticsService _analyticsService = AnalyticsService(CryptoP2PService());
  final List<String> _cryptoSymbols = ['BTC', 'ETH', 'USDT', 'USDC', 'BNB', 'XRP'];
  String _selectedCrypto = 'BTC';
  String _selectedTimeframe = '1D';
  bool _isLoading = true;
  
  MarketData? _marketData;
  PortfolioAnalytics? _portfolioAnalytics;
  RiskAnalytics? _riskAnalytics;
  MarketSentiment? _sentiment;

  @override
  void initState() {
    super.initState();
    _loadAnalyticsData();
  }

  Future<void> _loadAnalyticsData() async {
    setState(() {
      _isLoading = true;
    });

    try {
      // Load all analytics data
      _marketData = await _analyticsService.getMarketData(
        cryptoSymbol: _selectedCrypto,
        timeframe: _selectedTimeframe,
      );
      _portfolioAnalytics = await _analyticsService.getPortfolioAnalytics();
      _riskAnalytics = await _analyticsService.getRiskAnalytics();
      _sentiment = await _analyticsService.getMarketSentiment(_selectedCrypto);
    } catch (e) {
      print('Error loading analytics: $e');
    } finally {
      if (mounted) {
        setState(() {
          _isLoading = false;
        });
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Analytics & Tools'),
        backgroundColor: Colors.blue,
        foregroundColor: Colors.white,
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : RefreshIndicator(
              onRefresh: _loadAnalyticsData,
              child: SingleChildScrollView(
                child: Padding(
                  padding: const EdgeInsets.all(16),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      // Market selector
                      _buildMarketSelector(),
                      
                      const SizedBox(height: 16),
                      
                      // Market overview
                      _buildMarketOverview(),
                      
                      const SizedBox(height: 16),
                      
                      // Chart
                      _buildChart(),
                      
                      const SizedBox(height: 16),
                      
                      // Technical indicators
                      _buildTechnicalIndicators(),
                      
                      const SizedBox(height: 16),
                      
                      // Portfolio analytics
                      _buildPortfolioAnalytics(),
                      
                      const SizedBox(height: 16),
                      
                      // Risk analytics
                      _buildRiskAnalytics(),
                      
                      const SizedBox(height: 16),
                      
                      // Market sentiment
                      _buildMarketSentiment(),
                      
                      const SizedBox(height: 16),
                      
                      // Price prediction
                      _buildPricePrediction(),
                      
                      const SizedBox(height: 16),
                      
                      // Tax reporting
                      _buildTaxReport(),
                    ],
                  ),
                ),
              ),
            ),
    );
  }

  Widget _buildMarketSelector() {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.grey[100],
        borderRadius: BorderRadius.circular(12),
      ),
      child: Row(
        children: [
          Expanded(
            child: DropdownButtonFormField<String>(
              value: _selectedCrypto,
              decoration: const InputDecoration(
                labelText: 'Crypto',
                border: OutlineInputBorder(),
              ),
              items: _cryptoSymbols.map((symbol) {
                return DropdownMenuItem(
                  value: symbol,
                  child: Text(symbol),
                );
              }).toList(),
              onChanged: (value) {
                if (value != null) {
                  setState(() {
                    _selectedCrypto = value;
                  });
                  _loadAnalyticsData();
                }
              },
            ),
          ),
          const SizedBox(width: 16),
          Expanded(
            child: SegmentedButton<String>(
              segments: const [
                ButtonSegment(value: '1H', label: Text('1H')),
                ButtonSegment(value: '1D', label: Text('1D')),
                ButtonSegment(value: '1W', label: Text('1W')),
                ButtonSegment(value: '1M', label: Text('1M')),
              ],
              selected: {_selectedTimeframe},
              onSelectionChanged: (Set<String> newSelection) {
                setState(() {
                  _selectedTimeframe = newSelection.first;
                });
                _loadAnalyticsData();
              },
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildMarketOverview() {
    if (_marketData == null) return const SizedBox.shrink();
    
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
        boxShadow: [
          BoxShadow(
            color: Colors.grey.withOpacity(0.1),
            blurRadius: 10,
            offset: const Offset(0, 5),
          ),
        ],
      ),
      child: Column(
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(
                '${_marketData!.cryptoSymbol}/${_marketData!.fiatSymbol}',
                style: const TextStyle(
                  fontSize: 18,
                  fontWeight: FontWeight.bold,
                ),
              ),
              Text(
                '${_marketData!.priceChange24h > 0 ? '+' : ''}${_marketData!.priceChange24h.toStringAsFixed(2)}%',
                style: TextStyle(
                  fontSize: 16,
                  color: _marketData!.priceChange24h >= 0 ? Colors.green : Colors.red,
                  fontWeight: FontWeight.bold,
                ),
              ),
            ],
          ),
          const SizedBox(height: 8),
          Text(
            '\$${_marketData!.currentPrice.toStringAsFixed(2)}',
            style: const TextStyle(
              fontSize: 32,
              fontWeight: FontWeight.bold,
            ),
          ),
          const SizedBox(height: 16),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceAround,
            children: [
              _buildMarketStat(
                'Volume', 
                '\$${(_marketData!.volume24h / 1000000).toStringAsFixed(2)}M'
              ),
              _buildMarketStat(
                'Market Cap', 
                '\$${(_marketData!.marketCap / 1000000000).toStringAsFixed(2)}B'
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildMarketStat(String label, String value) {
    return Column(
      children: [
        Text(
          label,
          style: const TextStyle(
            fontSize: 12,
            color: Colors.grey,
          ),
        ),
        const SizedBox(height: 4),
        Text(
          value,
          style: const TextStyle(
            fontSize: 14,
            fontWeight: FontWeight.bold,
          ),
        ),
      ],
    );
  }

  Widget _buildChart() {
    if (_marketData == null) return const SizedBox.shrink();
    
    return Container(
      height: 300,
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
        boxShadow: [
          BoxShadow(
            color: Colors.grey.withOpacity(0.1),
            blurRadius: 10,
            offset: const Offset(0, 5),
          ),
        ],
      ),
      child: SfCartesianChart(
        primaryXAxis: DateTimeAxis(
          dateFormat: _selectedTimeframe == '1H' 
              ? DateFormat.Hm() 
              : _selectedTimeframe == '1D' 
                  ? DateFormat.Hm() 
                  : _selectedTimeframe == '1W' 
                      ? DateFormat.Md() 
                      : DateFormat.Md(),
        ),
        primaryYAxis: NumericAxis(
          numberFormat: NumberFormat.simpleCurrency(),
        ),
        series: <ChartSeries>[
          LineSeries<ChartData, DateTime>(
            dataSource: _marketData!.chartData,
            xValueMapper: (ChartData data, _) => data.x,
            yValueMapper: (ChartData data, _) => data.y,
            color: Colors.blue,
          ),
        ],
      ),
    );
  }

  Widget _buildTechnicalIndicators() {
    if (_marketData?.indicators == null) return const SizedBox.shrink();
    
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
        boxShadow: [
          BoxShadow(
            color: Colors.grey.withOpacity(0.1),
            blurRadius: 10,
            offset: const Offset(0, 5),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text(
            'Technical Indicators',
            style: TextStyle(
              fontSize: 18,
              fontWeight: FontWeight.bold,
            ),
          ),
          const SizedBox(height: 12),
          _buildIndicatorRow('RSI', '${_marketData!.indicators['RSI']}', _getRSIColor(_marketData!.indicators['RSI'])),
          _buildIndicatorRow('Volume', '\$${(_marketData!.indicators['Volume'] / 1000000).toStringAsFixed(2)}M', Colors.blue),
          _buildIndicatorRow('VWAP', '\$${_marketData!.indicators['VWAP'].toStringAsFixed(2)}', Colors.purple),
        ],
      ),
    );
  }

  Color _getRSIColor(double rsi) {
    if (rsi > 70) return Colors.red; // Overbought
    if (rsi < 30) return Colors.green; // Oversold
    return Colors.blue; // Neutral
  }

  Widget _buildIndicatorRow(String label, String value, Color color) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 8),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(
            label,
            style: const TextStyle(
              fontSize: 14,
              fontWeight: FontWeight.w500,
            ),
          ),
          Text(
            value,
            style: TextStyle(
              fontSize: 14,
              fontWeight: FontWeight.bold,
              color: color,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildPortfolioAnalytics() {
    if (_portfolioAnalytics == null) return const SizedBox.shrink();
    
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
        boxShadow: [
          BoxShadow(
            color: Colors.grey.withOpacity(0.1),
            blurRadius: 10,
            offset: const Offset(0, 5),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text(
            'Portfolio Analytics',
            style: TextStyle(
              fontSize: 18,
              fontWeight: FontWeight.bold,
            ),
          ),
          const SizedBox(height: 12),
          _buildPortfolioStat('Total Value', '\$${_portfolioAnalytics!.totalValue.toStringAsFixed(2)}', Colors.blue),
          _buildPortfolioStat('Gain/Loss', '\$${_portfolioAnalytics!.totalGainLoss.toStringAsFixed(2)}', _portfolioAnalytics!.totalGainLoss >= 0 ? Colors.green : Colors.red),
          _buildPortfolioStat('Gain/Loss %', '${_portfolioAnalytics!.gainLossPercent.toStringAsFixed(2)}%', _portfolioAnalytics!.totalGainLoss >= 0 ? Colors.green : Colors.red),
          const SizedBox(height: 12),
          const Text(
            'Portfolio Composition',
            style: TextStyle(
              fontSize: 16,
              fontWeight: FontWeight.bold,
            ),
          ),
          const SizedBox(height: 8),
          ..._portfolioAnalytics!.portfolioComposition.map((asset) => 
            _buildAssetRow(asset.symbol, asset.percentage, asset.value)
          ),
        ],
      ),
    );
  }

  Widget _buildPortfolioStat(String label, String value, Color color) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 4),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(
            label,
            style: const TextStyle(
              fontSize: 14,
            ),
          ),
          Text(
            value,
            style: TextStyle(
              fontSize: 14,
              fontWeight: FontWeight.bold,
              color: color,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildAssetRow(String symbol, double percentage, double value) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 2),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(
            symbol,
            style: const TextStyle(
              fontSize: 12,
            ),
          ),
          Text(
            '${percentage.toStringAsFixed(1)}% - \$${value.toStringAsFixed(2)}',
            style: const TextStyle(
              fontSize: 12,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildRiskAnalytics() {
    if (_riskAnalytics == null) return const SizedBox.shrink();
    
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
        boxShadow: [
          BoxShadow(
            color: Colors.grey.withOpacity(0.1),
            blurRadius: 10,
            offset: const Offset(0, 5),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text(
            'Risk Analytics',
            style: TextStyle(
              fontSize: 18,
              fontWeight: FontWeight.bold,
            ),
          ),
          const SizedBox(height: 12),
          _buildRiskStat('Risk Score', '${_riskAnalytics!.portfolioRiskScore}/100', _getRiskScoreColor(_riskAnalytics!.portfolioRiskScore)),
          _buildRiskStat('Volatility', '${(_riskAnalytics!.volatility * 100).toStringAsFixed(2)}%', Colors.blue),
          _buildRiskStat('Max Drawdown', '${(_riskAnalytics!.maxDrawdown * 100).toStringAsFixed(2)}%', Colors.orange),
          _buildRiskStat('Sharpe Ratio', _riskAnalytics!.sharpeRatio.toStringAsFixed(2), Colors.purple),
          _buildRiskStat('Value at Risk', '\$${_riskAnalytics!.valueAtRisk.toStringAsFixed(2)}', Colors.red),
          _buildRiskStat('Diversification', '${_riskAnalytics!.diversificationScore}/100', _getDiversificationColor(_riskAnalytics!.diversificationScore)),
        ],
      ),
    );
  }

  Color _getRiskScoreColor(int score) {
    if (score > 70) return Colors.red;
    if (score > 40) return Colors.orange;
    return Colors.green;
  }

  Color _getDiversificationColor(int score) {
    if (score > 70) return Colors.green;
    if (score > 40) return Colors.orange;
    return Colors.red;
  }

  Widget _buildRiskStat(String label, String value, Color color) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 4),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(
            label,
            style: const TextStyle(
              fontSize: 14,
            ),
          ),
          Text(
            value,
            style: TextStyle(
              fontSize: 14,
              fontWeight: FontWeight.bold,
              color: color,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildMarketSentiment() {
    if (_sentiment == null) return const SizedBox.shrink();
    
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
        boxShadow: [
          BoxShadow(
            color: Colors.grey.withOpacity(0.1),
            blurRadius: 10,
            offset: const Offset(0, 5),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text(
            'Market Sentiment',
            style: TextStyle(
              fontSize: 18,
              fontWeight: FontWeight.bold,
            ),
          ),
          const SizedBox(height: 12),
          _buildSentimentStat('Overall', '${(_sentiment!.sentimentScore * 100).round()}%', _getSentimentColor(_sentiment!.sentimentScore)),
          _buildSentimentStat('Fear/Greed', '${_sentiment!.fearGreedIndex}/100', _getFearGreedColor(_sentiment!.fearGreedIndex)),
          _buildSentimentStat('Social Media', '${_sentiment!.socialMediaScore}/100', Colors.blue),
          _buildSentimentStat('News Sentiment', '${_sentiment!.newsSentiment}/100', Colors.green),
          const SizedBox(height: 8),
          Text(
            'Trend: ${_sentiment!.trendDirection.toUpperCase()}',
            style: TextStyle(
              fontSize: 14,
              fontWeight: FontWeight.bold,
              color: _sentiment!.trendDirection == 'bullish' ? Colors.green : Colors.red,
            ),
          ),
        ],
      ),
    );
  }

  Color _getSentimentColor(double score) {
    if (score > 0.7) return Colors.green;
    if (score > 0.4) return Colors.blue;
    return Colors.red;
  }

  Color _getFearGreedColor(int index) {
    if (index > 70) return Colors.red; // Greed
    if (index > 50) return Colors.orange;
    if (index > 30) return Colors.yellow;
    if (index > 20) return Colors.blue;
    return Colors.green; // Fear
  }

  Widget _buildSentimentStat(String label, String value, Color color) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 4),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(
            label,
            style: const TextStyle(
              fontSize: 14,
            ),
          ),
          Text(
            value,
            style: TextStyle(
              fontSize: 14,
              fontWeight: FontWeight.bold,
              color: color,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildPricePrediction() {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
        boxShadow: [
          BoxShadow(
            color: Colors.grey.withOpacity(0.1),
            blurRadius: 10,
            offset: const Offset(0, 5),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text(
            'Price Prediction',
            style: TextStyle(
              fontSize: 18,
              fontWeight: FontWeight.bold,
            ),
          ),
          const SizedBox(height: 12),
          const Text(
            'Based on technical analysis and market patterns',
            style: TextStyle(
              fontSize: 14,
              color: Colors.grey,
            ),
          ),
          const SizedBox(height: 8),
          Card(
            color: Colors.blue[50],
            child: Padding(
              padding: const EdgeInsets.all(12),
              child: Column(
                children: [
                  Text(
                    'Next 7 days: Upward trend expected',
                    style: TextStyle(
                      fontSize: 16,
                      fontWeight: FontWeight.bold,
                      color: Colors.green[700],
                    ),
                  ),
                  const SizedBox(height: 4),
                  Text(
                    'Confidence: 75%',
                    style: TextStyle(
                      fontSize: 14,
                      color: Colors.grey[600],
                    ),
                  ),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildTaxReport() {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
        boxShadow: [
          BoxShadow(
            color: Colors.grey.withOpacity(0.1),
            blurRadius: 10,
            offset: const Offset(0, 5),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text(
            'Tax Reporting',
            style: TextStyle(
              fontSize: 18,
              fontWeight: FontWeight.bold,
            ),
          ),
          const SizedBox(height: 12),
          _buildTaxStat('Capital Gains', '\$12,500.00', Colors.green),
          _buildTaxStat('Capital Losses', '\$0.00', Colors.grey),
          _buildTaxStat('Net Gains', '\$12,500.00', Colors.green),
          const SizedBox(height: 8),
          OutlinedButton.icon(
            onPressed: () {},
            icon: const Icon(Icons.download),
            label: const Text('Export Tax Report'),
          ),
        ],
      ),
    );
  }

  Widget _buildTaxStat(String label, String value, Color color) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 4),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(
            label,
            style: const TextStyle(
              fontSize: 14,
            ),
          ),
          Text(
            value,
            style: TextStyle(
              fontSize: 14,
              fontWeight: FontWeight.bold,
              color: color,
            ),
          ),
        ],
      ),
    );
  }
}