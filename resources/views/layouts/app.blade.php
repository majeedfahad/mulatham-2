<!doctype html>
<html lang="ar">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>اليحياوي الملثم</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    @vite([
        'resources/sass/app.scss',
        'resources/js/app.js',
    ])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    @stack('styles')
</head>
<body style="direction: rtl">
    <div id="app">
        @stack('background')
        @auth
        <nav class="navbar navbar-expand-md navbar-light shadow-sm">
            <div class="container">
                <div class="navbar-brand text-white">

                        @if (Auth::user()->isAdmin())
                            <div class="row">
                                هلا بالمدير
                            </div>
                            <div class="row">
                                <div class="col-4">
                                    <a href="{{route('settings.index')}}" class="btn btn-sm btn-outline-info">الإعدادات</a>
                                </div>
                                <div class="col-4">
                                    <a href="{{route('home')}}" class="btn btn-sm btn-outline-info">الرئيسية</a>
                                </div>
                                <div class="col-4">
                                    <a href="https://alnamas.fun" class="btn btn-sm btn-outline-info">موقع النماص</a>
                                </div>
                            </div>

                        @else
                            <div class="row">
                                أهلًا {{Auth::user()->name}}
                            </div>
                            <div class="row">
                                <div class="col-4 pr-0">
                                    <a href="{{ route('logout') }}"
                                       class="btn btn-sm btn-outline-info"
                                       onclick="event.preventDefault();
                                            document.getElementById('logout-form').submit();">تسجيل الخروج</a>
                                </div>
                                <div class="col-4 pr-0">
                                    <a href="https://alnamas.fun" class="btn btn-sm btn-outline-info">موقع النماص</a>
                                </div>
                                <div class="col-4 pr-0">
                                    @if(Route::currentRouteName() == 'home')
                                        <a href="{{route('users')}}" class="btn btn-sm btn-outline-info">قائمة المشاركين</a>
                                    @else
                                        <a href="{{route('home')}}" class="btn btn-sm btn-outline-info">الرئيسية</a>
                                    @endif
                                </div>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>

                            </div>
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
