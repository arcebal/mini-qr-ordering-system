<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#fffdf9">
    <title>@yield('title', 'Kusina ni Juan')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root { --primary: #2f6b52; --primary-dark: #21513e; --ink: #22332b; --muted: #68766e; --line: #dce5dd; --canvas: #f4f7f3; --surface: #fffefb; --soft: #e3eee5; --success: #1f6b4f; }
        * { box-sizing: border-box; }
        body { min-height: 100vh; background: var(--canvas); color: var(--ink); font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; }
        h1, h2, h3 { letter-spacing: -.035em; }
        .skip-link { position: fixed; z-index: 2000; top: -4rem; left: 1rem; padding: .75rem 1rem; background: var(--ink); color: #fff; border-radius: .5rem; text-decoration: none; }
        .skip-link:focus { top: 1rem; }
        .customer-nav { border-bottom: 1px solid var(--line); background: rgba(255, 253, 249, .94); backdrop-filter: blur(12px); }
        .brand { color: var(--ink); font-size: 1rem; font-weight: 750; letter-spacing: -.02em; text-decoration: none; }
        .brand-mark { display: inline-grid; width: 31px; height: 31px; place-items: center; margin-right: .55rem; border-radius: 9px; background: var(--primary); color: #fff; }
        .cart-link { position: relative; display: grid; width: 38px; height: 38px; place-items: center; border: 1px solid var(--line); border-radius: 10px; color: var(--ink); text-decoration: none; }
        .cart-link:hover { border-color: var(--primary); color: var(--primary); }
        .cart-count { position: absolute; top: -7px; right: -7px; min-width: 18px; padding: 2px 5px; border: 2px solid var(--surface); border-radius: 20px; background: var(--primary); color: #fff; font-size: 10px; font-weight: 700; text-align: center; }
        .hero { padding: clamp(3.75rem, 8vw, 6rem) 0 clamp(2.75rem, 6vw, 4.5rem); background: var(--surface); border-bottom: 1px solid var(--line); }
        .hero .eyebrow { color: var(--primary); font-size: .75rem; font-weight: 800; letter-spacing: .11em; }
        .hero h1 { max-width: 680px; margin-bottom: .8rem; font-size: clamp(2.5rem, 6vw, 4.25rem); font-weight: 750; line-height: 1; }
        .hero p { max-width: 520px; margin-bottom: 0; color: var(--muted); font-size: 1.05rem; }
        .category-tabs { display: flex; gap: 8px; overflow-x: auto; margin: 26px 0 6px; padding: 3px 1px 12px; scrollbar-width: thin; }
        .category-tab { flex: 0 0 auto; border: 1px solid var(--line); border-radius: 999px; padding: 8px 14px; color: var(--muted); font-size: .86rem; font-weight: 650; text-decoration: none; }
        .category-tab:hover, .category-tab:focus, .category-tab.active { border-color: var(--primary); background: var(--primary); color: #fff; }
        .section-title { margin: 30px 0 8px; font-size: 1.5rem; font-weight: 750; }
        .menu-card, .customer-card { overflow: hidden; border: 1px solid var(--line); border-radius: 14px; background: var(--surface); box-shadow: 0 2px 7px rgba(41, 35, 33, .035); }
        .menu-card { height: 100%; transition: transform .2s ease, box-shadow .2s ease; }
        .menu-card:hover { transform: translateY(-3px); box-shadow: 0 12px 24px rgba(41, 35, 33, .08); }
        .menu-image, .image-placeholder { width: 100%; height: 188px; object-fit: cover; background: var(--soft); }
        .image-placeholder { display: grid; place-items: center; color: var(--primary); font-size: 38px; }
        .menu-card .card-body, .customer-card .card-body { padding: 20px; }
        .menu-card h3 { margin-bottom: .45rem; font-size: 1.08rem; font-weight: 750; }
        .product-description { min-height: 40px; margin-bottom: 0; color: var(--muted); font-size: .9rem; line-height: 1.45; }
        .price { margin: 15px 0; color: var(--ink); font-size: 1.05rem; font-weight: 800; }
        .btn { min-height: 42px; border-radius: 9px; font-size: .88rem; font-weight: 700; }
        .btn-copper { border: 0; background: var(--primary); color: #fff; }
        .btn-copper:hover, .btn-copper:focus { background: var(--primary-dark); color: #fff; }
        .btn-outline-rust { border-color: var(--line); color: var(--ink); }
        .btn-outline-rust:hover, .btn-outline-rust:focus { border-color: var(--primary); background: var(--primary); color: #fff; }
        .status-badge { display: inline-block; padding: 5px 9px; border-radius: 999px; font-size: 11px; font-weight: 750; }
        .status-out { background: #f9e2df; color: #9b3027; } .status-unavailable { background: #f5eed4; color: #775e20; }
        .status-accepted { background: #e6f0f7; color: #175a7a; } .status-preparing { background: #fff3d8; color: #855b16; } .status-completed { background: #e1f0e7; color: #1f6546; } .status-deleted { background: #f8e2e0; color: #9b3029; }
        .cart-image { width: 76px; height: 76px; flex: 0 0 76px; border-radius: 10px; object-fit: cover; background: var(--soft); }
        .quantity-control { display: inline-flex; align-items: center; gap: 8px; border: 1px solid var(--line); border-radius: 9px; padding: 3px; }
        .quantity-control form { margin: 0; } .quantity-control button { width: 30px; height: 30px; border: 0; border-radius: 7px; background: #f5f2ee; color: var(--ink); }
        .quantity-control button:hover { background: var(--soft); color: var(--primary); }
        .quantity-value { min-width: 20px; text-align: center; font-size: 14px; font-weight: 750; }
        .summary-total { color: var(--primary); font-size: 1.25rem; font-weight: 800; }
        .form-control { min-height: 46px; border-color: var(--line); border-radius: 9px; } .form-control:focus { border-color: var(--primary); box-shadow: 0 0 0 .2rem rgba(185, 77, 50, .13); }
        .empty-state { padding: 56px 24px; text-align: center; } .empty-state i { color: var(--primary); font-size: 48px; }
        .welcome-card { margin-top: clamp(1.5rem, 8vh, 5rem); } .welcome-mark { display: grid; width: 52px; height: 52px; place-items: center; border-radius: 14px; background: var(--soft); color: var(--primary); font-size: 24px; }
        .customer-identity { display: flex; align-items: center; gap: .6rem; padding: .8rem .9rem; border: 1px solid var(--line); border-radius: 9px; background: #fafcf9; color: var(--muted); font-size: .92rem; } .customer-identity i { color: var(--primary); font-size: 1.1rem; }
        .order-tracker h1 { font-size: clamp(1.8rem, 4vw, 2.3rem); font-weight: 750; }
        .status-timeline { position: relative; display: grid; gap: 0; margin: 2rem 0; padding: 0; list-style: none; }
        .status-timeline li { position: relative; display: grid; grid-template-columns: 38px 1fr; gap: 14px; min-height: 90px; color: var(--muted); }
        .status-timeline li:not(:last-child)::before { position: absolute; top: 38px; left: 18px; width: 2px; height: calc(100% - 8px); background: var(--line); content: ''; }
        .timeline-icon { z-index: 1; display: grid; width: 38px; height: 38px; place-items: center; border: 1px solid var(--line); border-radius: 50%; background: var(--surface); color: var(--muted); }
        .status-timeline strong { display: block; margin-top: 7px; color: var(--ink); } .status-timeline p { margin: .25rem 0 0; font-size: .9rem; }
        .status-timeline li.is-complete .timeline-icon { color: #fff; } .status-timeline li.is-complete[data-status="accepted"] .timeline-icon { border-color: #2480a6; background: #2480a6; } .status-timeline li.is-complete[data-status="preparing"] .timeline-icon { border-color: #d18b20; background: #d18b20; } .status-timeline li.is-complete[data-status="completed"] .timeline-icon { border-color: #2f8058; background: #2f8058; } .status-timeline li.is-complete:not(:last-child)::before { background: var(--line); }
        .tracker-footer { display: flex; align-items: center; justify-content: space-between; gap: 1rem; padding-top: 1.25rem; border-top: 1px solid var(--line); }
        .order-deleted-notice { display: flex; gap: .75rem; margin-bottom: 1.5rem; padding: 1rem; border: 1px solid #edc3bf; border-radius: 10px; background: #fff5f4; color: #7e3029; } .order-deleted-notice i { font-size: 1.2rem; }
        :focus-visible { outline: 3px solid rgba(47, 107, 82, .42); outline-offset: 2px; }
        .swal2-popup { border-radius: 14px !important; } .swal2-styled.swal2-confirm { background: var(--primary) !important; }
        @media (prefers-reduced-motion: reduce) { *, *::before, *::after { scroll-behavior: auto !important; transition-duration: .01ms !important; animation-duration: .01ms !important; } }
        @media (max-width: 575.98px) {
            .hero { padding-top: 3.25rem; }
            .customer-nav .container { padding-right: 14px; padding-left: 14px; }
            .brand { font-size: .92rem; }
            .hero h1 { font-size: 2.35rem; }
            .hero p { font-size: .96rem; }
            .menu-card .card-body, .customer-card .card-body { padding: 16px; }
            .menu-image, .image-placeholder { height: 160px; }
            .cart-image { width: 64px; height: 64px; flex-basis: 64px; }
            .cart-item-content { min-width: 0; }
            .cart-item-controls { align-items: flex-start !important; flex-direction: column; }
            .cart-item-total { width: 100%; justify-content: space-between; }
            .cart-header { align-items: flex-start !important; flex-direction: column; gap: .75rem; }
            .cart-header .btn { width: 100%; }
            .tracker-footer { align-items: stretch; flex-direction: column; }
            .tracker-footer .btn { width: 100%; }
            .status-timeline li { grid-template-columns: 34px 1fr; gap: 10px; min-height: 82px; }
            .timeline-icon { width: 34px; height: 34px; }
            .status-timeline li:not(:last-child)::before { left: 16px; top: 34px; }
        }
    </style>
</head>
<body>
    <a class="skip-link" href="#main-content">Skip to menu content</a>
    <nav class="navbar customer-nav sticky-top" aria-label="Customer navigation">
        <div class="container py-2">
            <a class="brand" href="{{ route('customer.menu') }}"><span class="brand-mark"><i class="bi bi-qr-code-scan"></i></span>Kusina ni Juan</a>
            <a class="cart-link" href="{{ route('customer.cart.index') }}" aria-label="View cart">
                <i class="bi bi-bag fs-5"></i>
                @if (array_sum(session('customer_cart', [])) > 0)<span class="cart-count">{{ array_sum(session('customer_cart', [])) }}</span>@endif
            </a>
        </div>
    </nav>
    @yield('content')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @if (session('success'))<script>Swal.fire({ icon: 'success', title: 'Added to cart', text: @json(session('success')), timer: 1800, showConfirmButton: false });</script>@endif
    @if (session('warning'))<script>Swal.fire({ icon: 'warning', title: 'Please note', text: @json(session('warning')) });</script>@endif
    @if (session('error'))<script>Swal.fire({ icon: 'error', title: 'Unable to continue', text: @json(session('error')) });</script>@endif
    @stack('scripts')
</body>
</html>
