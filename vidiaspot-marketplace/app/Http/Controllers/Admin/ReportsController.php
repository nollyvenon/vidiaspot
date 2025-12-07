<?php

namespace App\Http\Controllers\Admin;

use App\Models\Ad;
use App\Models\Vendor;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Payment;
use App\Models\Category;
use App\Models\FeaturedAd;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class ReportsController extends Controller
{
    /**
     * Display dashboard with analytics.
     */
    public function dashboard(): View
    {
        $this->checkAdminAccess();

        // Get basic counts
        $totalUsers = User::count();
        $totalVendors = Vendor::count();
        $totalAds = Ad::count();
        $totalCategories = Category::count();
        $totalSubscriptions = Subscription::count();
        $totalPayments = Payment::count();
        $totalFeaturedAds = FeaturedAd::count();
        
        // Get recent data
        $recentUsers = User::latest()->take(5)->get();
        $recentAds = Ad::with('user', 'category')->latest()->take(5)->get();
        $recentPayments = Payment::with('user', 'subscription', 'ad')->latest()->take(5)->get();
        
        // Monthly stats
        $monthlyStats = $this->getMonthlyStats();
        
        // Revenue stats
        $revenueStats = $this->getRevenueStats();

        return $this->adminView('admin.reports.dashboard', [
            'totalUsers' => $totalUsers,
            'totalVendors' => $totalVendors,
            'totalAds' => $totalAds,
            'totalCategories' => $totalCategories,
            'totalSubscriptions' => $totalSubscriptions,
            'totalPayments' => $totalPayments,
            'totalFeaturedAds' => $totalFeaturedAds,
            'recentUsers' => $recentUsers,
            'recentAds' => $recentAds,
            'recentPayments' => $recentPayments,
            'monthlyStats' => $monthlyStats,
            'revenueStats' => $revenueStats,
        ]);
    }

    /**
     * Get monthly stats.
     */
    private function getMonthlyStats()
    {
        $currentYear = now()->year;
        
        $stats = [];
        for ($month = 1; $month <= 12; $month++) {
            $startDate = now()->year($currentYear)->month($month)->startOfMonth();
            $endDate = now()->year($currentYear)->month($month)->endOfMonth();
            
            $stats[] = [
                'month' => $startDate->format('M'),
                'new_users' => User::whereBetween('created_at', [$startDate, $endDate])->count(),
                'new_ads' => Ad::whereBetween('created_at', [$startDate, $endDate])->count(),
                'new_payments' => Payment::whereBetween('created_at', [$startDate, $endDate])->count(),
                'total_revenue' => Payment::whereBetween('created_at', [$startDate, $endDate])
                    ->where('status', 'completed')
                    ->sum('amount'),
            ];
        }
        
        return $stats;
    }

    /**
     * Get revenue stats.
     */
    private function getRevenueStats()
    {
        return [
            'total_revenue' => Payment::where('status', 'completed')->sum('amount'),
            'monthly_revenue' => Payment::where('status', 'completed')
                ->whereMonth('created_at', now()->month)
                ->sum('amount'),
            'yearly_revenue' => Payment::where('status', 'completed')
                ->whereYear('created_at', now()->year)
                ->sum('amount'),
            'today_revenue' => Payment::where('status', 'completed')
                ->whereDate('created_at', today())
                ->sum('amount'),
        ];
    }

    /**
     * Get users report.
     */
    public function usersReport(Request $request): View
    {
        $this->checkAdminAccess();

        $query = User::with('roles');

        // Apply filters
        if ($request->filled('role')) {
            $query->whereHas('roles', function($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $users = $query->latest()->paginate(25);

        $roles = \App\Models\Role::all();

        return $this->adminView('admin.reports.users', [
            'users' => $users,
            'roles' => $roles,
        ]);
    }

    /**
     * Get vendors report.
     */
    public function vendorsReport(Request $request): View
    {
        $this->checkAdminAccess();

        $query = Vendor::with('user', 'country', 'state', 'city');

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $vendors = $query->latest()->paginate(25);

        return $this->adminView('admin.reports.vendors', [
            'vendors' => $vendors,
        ]);
    }

    /**
     * Get ads report.
     */
    public function adsReport(Request $request): View
    {
        $this->checkAdminAccess();

        $query = Ad::with('user', 'category');

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $ads = $query->latest()->paginate(25);

        $categories = Category::all();

        return $this->adminView('admin.reports.ads', [
            'ads' => $ads,
            'categories' => $categories,
        ]);
    }

    /**
     * Get payments report.
     */
    public function paymentsReport(Request $request): View
    {
        $this->checkAdminAccess();

        $query = Payment::with('user', 'subscription', 'ad');

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('gateway')) {
            $query->where('payment_gateway', $request->gateway);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $payments = $query->latest()->paginate(25);

        return $this->adminView('admin.reports.payments', [
            'payments' => $payments,
        ]);
    }

    /**
     * Get subscriptions report.
     */
    public function subscriptionsReport(Request $request): View
    {
        $this->checkAdminAccess();

        $query = Subscription::with('payments');

        // Apply filters
        if ($request->filled('active')) {
            $isActive = $request->active === 'yes';
            $query->where('is_active', $isActive);
        }

        $subscriptions = $query->orderBy('name')->paginate(25);

        return $this->adminView('admin.reports.subscriptions', [
            'subscriptions' => $subscriptions,
        ]);
    }

    /**
     * Get categories report.
     */
    public function categoriesReport(Request $request): View
    {
        $this->checkAdminAccess();

        $query = Category::with('ads');

        // Apply filters
        if ($request->filled('active')) {
            $isActive = $request->active === 'yes';
            $query->where('is_active', $isActive);
        }

        $categories = $query->orderBy('name')->paginate(25);

        return $this->adminView('admin.reports.categories', [
            'categories' => $categories,
        ]);
    }

    /**
     * Export report as CSV.
     */
    public function exportReport(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $this->checkAdminAccess();

        $type = $request->type;
        $filename = "report_{$type}_" . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        $callback = function() use ($type) {
            $file = fopen('php://output', 'w');
            
            // Write CSV headers based on report type
            switch ($type) {
                case 'users':
                    fputcsv($file, ['ID', 'Name', 'Email', 'Role', 'Verified', 'Created At']);
                    $data = User::with('roles')->get();
                    foreach ($data as $item) {
                        fputcsv($file, [
                            $item->id,
                            $item->name,
                            $item->email,
                            $item->roles->pluck('name')->join(', '),
                            $item->is_verified ? 'Yes' : 'No',
                            $item->created_at->format('Y-m-d H:i:s')
                        ]);
                    }
                    break;
                    
                case 'ads':
                    fputcsv($file, ['ID', 'Title', 'Category', 'User', 'Price', 'Status', 'Created At']);
                    $data = Ad::with('user', 'category')->get();
                    foreach ($data as $item) {
                        fputcsv($file, [
                            $item->id,
                            $item->title,
                            $item->category->name ?? 'N/A',
                            $item->user->name ?? 'N/A',
                            $item->price,
                            $item->status,
                            $item->created_at->format('Y-m-d H:i:s')
                        ]);
                    }
                    break;
                    
                case 'payments':
                    fputcsv($file, ['ID', 'Transaction ID', 'User', 'Amount', 'Gateway', 'Status', 'Created At']);
                    $data = Payment::with('user')->get();
                    foreach ($data as $item) {
                        fputcsv($file, [
                            $item->id,
                            $item->transaction_id,
                            $item->user->name ?? 'N/A',
                            $item->amount,
                            $item->payment_gateway,
                            $item->status,
                            $item->created_at->format('Y-m-d H:i:s')
                        ]);
                    }
                    break;
                    
                case 'vendors':
                    fputcsv($file, ['ID', 'Business Name', 'User', 'Status', 'Verified', 'Created At']);
                    $data = Vendor::with('user')->get();
                    foreach ($data as $item) {
                        fputcsv($file, [
                            $item->id,
                            $item->business_name,
                            $item->user->name ?? 'N/A',
                            $item->status,
                            $item->is_verified ? 'Yes' : 'No',
                            $item->created_at->format('Y-m-d H:i:s')
                        ]);
                    }
                    break;
                    
                default:
                    fputcsv($file, ['ID', 'Name', 'Created At']);
                    $data = Category::all();
                    foreach ($data as $item) {
                        fputcsv($file, [
                            $item->id,
                            $item->name,
                            $item->created_at->format('Y-m-d H:i:s')
                        ]);
                    }
                    break;
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}