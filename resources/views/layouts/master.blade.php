<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'TA Allocation System') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link href="/TeacherAssistantAllocationSystem/resources/css/app.css" rel="stylesheet">

    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'TA Allocation System') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a id="logout-button" class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        Logout
                                    </a>

                                    @if (Auth::user()->role == 'Teaching Assistant')
                                    <a class="dropdown-item" href="{{ route('edit_account') }}">Edit Your Account Details</a>
                                    @endif

                                    @if (Auth::user()->role == 'Admin')
                                    <a class="dropdown-item" href="{{ route('register') }}">Register New User</a>
                                    <a class="dropdown-item" href="{{ route('admin.areas_of_knowledge.show') }}">Create New Area Of Knowledge</a>
                                    <a class="dropdown-item" href="{{ route('admin.create_new_module.show') }}">Create New Module</a>
                                    @endif

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @if(session('success'))
            <div class="alert alert-success">
                {{session('success')}}
            </div>
            @elseif(session('error'))
            <div class="alert alert-danger">
                {{session('error')}}
            </div>
            @endif
            
            @if(Auth::check() && Auth::user()->teachingAssistant && !Auth::user()->teachingAssistant->isProfileComplete() && request()->route()->getName() == 'home')
            <div class="alert alert-warning">
                Your profile is incomplete! Please <a href="{{ route('edit_account') }}">click here</a> to update your information.
            </div>
             @endif
            @yield('content')
        </main>
    </div>
</body>
</html>
<script>
    setTimeout(function() {
        document.querySelector('.alert-success')?.remove();
    }, 5000);
    setTimeout(function() {
        document.querySelector('.alert-danger')?.remove();
    }, 10000);
</script>
