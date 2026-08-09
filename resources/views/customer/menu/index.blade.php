@extends('customer.layouts.app')

@section('title', 'Menu | Kusina Ni Aira')

@section('content')
<header class="hero">
    <div class="container">
        <p class="eyebrow text-uppercase mb-2">Freshly prepared for you</p>
        <h1>Our Menu</h1>
        <p>Choose your favourites, then place your order in a few simple steps.</p>
    </div>
</header>

<main id="main-content" class="container pb-5">
    @if ($selectedCategory)
        <nav class="category-tabs" aria-label="Menu categories">
            @foreach ($categories as $category)
                <a href="{{ route('customer.menu', ['category' => $category->id]) }}"
                   class="category-tab {{ $category->is($selectedCategory) ? 'active' : '' }}"
                   @if ($category->is($selectedCategory)) aria-current="page" @endif>
                    {{ $category->name }}
                </a>
            @endforeach
        </nav>

        <section>
            <h2 class="section-title">{{ $selectedCategory->name }}</h2>
            @if ($selectedCategory->description)
                <p class="text-muted mb-3">{{ $selectedCategory->description }}</p>
            @endif
            <div class="row g-3">
                @foreach ($selectedCategory->products as $product)
                    @php($orderable = $product->is_available && $product->stock > 0)
                    <div class="col-12 col-sm-6 col-lg-4">
                        <article class="menu-card">
                            @if ($product->image)
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="menu-image">
                            @else
                                <div class="image-placeholder"><i class="bi bi-cup-hot"></i></div>
                            @endif
                            <div class="card-body">
                                <h3>{{ $product->name }}</h3>
                                <p class="product-description">{{ $product->description ?: 'Freshly prepared and ready to order.' }}</p>
                                <p class="price">₱ {{ number_format($product->price, 2) }}</p>
                                <div class="mt-auto">
                                    @if ($orderable)
                                        <form method="POST" action="{{ route('customer.cart.add', $product) }}">
                                            @csrf
                                            <button class="btn btn-copper w-100"><i class="bi bi-plus-lg me-1"></i>Add to cart</button>
                                        </form>
                                    @elseif ($product->stock <= 0)
                                        <span class="status-badge status-out">Out of Stock</span>
                                    @else
                                        <span class="status-badge status-unavailable">Currently Unavailable</span>
                                    @endif
                                </div>
                            </div>
                        </article>
                    </div>
                @endforeach
            </div>
        </section>
    @else
        <div class="customer-card empty-state mt-4">
            <i class="bi bi-journal-x"></i>
            <h1 class="h3 mt-3">Menu coming soon</h1>
            <p class="text-muted mb-0">There are no menu items available right now.</p>
        </div>
    @endif
</main>
@endsection
