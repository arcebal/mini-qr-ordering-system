@extends('customer.layouts.app')

@section('title', 'Welcome | Kusina Ni Aira')

@section('content')
<main id="main-content" class="container py-5" style="max-width: 620px;">
    <div class="customer-card welcome-card">
        <div class="card-body p-4 p-md-5">
            <span class="welcome-mark"><img src="{{ asset('images/kusina-logo.png') }}" alt="Kusina Ni Aira"></span>
            <p class="eyebrow text-uppercase mt-4 mb-2">Welcome to Kusina Ni Aira</p>
            <h1 class="mb-2">Let’s start your order.</h1>
            <p class="text-muted mb-4">Enter your name so the restaurant can identify your order and keep you updated.</p>

            <form method="POST" action="{{ route('customer.start.store') }}">
                @csrf
                <label for="customer_name" class="form-label fw-bold">Your name</label>
                <input id="customer_name" name="customer_name" type="text" class="form-control @error('customer_name') is-invalid @enderror" value="{{ old('customer_name') }}" placeholder="e.g., Juan Dela Cruz" autocomplete="name" required autofocus>
                @error('customer_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <button class="btn btn-copper w-100 mt-4 py-2">Continue to menu <i class="bi bi-arrow-right ms-1"></i></button>
            </form>
        </div>
    </div>
</main>
@endsection
