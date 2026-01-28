@extends('layout')

@section('title', 'Удаление рецепта')

@section('content')
    <div class="row">
        <div class="col-lg-6 mx-auto">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h4 class="mb-0"><i class="bi bi-trash"></i> Удаление рецепта</h4>
                </div>
                <div class="card-body">
                    @php
                        $recipeId = request('id');
                        $recipe = $recipeId ? App\Models\SaveRecipe::forUser(Auth::id())->where('id', $recipeId)->first() : null;
                    @endphp

                    @if($recipe)
                        <div class="alert alert-danger mb-4">
                            <div class="d-flex">
                                <i class="bi bi-exclamation-triangle fs-4 me-3"></i>
                                <div>
                                    <h5>Внимание! Это действие необратимо</h5>
                                    <p class="mb-0">
                                        Вы собираетесь удалить рецепт "<strong>{{ $recipe->title }}</strong>".
                                        После удаления восстановить его будет невозможно.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Информация о рецепте -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">{{ $recipe->title }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-2 text-center mb-3">
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

                                @if(is_array($recipe->food) && count($recipe->food) > 0)
                                    <h6>Ингредиенты:</h6>
                                    <ul class="list-group list-group-flush">
                                        @foreach($recipe->food as $ingredient)
                                            @if(!empty(trim($ingredient)))
                                                <li class="list-group-item px-0 py-1 small">{{ $ingredient }}</li>
                                            @endif
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                            <div class="card-footer text-muted">
                                <small>Создан: {{ $recipe->created_at->format('d.m.Y H:i') }}</small>
                            </div>
                        </div>

                        <form action="{{ route('delete_recipes') }}" method="POST">
                            @csrf
                            <input type="hidden" name="id" value="{{ $recipe->id }}">

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="confirmDelete" required>
                                    <label class="form-check-label" for="confirmDelete">
                                        Я понимаю, что это действие необратимо, и подтверждаю удаление рецепта
                                    </label>
                                </div>
                            </div>

                            <div class="d-grid gap-2 d-md-flex">
                                <button type="submit" class="btn btn-danger btn-lg" id="deleteButton" disabled>
                                    <i class="bi bi-trash"></i> Удалить рецепт
                                </button>
                                <a href="{{ route('save_recipes') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle"></i> Отмена
                                </a>
                            </div>
                        </form>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-journal-x text-muted" style="font-size: 4rem;"></i>
                            <h4 class="mt-3">Рецепт не найден</h4>
                            <p class="text-muted">Рецепт не существует или у вас нет к нему доступа</p>
                            <a href="{{ route('save_recipes') }}" class="btn btn-success">
                                <i class="bi bi-arrow-left"></i> Назад к рецептам
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const confirmCheckbox = document.getElementById('confirmDelete');
        const deleteButton = document.getElementById('deleteButton');

        if (confirmCheckbox && deleteButton) {
            confirmCheckbox.addEventListener('change', function() {
                deleteButton.disabled = !this.checked;
            });
        }

        // Предотвращаем случайное закрытие страницы
        window.addEventListener('beforeunload', function(e) {
            if (confirmCheckbox && confirmCheckbox.checked) {
                e.preventDefault();
                e.returnValue = 'Вы уверены, что хотите покинуть страницу? Изменения могут быть не сохранены.';
            }
        });
    </script>
@endpush
