<?php

namespace App\Http\Controllers;

use App\Services\CryptoP2PReportingService;
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
use App\Models\AutomatedAlertReport;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CryptoP2PReportsController extends Controller
{
    protected $reportingService;

    public function __construct(CryptoP2PReportingService $reportingService)
    {
        $this->reportingService = $reportingService;
    }

    /**
     * Generate Balance Sheet Report
     */
    public function generateBalanceSheet(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $report = $this->reportingService->generateBalanceSheetReport(
            Carbon::parse($request->start_date),
            Carbon::parse($request->end_date)
        );

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }

    /**
     * Get Balance Sheet Reports
     */
    public function getBalanceSheets(Request $request)
    {
        $query = BalanceSheetReport::orderBy('created_at', 'desc');

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('period_start', [
                Carbon::parse($request->start_date),
                Carbon::parse($request->end_date)
            ]);
        }

        $reports = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $reports
        ]);
    }

    /**
     * Get a specific Balance Sheet Report
     */
    public function getBalanceSheet($id)
    {
        $report = BalanceSheetReport::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }

    /**
     * Generate Income Statement Report
     */
    public function generateIncomeStatement(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $report = $this->reportingService->generateIncomeStatementReport(
            Carbon::parse($request->start_date),
            Carbon::parse($request->end_date)
        );

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }

    /**
     * Get Income Statement Reports
     */
    public function getIncomeStatements(Request $request)
    {
        $query = IncomeStatementReport::orderBy('created_at', 'desc');

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('period_start', [
                Carbon::parse($request->start_date),
                Carbon::parse($request->end_date)
            ]);
        }

        $reports = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $reports
        ]);
    }

    /**
     * Get a specific Income Statement Report
     */
    public function getIncomeStatement($id)
    {
        $report = IncomeStatementReport::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }

    /**
     * Generate Daily Trading Report
     */
    public function generateDailyTradingReport(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
        ]);

        $report = $this->reportingService->generateDailyTradingReport(
            Carbon::parse($request->date)->toDateString()
        );

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }

    /**
     * Get Daily Trading Reports
     */
    public function getDailyTradingReports(Request $request)
    {
        $query = DailyTradingReport::orderBy('date', 'desc');

        if ($request->has('date')) {
            $query->whereDate('date', Carbon::parse($request->date));
        } elseif ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('date', [
                Carbon::parse($request->start_date),
                Carbon::parse($request->end_date)
            ]);
        }

        $reports = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $reports
        ]);
    }

    /**
     * Get a specific Daily Trading Report
     */
    public function getDailyTradingReport($id)
    {
        $report = DailyTradingReport::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }

    /**
     * Generate User Activity Report
     */
    public function generateUserActivityReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $report = $this->reportingService->generateUserActivityReport(
            Carbon::parse($request->start_date),
            Carbon::parse($request->end_date)
        );

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }

    /**
     * Get User Activity Reports
     */
    public function getUserActivityReports(Request $request)
    {
        $query = UserActivityReport::orderBy('created_at', 'desc');

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('period_start', [
                Carbon::parse($request->start_date),
                Carbon::parse($request->end_date)
            ]);
        }

        $reports = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $reports
        ]);
    }

    /**
     * Generate User Trade History Report
     */
    public function generateUserTradeHistoryReport(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $report = $this->reportingService->generateUserTradeHistoryReport(
            $request->user_id,
            Carbon::parse($request->start_date),
            Carbon::parse($request->end_date)
        );

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }

    /**
     * Get User Trade History Reports
     */
    public function getUserTradeHistoryReports(Request $request)
    {
        $query = UserTradeHistoryReport::orderBy('created_at', 'desc');

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('period_start', [
                Carbon::parse($request->start_date),
                Carbon::parse($request->end_date)
            ]);
        }

        $reports = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $reports
        ]);
    }

    /**
     * Get a specific User Trade History Report
     */
    public function getUserTradeHistoryReport($id)
    {
        $report = UserTradeHistoryReport::with('user')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }

    /**
     * Generate Cash Flow Report
     */
    public function generateCashFlowReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $report = $this->reportingService->generateCashFlowReport(
            Carbon::parse($request->start_date),
            Carbon::parse($request->end_date)
        );

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }

    /**
     * Get Cash Flow Reports
     */
    public function getCashFlowReports(Request $request)
    {
        $query = CashFlowReport::orderBy('created_at', 'desc');

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('period_start', [
                Carbon::parse($request->start_date),
                Carbon::parse($request->end_date)
            ]);
        }

        $reports = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $reports
        ]);
    }

    /**
     * Get a specific Cash Flow Report
     */
    public function getCashFlowReport($id)
    {
        $report = CashFlowReport::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }

    /**
     * Generate User Segmentation Report
     */
    public function generateUserSegmentationReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $report = $this->reportingService->generateUserSegmentationReport(
            Carbon::parse($request->start_date),
            Carbon::parse($request->end_date)
        );

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }

    /**
     * Get User Segmentation Reports
     */
    public function getUserSegmentationReports(Request $request)
    {
        $query = UserSegmentationReport::orderBy('created_at', 'desc');

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('period_start', [
                Carbon::parse($request->start_date),
                Carbon::parse($request->end_date)
            ]);
        }

        $reports = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $reports
        ]);
    }

    /**
     * Get a specific User Segmentation Report
     */
    public function getUserSegmentationReport($id)
    {
        $report = UserSegmentationReport::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }

    /**
     * Generate Security Report
     */
    public function generateSecurityReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $report = $this->reportingService->generateSecurityReport(
            Carbon::parse($request->start_date),
            Carbon::parse($request->end_date)
        );

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }

    /**
     * Get Security Reports
     */
    public function getSecurityReports(Request $request)
    {
        $query = SecurityReport::orderBy('created_at', 'desc');

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('period_start', [
                Carbon::parse($request->start_date),
                Carbon::parse($request->end_date)
            ]);
        }

        $reports = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $reports
        ]);
    }

    /**
     * Get a specific Security Report
     */
    public function getSecurityReport($id)
    {
        $report = SecurityReport::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }

    /**
     * Generate Market Risk Report
     */
    public function generateMarketRiskReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $report = $this->reportingService->generateMarketRiskReport(
            Carbon::parse($request->start_date),
            Carbon::parse($request->end_date)
        );

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }

    /**
     * Get Market Risk Reports
     */
    public function getMarketRiskReports(Request $request)
    {
        $query = MarketRiskReport::orderBy('created_at', 'desc');

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('period_start', [
                Carbon::parse($request->start_date),
                Carbon::parse($request->end_date)
            ]);
        }

        $reports = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $reports
        ]);
    }

    /**
     * Get a specific Market Risk Report
     */
    public function getMarketRiskReport($id)
    {
        $report = MarketRiskReport::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }

    /**
     * Generate AML/KYC Report
     */
    public function generateAmlKycReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $report = $this->reportingService->generateAmlKycReport(
            Carbon::parse($request->start_date),
            Carbon::parse($request->end_date)
        );

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }

    /**
     * Get AML/KYC Reports
     */
    public function getAmlKycReports(Request $request)
    {
        $query = AmlKycReport::orderBy('created_at', 'desc');

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('period_start', [
                Carbon::parse($request->start_date),
                Carbon::parse($request->end_date)
            ]);
        }

        $reports = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $reports
        ]);
    }

    /**
     * Get a specific AML/KYC Report
     */
    public function getAmlKycReport($id)
    {
        $report = AmlKycReport::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }

    /**
     * Generate Tax Report
     */
    public function generateTaxReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $report = $this->reportingService->generateTaxReport(
            Carbon::parse($request->start_date),
            Carbon::parse($request->end_date)
        );

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }

    /**
     * Get Tax Reports
     */
    public function getTaxReports(Request $request)
    {
        $query = TaxReport::orderBy('created_at', 'desc');

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('period_start', [
                Carbon::parse($request->start_date),
                Carbon::parse($request->end_date)
            ]);
        }

        $reports = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $reports
        ]);
    }

    /**
     * Get a specific Tax Report
     */
    public function getTaxReport($id)
    {
        $report = TaxReport::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }

    /**
     * Generate System Performance Report
     */
    public function generateSystemPerformanceReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $report = $this->reportingService->generateSystemPerformanceReport(
            Carbon::parse($request->start_date),
            Carbon::parse($request->end_date)
        );

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }

    /**
     * Get System Performance Reports
     */
    public function getSystemPerformanceReports(Request $request)
    {
        $query = SystemPerformanceReport::orderBy('created_at', 'desc');

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('period_start', [
                Carbon::parse($request->start_date),
                Carbon::parse($request->end_date)
            ]);
        }

        $reports = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $reports
        ]);
    }

    /**
     * Get a specific System Performance Report
     */
    public function getSystemPerformanceReport($id)
    {
        $report = SystemPerformanceReport::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }

    /**
     * Generate Customer Service Report
     */
    public function generateCustomerServiceReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $report = $this->reportingService->generateCustomerServiceReport(
            Carbon::parse($request->start_date),
            Carbon::parse($request->end_date)
        );

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }

    /**
     * Get Customer Service Reports
     */
    public function getCustomerServiceReports(Request $request)
    {
        $query = CustomerServiceReport::orderBy('created_at', 'desc');

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('period_start', [
                Carbon::parse($request->start_date),
                Carbon::parse($request->end_date)
            ]);
        }

        $reports = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $reports
        ]);
    }

    /**
     * Get a specific Customer Service Report
     */
    public function getCustomerServiceReport($id)
    {
        $report = CustomerServiceReport::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }

    /**
     * Generate General Ledger Report
     */
    public function generateGeneralLedgerReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $report = $this->reportingService->generateGeneralLedgerReport(
            Carbon::parse($request->start_date),
            Carbon::parse($request->end_date)
        );

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }

    /**
     * Get General Ledger Reports
     */
    public function getGeneralLedgerReports(Request $request)
    {
        $query = GeneralLedgerReport::orderBy('created_at', 'desc');

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('period_start', [
                Carbon::parse($request->start_date),
                Carbon::parse($request->end_date)
            ]);
        }

        $reports = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $reports
        ]);
    }

    /**
     * Get a specific General Ledger Report
     */
    public function getGeneralLedgerReport($id)
    {
        $report = GeneralLedgerReport::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }

    /**
     * Generate Revenue Recognition Report
     */
    public function generateRevenueRecognitionReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $report = $this->reportingService->generateRevenueRecognitionReport(
            Carbon::parse($request->start_date),
            Carbon::parse($request->end_date)
        );

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }

    /**
     * Get Revenue Recognition Reports
     */
    public function getRevenueRecognitionReports(Request $request)
    {
        $query = RevenueRecognitionReport::orderBy('created_at', 'desc');

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('period_start', [
                Carbon::parse($request->start_date),
                Carbon::parse($request->end_date)
            ]);
        }

        $reports = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $reports
        ]);
    }

    /**
     * Get a specific Revenue Recognition Report
     */
    public function getRevenueRecognitionReport($id)
    {
        $report = RevenueRecognitionReport::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }

    /**
     * Generate Predictive Analytics Report
     */
    public function generatePredictiveAnalyticsReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $report = $this->reportingService->generatePredictiveAnalyticsReport(
            Carbon::parse($request->start_date),
            Carbon::parse($request->end_date)
        );

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }

    /**
     * Get Predictive Analytics Reports
     */
    public function getPredictiveAnalyticsReports(Request $request)
    {
        $query = PredictiveAnalyticsReport::orderBy('created_at', 'desc');

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('period_start', [
                Carbon::parse($request->start_date),
                Carbon::parse($request->end_date)
            ]);
        }

        $reports = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $reports
        ]);
    }

    /**
     * Get a specific Predictive Analytics Report
     */
    public function getPredictiveAnalyticsReport($id)
    {
        $report = PredictiveAnalyticsReport::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }

    /**
     * Generate Performance Metrics Report
     */
    public function generatePerformanceMetricsReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $report = $this->reportingService->generatePerformanceMetricsReport(
            Carbon::parse($request->start_date),
            Carbon::parse($request->end_date)
        );

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }

    /**
     * Get Performance Metrics Reports
     */
    public function getPerformanceMetricsReports(Request $request)
    {
        $query = PerformanceMetricsReport::orderBy('created_at', 'desc');

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('period_start', [
                Carbon::parse($request->start_date),
                Carbon::parse($request->end_date)
            ]);
        }

        $reports = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $reports
        ]);
    }

    /**
     * Get a specific Performance Metrics Report
     */
    public function getPerformanceMetricsReport($id)
    {
        $report = PerformanceMetricsReport::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }

    /**
     * Get Live Dashboard Report
     */
    public function getLiveDashboardReport()
    {
        $report = $this->reportingService->getLiveDashboardReport();

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }

    /**
     * Get Automated Alert Reports
     */
    public function getAutomatedAlertReports(Request $request)
    {
        $query = AutomatedAlertReport::orderBy('created_at', 'desc');

        if ($request->has('alert_type')) {
            $query->where('alert_type', $request->alert_type);
        }

        if ($request->has('severity')) {
            $query->where('severity', $request->severity);
        }

        if ($request->has('is_resolved')) {
            $query->where('is_resolved', $request->is_resolved);
        }

        $reports = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $reports
        ]);
    }

    /**
     * Get a specific Automated Alert Report
     */
    public function getAutomatedAlertReport($id)
    {
        $report = AutomatedAlertReport::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }
}