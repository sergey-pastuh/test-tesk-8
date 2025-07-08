<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Task Planner</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="d-flex flex-column min-vh-100">

<header class="bg-light border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
    <h1 class="h4 mb-0">🗂️ Task Planner</h1>
    <div>
        @auth
            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                @csrf
                <button class="btn btn-outline-danger btn-sm">Logout</button>
            </form>
        @else
            <a href="{{ route('login') }}" class="btn btn-primary btn-sm me-1 fs-6">Login</a>
            <span class="text-muted small">or</span>
            <a href="{{ route('register') }}" class="btn btn-primary btn-sm ms-1 fs-6">Register</a>
        @endauth
    </div>
</header>

<main class="flex-grow-1 container py-4">
    @if (session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            {{ session('warning') }}
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @yield('content')
</main>

<footer class="bg-light border-top py-3 text-center text-muted small mt-auto">
    &copy; {{ date('Y') }} Task Planner. All rights reserved.
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
