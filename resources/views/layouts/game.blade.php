<!doctype html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'الملثم') - الملثم</title>
    <meta name="description" content="الملثم - يا قافط .. يا مقفوط">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('imgs/mulatham-logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('imgs/mulatham-logo.png') }}">

    <!-- Open Graph / Facebook / WhatsApp -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('title', 'الملثم') - الملثم">
    <meta property="og:description" content="الملثم - يا قافط .. يا مقفوط">
    <meta property="og:image" content="{{ asset('imgs/mulatham-logo.png') }}">
    <meta property="og:image:width" content="512">
    <meta property="og:image:height" content="512">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', 'الملثم') - الملثم">
    <meta name="twitter:description" content="الملثم - يا قافط .. يا مقفوط">
    <meta name="twitter:image" content="{{ asset('imgs/mulatham-logo.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <!-- Bootstrap RTL -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    @stack('styles')

    <style>
        :root {
            /* Night Sky Theme - Deep navy with gold/silver accents */
            --color-primary: #c9a227;
            --color-primary-hover: #a88620;
            --color-primary-light: #e6c353;
            --color-secondary: #6366f1;
            --color-secondary-light: #818cf8;
            --color-background: #0a0e1a;
            --color-background-dark: #060810;
            --color-background-light: #111827;
            --color-card: #151c2c;
            --color-card-hover: #1a2235;
            --color-border: #2a3548;
            --color-border-light: #3d4a5f;
            --color-text: #e8eaed;
            --color-text-muted: #8b95a5;
            --color-success: #10b981;
            --color-success-light: #34d399;
            --color-danger: #ef4444;
            --color-danger-light: #f87171;
            --color-warning: #f59e0b;
            --color-info: #3b82f6;
            --color-gold: #ffd700;
            --color-silver: #c0c0c0;
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 18px;
            --radius-xl: 24px;
            --radius-full: 999px;
            --shadow-sm: 0 4px 12px rgba(0, 0, 0, 0.3);
            --shadow-md: 0 10px 30px rgba(0, 0, 0, 0.4);
            --shadow-lg: 0 20px 40px rgba(0, 0, 0, 0.5);
            --shadow-glow: 0 0 40px rgba(201, 162, 39, 0.2);
            --shadow-glow-purple: 0 0 30px rgba(99, 102, 241, 0.15);
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Cairo', system-ui, -apple-system, sans-serif;
            background: var(--color-background);
            background-image:
                radial-gradient(ellipse at 20% 0%, rgba(99, 102, 241, 0.15) 0%, transparent 50%),
                radial-gradient(ellipse at 80% 100%, rgba(201, 162, 39, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 50% 50%, rgba(17, 24, 39, 1) 0%, rgba(10, 14, 26, 1) 100%);
            color: var(--color-text);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Stars animation background */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image:
                radial-gradient(2px 2px at 20px 30px, rgba(255, 255, 255, 0.3), transparent),
                radial-gradient(2px 2px at 40px 70px, rgba(255, 215, 0, 0.2), transparent),
                radial-gradient(1px 1px at 90px 40px, rgba(255, 255, 255, 0.4), transparent),
                radial-gradient(2px 2px at 160px 120px, rgba(255, 255, 255, 0.2), transparent),
                radial-gradient(1px 1px at 230px 80px, rgba(255, 215, 0, 0.3), transparent),
                radial-gradient(2px 2px at 300px 150px, rgba(255, 255, 255, 0.3), transparent),
                radial-gradient(1px 1px at 370px 50px, rgba(255, 255, 255, 0.2), transparent),
                radial-gradient(2px 2px at 450px 180px, rgba(255, 215, 0, 0.2), transparent);
            background-size: 500px 200px;
            animation: twinkle 8s ease-in-out infinite;
            pointer-events: none;
            z-index: 0;
            opacity: 0.6;
        }

        @keyframes twinkle {

            0%,
            100% {
                opacity: 0.6;
            }

            50% {
                opacity: 0.3;
            }
        }

        #app {
            position: relative;
            z-index: 1;
        }

        /* Typography */
        .text-primary-custom {
            color: var(--color-primary) !important;
        }

        .text-muted-custom {
            color: var(--color-text-muted) !important;
        }

        .text-success-custom {
            color: var(--color-success) !important;
        }

        .text-danger-custom {
            color: var(--color-danger) !important;
        }

        /* Buttons */
        .btn-game {
            padding: 12px 28px;
            border-radius: var(--radius-md);
            font-weight: 700;
            font-size: 1rem;
            border: 2px solid transparent;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-game-primary {
            background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-light) 100%);
            color: #0a0e1a;
            box-shadow: var(--shadow-sm), 0 4px 0 var(--color-primary-hover), var(--shadow-glow);
        }

        .btn-game-primary:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md), 0 6px 0 var(--color-primary-hover), 0 0 50px rgba(201, 162, 39, 0.35);
            color: #0a0e1a;
        }

        .btn-game-primary:active {
            transform: translateY(2px);
            box-shadow: var(--shadow-sm), 0 2px 0 var(--color-primary-hover);
        }

        .btn-game-secondary {
            background: var(--color-card);
            color: var(--color-text);
            border-color: var(--color-border);
            box-shadow: var(--shadow-sm);
        }

        .btn-game-secondary:hover {
            background: var(--color-card-hover);
            border-color: var(--color-secondary);
            transform: translateY(-2px);
            color: var(--color-secondary-light);
        }

        .btn-game-success {
            background: linear-gradient(135deg, var(--color-success) 0%, var(--color-success-light) 100%);
            color: #fff;
            box-shadow: var(--shadow-sm), 0 4px 0 #059669;
        }

        .btn-game-success:hover {
            transform: translateY(-2px);
            color: #fff;
            box-shadow: var(--shadow-md), 0 6px 0 #059669, 0 0 30px rgba(16, 185, 129, 0.3);
        }

        .btn-game-danger {
            background: linear-gradient(135deg, var(--color-danger) 0%, var(--color-danger-light) 100%);
            color: #fff;
            box-shadow: var(--shadow-sm), 0 4px 0 #b91c1c;
        }

        .btn-game-danger:hover {
            transform: translateY(-2px);
            color: #fff;
            box-shadow: var(--shadow-md), 0 6px 0 #b91c1c, 0 0 30px rgba(239, 68, 68, 0.3);
        }

        .btn-game-outline {
            background: transparent;
            color: var(--color-text);
            border-color: var(--color-border);
        }

        .btn-game-outline:hover {
            background: var(--color-card);
            border-color: var(--color-primary);
            color: var(--color-primary);
        }

        .btn-game-sm {
            padding: 8px 16px;
            font-size: 0.875rem;
        }

        .btn-game-lg {
            padding: 16px 36px;
            font-size: 1.125rem;
        }

        /* Cards */
        .game-card {
            background: var(--color-card);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            overflow: hidden;
            backdrop-filter: blur(10px);
        }

        .game-card-header {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.1) 0%, rgba(201, 162, 39, 0.08) 100%);
            padding: 16px 24px;
            border-bottom: 1px solid var(--color-border);
        }

        .game-card-body {
            padding: 24px;
        }

        .game-card-highlight {
            border: 2px solid var(--color-primary);
            box-shadow: var(--shadow-lg), var(--shadow-glow);
        }

        /* Inputs */
        .game-input {
            width: 100%;
            padding: 14px 18px;
            border: 2px solid var(--color-border);
            border-radius: var(--radius-md);
            font-size: 1rem;
            font-family: inherit;
            background: var(--color-background-light);
            color: var(--color-text);
            transition: all 0.2s ease;
        }

        .game-input:focus {
            outline: none;
            border-color: var(--color-primary);
            box-shadow: 0 0 0 4px rgba(201, 162, 39, 0.15), var(--shadow-glow);
            background: var(--color-card);
        }

        .game-input::placeholder {
            color: var(--color-text-muted);
            opacity: 0.7;
        }

        .game-label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--color-text);
        }

        /* Room Code */
        .room-code {
            font-family: 'Cairo', monospace;
            font-size: 2.5rem;
            font-weight: 900;
            letter-spacing: 0.15em;
            color: var(--color-primary);
            background: linear-gradient(135deg, rgba(201, 162, 39, 0.15) 0%, rgba(99, 102, 241, 0.1) 100%);
            padding: 16px 32px;
            border-radius: var(--radius-lg);
            border: 2px dashed var(--color-primary);
            display: inline-block;
            text-shadow: 0 0 20px rgba(201, 162, 39, 0.5);
        }

        /* Player Cards */
        .player-card {
            background: var(--color-card);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-md);
            padding: 16px;
            transition: all 0.2s ease;
        }

        .player-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
            border-color: var(--color-border-light);
        }

        .player-card.is-ready {
            border-color: var(--color-success);
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(52, 211, 153, 0.08) 100%);
        }

        .player-card.is-host {
            border-color: var(--color-primary);
            background: linear-gradient(135deg, rgba(201, 162, 39, 0.1) 0%, rgba(230, 195, 83, 0.08) 100%);
        }

        .player-card.is-eliminated {
            opacity: 0.5;
            border-color: var(--color-danger);
        }

        .player-card.is-revealed {
            border-color: var(--color-info);
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(59, 130, 246, 0.05) 100%);
        }

        .player-avatar {
            width: 48px;
            height: 48px;
            border-radius: var(--radius-full);
            background: linear-gradient(135deg, var(--color-secondary) 0%, var(--color-secondary-light) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 1.25rem;
            color: #fff;
        }

        /* Status Badge */
        .status-badge {
            padding: 4px 12px;
            border-radius: var(--radius-full);
            font-size: 0.75rem;
            font-weight: 700;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
        }

        .status-badge-ready {
            background: rgba(16, 185, 129, 0.2);
            color: var(--color-success-light);
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .status-badge-waiting {
            background: rgba(245, 158, 11, 0.2);
            color: var(--color-warning);
            border: 1px solid rgba(245, 158, 11, 0.3);
        }

        .status-badge-host {
            background: rgba(201, 162, 39, 0.25);
            color: var(--color-primary-light);
            border: 1px solid rgba(201, 162, 39, 0.4);
        }

        .status-badge-eliminated {
            background: rgba(239, 68, 68, 0.2);
            color: var(--color-danger-light);
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .status-badge-revealed {
            background: rgba(59, 130, 246, 0.2);
            color: var(--color-info);
            border: 1px solid rgba(59, 130, 246, 0.3);
        }

        /* Answer Card */
        .answer-card {
            background: var(--color-card);
            border: 2px solid var(--color-border);
            border-radius: var(--radius-lg);
            padding: 20px;
            text-align: center;
            transition: all 0.2s ease;
            cursor: pointer;
            backdrop-filter: blur(8px);
        }

        .answer-card:hover {
            border-color: var(--color-secondary);
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg), var(--shadow-glow-purple);
        }

        .answer-card .fake-name {
            font-size: 1.125rem;
            font-weight: 800;
            color: var(--color-primary);
            margin-bottom: 8px;
            text-shadow: 0 0 20px rgba(201, 162, 39, 0.4);
        }

        .answer-card .answer-text {
            font-size: 1rem;
            color: var(--color-text);
            background: rgba(99, 102, 241, 0.1);
            padding: 12px 16px;
            border-radius: var(--radius-md);
            margin-bottom: 12px;
            border: 1px solid rgba(99, 102, 241, 0.2);
        }

        /* Question Display */
        .question-display {
            background: linear-gradient(135deg, var(--color-card) 0%, var(--color-background-light) 100%);
            color: var(--color-text);
            padding: 32px;
            border-radius: var(--radius-xl);
            text-align: center;
            box-shadow: var(--shadow-lg);
            border: 2px solid var(--color-primary);
            position: relative;
            overflow: hidden;
        }

        .question-display::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--color-primary) 0%, var(--color-secondary) 50%, var(--color-primary) 100%);
        }

        .question-display .question-number {
            font-size: 0.875rem;
            color: var(--color-primary);
            margin-bottom: 8px;
            font-weight: 600;
        }

        .question-display .question-text {
            font-size: 1.5rem;
            font-weight: 700;
            line-height: 1.6;
            color: var(--color-text);
        }

        /* Progress */
        .game-progress {
            height: 8px;
            background: var(--color-border);
            border-radius: var(--radius-full);
            overflow: hidden;
        }

        .game-progress-bar {
            height: 100%;
            background: linear-gradient(90deg, var(--color-secondary) 0%, var(--color-primary) 100%);
            border-radius: var(--radius-full);
            transition: width 0.3s ease;
        }

        /* Logo */
        .game-logo {
            width: 80%;
            object-fit: contain;
            filter: drop-shadow(0 8px 24px rgba(201, 162, 39, 0.3));
        }

        .game-title {
            font-size: 3rem;
            font-weight: 900;
            color: var(--color-primary);
            margin-bottom: 0;
            text-shadow: 0 0 40px rgba(201, 162, 39, 0.5);
        }

        .game-subtitle {
            font-size: 1.125rem;
            color: var(--color-text-muted);
            font-weight: 500;
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }
        }

        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(-5px);
            }

            75% {
                transform: translateX(5px);
            }
        }

        .animate-fade-in {
            animation: fadeInUp 0.4s ease forwards;
        }

        .animate-pulse {
            animation: pulse 2s ease-in-out infinite;
        }

        .animate-shake {
            animation: shake 0.5s ease;
        }

        /* Alerts */
        .game-alert {
            padding: 16px 20px;
            border-radius: var(--radius-md);
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 12px;
            backdrop-filter: blur(8px);
        }

        .game-alert-success {
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: var(--color-success-light);
        }

        .game-alert-danger {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: var(--color-danger-light);
        }

        .game-alert-warning {
            background: rgba(245, 158, 11, 0.15);
            border: 1px solid rgba(245, 158, 11, 0.3);
            color: var(--color-warning);
        }

        .game-alert-info {
            background: rgba(59, 130, 246, 0.15);
            border: 1px solid rgba(59, 130, 246, 0.3);
            color: var(--color-info);
        }

        /* Modal Overrides */
        .modal-content {
            border: 1px solid var(--color-border);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-lg), var(--shadow-glow);
            background: var(--color-card);
            backdrop-filter: blur(20px);
        }

        .modal-header {
            border-bottom: 1px solid var(--color-border);
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.1) 0%, rgba(201, 162, 39, 0.08) 100%);
            border-radius: var(--radius-xl) var(--radius-xl) 0 0;
            color: var(--color-text);
        }

        .modal-body {
            background: var(--color-card);
            color: var(--color-text);
        }

        .modal-footer {
            border-top: 1px solid var(--color-border);
            background: var(--color-card);
        }

        .modal-backdrop {
            background-color: rgba(6, 8, 16, 0.85);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .game-title {
                font-size: 2rem;
            }

            .room-code {
                font-size: 1.75rem;
                padding: 12px 24px;
            }

            .question-display .question-text {
                font-size: 1.25rem;
            }

            .btn-game {
                padding: 10px 20px;
            }

            .btn-game-lg {
                padding: 12px 24px;
            }
        }

        /* Utilities */
        .gap-2 {
            gap: 0.5rem;
        }

        .gap-3 {
            gap: 1rem;
        }

        .gap-4 {
            gap: 1.5rem;
        }

        /* Player Chips (compact lobby) */
        .player-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            background: var(--color-card);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-full);
            font-size: 0.85rem;
            transition: all 0.2s ease;
        }

        .player-chip .avatar {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--color-secondary) 0%, var(--color-secondary-light) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.7rem;
            color: var(--color-primary);
        }

        .player-chip .name {
            font-weight: 600;
        }

        .player-chip.ready {
            border-color: var(--color-success);
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(52, 211, 153, 0.15) 100%);
        }

        .player-chip.waiting {
            border-color: var(--color-border);
            opacity: 0.7;
        }

        .player-chip.is-you {
            border-color: var(--color-primary);
            background: linear-gradient(135deg, rgba(201, 162, 39, 0.15) 0%, rgba(230, 195, 83, 0.2) 100%);
            box-shadow: 0 0 15px rgba(201, 162, 39, 0.2);
        }

        .player-chip.is-you .avatar {
            background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-light) 100%);
            color: #0a0e1a;
        }
    </style>
</head>

<body>
    <div id="app" class="min-vh-100 d-flex flex-column">
        @yield('content')
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Vite (for Laravel Echo) -->
    @vite(['resources/js/app.js'])

    <!-- PostHog Analytics -->
    @if(config('posthog.enabled') && config('posthog.api_key'))
        <script>
            !function (t, e) { var o, n, p, r; e.__SV || (window.posthog = e, e._i = [], e.init = function (i, s, a) { function g(t, e) { var o = e.split("."); 2 == o.length && (t = t[o[0]], e = o[1]), t[e] = function () { t.push([e].concat(Array.prototype.slice.call(arguments, 0))) } } (p = t.createElement("script")).type = "text/javascript", p.async = !0, p.src = s.api_host + "/static/array.js", (r = t.getElementsByTagName("script")[0]).parentNode.insertBefore(p, r); var u = e; for (void 0 !== a ? u = e[a] = [] : a = "posthog", u.people = u.people || [], u.toString = function (t) { var e = "posthog"; return "posthog" !== a && (e += "." + a), t || (e += " (stub)"), e }, u.people.toString = function () { return u.toString(1) + ".people (stub)" }, o = "capture identify alias people.set people.set_once set_config register register_once unregister opt_out_capturing has_opted_out_capturing opt_in_capturing reset isFeatureEnabled onFeatureFlags getFeatureFlag getFeatureFlagPayload reloadFeatureFlags group updateEarlyAccessFeatureEnrollment getEarlyAccessFeatures getActiveMatchingSurveys getSurveys onSessionId".split(" "), n = 0; n < o.length; n++)g(u, o[n]); e._i.push([i, s, a]) }, e.__SV = 1) }(document, window.posthog || []);
            posthog.init('{{ config('posthog.api_key') }}', { api_host: '{{ config('posthog.host') }}' });
        </script>
    @endif

    @stack('scripts')
</body>

</html>