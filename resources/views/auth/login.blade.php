<x-guest-layout>
    <div class="auth-heading">
        <p style="margin: 0 0 8px; color: var(--auth-primary); font-size: .75rem; font-weight: 700; letter-spacing: .11em; text-transform: uppercase;">Welcome back</p>
        <h1>Sign in to your dashboard</h1>
        <p class="auth-card-subtitle">Manage your menu, orders, and restaurant performance from one place.</p>
    </div>

    @if (session('status'))
        <div class="auth-status" role="status">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <label class="auth-label" for="email">Email address</label>
            <div class="auth-field">
                <i class="bi bi-envelope auth-field-icon" aria-hidden="true"></i>
                <input id="email" class="auth-input" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="you@example.com">
            </div>
            @error('email')<p class="auth-error" role="alert">{{ $message }}</p>@enderror
        </div>

        <div style="margin-top: 18px;">
            <label class="auth-label" for="password">Password</label>
            <div class="auth-field">
                <i class="bi bi-lock auth-field-icon" aria-hidden="true"></i>
                <input id="password" class="auth-input" type="password" name="password" required autocomplete="current-password" placeholder="Enter your password">
            </div>
            @error('password')<p class="auth-error" role="alert">{{ $message }}</p>@enderror
        </div>

        <div class="auth-options">
            <label class="auth-remember" for="remember_me">
                <input id="remember_me" type="checkbox" name="remember">
                <span>Remember me</span>
            </label>

            @if (Route::has('password.request'))
                <a class="auth-link" href="{{ route('password.request') }}">Forgot password?</a>
            @endif
        </div>

        <button class="auth-submit" type="submit">
            <span>Log in to dashboard</span>
            <i class="bi bi-arrow-right" aria-hidden="true"></i>
        </button>
    </form>
</x-guest-layout>
