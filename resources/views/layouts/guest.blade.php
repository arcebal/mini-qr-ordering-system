<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Kusina Ni Aira') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            :root {
                --auth-ink: #4a2d36;
                --auth-muted: #80616b;
                --auth-primary: #c45c78;
                --auth-primary-dark: #9f405d;
                --auth-line: #f0cfd8;
                --auth-canvas: #fff5f7;
                --auth-surface: #fffafb;
                --auth-soft: #fde4ea;
            }

            * { box-sizing: border-box; }

            body {
                min-height: 100vh;
                margin: 0;
                background: var(--auth-canvas);
                color: var(--auth-ink);
                font-family: 'DM Sans', Inter, ui-sans-serif, system-ui, sans-serif;
            }

            .auth-page {
                position: relative;
                display: grid;
                min-height: 100vh;
                place-items: center;
                overflow: hidden;
                padding: 32px 16px;
            }

            .auth-page::before,
            .auth-page::after {
                position: absolute;
                z-index: 0;
                border-radius: 50%;
                content: '';
                pointer-events: none;
            }

            .auth-page::before {
                top: -180px;
                right: -120px;
                width: 420px;
                height: 420px;
                background: rgba(196, 92, 120, .10);
            }

            .auth-page::after {
                bottom: -230px;
                left: -140px;
                width: 500px;
                height: 500px;
                background: rgba(230, 160, 178, .14);
            }

            .auth-shell {
                position: relative;
                z-index: 1;
                width: min(100%, 440px);
            }

            .auth-brand {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 11px;
                margin-bottom: 22px;
                color: var(--auth-ink);
                text-decoration: none;
            }

            .auth-brand-mark {
                display: grid;
                width: 44px;
                height: 44px;
                place-items: center;
                border-radius: 13px;
                background: var(--auth-primary);
                color: #fff;
                font-size: 22px;
                box-shadow: 0 9px 18px rgba(196, 92, 120, .18);
            }

            .auth-brand-name { font-size: 1.1rem; font-weight: 700; letter-spacing: -.02em; }

            .auth-card {
                padding: clamp(25px, 6vw, 38px);
                border: 1px solid rgba(220, 229, 221, .95);
                border-radius: 20px;
                background: rgba(255, 254, 251, .94);
                box-shadow: 0 22px 55px rgba(34, 51, 43, .10);
                backdrop-filter: blur(12px);
            }

            .auth-card h1 { margin: 0; font-size: clamp(1.7rem, 5vw, 2.05rem); font-weight: 700; letter-spacing: -.045em; }
            .auth-card-subtitle { margin: 8px 0 25px; color: var(--auth-muted); font-size: .94rem; line-height: 1.5; }
            .auth-label { display: block; margin-bottom: 8px; color: var(--auth-ink); font-size: .84rem; font-weight: 700; }
            .auth-field { position: relative; }
            .auth-field-icon { position: absolute; top: 50%; left: 14px; color: var(--auth-muted); transform: translateY(-50%); }
            .auth-input {
                display: block;
                width: 100%;
                min-height: 48px;
                padding: 11px 14px 11px 42px;
                border: 1px solid var(--auth-line);
                border-radius: 10px;
                background: #fafcf9;
                color: var(--auth-ink);
                font-size: .92rem;
                outline: none;
                transition: border-color .2s ease, box-shadow .2s ease, background .2s ease;
            }
            .auth-input:focus { border-color: var(--auth-primary); background: #fff; box-shadow: 0 0 0 4px rgba(196, 92, 120, .12); }
            .auth-input::placeholder { color: #9aa79e; }
            .auth-error { margin-top: 7px; color: #9b3029; font-size: .8rem; }
            .auth-status { margin-bottom: 18px; padding: 10px 12px; border: 1px solid #b9d8c3; border-radius: 9px; background: #edf8f0; color: #28613f; font-size: .84rem; }
            .auth-options { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-top: 18px; }
            .auth-remember { display: inline-flex; align-items: center; gap: 8px; color: var(--auth-muted); font-size: .83rem; }
            .auth-remember input { width: 16px; height: 16px; accent-color: var(--auth-primary); }
            .auth-link { color: var(--auth-primary); font-size: .83rem; font-weight: 700; text-decoration: none; }
            .auth-link:hover { color: var(--auth-primary-dark); text-decoration: underline; }
            .auth-submit {
                display: inline-flex;
                width: 100%;
                min-height: 48px;
                align-items: center;
                justify-content: center;
                gap: 8px;
                margin-top: 25px;
                border: 0;
                border-radius: 10px;
                background: var(--auth-primary);
                color: #fff;
                font-size: .9rem;
                font-weight: 700;
                cursor: pointer;
                transition: background .2s ease, transform .2s ease, box-shadow .2s ease;
            }
            .auth-submit:hover { background: var(--auth-primary-dark); box-shadow: 0 8px 17px rgba(196, 92, 120, .18); transform: translateY(-1px); }
            .auth-submit:focus-visible, .auth-link:focus-visible, .auth-brand:focus-visible { outline: 3px solid rgba(196, 92, 120, .35); outline-offset: 3px; }
            .auth-footer { margin: 20px 0 0; color: var(--auth-muted); font-size: .76rem; text-align: center; }
            .auth-footer i { margin-right: 4px; color: var(--auth-primary); }

            @media (max-width: 420px) {
                .auth-page { padding: 24px 12px; }
                .auth-card { padding: 24px 20px; }
                .auth-options { align-items: flex-start; flex-direction: column; }
            }
            @media (prefers-reduced-motion: reduce) {
                *, *::before, *::after { transition-duration: .01ms !important; }
            }
        </style>
    </head>
    <body>
        <main class="auth-page">
            <div class="auth-shell">
                <a class="auth-brand" href="{{ url('/') }}" aria-label="Kusina Ni Aira home">
                    <span class="auth-brand-mark"><img src="{{ Vite::asset('resources/images/kusina-logo.png') }}" alt="" style="width: 100%; height: 100%; object-fit: contain;"></span>
                    <span class="auth-brand-name">Kusina Ni Aira</span>
                </a>

                <section class="auth-card">
                    {{ $slot }}
                </section>

                <p class="auth-footer"><i class="bi bi-shield-check" aria-hidden="true"></i> Secure restaurant administration</p>
            </div>
        </main>
    </body>
</html>
