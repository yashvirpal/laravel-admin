<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\Transaction;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // --- Basic Counts ---
        $totalUsers = User::count();
        $totalProducts = Product::count();
        $totalOrders = Order::count();
        $totalSales = Transaction::where('status', 'success')->sum('amount');

        // --- This Month’s Data ---
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();

        $monthlyOrders = Order::whereBetween('created_at', [$monthStart, $monthEnd])->count();
        $monthlySales = Transaction::where('status', 'success')
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->sum('amount');

        // --- Recent Activity ---
        $latestUsers = User::latest()->take(5)->get(['id', 'name', 'email', 'created_at']);
        $latestOrders = Order::latest()->take(5)->get(['id', 'order_number', 'total', 'status', 'created_at']);

        // --- Chart Data (Last 7 days sales) ---
        $salesData = Transaction::selectRaw('DATE(created_at) as date, SUM(amount) as total')
            ->where('status', 'success')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->take(7)
            ->get();

        $chartLabels = $salesData->pluck('date')->map(fn($d) => Carbon::parse($d)->format('d M'))->toArray();
        $chartValues = $salesData->pluck('total')->toArray();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalProducts',
            'totalOrders',
            'totalSales',
            'monthlyOrders',
            'monthlySales',
            'latestUsers',
            'latestOrders',
            'chartLabels',
            'chartValues'
        ));
    }
}
