<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class RestaurantReportExport implements WithMultipleSheets
{
    /** @param array<string, mixed> $report */
    public function __construct(private readonly array $report)
    {
    }

    public function sheets(): array
    {
        $summary = $this->report['summary'];
        $charts = $this->report['charts'];

        return [
            new ReportSheet('Summary', ['Metric', 'Value'], [
                ['Period', $this->report['startDate']->format('F j, Y').' - '.$this->report['endDate']->format('F j, Y')],
                ['Orders received', $summary['periodOrders']],
                ['Completed orders', $summary['periodCompletedOrders']],
                ['Completed revenue', $summary['periodRevenue']],
                ['Today completed orders', $summary['todayOrders']],
                ['Today completed revenue', $summary['todayRevenue']],
                ['Current-month revenue', $summary['monthRevenue']],
                ['Orders awaiting fulfilment', $summary['awaitingFulfilment']],
                ['Low-stock products', $summary['lowStockProducts']],
            ]),
            new ReportSheet('Orders', ['Order Number', 'Customer', 'Order Date', 'Status', 'Total'], $this->report['orders']
                ->map(fn ($order) => [$order->order_number, $order->customer_name, $order->created_at->format('F j, Y g:i A'), str($order->status)->headline()->toString(), (float) $order->total_amount])
                ->all()),
            new ReportSheet('Top Products', ['Product', 'Quantity Sold'], $charts['topProducts']->map(fn ($item) => [$item['name'], $item['quantity']])->all()),
            new ReportSheet('Top Categories', ['Category', 'Quantity Sold'], $charts['topCategories']->map(fn ($item) => [$item['name'], $item['quantity']])->all()),
        ];
    }
}
