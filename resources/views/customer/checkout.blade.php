@extends('customer.layouts.app')

@section('title', 'Checkout | Kusina Ni Aira')

@section('content')
<main id="main-content" class="container py-4 py-md-5" style="max-width: 760px;">
    <div class="mb-4">
        <a href="{{ route('customer.cart.index') }}" class="text-decoration-none" style="color: var(--primary);"><i class="bi bi-arrow-left me-1"></i>Back to cart</a>
        <h1 class="h2 mt-3 mb-1">Checkout</h1>
        <p class="text-muted mb-0">Just one more detail and we’ll receive your order.</p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <div class="customer-card">
        <div class="card-body">
            <h2 class="h4 mb-3">Order total <span class="float-end summary-total">₱ {{ number_format($subtotal, 2) }}</span></h2>
            <ul class="list-unstyled border-top pt-3 mb-4">
                @foreach ($items as $item)
                    <li class="d-flex justify-content-between mb-2"><span>{{ $item->quantity }} × {{ $item->product->name }}</span><span>₱ {{ number_format($item->subtotal, 2) }}</span></li>
                @endforeach
            </ul>

            <div class="customer-identity mb-4">
                <i class="bi bi-person-circle"></i>
                <span>Ordering as <strong>{{ session('customer_name') }}</strong></span>
            </div>
            <form method="POST" action="{{ route('customer.checkout.place') }}">
                @csrf
                <button class="btn btn-copper w-100 py-2">Place Order <i class="bi bi-arrow-right ms-1"></i></button>
            </form>
        </div>
    </div>
</main>
@endsection
