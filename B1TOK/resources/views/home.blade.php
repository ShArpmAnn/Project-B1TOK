@extends('layout')

@section('title', 'Главная')

@section('content')
    <div class="row align-items-center min-vh-50">
        <div class="col-lg-6">
            <h1 class="display-4 fw-bold mb-4">
                Контролируйте калории легко и удобно
            </h1>
            <p class="lead mb-4">
                Помогаем следить за питанием, считать калории и достигать ваших фитнес-целей.
                Интеграция с FatSecret API для быстрого поиска продуктов.
            </p>
            <div class="d-grid gap-2 d-md-flex">
                @guest
                    <a href="{{ route('register-home') }}" class="btn btn-success btn-lg px-4">
                        <i class="bi bi-person-plus"></i> Начать бесплатно
                    </a>
                    <a href="{{ route('login-home') }}" class="btn btn-outline-success btn-lg px-4">
                        <i class="bi bi-box-arrow-in-right"></i> Войти
                    </a>
                @else
                    <a href="{{ route('personal_cabinet') }}" class="btn btn-success btn-lg px-4">
                        <i class="bi bi-person-circle"></i> Мой кабинет
                    </a>
                    <a href="{{ route('diary') }}" class="btn btn-outline-success btn-lg px-4">
                        <i class="bi bi-journal-text"></i> Дневник питания
                    </a>
                @endguest
            </div>
        </div>
        <div class="col-lg-6">
            <img src="https://images.unsplash.com/photo-1490818387583-1baba5e638af?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80"
                 class="img-fluid rounded shadow"
                 alt="Здоровое питание">
        </div>
    </div>

    <div class="row mt-5 pt-5">
        <div class="col-md-4 mb-4">
            <div class="card h-100 border-0 text-center p-4">
                <div class="card-body">
                    <i class="bi bi-search text-success" style="font-size: 3rem;"></i>
                    <h4 class="card-title mt-3">База продуктов</h4>
                    <p class="card-text">
                        Доступ к миллионам продуктов через FatSecret API. Быстрый поиск и точные данные о КБЖУ.
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100 border-0 text-center p-4">
                <div class="card-body">
                    <i class="bi bi-graph-up text-success" style="font-size: 3rem;"></i>
                    <h4 class="card-title mt-3">Отслеживание целей</h4>
                    <p class="card-text">
                        Установите цели по весу, отслеживайте прогресс и получайте рекомендации по калориям.
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100 border-0 text-center p-4">
                <div class="card-body">
                    <i class="bi bi-book text-success" style="font-size: 3rem;"></i>
                    <h4 class="card-title mt-3">Ваши рецепты</h4>
                    <p class="card-text">
                        Сохраняйте любимые рецепты, считайте их пищевую ценность и быстро добавляйте в дневник.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
