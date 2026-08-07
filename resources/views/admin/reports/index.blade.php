@extends('admin.layouts.app')

@section('content')
@php($summary = $report['summary'])
<div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div><h2 class="mb-1">Reports</h2><p class="text-muted mb-0">Download completed-order performance for a selected period.</p></div>
    <a href="{{ route('admin.dashboard', request()->only(['start_date', 'end_date'])) }}" class="btn btn-secondary">Dashboard charts</a>
</div>

<form method="GET" class="card mb-4"><div class="card-body"><div class="row align-items-end g-3"><div class="col-md-4"><label class="form-label" for="start_date">From</label><input id="start_date" name="start_date" type="date" class="form-control" value="{{ $report['startDate']->toDateString() }}" aria-describedby="start-date-format"><small id="start-date-format" class="form-text text-muted">Selected: {{ $report['startDate']->format('F j, Y') }}</small></div><div class="col-md-4"><label class="form-label" for="end_date">To</label><input id="end_date" name="end_date" type="date" class="form-control" value="{{ $report['endDate']->toDateString() }}" aria-describedby="end-date-format"><small id="end-date-format" class="form-text text-muted">Selected: {{ $report['endDate']->format('F j, Y') }}</small></div><div class="col-md-auto"><button class="btn btn-primary w-100">Apply range</button></div></div></div></form>

<div class="card mb-4"><div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3"><div><h3 class="h5 mb-1">{{ $report['startDate']->format('F j, Y') }} – {{ $report['endDate']->format('F j, Y') }}</h3><p class="text-muted mb-0">{{ $summary['periodOrders'] }} order{{ $summary['periodOrders'] === 1 ? '' : 's' }} received · {{ $summary['periodCompletedOrders'] }} completed · ₱ {{ number_format($summary['periodRevenue'], 2) }} completed revenue</p></div><div class="d-flex gap-2"><a class="btn btn-secondary" href="{{ route('admin.reports.pdf', request()->only(['start_date', 'end_date'])) }}">PDF</a><a class="btn btn-primary" href="{{ route('admin.reports.excel', request()->only(['start_date', 'end_date'])) }}">Excel</a></div></div></div>

<div class="card"><div class="card-body"><h3 class="h5 mb-3">Orders in Selected Period</h3><div class="table-responsive"><table class="table table-hover align-middle"><thead class="table-dark"><tr><th>Order</th><th>Customer</th><th>Order Date</th><th>Status</th><th class="text-end">Total</th></tr></thead><tbody>@forelse($report['orders'] as $order)<tr><td class="fw-bold">#{{ $order->order_number }}</td><td>{{ $order->customer_name }}</td><td>{{ $order->created_at->format('F j, Y g:i A') }}</td><td><span class="order-status {{ $order->status }}">{{ str($order->status)->headline() }}</span></td><td class="text-end">₱ {{ number_format($order->total_amount, 2) }}</td></tr>@empty<tr><td colspan="5" class="text-center py-5 text-muted">No orders were received in this period.</td></tr>@endforelse</tbody></table></div></div></div>
@endsection
