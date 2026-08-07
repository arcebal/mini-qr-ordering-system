<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;

class RestaurantReportService
{
    public const LOW_STOCK_THRESHOLD = 5;

    /**
     * Build all metrics needed by the dashboard and downloadable reports.
     *
     * @return array<string, mixed>
     */
    public function build(Carbon $startDate, Carbon $endDate): array
    {
        $completedOrders = Order::query()
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()]);

        $revenueByDate = (clone $completedOrders)
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as revenue')
            ->groupBy('date')
            ->pluck('revenue', 'date');

        $dailyRevenue = collect(CarbonPeriod::create($startDate, $endDate))
            ->map(fn (Carbon $date) => [
                'date' => $date->toDateString(),
                'label' => $date->format('M j'),
                'revenue' => (float) ($revenueByDate[$date->toDateString()] ?? 0),
            ])
            ->values();

        $statuses = ['accepted', 'preparing', 'completed'];
        $statusCounts = Order::query()
            ->where('status', '!=', 'deleted')
            ->whereBetween('created_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()])
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $ordersInPeriod = Order::query()
            ->where('status', '!=', 'deleted')
            ->whereBetween('created_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()]);

        $topProducts = $this->salesItems($startDate, $endDate)
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->selectRaw('products.name, SUM(order_items.quantity) as quantity')
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('quantity')
            ->limit(5)
            ->get()
            ->map(fn ($row) => ['name' => $row->name, 'quantity' => (int) $row->quantity]);

        $topCategories = $this->salesItems($startDate, $endDate)
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->selectRaw('categories.name, SUM(order_items.quantity) as quantity')
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('quantity')
            ->limit(5)
            ->get()
            ->map(fn ($row) => ['name' => $row->name, 'quantity' => (int) $row->quantity]);

        return [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'summary' => [
                'todayOrders' => Order::query()->where('status', 'completed')->whereDate('created_at', today())->count(),
                'todayRevenue' => (float) Order::query()->where('status', 'completed')->whereDate('created_at', today())->sum('total_amount'),
                'monthRevenue' => (float) Order::query()->where('status', 'completed')->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->sum('total_amount'),
                'awaitingFulfilment' => Order::query()->whereIn('status', ['accepted', 'preparing'])->count(),
                'lowStockProducts' => Product::query()->where('stock', '<=', self::LOW_STOCK_THRESHOLD)->count(),
                'periodOrders' => (clone $ordersInPeriod)->count(),
                'periodCompletedOrders' => (clone $completedOrders)->count(),
                'periodRevenue' => (float) (clone $completedOrders)->sum('total_amount'),
            ],
            'charts' => [
                'dailyRevenue' => $dailyRevenue,
                'orderStatuses' => collect($statuses)->map(fn (string $status) => [
                    'status' => $status,
                    'label' => str($status)->headline()->toString(),
                    'count' => (int) ($statusCounts[$status] ?? 0),
                ])->values(),
                'topProducts' => $topProducts,
                'topCategories' => $topCategories,
            ],
            'orders' => (clone $ordersInPeriod)
                ->with('items.product.category')
                ->latest()
                ->get(),
        ];
    }

    private function salesItems(Carbon $startDate, Carbon $endDate)
    {
        return OrderItem::query()
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'completed')
            ->whereBetween('orders.created_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()]);
    }
}
