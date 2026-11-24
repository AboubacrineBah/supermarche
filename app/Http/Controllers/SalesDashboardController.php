<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class SalesDashboardController extends Controller
{
    public function index(): View
    {
        Gate::authorize('reports.view');

        $today = now()->startOfDay();
        $weekStart = now()->startOfWeek();
        $monthStart = now()->startOfMonth();

        $stats = [
            'today_sales' => Sale::query()->where('sold_at', '>=', $today)->count(),
            'today_revenue' => (int) Sale::query()->where('sold_at', '>=', $today)->sum('total'),
            'week_revenue' => (int) Sale::query()->where('sold_at', '>=', $weekStart)->sum('total'),
            'month_revenue' => (int) Sale::query()->where('sold_at', '>=', $monthStart)->sum('total'),
            'total_sales' => Sale::query()->count(),
            'total_revenue' => (int) Sale::query()->sum('total'),
            'credit_outstanding' => (int) Customer::query()->sum('credit_balance'),
        ];

        $topProducts = SaleItem::query()
            ->select('product_id', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(line_total) as total_revenue'))
            ->groupBy('product_id')
            ->orderByDesc('total_revenue')
            ->take(10)
            ->with('product')
            ->get();

        $recentSales = Sale::query()
            ->with(['user', 'customer', 'items'])
            ->latest('sold_at')
            ->take(15)
            ->get();

        $dailyRevenue = Sale::query()
            ->select(DB::raw('DATE(sold_at) as day'), DB::raw('SUM(total) as revenue'), DB::raw('COUNT(*) as count'))
            ->where('sold_at', '>=', now()->subDays(14))
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        $paymentMethods = Sale::query()
            ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(total) as revenue'))
            ->groupBy('payment_method')
            ->get();

        return view('sales-dashboard.index', compact(
            'stats',
            'topProducts',
            'recentSales',
            'dailyRevenue',
            'paymentMethods',
        ));
    }
}
