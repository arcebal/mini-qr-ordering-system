@extends('customer.layouts.app')

@section('title', 'Order Status | Kusina ni Juan')

@section('content')
<main id="main-content" class="container py-5" style="max-width: 720px;">
    <div class="customer-card order-tracker">
        <div class="card-body p-4 p-md-5">
            <div class="d-flex justify-content-between align-items-start gap-3 mb-4">
                <div>
                    <p class="eyebrow text-uppercase mb-2">Order #{{ $order->order_number }}</p>
                    <h1 class="mb-2">Thanks, {{ $order->customer_name }}.</h1>
                    <p class="text-muted mb-0">The restaurant has received your order. This page updates automatically.</p>
                </div>
                <span id="status-badge" class="status-badge status-{{ $order->status }}">{{ str($order->status)->headline() }}</span>
            </div>

            <div id="deleted-notice" class="order-deleted-notice {{ $order->status === 'deleted' ? '' : 'd-none' }}" role="alert">
                <i class="bi bi-exclamation-circle"></i>
                <div><strong>This order has been deleted.</strong><br>Please speak with the cashier if you need assistance.</div>
            </div>

            <ol class="status-timeline" aria-label="Order progress">
                <li data-status="accepted" class="{{ in_array($order->status, ['accepted', 'preparing', 'completed']) ? 'is-complete' : '' }}">
                    <span class="timeline-icon"><i class="bi bi-check-lg"></i></span>
                    <div><strong>Accepted</strong><p>Your order is in the restaurant’s queue. Please go to the cashier and pay for your order.</p></div>
                </li>
                <li data-status="preparing" class="{{ in_array($order->status, ['preparing', 'completed']) ? 'is-complete' : '' }}">
                    <span class="timeline-icon"><i class="bi bi-fire"></i></span>
                    <div><strong>Preparing</strong><p>The kitchen is preparing your order.</p></div>
                </li>
                <li data-status="completed" class="{{ $order->status === 'completed' ? 'is-complete' : '' }}">
                    <span class="timeline-icon"><i class="bi bi-bag-check"></i></span>
                    <div><strong>Completed</strong><p>Your order is ready. Please collect it from the counter.</p></div>
                </li>
            </ol>

            <div class="tracker-footer">
                <span id="status-message" class="text-muted small">Last checked just now</span>
                <button id="refresh-status" class="btn btn-outline-rust" type="button"><i class="bi bi-arrow-clockwise me-1"></i>Refresh now</button>
            </div>
        </div>
    </div>
    <div class="text-center mt-4"><a href="{{ route('customer.menu') }}" class="text-decoration-none" style="color: var(--primary);">Start a new order</a></div>
</main>
@endsection

@push('scripts')
<script>
    const statusUrl = @json(route('customer.order-status', $order));
    const statuses = ['accepted', 'preparing', 'completed'];
    const badge = document.getElementById('status-badge');
    const message = document.getElementById('status-message');
    const refreshButton = document.getElementById('refresh-status');
    const deletedNotice = document.getElementById('deleted-notice');

    function updateTracker(status, label) {
        const currentIndex = statuses.indexOf(status);
        document.querySelectorAll('.status-timeline li').forEach((item) => {
            item.classList.toggle('is-complete', currentIndex >= 0 && statuses.indexOf(item.dataset.status) <= currentIndex);
        });
        badge.textContent = label;
        badge.className = `status-badge status-${status}`;
        deletedNotice.classList.toggle('d-none', status !== 'deleted');
    }

    async function refreshStatus() {
        refreshButton.disabled = true;
        try {
            const response = await fetch(statusUrl, { headers: { Accept: 'application/json' }, cache: 'no-store' });
            if (!response.ok) throw new Error('Unable to refresh status');
            const data = await response.json();
            updateTracker(data.status, data.label);
            message.textContent = `Last checked ${new Date().toLocaleTimeString([], { hour: 'numeric', minute: '2-digit' })}`;
        } catch (error) {
            message.textContent = 'Could not refresh right now. Please try again.';
        } finally {
            refreshButton.disabled = false;
        }
    }

    refreshButton.addEventListener('click', refreshStatus);
    setInterval(refreshStatus, 10000);
</script>
@endpush
