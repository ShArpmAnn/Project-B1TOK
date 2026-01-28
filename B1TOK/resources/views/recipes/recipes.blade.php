@extends('layout')

@section('title', 'Мои рецепты')

@section('content')
    <div class="row mb-4">
        <div class="col">
            <h1><i class="bi bi-book text-success"></i> Мои рецепты</h1>
            <p class="text-muted">Сохраняйте и управляйте своими рецептами</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('new_recipes') }}" class="btn btn-success">
                <i class="bi bi-plus-circle"></i> Новый рецепт
            </a>
        </div>
    </div>

    @php
        $recipes = App\Models\SaveRecipe::forUser(Auth::id())->get();
    @endphp

    @if($recipes->count() > 0)
        <div class="row">
            @foreach($recipes as $recipe)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">{{ $recipe->title }}</h5>
                            <span class="badge bg-success">{{ $recipe->callorage }} ккал</span>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <h6>Пищевая ценность на порцию:</h6>
                                <div class="row g-2 text-center">
                                    <div class="col-3">
                                        <div class="bg-info bg-opacity-10 p-2 rounded">
                                            <div class="text-info fw-bold">{{ $recipe->proteins }}г</div>
                                            <small class="text-muted">Белки</small>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="bg-warning bg-opacity-10 p-2 rounded">
                                            <div class="text-warning fw-bold">{{ $recipe->fats }}г</div>
                                            <small class="text-muted">Жиры</small>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="bg-success bg-opacity-10 p-2 rounded">
                                            <div class="text-success fw-bold">{{ $recipe->carbohydrates }}г</div>
                                            <small class="text-muted">Углеводы</small>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="bg-primary bg-opacity-10 p-2 rounded">
                                            <div class="text-primary fw-bold">{{ $recipe->callorage }}</div>
                                            <small class="text-muted">Ккал</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if(is_array($recipe->food) && count($recipe->food) > 0)
                                <div class="mb-3">
                                    <h6>Ингредиенты:</h6>
                                    <ul class="list-group list-group-flush">
                                        @foreach($recipe->food as $ingredient)
                                            @if(!empty(trim($ingredient)))
                                                <li class="list-group-item px-0 py-1 small">{{ $ingredient }}</li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                        <div class="card-footer bg-transparent">
                            <div class="d-flex justify-content-between">
                                <form action="{{ route('update_recipes') }}" method="GET" class="d-inline">
                                    <input type="hidden" name="id" value="{{ $recipe->id }}">
                                    <button type="submit" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i> Изменить
                                    </button>
                                </form>

                                <form action="{{ route('delete_recipes') }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $recipe->id }}">
                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('Удалить этот рецепт?')">
                                        <i class="bi bi-trash"></i> Удалить
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-journal-x text-muted" style="font-size: 4rem;"></i>
                <h4 class="mt-3">Рецепты не найдены</h4>
                <p class="text-muted">Создайте свой первый рецепт для быстрого добавления в дневник</p>
                <a href="{{ route('new_recipes') }}" class="btn btn-success btn-lg">
                    <i class="bi bi-plus-circle"></i> Создать рецепт
                </a>
            </div>
        </div>
    @endif
@endsection
