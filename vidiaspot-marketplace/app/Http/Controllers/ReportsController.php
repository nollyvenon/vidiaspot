<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CryptoP2PReportingService;
use App\Services\FoodReportingService;
use App\Services\ClassifiedReportingService;
use App\Services\EcommerceReportingService;
use App\Services\CrossPlatformReportingService;
use App\Models\BalanceSheetReport;
use App\Models\IncomeStatementReport;
use App\Models\CashFlowReport;
use App\Models\DailyTradingReport;
use App\Models\UserActivityReport;
use App\Models\UserTradeHistoryReport;
use App\Models\UserSegmentationReport;
use App\Models\SecurityReport;
use App\Models\MarketRiskReport;
use App\Models\AmlKycReport;
use App\Models\TaxReport;
use App\Models\SystemPerformanceReport;
use App\Models\CustomerServiceReport;
use App\Models\GeneralLedgerReport;
use App\Models\RevenueRecognitionReport;
use App\Models\PredictiveAnalyticsReport;
use App\Models\PerformanceMetricsReport;
use App\Models\LiveDashboardReport;

class ReportsController extends Controller
{
    protected $cryptoP2PReportingService;
    protected $foodReportingService;
    protected $classifiedReportingService;
    protected $ecommerceReportingService;
    protected $crossPlatformReportingService;

    public function __construct(
        CryptoP2PReportingService $cryptoP2PReportingService,
        FoodReportingService $foodReportingService,
        ClassifiedReportingService $classifiedReportingService,
        EcommerceReportingService $ecommerceReportingService,
        CrossPlatformReportingService $crossPlatformReportingService
    ) {
        $this->cryptoP2PReportingService = $cryptoP2PReportingService;
        $this->foodReportingService = $foodReportingService;
        $this->classifiedReportingService = $classifiedReportingService;
        $this->ecommerceReportingService = $ecommerceReportingService;
        $this->crossPlatformReportingService = $crossPlatformReportingService;
        $this->middleware('auth');
    }

    /**
     * Display the reports dashboard
     */
    public function index()
    {
        return view('reports.index');
    }

    /**
     * Show a specific report
     */
    public function show($type, $id)
    {
        $report = null;

        switch ($type) {
            case 'balance-sheet':
                $report = BalanceSheetReport::findOrFail($id);
                break;
            case 'income-statement':
                $report = IncomeStatementReport::findOrFail($id);
                break;
            case 'cash-flow':
                $report = CashFlowReport::findOrFail($id);
                break;
            case 'daily-trading':
                $report = DailyTradingReport::findOrFail($id);
                break;
            case 'user-activity':
                $report = UserActivityReport::findOrFail($id);
                break;
            case 'user-trade-history':
                $report = UserTradeHistoryReport::findOrFail($id);
                break;
            case 'user-segmentation':
                $report = UserSegmentationReport::findOrFail($id);
                break;
            case 'security':
                $report = SecurityReport::findOrFail($id);
                break;
            case 'market-risk':
                $report = MarketRiskReport::findOrFail($id);
                break;
            case 'aml-kyc':
                $report = AmlKycReport::findOrFail($id);
                break;
            case 'tax':
                $report = TaxReport::findOrFail($id);
                break;
            case 'system-performance':
                $report = SystemPerformanceReport::findOrFail($id);
                break;
            case 'customer-service':
                $report = CustomerServiceReport::findOrFail($id);
                break;
            case 'general-ledger':
                $report = GeneralLedgerReport::findOrFail($id);
                break;
            case 'revenue-recognition':
                $report = RevenueRecognitionReport::findOrFail($id);
                break;
            case 'predictive-analytics':
                $report = PredictiveAnalyticsReport::findOrFail($id);
                break;
            case 'performance-metrics':
                $report = PerformanceMetricsReport::findOrFail($id);
                break;
            default:
                abort(404);
        }

        return view('reports.show', compact('report', 'type'));
    }

    /**
     * List reports of a specific type
     */
    public function list($type)
    {
        $reports = collect();
        $reportClass = null;

        switch ($type) {
            case 'balance-sheet':
                $reportClass = BalanceSheetReport::class;
                $reports = BalanceSheetReport::orderBy('created_at', 'desc')->paginate(15);
                break;
            case 'income-statement':
                $reportClass = IncomeStatementReport::class;
                $reports = IncomeStatementReport::orderBy('created_at', 'desc')->paginate(15);
                break;
            case 'cash-flow':
                $reportClass = CashFlowReport::class;
                $reports = CashFlowReport::orderBy('created_at', 'desc')->paginate(15);
                break;
            case 'daily-trading':
                $reportClass = DailyTradingReport::class;
                $reports = DailyTradingReport::orderBy('date', 'desc')->paginate(15);
                break;
            case 'user-activity':
                $reportClass = UserActivityReport::class;
                $reports = UserActivityReport::orderBy('created_at', 'desc')->paginate(15);
                break;
            case 'user-trade-history':
                $reportClass = UserTradeHistoryReport::class;
                $reports = UserTradeHistoryReport::orderBy('created_at', 'desc')->paginate(15);
                break;
            case 'user-segmentation':
                $reportClass = UserSegmentationReport::class;
                $reports = UserSegmentationReport::orderBy('created_at', 'desc')->paginate(15);
                break;
            case 'security':
                $reportClass = SecurityReport::class;
                $reports = SecurityReport::orderBy('created_at', 'desc')->paginate(15);
                break;
            case 'market-risk':
                $reportClass = MarketRiskReport::class;
                $reports = MarketRiskReport::orderBy('created_at', 'desc')->paginate(15);
                break;
            case 'aml-kyc':
                $reportClass = AmlKycReport::class;
                $reports = AmlKycReport::orderBy('created_at', 'desc')->paginate(15);
                break;
            case 'tax':
                $reportClass = TaxReport::class;
                $reports = TaxReport::orderBy('created_at', 'desc')->paginate(15);
                break;
            case 'system-performance':
                $reportClass = SystemPerformanceReport::class;
                $reports = SystemPerformanceReport::orderBy('created_at', 'desc')->paginate(15);
                break;
            case 'customer-service':
                $reportClass = CustomerServiceReport::class;
                $reports = CustomerServiceReport::orderBy('created_at', 'desc')->paginate(15);
                break;
            case 'general-ledger':
                $reportClass = GeneralLedgerReport::class;
                $reports = GeneralLedgerReport::orderBy('created_at', 'desc')->paginate(15);
                break;
            case 'revenue-recognition':
                $reportClass = RevenueRecognitionReport::class;
                $reports = RevenueRecognitionReport::orderBy('created_at', 'desc')->paginate(15);
                break;
            case 'predictive-analytics':
                $reportClass = PredictiveAnalyticsReport::class;
                $reports = PredictiveAnalyticsReport::orderBy('created_at', 'desc')->paginate(15);
                break;
            case 'performance-metrics':
                $reportClass = PerformanceMetricsReport::class;
                $reports = PerformanceMetricsReport::orderBy('created_at', 'desc')->paginate(15);
                break;
            case 'automated-alerts':
                // For automated alerts, we use the AutomatedAlertReport model
                $reports = \App\Models\AutomatedAlertReport::orderBy('created_at', 'desc')->paginate(15);
                break;
            default:
                abort(404);
        }

        return view('reports.list', compact('reports', 'type'));
    }

    /**
     * Generate a new report
     */
    public function generate($type, Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        
        if (!$startDate || !$endDate) {
            // Default to last 30 days
            $endDate = now();
            $startDate = now()->subDays(30);
        } else {
            $startDate = \Carbon\Carbon::parse($startDate);
            $endDate = \Carbon\Carbon::parse($endDate);
        }

        $report = null;

        switch ($type) {
            case 'balance-sheet':
                $report = $this->reportingService->generateBalanceSheetReport($startDate, $endDate);
                break;
            case 'income-statement':
                $report = $this->reportingService->generateIncomeStatementReport($startDate, $endDate);
                break;
            case 'cash-flow':
                $report = $this->reportingService->generateCashFlowReport($startDate, $endDate);
                break;
            case 'daily-trading':
                // For daily trading, we use today's date
                $report = $this->reportingService->generateDailyTradingReport(now());
                break;
            case 'user-activity':
                $report = $this->reportingService->generateUserActivityReport($startDate, $endDate);
                break;
            case 'user-trade-history':
                // For user trade history, we need a user ID
                $userId = $request->get('user_id', auth()->id());
                $report = $this->reportingService->generateUserTradeHistoryReport($userId, $startDate, $endDate);
                break;
            case 'user-segmentation':
                $report = $this->reportingService->generateUserSegmentationReport($startDate, $endDate);
                break;
            case 'security':
                $report = $this->reportingService->generateSecurityReport($startDate, $endDate);
                break;
            case 'market-risk':
                $report = $this->reportingService->generateMarketRiskReport($startDate, $endDate);
                break;
            case 'aml-kyc':
                $report = $this->reportingService->generateAmlKycReport($startDate, $endDate);
                break;
            case 'tax':
                $report = $this->reportingService->generateTaxReport($startDate, $endDate);
                break;
            case 'system-performance':
                $report = $this->reportingService->generateSystemPerformanceReport($startDate, $endDate);
                break;
            case 'customer-service':
                $report = $this->reportingService->generateCustomerServiceReport($startDate, $endDate);
                break;
            case 'general-ledger':
                $report = $this->reportingService->generateGeneralLedgerReport($startDate, $endDate);
                break;
            case 'revenue-recognition':
                $report = $this->reportingService->generateRevenueRecognitionReport($startDate, $endDate);
                break;
            case 'predictive-analytics':
                $report = $this->reportingService->generatePredictiveAnalyticsReport($startDate, $endDate);
                break;
            case 'performance-metrics':
                $report = $this->reportingService->generatePerformanceMetricsReport($startDate, $endDate);
                break;
            default:
                abort(404);
        }

        if ($report) {
            return redirect()->route('reports.show', ['type' => $type, 'id' => $report->id])
                ->with('success', ucfirst(str_replace('-', ' ', $type)) . ' report generated successfully!');
        }

        return redirect()->route('reports.index')
            ->with('error', 'Failed to generate report.');
    }

    /**
     * Show the live dashboard
     */
    public function liveDashboard()
    {
        $report = $this->cryptoP2PReportingService->getLiveDashboardReport();
        return view('reports.live-dashboard', compact('report'));
    }

    /**
     * Generate Food Sales & Revenue Report
     */
    public function generateFoodSalesRevenueReport(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        $vendorId = $request->get('vendor_id');

        $report = $this->foodReportingService->generateSalesRevenueReport(
            \Carbon\Carbon::parse($startDate),
            \Carbon\Carbon::parse($endDate),
            $vendorId
        );

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }

    /**
     * Generate Food Operational Efficiency Report
     */
    public function generateFoodOperationalEfficiencyReport(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        $vendorId = $request->get('vendor_id');

        $report = $this->foodReportingService->generateOperationalEfficiencyReport(
            \Carbon\Carbon::parse($startDate),
            \Carbon\Carbon::parse($endDate),
            $vendorId
        );

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }

    /**
     * Generate Food Customer Experience Report
     */
    public function generateFoodCustomerExperienceReport(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        $vendorId = $request->get('vendor_id');

        $report = $this->foodReportingService->generateCustomerExperienceReport(
            \Carbon\Carbon::parse($startDate),
            \Carbon\Carbon::parse($endDate),
            $vendorId
        );

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }

    /**
     * Generate Food Financial Report
     */
    public function generateFoodFinancialReport(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        $vendorId = $request->get('vendor_id');

        $report = $this->foodReportingService->generateFoodFinancialReport(
            \Carbon\Carbon::parse($startDate),
            \Carbon\Carbon::parse($endDate),
            $vendorId
        );

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }

    /**
     * Generate Classified User Activity Report
     */
    public function generateClassifiedUserActivityReport(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $report = $this->classifiedReportingService->generateUserActivityReport(
            \Carbon\Carbon::parse($startDate),
            \Carbon\Carbon::parse($endDate)
        );

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }

    /**
     * Generate Classified Revenue Report
     */
    public function generateClassifiedRevenueReport(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $report = $this->classifiedReportingService->generateRevenueReport(
            \Carbon\Carbon::parse($startDate),
            \Carbon\Carbon::parse($endDate)
        );

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }

    /**
     * Generate Classified Content Quality Report
     */
    public function generateClassifiedContentQualityReport(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $report = $this->classifiedReportingService->generateContentQualityReport(
            \Carbon\Carbon::parse($startDate),
            \Carbon\Carbon::parse($endDate)
        );

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }

    /**
     * Generate Classified Market Intelligence Report
     */
    public function generateClassifiedMarketIntelligenceReport(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $report = $this->classifiedReportingService->generateMarketIntelligenceReport(
            \Carbon\Carbon::parse($startDate),
            \Carbon\Carbon::parse($endDate)
        );

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }

    /**
     * Generate E-commerce Sales Performance Report
     */
    public function generateEcommerceSalesPerformanceReport(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $report = $this->ecommerceReportingService->generateSalesPerformanceReport(
            \Carbon\Carbon::parse($startDate),
            \Carbon\Carbon::parse($endDate)
        );

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }

    /**
     * Generate E-commerce Inventory Report
     */
    public function generateEcommerceInventoryReport(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $report = $this->ecommerceReportingService->generateInventoryReport(
            \Carbon\Carbon::parse($startDate),
            \Carbon\Carbon::parse($endDate)
        );

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }

    /**
     * Generate E-commerce Marketing & Customer Report
     */
    public function generateEcommerceMarketingCustomerReport(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $report = $this->ecommerceReportingService->generateMarketingCustomerReport(
            \Carbon\Carbon::parse($startDate),
            \Carbon\Carbon::parse($endDate)
        );

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }

    /**
     * Generate E-commerce Financial & Operational Report
     */
    public function generateEcommerceFinancialOperationalReport(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $report = $this->ecommerceReportingService->generateFinancialOperationalReport(
            \Carbon\Carbon::parse($startDate),
            \Carbon\Carbon::parse($endDate)
        );

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }

    /**
     * Generate Unified Financial Dashboard Report
     */
    public function generateUnifiedFinancialDashboardReport(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $report = $this->crossPlatformReportingService->generateUnifiedFinancialDashboard(
            \Carbon\Carbon::parse($startDate),
            \Carbon\Carbon::parse($endDate)
        );

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }

    /**
     * Generate Cross-Platform Customer Journey Report
     */
    public function generateCrossPlatformCustomerJourneyReport(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $report = $this->crossPlatformReportingService->generateCustomerJourneyReport(
            \Carbon\Carbon::parse($startDate),
            \Carbon\Carbon::parse($endDate)
        );

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }

    /**
     * Generate Cross-Platform Operational Efficiency Report
     */
    public function generateCrossPlatformOperationalEfficiencyReport(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $report = $this->crossPlatformReportingService->generateOperationalEfficiencyReport(
            \Carbon\Carbon::parse($startDate),
            \Carbon\Carbon::parse($endDate)
        );

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }

    /**
     * Generate Cross-Platform Risk Management Report
     */
    public function generateCrossPlatformRiskManagementReport(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $report = $this->crossPlatformReportingService->generateRiskManagementReport(
            \Carbon\Carbon::parse($startDate),
            \Carbon\Carbon::parse($endDate)
        );

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }
}