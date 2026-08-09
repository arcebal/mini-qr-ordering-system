<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Ordering System</title>

    @vite(['resources/js/app.js'])

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        @font-face {
            font-family: 'Katipunan Regular';
            src: local('Katipunan Regular'), local('Katipunan');
            font-display: swap;
        }

        :root {
            --app-bg: #fff5f7;
            --surface: #fffafb;
            --surface-muted: #fde4ea;
            --soft: #fde4ea;
            --ink: #4a2d36;
            --muted: #80616b;
            --border: #f0cfd8;
            --primary: #c45c78;
            --primary-dark: #9f405d;
            --accent: #e6a0b2;
            --danger: #a33b52;
            --info-bg: #e6f0f7;
            --info-ink: #175a7a;
            --warning-bg: #fff3d8;
            --warning-ink: #855b16;
            --success-bg: #e1f0e7;
            --success-ink: #1f6546;
            --danger-bg: #f8e2e0;
            --danger-ink: #9b3029;
            --sidebar: #542f3e;
            --sidebar-muted: #f6dce3;
            --on-dark: #fffafb;
        }

        * { box-sizing: border-box; }

        body {
            min-height: 100vh;
            margin: 0;
            background: var(--app-bg);
            color: var(--ink);
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .app-shell { min-height: 100vh; }

        .sidebar-backdrop { display: none; }

        .app-sidebar {
            display: flex;
            position: sticky;
            top: 0;
            width: 252px;
            min-width: 252px;
            height: 100vh;
            flex-direction: column;
            padding: 22px 14px 16px;
            background: var(--sidebar);
            color: var(--on-dark);
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0 10px 26px;
            border-bottom: 1px solid rgba(255, 255, 255, .12);
            color: var(--on-dark);
            text-decoration: none;
        }

        .sidebar-brand-mark {
            display: grid;
            width: 38px;
            height: 38px;
            place-items: center;
            border-radius: 12px;
            background: var(--primary);
            color: #fff;
            font-size: 20px;
        }
        .sidebar-brand-mark img { width: 100%; height: 100%; object-fit: contain; }

        .sidebar-brand strong { display: block; font-size: 16px; letter-spacing: -.2px; }
        .sidebar-brand small { color: var(--sidebar-muted); font-size: 11px; }
        .sidebar-nav { display: grid; gap: 6px; padding-top: 24px; }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 13px;
            border-radius: 10px;
            color: var(--sidebar-muted);
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: background .18s ease, color .18s ease, transform .18s ease;
        }

        .sidebar-link:hover { background: rgba(255, 224, 234, .15); color: var(--on-dark); transform: translateX(2px); }
        .sidebar-link.active { background: rgba(255, 255, 255, .13); color: var(--on-dark); }
        .sidebar-link i { font-size: 17px; }

        .sidebar-user {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: auto;
            padding: 15px 10px 0;
            border-top: 1px solid rgba(255, 255, 255, .12);
        }

        .user-avatar {
            display: grid;
            width: 36px;
            height: 36px;
            flex: 0 0 36px;
            place-items: center;
            border-radius: 50%;
            background: var(--soft, #f2ece6);
            color: var(--ink);
            font-size: 14px;
            font-weight: 800;
        }

        .sidebar-user-name { overflow: hidden; font-size: 13px; font-weight: 700; text-overflow: ellipsis; white-space: nowrap; }
        .sidebar-user-role { color: var(--sidebar-muted); font-size: 11px; }
        .sidebar-logout { border: 0; background: transparent; color: #ffc4d4; }
        .sidebar-logout:hover { color: var(--on-dark); background: rgba(155, 56, 46, .45); }

        .app-main { min-width: 0; }

        .app-navbar {
            position: sticky;
            top: 0;
            z-index: 1030;
            display: flex;
            min-height: 82px;
            padding: 0 32px;
            border-bottom: 1px solid var(--border);
            background: rgba(255, 250, 251, .94);
            backdrop-filter: blur(12px);
        }

        .app-navbar-title { margin: 0; color: var(--ink); font-size: 18px; font-weight: 750; letter-spacing: -.3px; }
        .app-navbar-subtitle { margin-top: 2px; color: var(--muted); font-size: 12px; }
        .sidebar-toggle { display: none; width: 42px; height: 42px; border: 1px solid var(--border); border-radius: 10px; background: var(--surface); color: var(--ink); }
        .app-content { max-width: 1440px; padding: 32px; }

        h1, h2, h3, h4, h5, h6 { color: var(--ink); letter-spacing: -.35px; }
        h2 { font-size: 25px; font-weight: 750; }
        .card { border: 1px solid var(--border); border-radius: 14px; box-shadow: 0 2px 8px rgba(41, 35, 33, .04); }
        .card-body { padding: 24px; }
        .metric-card { overflow: hidden; border: 0; }
        .metric-card .card-body { position: relative; padding: 24px; }
        .metric-icon { display: grid; width: 42px; height: 42px; place-items: center; border-radius: 12px; font-size: 19px; }
        .metric-label { margin: 16px 0 2px; color: var(--muted); font-size: 13px; font-weight: 700; }
        .metric-value { margin: 0; font-size: 30px; font-weight: 800; }
        .metric-categories { background: #fde4ea; color: #9f405d; }
        .metric-products { background: #f8e8dd; color: #996142; }
        .metric-orders { background: #e1edf0; color: #315d6a; }

        .btn { border-radius: 9px; font-size: 13px; font-weight: 650; padding: .52rem .9rem; box-shadow: none !important; }
        .btn-primary, .btn-success { border-color: var(--primary); background: var(--primary); }
        .btn-primary:hover, .btn-primary:focus, .btn-success:hover, .btn-success:focus { border-color: var(--primary-dark); background: var(--primary-dark); }
        .btn-warning { border-color: var(--border); background: var(--surface); color: var(--ink); }
        .btn-warning:hover, .btn-warning:focus { border-color: var(--primary); background: var(--surface-muted); color: var(--ink); }
        .btn-danger { border-color: var(--danger); background: var(--danger); }
        .btn-danger:hover, .btn-danger:focus { border-color: #a83d3d; background: #a83d3d; }
        .btn-secondary { border-color: var(--border); background: var(--surface); color: var(--ink); }
        .btn-secondary:hover, .btn-secondary:focus { border-color: var(--primary); background: var(--surface-muted); color: var(--ink); }

        .form-control, .form-select { min-height: 42px; border-color: var(--border); border-radius: 9px; color: var(--ink); }
        textarea.form-control { min-height: auto; }
        .form-control:focus, .form-select:focus { border-color: var(--primary); box-shadow: 0 0 0 .2rem rgba(196, 92, 120, .16); }
        .form-label { margin-bottom: .45rem; color: #704452; font-size: 13px; font-weight: 700; }
        .form-check-input:checked { border-color: var(--primary); background-color: var(--primary); }

        .table { margin-bottom: 0; color: var(--ink); }
        .table > :not(caption) > * > * { padding: 15px 14px; border-bottom-color: var(--border); }
        .table-dark { --bs-table-bg: #292321; --bs-table-border-color: #4c423e; }
        .table-hover > tbody > tr:hover > * { --bs-table-bg-state: #faf6f2; }
        .table-actions { display: flex; align-items: center; gap: 8px; flex-wrap: nowrap; }
        .table-actions form { margin: 0; }
        .table-actions .btn { white-space: nowrap; }
        .badge { border-radius: 999px; padding: .45em .7em; font-family: ui-sans-serif, system-ui, sans-serif; font-size: 11px; font-weight: 700; }
        .badge.bg-success { background-color: var(--success-bg) !important; color: var(--success-ink); }
        .badge.bg-danger { background-color: var(--danger-bg) !important; color: var(--danger-ink); }
        .order-status { display: inline-block; padding: .45em .7em; border-radius: 999px; font-family: ui-sans-serif, system-ui, sans-serif; font-size: 11px; font-weight: 700; }
        .order-status.accepted { background: var(--info-bg); color: var(--info-ink); }
        .order-status.preparing { background: var(--warning-bg); color: var(--warning-ink); }
        .order-status.completed { background: var(--success-bg); color: var(--success-ink); }
        .payment-status { display: inline-block; padding: .4em .65em; border-radius: 999px; font-size: 11px; font-weight: 700; }
        .payment-status.paid { background: var(--success-bg); color: var(--success-ink); }
        .payment-status.unpaid { background: var(--warning-bg); color: var(--warning-ink); }
        .pagination { --bs-pagination-color: var(--primary); --bs-pagination-border-color: var(--border); --bs-pagination-hover-color: var(--primary-dark); --bs-pagination-hover-bg: var(--surface-muted); --bs-pagination-hover-border-color: var(--border); --bs-pagination-active-bg: var(--primary); --bs-pagination-active-border-color: var(--primary); }

        .mobile-data-card { display: none; }
        .mobile-data-card .data-card-label { color: var(--muted); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; }
        .mobile-data-card .data-card-value { color: var(--ink); font-weight: 700; }
        .mobile-data-card + .mobile-data-card { margin-top: 12px; }
        .mobile-product-image { display: grid; width: 64px; height: 64px; flex: 0 0 64px; place-items: center; padding: 4px; border-radius: 10px; object-fit: contain; background: var(--surface-muted); }
        .min-w-0 { min-width: 0; }

        .swal2-popup { border-radius: 18px !important; padding: 1.75rem !important; }
        .swal2-title { color: var(--ink) !important; font-size: 21px !important; }
        .swal2-html-container { color: var(--muted) !important; font-size: 14px !important; }
        .swal2-styled { border-radius: 9px !important; font-weight: 700 !important; }
        :focus-visible { outline: 3px solid rgba(47, 107, 82, .42); outline-offset: 2px; }
        @media (prefers-reduced-motion: reduce) { *, *::before, *::after { scroll-behavior: auto !important; transition-duration: .01ms !important; animation-duration: .01ms !important; } }

        @media (max-width: 991.98px) {
            .app-sidebar { position: fixed; z-index: 1050; top: 0; bottom: 0; left: 0; width: min(292px, 86vw); min-width: 0; height: 100vh; transform: translateX(-105%); transition: transform .2s ease; box-shadow: 14px 0 32px rgba(34, 51, 43, .18); }
            body.admin-nav-open { overflow: hidden; }
            body.admin-nav-open .app-sidebar { transform: translateX(0); }
            body.admin-nav-open .sidebar-backdrop { display: block; position: fixed; z-index: 1040; inset: 0; background: rgba(24, 35, 29, .48); }
            .sidebar-nav { grid-template-columns: 1fr; }
            .sidebar-user { margin-top: auto; }
            .sidebar-close { display: grid !important; }
            .sidebar-toggle { display: grid; place-items: center; flex: 0 0 42px; }
            .app-navbar { padding: 0 20px; }
            .app-content { padding: 24px 20px; }
        }

        @media (max-width: 575.98px) {
            .sidebar-nav { grid-template-columns: 1fr; }
            .app-navbar { min-height: 70px; }
            .app-content { padding: 20px 14px; }
            .card-body { padding: 16px; }
            .app-navbar-title { font-size: 16px; }
            .app-navbar-subtitle { display: none; }
            .page-heading { align-items: stretch !important; flex-direction: column; }
            .page-heading .btn { width: 100%; }
            .metric-card .card-body { padding: 16px; }
            .metric-value { font-size: 24px; }
            .table { min-width: 660px; }
            .table-responsive { margin-right: -16px; margin-left: -16px; padding: 0 16px; }
            .mobile-data-card { display: block; }
            .desktop-data-table { display: none; }
        }
    </style>
</head>

<body class="app-body">

<div class="d-flex app-shell">

    {{-- Sidebar --}}
    @include('admin.layouts.sidebar')
    <div class="sidebar-backdrop" data-sidebar-close></div>

    {{-- Main Content --}}
    <main class="flex-grow-1 app-main">

        @include('admin.layouts.navbar')

        <div class="container-fluid app-content">

            @yield('content')

        </div>

    </main>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if(session('success'))

<script>

Swal.fire({
    icon: 'success',
    title: 'Success!',
    text: @json(session('success')),
    timer: 2200,
    showConfirmButton: false
});

</script>

@endif

@if(session('error'))

<script>

Swal.fire({
    icon: 'error',
    title: 'Oops!',
    text: @json(session('error'))
});

</script>

@endif

@if(session('warning'))

<script>

Swal.fire({
    icon: 'warning',
    title: 'Warning',
    text: @json(session('warning'))
});

</script>

@endif
<script>
document.addEventListener('submit', (event) => {
    const form = event.target.closest('form[data-swal-confirm]');

    if (!form || form.dataset.confirmed === 'true') {
        return;
    }

    event.preventDefault();

    Swal.fire({
        title: form.dataset.swalTitle,
        text: form.dataset.swalText,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#c45c78',
        cancelButtonColor: '#80616b',
        confirmButtonText: form.dataset.swalConfirmText,
        cancelButtonText: 'Cancel',
    }).then((result) => {
        if (result.isConfirmed) {
            form.dataset.confirmed = 'true';
            form.submit();
        }
    });
});

const adminNav = document.body;
const sidebarToggle = document.querySelector('[data-sidebar-toggle]');
const sidebarCloseControls = document.querySelectorAll('[data-sidebar-close]');

function closeAdminNavigation() {
    adminNav.classList.remove('admin-nav-open');
    sidebarToggle?.setAttribute('aria-expanded', 'false');
}

sidebarToggle?.addEventListener('click', () => {
    const isOpen = adminNav.classList.toggle('admin-nav-open');
    sidebarToggle.setAttribute('aria-expanded', String(isOpen));
});
sidebarCloseControls.forEach((control) => control.addEventListener('click', closeAdminNavigation));
document.querySelectorAll('.app-sidebar a').forEach((link) => link.addEventListener('click', closeAdminNavigation));
document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') closeAdminNavigation();
});
</script>
@stack('scripts')
</body>
</html>
