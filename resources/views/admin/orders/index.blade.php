@extends('admin.layouts.app')

@section('content')
<div class="page-heading d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Orders</h2>
        <p class="text-muted mb-0">Track and manage incoming customer orders.</p>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="desktop-data-table table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Order</th>
                        <th>Customer</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Payment</th>
                        <th>Received</th>
                        <th width="110">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr>
                            <td class="fw-bold">#{{ $order->order_number }}</td>
                            <td>{{ $order->customer_name }}</td>
                            <td>{{ $order->items_count }}</td>
                            <td>₱ {{ number_format($order->total_amount, 2) }}</td>
                            <td><span class="order-status {{ $order->status }}">{{ str($order->status)->headline() }}</span></td>
                            <td><span class="payment-status {{ $order->payment_status }}">{{ str($order->payment_status)->headline() }}</span></td>
                            <td>{{ $order->created_at->format('M d, Y g:i A') }}</td>
                            <td><a href="{{ route('admin.orders.show', $order) }}" class="btn btn-warning btn-sm">View</a></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">No customer orders have been received yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mobile-data-list">
            @forelse ($orders as $order)
                <article class="mobile-data-card card border shadow-sm p-3">
                    <div class="d-flex justify-content-between gap-3 mb-2"><strong>#{{ $order->order_number }}</strong><span class="order-status {{ $order->status }}">{{ str($order->status)->headline() }}</span></div>
                    <div class="d-flex justify-content-between gap-3 small mb-1"><span class="data-card-label">Customer</span><span class="text-end text-break">{{ $order->customer_name }}</span></div>
                    <div class="d-flex justify-content-between gap-3 small mb-1"><span class="data-card-label">Items</span><span>{{ $order->items_count }}</span></div>
                    <div class="d-flex justify-content-between gap-3 small mb-3"><span class="data-card-label">Total</span><strong>₱ {{ number_format($order->total_amount, 2) }}</strong></div>
                    <div class="d-flex justify-content-between gap-3 small mb-3"><span class="data-card-label">Payment</span><span class="payment-status {{ $order->payment_status }}">{{ str($order->payment_status)->headline() }}</span></div>
                    <div class="d-flex justify-content-between align-items-center gap-2"><small class="text-muted">{{ $order->created_at->format('M d, Y g:i A') }}</small><a href="{{ route('admin.orders.show', $order) }}" class="btn btn-warning btn-sm">View order</a></div>
                </article>
            @empty
                <p class="text-center text-muted py-3 mb-0">No customer orders have been received yet.</p>
            @endforelse
        </div>

        <div class="mt-3">{{ $orders->links() }}</div>
    </div>
</div>
@endsection
