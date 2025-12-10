<?php

namespace App\Http\Controllers;

use App\Models\TradingPortfolio;
use App\Models\TradingTransaction;
use App\Services\TradingPortfolioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TradingController extends Controller
{
    protected $tradingService;

    public function __construct(TradingPortfolioService $tradingService)
    {
        $this->tradingService = $tradingService;
        $this->middleware('auth');
    }

    /**
     * Display a listing of the user's trading portfolios.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $portfolios = TradingPortfolio::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('trading.portfolios.index', compact('portfolios'));
    }

    /**
     * Show the form for creating a new trading portfolio.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('trading.portfolios.create');
    }

    /**
     * Store a newly created trading portfolio in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:crypto,stocks,forex,commodities,hybrid',
            'strategy' => 'required|in:long_term,day_trading,swing_trading,scalping,hodling,arbitrage,algorithmic,social_trading,copy_trading',
            'initial_capital' => 'required|numeric|min:0',
            'auto_rebalance' => 'boolean',
            'rebalance_threshold' => 'numeric|min:0|max:100',
        ]);

        $portfolio = $this->tradingService->createPortfolio([
            'name' => $request->name,
            'description' => $request->description,
            'type' => $request->type,
            'strategy' => $request->strategy,
            'initial_capital' => $request->initial_capital,
            'auto_rebalance' => $request->auto_rebalance,
            'rebalance_threshold' => $request->rebalance_threshold ?? 5.00,
        ], Auth::id());

        return redirect()->route('trading.portfolios.show', $portfolio->id)
            ->with('success', 'Trading portfolio created successfully!');
    }

    /**
     * Display the specified trading portfolio.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $portfolio = TradingPortfolio::where('id', $id)
            ->where('user_id', Auth::id())
            ->with(['allocations', 'transactions'])
            ->firstOrFail();

        $performanceMetrics = $this->tradingService->getPerformanceMetrics($portfolio);

        return view('trading.portfolios.show', compact('portfolio', 'performanceMetrics'));
    }

    /**
     * Show the form for adding a transaction to a portfolio.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function addTransaction($id)
    {
        $portfolio = TradingPortfolio::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('trading.transactions.create', compact('portfolio'));
    }

    /**
     * Store a newly created transaction for a portfolio.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function storeTransaction(Request $request, $id)
    {
        $request->validate([
            'asset_symbol' => 'required|string|max:20',
            'asset_name' => 'required|string|max:255',
            'transaction_type' => 'required|in:buy,sell,transfer,dividend,airdrop,fork',
            'order_type' => 'required|in:market,limit,stop_loss,take_profit,oco,iceberg',
            'quantity' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'fee' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $portfolio = TradingPortfolio::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $transaction = $this->tradingService->addTransaction([
            'asset_symbol' => $request->asset_symbol,
            'asset_name' => $request->asset_name,
            'transaction_type' => $request->transaction_type,
            'order_type' => $request->order_type,
            'quantity' => $request->quantity,
            'price' => $request->price,
            'fee' => $request->fee ?? 0,
            'notes' => $request->notes ?? null,
        ], $portfolio);

        return redirect()->route('trading.portfolios.show', $portfolio->id)
            ->with('success', 'Transaction added successfully!');
    }

    /**
     * Display trading analytics dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function analytics()
    {
        $userPortfolios = TradingPortfolio::where('user_id', Auth::id())
            ->get();

        $totalValue = $userPortfolios->sum('current_value');
        $totalPnL = $userPortfolios->sum('total_profit_loss');
        $totalTrades = TradingTransaction::where('user_id', Auth::id())->count();

        return view('trading.analytics.dashboard', compact(
            'userPortfolios',
            'totalValue',
            'totalPnL',
            'totalTrades'
        ));
    }
}