<aside id="admin-sidebar" class="app-sidebar">
    <div class="d-flex justify-content-end mb-3">
        <button type="button" class="sidebar-close btn btn-sm sidebar-logout d-none" data-sidebar-close aria-label="Close navigation">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
    <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
        <span class="sidebar-brand-mark"><img src="{{ asset('images/kusina-logo.png') }}" alt=""></span>
        <span>
            <strong>Kusina Ni Aira</strong>
            <small>Restaurant admin</small>
        </span>
    </a>

    <nav class="sidebar-nav" aria-label="Admin navigation">
        <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2"></i> Dashboard
        </a>
        <a href="{{ route('admin.categories.index') }}" class="sidebar-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
            <i class="bi bi-tags"></i> Categories
        </a>
        <a href="{{ route('admin.products.index') }}" class="sidebar-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
            <i class="bi bi-box-seam"></i> Products
        </a>
        <a href="{{ route('admin.orders.index') }}" class="sidebar-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
            <i class="bi bi-receipt"></i> Orders
        </a>
        <a href="{{ route('admin.reports.index') }}" class="sidebar-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
            <i class="bi bi-bar-chart-line"></i> Reports
        </a>
    </nav>

    <div class="sidebar-user">
        <span class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
        <div class="flex-grow-1 overflow-hidden">
            <div class="sidebar-user-name">{{ auth()->user()->name }}</div>
            <div class="sidebar-user-role">Administrator</div>
        </div>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-sm sidebar-logout" title="Log out" aria-label="Log out">
                <i class="bi bi-box-arrow-right"></i>
            </button>
        </form>
    </div>
</aside>
