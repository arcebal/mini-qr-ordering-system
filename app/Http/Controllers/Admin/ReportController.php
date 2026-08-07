<?php

namespace App\Http\Controllers\Admin;

use App\Exports\RestaurantReportExport;
use App\Http\Controllers\Controller;
use App\Services\RestaurantReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function __construct(private readonly RestaurantReportService $reports)
    {
    }

    public function index(Request $request)
    {
        $report = $this->reports->build(...$this->dates($request));

        return view('admin.reports.index', compact('report'));
    }

    public function pdf(Request $request)
    {
        $report = $this->reports->build(...$this->dates($request));
        $filename = 'restaurant-report-'.$report['startDate']->format('Ymd').'-'.$report['endDate']->format('Ymd').'.pdf';

        return Pdf::loadView('admin.reports.pdf', compact('report'))
            ->setPaper('a4', 'portrait')
            ->download($filename);
    }

    public function excel(Request $request)
    {
        $report = $this->reports->build(...$this->dates($request));
        $filename = 'restaurant-report-'.$report['startDate']->format('Ymd').'-'.$report['endDate']->format('Ymd').'.xlsx';

        return Excel::download(new RestaurantReportExport($report), $filename);
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
