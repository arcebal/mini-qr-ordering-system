@extends('admin.layouts.app')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
        <a href="{{ route('admin.orders.index') }}" class="text-decoration-none" style="color: var(--primary-dark);"><i class="bi bi-arrow-left me-1"></i>All orders</a>
        <h2 class="mt-2 mb-1">Order #{{ $order->order_number }}</h2>
        <p class="text-muted mb-0">Received {{ $order->created_at->format('M d, Y g:i A') }}</p>
    </div>
    <span class="order-status {{ $order->status }}">{{ str($order->status)->headline() }}</span>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <h3 class="h5 mb-3">Order Items</h3>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="table-dark">
                            <tr><th>Product</th><th>Unit Price</th><th>Quantity</th><th class="text-end">Subtotal</th></tr>
                        </thead>
                        <tbody>
                            @foreach ($order->items as $item)
                                <tr>
                                    <td>{{ $item->product->name }}</td>
                                    <td>₱ {{ number_format($item->unit_price, 2) }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td class="text-end">₱ {{ number_format($item->subtotal, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-end align-items-center gap-3 pt-3">
                    <span class="text-muted">Order Total</span>
                    <strong class="fs-5">₱ {{ number_format($order->total_amount, 2) }}</strong>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <h3 class="h5 mb-3">Customer</h3>
                <p class="mb-0"><i class="bi bi-person me-2"></i>{{ $order->customer_name }}</p>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-body">
                <h3 class="h5 mb-3">Payment</h3>
                <p class="mb-2"><span class="text-muted">Method:</span> {{ $order->payment_method === 'mock_online' ? 'Mock online payment' : 'Pay at counter' }}</p>
                <p class="mb-3"><span class="text-muted">Status:</span> <span class="payment-status {{ $order->payment_status }}">{{ str($order->payment_status)->headline() }}</span></p>
                @if ($order->payment_status !== 'paid' && $order->status !== 'deleted')
                    <form method="POST" action="{{ route('admin.orders.payment.update', $order) }}" data-swal-confirm data-swal-title="Mark payment as paid?" data-swal-text="This records the order payment as received." data-swal-confirm-text="Mark as paid">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="payment_status" value="paid">
                        <button class="btn btn-success w-100 mb-3"><i class="bi bi-check-circle me-1"></i>Mark as Paid</button>
                    </form>
                @endif
                <h3 class="h5 mb-3">Update Status</h3>
                @if (count($allowedStatuses))
                    <div class="d-grid gap-2">
                        @foreach ($allowedStatuses as $status)
                            <form method="POST" action="{{ route('admin.orders.update', $order) }}"
                                  data-swal-confirm
                                  data-swal-title="Mark order as {{ str($status)->headline() }}?"
                                  data-swal-text="This updates the order's current status."
                                  data-swal-confirm-text="Confirm">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="{{ $status }}">
                                <button class="btn btn-primary w-100">
                                    Mark as {{ str($status)->headline() }}
                                </button>
                            </form>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted mb-0">This order is {{ $order->status }} and can no longer be updated.</p>
                @endif
            </div>
        </div>

        <div class="card mt-4 border-danger-subtle">
            <div class="card-body">
                <h3 class="h5 mb-2">Delete Order</h3>
                <p class="text-muted small">This permanently removes the order and its items. This cannot be undone.</p>
                <form method="POST" action="{{ route('admin.orders.destroy', $order) }}"
                      data-swal-confirm
                      data-swal-title="Delete order #{{ $order->order_number }}?"
                      data-swal-text="This action permanently removes this order and cannot be undone."
                      data-swal-confirm-text="Delete order">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger w-100">Delete this order</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
