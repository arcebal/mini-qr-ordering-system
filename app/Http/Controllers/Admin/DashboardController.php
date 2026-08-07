<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Services\RestaurantReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DashboardController extends Controller
{
    public function __construct(private readonly RestaurantReportService $reports)
    {
    }

    public function index(Request $request)
    {
        $totalCategories = Category::count();

        $totalProducts = Product::count();

        $totalOrders = Order::count();

        [$startDate, $endDate] = $this->dates($request);
        $report = $this->reports->build($startDate, $endDate);

        return view('admin.dashboard.index', compact(
            'totalCategories',
            'totalProducts',
            'totalOrders',
            'report'
        ));
    }

    /** @return array{Carbon, Carbon} */
    private function dates(Request $request): array
    {
        Validator::make($request->query(), [
            'start_date' => ['nullable', 'date_format:Y-m-d', 'before_or_equal:end_date'],
            'end_date' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:start_date'],
        ])->validate();

        $endDate = $request->filled('end_date')
            ? Carbon::createFromFormat('Y-m-d', $request->query('end_date'))
            : today();
        $startDate = $request->filled('start_date')
            ? Carbon::createFromFormat('Y-m-d', $request->query('start_date'))
            : $endDate->copy()->subDays(29);

        return [$startDate, $endDate];
    }
}
