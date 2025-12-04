<!doctype html>
<html lang="ar">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>سفرة حايل</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">

    <!-- Styles -->
    @vite([
        'resources/sass/app.scss',
        'resources/js/app.js',
    ])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    @stack('styles')
    <style>
        body {
            direction: rtl;
            font-family: 'Cairo', 'Nunito', system-ui, -apple-system, sans-serif;
            background: radial-gradient(circle at 15% 20%, rgba(191, 147, 92, 0.08), transparent 22%), radial-gradient(circle at 80% 10%, rgba(83, 120, 156, 0.08), transparent 20%), linear-gradient(180deg, #f9f3e9 0%, #f3e7d7 50%, #f7f0e5 100%);
            color: #4b3a2c;
            min-height: 100vh;
        }
        .desert-navbar {
            background: #fdf7ee;
            border-bottom: 1px solid #e6d7c3;
            box-shadow: 0 8px 20px rgba(73, 54, 36, 0.06);
        }
        .brand-logo {
            width: 64px;
            height: 64px;
            object-fit: contain;
            filter: drop-shadow(0 6px 12px rgba(58, 46, 36, 0.12));
        }
        .brand-title {
            font-weight: 800;
            color: #5a4737;
            line-height: 1.2;
        }
        .brand-sub {
            color: #8a725e;
            letter-spacing: 0.04em;
        }
        .nav-actions .btn,
        .nav-actions a.btn {
            border-radius: 12px;
            font-weight: 700;
            border-width: 2px;
        }
        .btn-outline-info,
        .btn-outline-info:focus {
            color: #6b4a2f;
            border-color: #d9b180;
            background-color: transparent;
        }
        .btn-outline-info:hover,
        .btn-outline-info:active {
            color: #fff;
            background-color: #6b4a2f;
            border-color: #6b4a2f;
        }
        .btn-primary,
        .btn-primary:focus {
            background-color: #6b4a2f;
            border-color: #6b4a2f;
        }
        .btn-primary:hover,
        .btn-primary:active {
            background-color: #553a27;
            border-color: #553a27;
        }
        .card {
            border: 1px solid #eadac4;
            border-radius: 18px;
            box-shadow: 0 10px 30px rgba(73, 54, 36, 0.08);
            background: #fffcf7;
        }
        main {
            position: relative;
        }
    </style>
</head>
<body>
    <div id="app">
        @stack('background')
        @auth
        <nav class="navbar navbar-expand-md navbar-light shadow-sm desert-navbar">
            <div class="container d-flex align-items-center justify-content-between">
                <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
                    <img src="{{ asset('imgs/logo-desert.svg') }}" alt="سفرة حايل" class="brand-logo ml-2">
                    <div>
                        <div class="brand-title">سفرة حايل</div>
                        <div class="brand-sub">هـ ١٤٤٧</div>
                    </div>
                </a>
                <div class="nav-actions d-flex align-items-center">
                    @if (Auth::user()->isAdmin())
                        <span class="mr-3 font-weight-bold text-dark">هلا بالمدير</span>
                        <a href="{{route('settings.index')}}" class="btn btn-sm btn-outline-info ml-2">الإعدادات</a>
                        <a href="{{route('home')}}" class="btn btn-sm btn-outline-info ml-2">الرئيسية</a>
                    @else
                        <span class="mr-3 font-weight-bold text-dark">أهلًا {{Auth::user()->name}}</span>
                        <a href="{{ route('logout') }}"
                           class="btn btn-sm btn-outline-info ml-2"
                           onclick="event.preventDefault();
                                document.getElementById('logout-form').submit();">تسجيل الخروج</a>
                        @if(Route::currentRouteName() == 'home')
                            <a href="{{route('users')}}" class="btn btn-sm btn-outline-info ml-2">قائمة المشاركين</a>
                        @else
                            <a href="{{route('home')}}" class="btn btn-sm btn-outline-info ml-2">الرئيسية</a>
                        @endif

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>

                    @endif
                </div>
            </div>
        </nav>
            @endauth

        <main class="py-4">
            @yield('content')
        </main>
    </div>
    <script>
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        })
    </script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"
    integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj"
    crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx"
    crossorigin="anonymous"></script>

    @stack('scripts')
</body>
</html>
