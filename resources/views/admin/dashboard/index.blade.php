@extends('admin.layouts.app')

@section('content')
@php($summary = $report['summary'])
<style>
    .report-chart { min-height: 310px; }
    .metric-today { background: #e6f0f7; color: #175a7a; }
    .metric-revenue { background: #e1f0e7; color: #1f6546; }
    .metric-queue { background: #fff3d8; color: #855b16; }
    .metric-stock { background: #f8e2e0; color: #9b3029; }
</style>

{{--<div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
        <h2 class="mb-1">Dashboard</h2>
        <p class="text-muted mb-0">Sales and kitchen activity for {{ $report['startDate']->format('M j, Y') }}–{{ $report['endDate']->format('M j, Y') }}.</p>
    </div>
    <a href="{{ route('admin.reports.index', request()->only(['start_date', 'end_date'])) }}" class="btn btn-secondary">Full reports</a>
</div>

<form method="GET" class="card mb-4">
    <div class="card-body py-3">
        <div class="row align-items-end g-3">
            <div class="col-sm-5 col-lg-3"><label class="form-label" for="start_date">From</label><input id="start_date" name="start_date" type="date" class="form-control" value="{{ $report['startDate']->toDateString() }}"></div>
            <div class="col-sm-5 col-lg-3"><label class="form-label" for="end_date">To</label><input id="end_date" name="end_date" type="date" class="form-control" value="{{ $report['endDate']->toDateString() }}"></div>
            <div class="col-sm-2 col-lg-auto"><button class="btn btn-primary w-100">Apply</button></div>
        </div>
    </div>
</form>
--}}
<div class="row g-3 mb-4">
    <div class="col-6 col-lg"><div class="card metric-card"><div class="card-body"><span class="metric-icon metric-today"><i class="bi bi-calendar-check"></i></span><p class="metric-label">Today's Orders</p><p class="metric-value">{{ $summary['todayOrders'] }}</p></div></div></div>
    <div class="col-6 col-lg"><div class="card metric-card"><div class="card-body"><span class="metric-icon metric-revenue"><i class="bi bi-cash-stack"></i></span><p class="metric-label">Today's Revenue</p><p class="metric-value">₱{{ number_format($summary['todayRevenue'], 0) }}</p></div></div></div>
    <div class="col-6 col-lg"><div class="card metric-card"><div class="card-body"><span class="metric-icon metric-revenue"><i class="bi bi-graph-up-arrow"></i></span><p class="metric-label">Monthly Revenue</p><p class="metric-value">₱{{ number_format($summary['monthRevenue'], 0) }}</p></div></div></div>
    <div class="col-6 col-lg"><div class="card metric-card"><div class="card-body"><span class="metric-icon metric-queue"><i class="bi bi-hourglass-split"></i></span><p class="metric-label">Kitchen Queue</p><p class="metric-value">{{ $summary['awaitingFulfilment'] }}</p></div></div></div>
    <div class="col-6 col-lg"><div class="card metric-card"><div class="card-body"><span class="metric-icon metric-stock"><i class="bi bi-box-seam"></i></span><p class="metric-label">Low Stock</p><p class="metric-value">{{ $summary['lowStockProducts'] }}</p></div></div></div>
</div>

<div class="row g-4">
    <div class="col-lg-8"><div class="card h-100"><div class="card-body"><h3 class="h5 mb-1">Daily Revenue</h3><p class="text-muted small mb-3">Completed orders in the selected period.</p><div class="report-chart"><canvas id="dailyRevenueChart"></canvas></div></div></div></div>
    <div class="col-lg-4"><div class="card h-100"><div class="card-body"><h3 class="h5 mb-1">Order Status</h3><p class="text-muted small mb-3">All received orders in the selected period.</p><div class="report-chart"><canvas id="orderStatusChart"></canvas></div></div></div></div>
    <div class="col-lg-6"><div class="card"><div class="card-body"><h3 class="h5 mb-1">Top Products</h3><p class="text-muted small mb-3">Most quantities sold from completed orders.</p><div class="report-chart"><canvas id="topProductsChart"></canvas></div></div></div></div>
    <div class="col-lg-6"><div class="card"><div class="card-body"><h3 class="h5 mb-1">Top Categories</h3><p class="text-muted small mb-3">Most quantities sold from completed orders.</p><div class="report-chart"><canvas id="topCategoriesChart"></canvas></div></div></div></div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const charts = @json($report['charts']);
    const statusColors = {
        accepted: '#2480a6',
        preparing: '#d18b20',
        completed: '#2f8058',
    };
    const currency = (value) => new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP', maximumFractionDigits: 0 }).format(value);
    const common = { responsive: true, maintainAspectRatio: false, plugins: { legend: { labels: { font: { family: 'Georgia' } } } } };

    new Chart(document.getElementById('dailyRevenueChart'), { type: 'line', data: { labels: charts.dailyRevenue.map(item => item.label), datasets: [{ label: 'Revenue', data: charts.dailyRevenue.map(item => item.revenue), borderColor: '#c8603d', backgroundColor: 'rgba(200, 96, 61, .18)', fill: true, tension: .35 }] }, options: { ...common, scales: { y: { beginAtZero: true, ticks: { callback: currency } } } } });
    new Chart(document.getElementById('orderStatusChart'), { type: 'doughnut', data: { labels: charts.orderStatuses.map(item => item.label), datasets: [{ data: charts.orderStatuses.map(item => item.count), backgroundColor: charts.orderStatuses.map(item => statusColors[item.status]) }] }, options: common });
    const barOptions = { ...common, indexAxis: 'y', plugins: { legend: { display: false } }, scales: { x: { beginAtZero: true, ticks: { precision: 0 } } } };
    new Chart(document.getElementById('topProductsChart'), { type: 'bar', data: { labels: charts.topProducts.map(item => item.name), datasets: [{ data: charts.topProducts.map(item => item.quantity), backgroundColor: '#c8603d', borderRadius: 6 }] }, options: barOptions });
    new Chart(document.getElementById('topCategoriesChart'), { type: 'bar', data: { labels: charts.topCategories.map(item => item.name), datasets: [{ data: charts.topCategories.map(item => item.quantity), backgroundColor: '#d97949', borderRadius: 6 }] }, options: barOptions });
});
</script>
@endpush
