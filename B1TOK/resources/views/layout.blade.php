<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - B1TOK</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .navbar-brand {
            font-weight: bold;
            color: #198754 !important;
        }
        .main-content {
            flex: 1;
            padding: 20px 0;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .btn-success {
            background-color: #198754;
            border-color: #198754;
        }
        .btn-success:hover {
            background-color: #157347;
            border-color: #146c43;
        }
        .alert {
            border-radius: 8px;
        }
    </style>
</head>
<body>
<!-- Навигация -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">
            <i class="bi bi-heart-pulse"></i> B1TOK
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                @auth
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('personal_cabinet') }}">
                            <i class="bi bi-person-circle"></i> Кабинет
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('diary') }}">
                            <i class="bi bi-journal-text"></i> Дневник
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('save_recipes') }}">
                            <i class="bi bi-book"></i> Рецепты
                        </a>
                    </li>
                @endauth
            </ul>

            <div class="navbar-nav">
                @guest
                    <a class="nav-link" href="{{ route('login-home') }}">
                        <i class="bi bi-box-arrow-in-right"></i> Войти
                    </a>
                    <a class="btn btn-success ms-2" href="{{ route('register-home') }}">
                        Регистрация
                    </a>
                @else
                    <span class="nav-link text-muted">
                            Привет, {{ Auth::user()->name ?? 'Пользователь' }}
                        </span>
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger ms-2">
                            <i class="bi bi-box-arrow-right"></i> Выйти
                        </button>
                    </form>
                @endguest
            </div>
        </div>
    </div>
</nav>

<!-- Основной контент -->
<main class="main-content">
    <div class="container">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>
</main>

<!-- Футер -->
<footer class="bg-dark text-white py-4 mt-auto">
    <div class="container text-center">
        <p class="mb-0">&copy; {{ date('Y') }} B1TOK. Все права защищены.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Автоматическое скрытие alert через 5 секунд
    setTimeout(() => {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
</script>
@stack('scripts')
</body>
</html>
