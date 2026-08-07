@extends('customer.layouts.app')

@section('title', 'Your Cart | Kusina ni Juan')

@section('content')
<main id="main-content" class="container py-4 py-md-5">
    <div class="cart-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 mb-1">Your Cart</h1>
            <p class="text-muted mb-0">Review your order before checkout.</p>
        </div>
        <a href="{{ route('customer.menu') }}" class="btn btn-outline-rust">Continue browsing</a>
    </div>

    @if ($items->isEmpty())
        <div class="customer-card empty-state">
            <i class="bi bi-bag-x"></i>
            <h2 class="h3 mt-3">Your cart is empty</h2>
            <p class="text-muted">Add something delicious from the menu to get started.</p>
            <a href="{{ route('customer.menu') }}" class="btn btn-copper mt-2">View menu</a>
        </div>
    @else
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="customer-card">
                    <div class="card-body">
                        @foreach ($items as $item)
                            <div class="d-flex gap-3 {{ ! $loop->last ? 'border-bottom pb-3 mb-3' : '' }}">
                                @if ($item->product->image)
                                    <img src="{{ asset('storage/'.$item->product->image) }}" alt="{{ $item->product->name }}" class="cart-image">
                                @else
                                    <div class="cart-image d-grid place-items-center"><i class="bi bi-cup-hot fs-3 text-muted"></i></div>
                                @endif
                                <div class="cart-item-content flex-grow-1">
                                    <h2 class="h5 mb-1">{{ $item->product->name }}</h2>
                                    <p class="text-muted small mb-2">₱ {{ number_format($item->product->price, 2) }} each</p>
                                    <div class="cart-item-controls d-flex flex-wrap justify-content-between align-items-center gap-2">
                                        <div class="quantity-control">
                                            <form method="POST" action="{{ route('customer.cart.update', $item->product) }}">
                                                @csrf @method('PATCH')
                                                <button name="operation" value="decrease" aria-label="Decrease quantity"><i class="bi bi-dash"></i></button>
                                            </form>
                                            <span class="quantity-value">{{ $item->quantity }}</span>
                                            <form method="POST" action="{{ route('customer.cart.update', $item->product) }}">
                                                @csrf @method('PATCH')
                                                <button name="operation" value="increase" aria-label="Increase quantity"><i class="bi bi-plus"></i></button>
                                            </form>
                                        </div>
                                        <div class="cart-item-total d-flex align-items-center gap-3">
                                            <strong>₱ {{ number_format($item->subtotal, 2) }}</strong>
                                            <form method="POST" action="{{ route('customer.cart.remove', $item->product) }}">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-link p-0 text-danger small">Remove</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <aside class="customer-card">
                    <div class="card-body">
                        <h2 class="h4 mb-3">Order Summary</h2>
                        <div class="d-flex justify-content-between border-bottom pb-3 mb-3">
                            <span>Subtotal</span><strong>₱ {{ number_format($subtotal, 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <span>Total</span><span class="summary-total">₱ {{ number_format($subtotal, 2) }}</span>
                        </div>
                        <a href="{{ route('customer.checkout') }}" class="btn btn-copper w-100 py-2">Proceed to checkout</a>
                    </div>
                </aside>
            </div>
        </div>
    @endif
</main>
@endsection
